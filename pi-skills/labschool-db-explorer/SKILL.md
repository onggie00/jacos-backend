---
name: labschool-db-explorer
description: "Use this skill whenever working on the Labschool Cibubur project and you need to read, inspect, or understand the structure/data of `labschool_db` MySQL databases — e.g. before building a new module (like Jadwal Pelajaran), checking existing table schemas (guru, kelas, mapel), inspecting columns/indexes/foreign keys, or sampling data to understand naming conventions. This skill is READ-ONLY by design: it must NEVER be used to INSERT, UPDATE, DELETE, ALTER, DROP, or TRUNCATE. For schema migrations or data writes, those must go through the application layer (CI3 migrations/models) and a human-reviewed process, not this skill."
license: Proprietary
---

# Labschool DB Explorer (Read-Only)

## Overview

This skill lets the agent safely explore the structure and contents of
the Labschool databases (`labschool_db`) without
any risk of modifying data. It is intended to be used BEFORE building
new modules (e.g. Jadwal Pelajaran) so the agent designs new tables
that are consistent with existing schema, naming conventions, and
relationships — instead of guessing or duplicating tables.

**Hard rule: this skill only ever runs SELECT, SHOW, DESCRIBE, and
EXPLAIN statements.** No exceptions, even if asked. If a task requires
writing data or altering schema, stop and tell the user this must be
done through a reviewed migration/model change, not through this
skill.

## Setup (one-time, human does this — not the agent)

The agent must NEVER use the application's main DB credentials
(the ones in `application/config/database.php` used by CI3 at
runtime) for exploration, because those credentials typically have
full read/write/alter privileges. Instead, a dedicated read-only
MySQL user must exist first.

If this user does not exist yet, surface this SQL to the human and
ask them to run it (the agent should not run privilege-granting SQL
itself):

```sql
CREATE USER IF NOT EXISTS 'labschool_ro'@'localhost' IDENTIFIED BY '';

GRANT SELECT ON labschool_db.* TO 'labschool_ro'@'localhost';

FLUSH PRIVILEGES;
```

Notes:
- Replace `CHANGE_THIS_PASSWORD` with a real password before running.
- If MySQL access is needed from a non-localhost host (e.g. Docker
  container), replace `'localhost'` accordingly, e.g. `'%'` scoped
  with a firewall, or the specific container network host.
- Store this read-only credential separately, e.g. in a
  `.env.readonly` or a dedicated section of `database.php`'s
  `db['readonly']` group — NOT mixed with the main `db['default']`
  group that the application uses for writes.

## Locating credentials

The project uses CodeIgniter 3 HMVC. Main app credentials live in:

```
application/config/database.php
```

This file defines one or more groups, typically:

```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => '...',
    'password' => '...',
    'database' => 'labschool_db',
    ...
);
```

There may be a second group for `neo_labschool` (check for a second
array key, e.g. `$db['neo']` or similar — naming varies by project,
so grep for it rather than assuming).

**For exploration, the agent should look for a read-only group**
instead, e.g. `$db['readonly']` or `$db['explorer']`. If no such group
exists yet, tell the user and point them to the Setup section above
— do not fall back to using `$db['default']` credentials for
exploration.

To extract credentials without hardcoding them in scripts, parse the
PHP file directly:

```bash
php -r '
require "application/config/database.php";
$g = $db["readonly"] ?? null;
if (!$g) { fwrite(STDERR, "No readonly db group found.\n"); exit(1); }
echo $g["hostname"] . "|" . $g["username"] . "|" . $g["password"] . "|" . $g["database"] . "\n";
'
```

This avoids ever writing the actual password into a script file or
chat transcript.

## Connecting

Once credentials are resolved, connect using the MySQL CLI explicitly
in read-only spirit (even though the user-level GRANT is the real
safety net):

```bash
mysql -h <hostname> -u labschool_ro -p<password> <database> -e "<QUERY>"
```

Prefer running one diagnostic query at a time and reviewing output,
rather than chaining many statements blindly.

## Exploration workflow

When starting work on a new module (e.g. Jadwal Pelajaran), follow
this sequence:

### 1. List all tables in both databases

```sql
SHOW TABLES;
```

Run against both `labschool_db` and `neo_labschool` — they are
separate databases, a table existing in one does not mean it exists
in the other.

### 2. Identify tables relevant to the new module

For Jadwal Pelajaran specifically, look for tables likely already
covering: guru (teachers), kelas (classes), mapel/mata_pelajaran
(subjects), jam/sesi (time slots), tahun_ajaran (academic year),
semester. Use pattern search since naming may vary:

```sql
SHOW TABLES LIKE '%guru%';
SHOW TABLES LIKE '%kelas%';
SHOW TABLES LIKE '%mapel%';
SHOW TABLES LIKE '%jadwal%';
```

### 3. Inspect structure of each relevant table

```sql
DESCRIBE guru_sd;
SHOW CREATE TABLE guru_sd;
```

`SHOW CREATE TABLE` is more useful than `DESCRIBE` because it also
reveals indexes, foreign keys, engine, and charset — important for
designing a new table that joins correctly with existing ones.

### 4. Check relationships / foreign keys

```sql
SELECT
    TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_SCHEMA = '<database>'
    AND REFERENCED_TABLE_NAME IS NOT NULL;
```

This reveals existing FK relationships across the schema, which the
new `jadwal_pelajaran` table should respect (e.g. if `kelas_id` is
referenced elsewhere as `INT(11) UNSIGNED`, the new table's foreign
key column should match the type exactly).

### 5. Sample real data to understand conventions

```sql
SELECT * FROM guru_sd LIMIT 5;
SELECT * FROM kelas LIMIT 5;
```

Use this to understand naming conventions (e.g. is it `nama_guru` or
`nama`? Is `status` an enum, tinyint flag, or varchar?), not to dump
large amounts of data. Always use `LIMIT`.

### 6. Check existing indexes (for performance planning)

```sql
SHOW INDEX FROM guru_sd;
```

Useful before designing queries for the new module's auto-generate /
shuffle algorithm (per AGENTS.md context: weekly teacher hour limits,
no double-booking constraints) — knowing what's indexed avoids
recommending a schema that will be slow at scale.

## What this skill must NOT do

- Never run `INSERT`, `UPDATE`, `DELETE`, `REPLACE`, `ALTER`, `DROP`,
  `TRUNCATE`, `CREATE TABLE`, `CREATE USER`, or `GRANT` statements.
- Never use the main `db['default']` application credentials for
  exploration, even temporarily, even if the readonly group doesn't
  exist yet — ask the human to set it up instead (see Setup).
- Never print full credential strings (host/user/password) into chat
  output or logs. Only use them inline within a single command
  execution.
- Never assume `neo_labschool` and `labschool_db` mirror each other —
  always check both explicitly.

## Output expectations

When reporting findings back (e.g. "here's the schema for the new
Jadwal Pelajaran module to build on"), summarize in prose: which
existing tables will be referenced, their relevant columns and types,
and what foreign keys the new table needs — rather than dumping raw
`SHOW CREATE TABLE` output unfiltered. Keep it actionable for the next
step (designing/migrating the new `jadwal_pelajaran` table).

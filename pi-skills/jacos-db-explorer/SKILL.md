---
name: jacos-db-explorer
description: "Use this skill whenever working on the Jacos project and you need to read, inspect, or understand the structure/data of the `jacos_db` MySQL database — e.g. before building a new module, checking existing table schemas (guru, kelas, mapel), inspecting columns/indexes/foreign keys, or sampling data to understand naming conventions. This skill is READ-ONLY by design: it must NEVER be used to INSERT, UPDATE, DELETE, ALTER, DROP, or TRUNCATE. For schema migrations or data writes, those must go through the application layer (CI3 migrations/models) and a human-reviewed process, not this skill."
license: Proprietary
---

# Jacos DB Explorer (Read-Only)

## Overview

This skill lets the agent safely explore the structure and contents of
the Jacos database (`jacos_db`) without any risk of modifying data.
It is intended to be used BEFORE building new modules so the agent
designs new tables that are consistent with existing schema, naming
conventions, and relationships — instead of guessing or duplicating
tables.

**Hard rule: this skill only ever runs SELECT, SHOW, DESCRIBE, and
EXPLAIN statements.** No exceptions, even if asked. If a task requires
writing data or altering schema, stop and tell the user this must be
done through a reviewed migration/model change, not through this skill.

## Environment (Laragon lokal)

| Item | Value |
|---|---|
| Host | `127.0.0.1:3306` |
| Database | `jacos_db` |
| Credentials | Lihat `application/config/database.php` group `default` (gitignored) |
| PHP CLI | `D:/laragon/bin/php/php-7.4.33-Win32-vc15-x64/php.exe` |

The database and the app share the same local environment — there is
no separate read-only user setup anymore (the old `labschool_ro`/
Docker setup is retired). Read-only discipline is enforced by THIS
skill's hard rule, not by DB grants.

**Production credentials must never be used here.** If a task needs
data from the production DB, stop and ask the user — never grep
production credentials from deployment configs.

## Connecting

Two equivalent ways (prefer PHP mysqli — no password-on-CLI issues):

```bash
# Option A: PHP one-liner (recommended)
php -r "\$m=new mysqli('127.0.0.1','root','','jacos_db'); \$r=\$m->query('SHOW TABLES'); while(\$x=\$r->fetch_row()) echo \$x[0],PHP_EOL;"

# Option B: mysql CLI (via Laragon's mysql client if on PATH)
mysql -h 127.0.0.1 -u root jacos_db -e "<QUERY>"
```

Parse credentials from `application/config/database.php` if they
change — do not hardcode passwords in scripts or chat output.

## Exploration workflow

When starting work on a new module, follow this sequence:

### 1. List all tables

```sql
SHOW TABLES;
```

### 2. Identify tables relevant to the new module

Use pattern search since naming may vary (many tables have jenjang
suffixes `_ft`, `_sd`, `_smp`, `_sma`):

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
WHERE REFERENCED_TABLE_SCHEMA = 'jacos_db'
    AND REFERENCED_TABLE_NAME IS NOT NULL;
```

This reveals existing FK relationships across the schema, which the
new table should respect (e.g. match column types exactly).

### 5. Sample real data to understand conventions

```sql
SELECT * FROM guru_sd LIMIT 5;
```

Use this to understand naming conventions (e.g. `nama_lengkap` vs
`nama`? `status` as enum, tinyint flag, or varchar?), not to dump
large amounts of data. Always use `LIMIT`.

### 6. Check existing indexes (for performance planning)

```sql
SHOW INDEX FROM guru_sd;
```

## What this skill must NOT do

- Never run `INSERT`, `UPDATE`, `DELETE`, `REPLACE`, `ALTER`, `DROP`,
  `TRUNCATE`, `CREATE TABLE`, `CREATE USER`, or `GRANT` statements.
- Never touch the `otherdb`/`jacos_sec_db` connection group — it is a
  separate secondary database (see `application/config/database.php`).
- Never print full credential strings (password) into chat output or
  logs. Only use them inline within a single command execution.
- Never assume tables mirror each other across jenjang variants
  (`_ft`/`_sd`/`_smp`/`_sma`) — check each explicitly.

## Output expectations

When reporting findings back, summarize in prose: which existing
tables will be referenced, their relevant columns and types, and what
foreign keys the new table needs — rather than dumping raw
`SHOW CREATE TABLE` output unfiltered. Keep it actionable for the
next step (designing/migrating the new module's tables).

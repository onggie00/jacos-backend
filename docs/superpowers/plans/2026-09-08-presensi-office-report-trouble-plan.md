# Presensi Office Report Trouble Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make digital check-in/check-out reports independent, correctly reviewable, and classify approved checkout at or after shift start as `SH`.

**Architecture:** Keep one `presensi_office` row per NPP/date. Report API fills only the missing side. Admin approval processes only requested side, including reports without files. Both history APIs expose consistent pending/completion flags so approved check-in does not block checkout.

**Tech Stack:** CodeIgniter 3, PHP 5.2-compatible production code, MySQL, standalone assert test executed in PHP container.

## Global Constraints

- No production data writes during development.
- Preserve existing tables and columns; no migration in this task.
- Use `array()` and PHP 5.2-compatible syntax in new production code.
- `check_out >= start_checkout` approved status is `SH`; `limit_checkout` does not downgrade it to `-`.
- Check-in and check-out approval state must remain independent.

### Task 1: Add failing attendance-rule test

**Files:**
- Create: `Berkas_sample/file_test_tmp/test_presensi_office_rules.php`

- [ ] Test classification rules: before start checkout = `E`; at start = `SH`; after limit = `SH`; missing time = `-`.
- [ ] Test report state: approved check-in with empty checkout still allows checkout; pending checkout without a file remains actionable.
- [ ] Run:

```bash
MSYS_NO_PATHCONV=1 docker exec php74 php /var/www/html/Berkas_sample/file_test_tmp/test_presensi_office_rules.php
```

Expected: FAIL because shared rule class does not exist.

### Task 2: Implement shared rules

**Files:**
- Create: `application/libraries/Presensi_office_rules.php`
- Modify: `Berkas_sample/file_test_tmp/test_presensi_office_rules.php`

**Interface:**
- `Presensi_office_rules::checkoutStatus($checkOut, $startCheckout)` returns `E`, `SH`, or `-`.
- `Presensi_office_rules::canReportCheckout($row)` returns boolean based on empty `check_out`, independent of check-in approval.
- `Presensi_office_rules::isPendingCheckin($row)` and `isPendingCheckout($row)` return boolean based on status/report flags, not file presence.

- [ ] Implement minimum rule methods.
- [ ] Run test and expect PASS.

### Task 3: Fix Report_trouble API

**Files:**
- Modify: `application/controllers/apiapp/presensi_office/Report_trouble.php`

- [ ] Validate non-empty, valid `waktu_laporan` and `presensi_date` before DB access.
- [ ] Keep check-in/check-out selection based on shift `start_checkout`.
- [ ] Ensure existing approved check-in plus empty check-out accepts checkout report.
- [ ] Preserve check-in fields when adding checkout and preserve checkout fields when adding check-in.
- [ ] Set `status_presensi_selesai=TROUBLE` and `report_status_end=0` for checkout even without upload.
- [ ] Use a transaction/lock or equivalent re-check to prevent duplicate same-NPP/date rows.

### Task 4: Fix admin approval and action visibility

**Files:**
- Modify: `modules/presensi_office/controllers/backend/Presensi_office.php`

- [ ] Approve/decline check-out when `status_presensi_selesai=TROUBLE` even if `file_report_end` is empty.
- [ ] Approve/decline only the requested `type`.
- [ ] Set checkout status using shared rule: before start = `E`, at/after start = `SH`, missing = `-`.
- [ ] Mark report flags independently.
- [ ] Show Approve/Decline Pulang for status-based pending checkout, not file-only.

### Task 5: Fix both history APIs

**Files:**
- Modify: `application/controllers/apiapp/presensi_office/Riwayat_presensi.php`
- Modify: `application/controllers/apiapp/presensi_office/Riwayat_presensi_by_npp.php`

- [ ] Show `Pengajuan Masuk` for pending TROUBLE check-in even without file.
- [ ] Show `Pengajuan Pulang` for pending TROUBLE checkout even without file.
- [ ] Set `is_presensi_today` only when both check-in and checkout are complete, or expose separate `can_report_check_in` and `can_report_check_out` flags.
- [ ] Keep both endpoints' status and flag semantics identical.
- [ ] Remove nondeterministic date grouping from the personal history query.

### Task 6: Verify

**Files:**
- Modify: `TASKS.md`

- [ ] Run rules test and PHP lint on all changed PHP files.
- [ ] Run read-only SQL checks for pending/approved app rows and status `-` examples.
- [ ] Run `git diff --check` with repository CRLF handling.
- [ ] Do not repair historical rows automatically; report repair candidates separately.

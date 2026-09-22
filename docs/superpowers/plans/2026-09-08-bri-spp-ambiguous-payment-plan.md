# BRI SPP Ambiguous Payment Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Prevent legacy BRI payment notifications and BRI report fallback from marking the wrong SD SPP month while preserving grouped multi-month invoices.

**Architecture:** Add one small pure matcher that resolves a payment only when exactly one active `kode_tagihan` group matches VA and total amount. `Notification.php` uses matcher result and updates that group only. `Cron_cek_spp.php` applies same ambiguity guard and updates the group only; unresolved matches stay pending. Normalize SD transaction casing in webhook path.

**Tech Stack:** CodeIgniter 3, PHP 5.2-compatible production syntax, MySQL read/write through existing `Mymodel`, standalone PHP assert test.

## Global Constraints

- Do not modify production data or run write SQL during implementation.
- Use `array()` and PHP 5.2-compatible production syntax.
- Preserve multi-month semantics: one `kode_tagihan`, comma-separated `detail_bulan`, `count_bill` amount validation.
- Never choose between multiple matching invoice groups automatically.
- Touch only `application/controllers/apiapp/Notification.php`, `application/controllers/apiapp/cron/Cron_cek_spp.php`, new matcher library, test file, and task tracking.
- Preserve unrelated existing working-tree changes.

### Task 1: Add failing matcher test

**Files:**
- Create: `Berkas_sample/file_test_tmp/test_bri_spp_matcher.php`

- [ ] Write assertions for these behaviors:
  - two active groups with same VA and amount return ambiguous/no match;
  - one grouped invoice with `detail_bulan=Juli,Agustus`, `count_bill=2`, and matching amount returns its `kode_tagihan`;
  - wrong amount returns no match.

- [ ] Run:

```bash
docker cp Berkas_sample/file_test_tmp/test_bri_spp_matcher.php php74:/tmp/test_bri_spp_matcher.php
docker exec php74 php /tmp/test_bri_spp_matcher.php
```

Expected: FAIL because `Bri_spp_matcher` does not exist.

### Task 2: Implement matcher

**Files:**
- Create: `application/libraries/Bri_spp_matcher.php`
- Modify: `Berkas_sample/file_test_tmp/test_bri_spp_matcher.php`

**Interface:**
- `Bri_spp_matcher::resolve($rows, $billAmount)` returns representative transaction object when exactly one `kode_tagihan` group matches, otherwise `false`.
- Matching requires non-empty `kode_tagihan`, `total_biaya * count_bill == billAmount`, and non-empty `detail_bulan` count equal to `count_bill`.

- [ ] Implement minimum PHP 5.2-compatible matcher.
- [ ] Run same test and expect PASS.

### Task 3: Harden legacy BRI webhook

**Files:**
- Modify: `application/controllers/apiapp/Notification.php:93-110, 313-370`

- [ ] Load matcher only in SPP path.
- [ ] Replace single latest-row lookup with active SD candidate query filtered by VA and unexpired status.
- [ ] Resolve by exact amount and unique `kode_tagihan` group.
- [ ] Return payment error without updating rows when no group or multiple groups match.
- [ ] Update `transaksi_spp` by selected `kode_tagihan` plus `status_transaksi = 1`, never by VA alone.
- [ ] Normalize `$jenjang` with `strtoupper()` and update `spp_sd` columns from `detail_bulan`.
- [ ] Keep `detail_bulan` multi-month loop and `count_bill` amount validation.
- [ ] Keep raw payload logging in `bri_res`; do not alter unrelated PSB/tagihan-lain branches.
- [ ] Run `docker exec php74 php -l /var/www/html/application/controllers/apiapp/Notification.php`.

### Task 4: Harden BRI report fallback

**Files:**
- Modify: `application/controllers/apiapp/cron/Cron_cek_spp.php:28-105`

- [ ] Before applying a report row, query current active candidates for same VA and exact amount.
- [ ] Resolve candidates through `Bri_spp_matcher`; skip ambiguous or unmatched groups.
- [ ] Preserve existing blank-name, account, teller, and amount guards.
- [ ] Update the selected `kode_tagihan` group, not one stale `id_transaksi` row.
- [ ] Update every month in selected `detail_bulan` on `spp_sd`.
- [ ] Re-check group status before update so repeated loop iterations cannot process same group twice.
- [ ] Keep existing `payment_response_bri` skip as a conservative duplicate guard; do not broaden it.
- [ ] Run `docker exec php74 php -l /var/www/html/application/controllers/apiapp/cron/Cron_cek_spp.php`.

### Task 5: Verify and review

**Files:**
- Modify: `TASKS.md`

- [ ] Run matcher test, PHP lint for all changed PHP files, and `git diff --check`.
- [ ] Run read-only SQL showing Evano's July/August/September rows and `spp_sd` status; no data writes.
- [ ] Confirm only intended task files changed in final diff; preserve unrelated pre-existing changes.
- [ ] Report that ambiguous payments remain pending for manual reconciliation; no production repair SQL is executed.

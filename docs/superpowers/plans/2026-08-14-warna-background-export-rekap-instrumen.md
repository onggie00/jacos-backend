# Warna Background Export Rekap Instrumen Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Apply pastel background colors to per-instrument score cells and overall category in Excel export.

**Architecture:** Controller bulk query joins score bands and profile categories, then passes color metadata with each export row. PHPExcel library writes rows and applies known pastel fills after row striping so score/category fills win.

**Tech Stack:** CodeIgniter 3, PHP 5.2-compatible syntax, legacy PHPExcel.

## Global Constraints

- Reuse `mhcu_band_kategori`; no threshold mapping in controller.
- Use `mhcu_profil_kategori.warna` for overall category.
- Do not color Instrumen 6.
- Use pastel palette: `C6EFCE`, `FFEB9C`, `FCE4D6`, `FFC7CE`.
- Do not add dependencies or unrelated refactors.

---

### Task 1: Add score and category color metadata

**Files:**
- Modify: `modules/mhcu_sesi/controllers/backend/Mhcu_sesi.php:221-280`

**Interfaces:**
- Produces export fields `who5_warna`, `phq9_warna`, `gad7_warna`, `cbi_warna`, `psikososial_warna`, and `kategori_warna`.

- [ ] **Step 1: Extend score SELECT with `bk.warna` and LEFT JOIN band ranges.**

Use the existing session ID list and add:

```sql
SELECT mis.id_sesi, mi.kode_instrument, mis.dimensi_aspek, mis.skor, bk.warna
FROM mhcu_instrument_score mis
JOIN mhcu_instrument mi ON mi.id_instrument = mis.id_instrument
LEFT JOIN mhcu_band_kategori bk
    ON bk.kode_instrument = mi.kode_instrument
    AND (
         (bk.dimensi_aspek IS NULL AND mis.dimensi_aspek IN ('Kesejahteraan Psikologis','Depresi','Kecemasan','CBI Keseluruhan'))
      OR (bk.dimensi_aspek = 'Total' AND mis.dimensi_aspek LIKE '%Total%')
    )
    AND mis.skor BETWEEN bk.batas_bawah AND bk.batas_atas
WHERE mis.id_sesi IN (" . $ids_csv . ")
ORDER BY mi.no_urut ASC
```

- [ ] **Step 2: Store each matched `warna` beside its score.**

For each existing score branch, assign both score and color:

```php
$skor_map[$sr->id_sesi]['who5'] = $sr->skor;
$skor_map[$sr->id_sesi]['who5_warna'] = $sr->warna;
```

Repeat for `phq9`, `gad7`, `cbi`, and `psikososial`, preserving existing case-insensitive dimension checks and Psikososial role handling.

- [ ] **Step 3: Include profile color in hasil query and export row.**

Change hasil SELECT to include `pk.warna AS kategori_warna`; initialize `$kategori_warna`; copy it from `$hasil_map[$sid]`; add all six color fields to `$export_data`, including `kategori_warna`.

- [ ] **Step 4: Run PHP lint.**

Run:

```bash
"C:/xampp/php/php.exe" -l modules/mhcu_sesi/controllers/backend/Mhcu_sesi.php
```

Expected: `No syntax errors detected`.

### Task 2: Apply pastel fills in PHPExcel export

**Files:**
- Modify: `application/libraries/Mhcu_excel.php:456-620`

**Interfaces:**
- Consumes the color fields produced by Task 1.
- Uses private helper `_get_rekap_color_hex($warna)` returning a six-character hex string or empty string.

- [ ] **Step 1: Add minimal color helper.**

Add before `_col()`:

```php
private function _get_rekap_color_hex($warna)
{
    $colors = array(
        'hijau' => 'C6EFCE',
        'kuning' => 'FFEB9C',
        'orange' => 'FCE4D6',
        'merah' => 'FFC7CE',
    );
    $warna = strtolower(trim((string) $warna));
    return isset($colors[$warna]) ? $colors[$warna] : '';
}
```

- [ ] **Step 2: Apply score fills after row striping.**

Keep existing score writes. After alternating-row fill, map score keys to color keys and apply only when helper returns a color:

```php
$warna_keys = array('who5_warna', 'phq9_warna', 'gad7_warna', 'cbi_warna', 'psikososial_warna');
for ($warna_idx = 0; $warna_idx < count($warna_keys); $warna_idx++) {
    $hex = $this->_get_rekap_color_hex(isset($d[$warna_keys[$warna_idx]]) ? $d[$warna_keys[$warna_idx]] : '');
    if (!empty($hex)) {
        $cell = $this->_col($skor_start_col + $warna_idx) . $row;
        $sheet->getStyle($cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB($hex);
    }
}
```

- [ ] **Step 3: Apply category fill, leave Instrumen 6 untouched.**

After score fills, apply `kategori_warna` to `$kat_col` using same helper and PHPExcel fill API. Do not apply fill to `$skor_start_col + 5`.

- [ ] **Step 4: Run PHP lint.**

Run:

```bash
"C:/xampp/php/php.exe" -l application/libraries/Mhcu_excel.php
```

Expected: `No syntax errors detected`.

### Task 3: Verify diff and runtime prerequisites

**Files:**
- Review: `modules/mhcu_sesi/controllers/backend/Mhcu_sesi.php`
- Review: `application/libraries/Mhcu_excel.php`

- [ ] **Step 1: Inspect diff.**

Run `git diff --check` and `git diff -- <two files>`; confirm no unrelated edits.

- [ ] **Step 2: Check database seed availability.**

Use read-only inspection only: confirm `mhcu_band_kategori` contains both `PSIKOSOSIAL` and `PSIKOSOSIAL_PIMPINAN` rows and expected color names. Do not modify database.

- [ ] **Step 3: Run available project checks.**

Run lint commands from Tasks 1-2. If authenticated export cannot run in harness, report live three-person verification as pending rather than claiming it passed.

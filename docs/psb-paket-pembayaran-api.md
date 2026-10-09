# API Paket Pembayaran PSB (KB/TK/SD)

> Dibuat: 2026-10-10 · Prompt: `Berkas_sample/agent-prompt/20261010-psb-paket-pembayaran-be.md`
> Untuk prompt FE (web PSB Next.js). Bentuk data = kontrak final.

## Ringkasan

Tiga paket pembayaran (pilih salah satu, default **opsi 1** saat submit pendaftaran):

1. Uang Pendaftaran saja.
2. Pendaftaran + cicilan pertama uang pangkal (`tipe_tagihan='open'`, bisa dibayar bertahap; admin flag `fixed` manual saat rekap).
3. Pendaftaran + pelunasan uang pangkal dengan diskon pelunasan (default 10%).

Nominal **dihitung backend** dari `biaya_pendaftaran` per jenjang (reguler / internal).
Pendaftar **internal** = TK asal terdaftar di `list_sekolah_tk` dengan `is_internal=1`
(saat ini "TK JAKARTA COSMOPOLITE SCHOOL (JACOS)").

## Endpoint

Base: `{base_url}/apiweb/psb/paket_pembayaran`

| Endpoint | Method | Fungsi |
|---|---|---|
| `/opsi` | POST | Daftar opsi + status kunci utk transaksi milik pemanggil |
| `/pilih` | POST | Pilih paket (update transaksi yang sama, bukan transaksi baru) |

**Header wajib:** `X-API-KEY: <key>` (sama dengan endpoint apiweb lain).

**Body: form-encoded / form-data** (bukan raw JSON — REST_Controller CI3 di project ini
hanya mem-parse `$_POST`; JSON body tidak dibaca).

### Identitas pemanggil

`email` + `nama_lengkap` harus sama dengan data di transaksi (`user_email`, `user_name`)
dan siswa — mekanisme sama dengan API Cek Data.

---

## A. POST `/opsi`

Request: `email`, `nama_lengkap`, `no_transaksi`

Response sukses (field kontrak di level atas; `status`/`message` tambahan):

```json
{
  "status": 1,
  "message": "Berhasil",
  "no_transaksi": "JI-KB-20261010-3",
  "pilihan_aktif": 1,
  "terkunci": false,
  "alasan_terkunci": null,
  "is_internal": false,
  "opsi": [
    {
      "kode": 1,
      "judul": "Uang Pendaftaran",
      "deskripsi": "Admission Fee",
      "rincian": [ { "label": "Uang Pendaftaran", "nominal": 1000000 } ],
      "diskon_persen": 0,
      "total": 1000000
    },
    {
      "kode": 2,
      "judul": "Pendaftaran + Cicilan Pertama Uang Pangkal",
      "deskripsi": "Admission Fee + First Installment of the Enrollment Fee",
      "rincian": [
        { "label": "Uang Pendaftaran", "nominal": 1000000 },
        { "label": "Cicilan Pertama Uang Pangkal", "nominal": 10000000 }
      ],
      "diskon_persen": 0,
      "total": 11000000
    },
    {
      "kode": 3,
      "judul": "Pendaftaran + Pelunasan Uang Pangkal",
      "deskripsi": "Admission Fee + Full Payment Enrollment Fee - (Disc. 10%)",
      "rincian": [
        { "label": "Uang Pendaftaran", "nominal": 1000000 },
        { "label": "Uang Pangkal", "nominal": 35000000 },
        { "label": "Diskon Pelunasan 10%", "nominal": -3500000 }
      ],
      "diskon_persen": 10,
      "total": 32500000
    }
  ]
}
```

- `pilihan_aktif`: `1`–`3`; `NULL` di DB (data lama) dikembalikan sebagai `1`.
- `terkunci=true` + `alasan_terkunci`: `"sudah_terkunci"` (VA sudah dibuat) atau
  `"sudah_lunas"` → FE tampilkan pilihan aktif saja (tidak bisa diganti).
- `deskripsi` = teks yang masuk kwitansi (bahasa Inggris); `judul` = label FE (Indonesia).
- Contoh `opsi[2]` di atas untuk **KB** (total 32,5 jt). SD = 40,6 jt, TK = 28 jt.

## B. POST `/pilih`

Request: `email`, `nama_lengkap`, `no_transaksi`, `pilihan_pembayaran` (`1`|`2`|`3`)

Response sukses:

```json
{
  "status": 1,
  "message": "Berhasil memilih paket pembayaran",
  "no_transaksi": "JI-KB-20261010-3",
  "pilihan_pembayaran": 2,
  "total_biaya": 11000000,
  "description": "Admission Fee + First Installment of the Enrollment Fee",
  "judul_tagihan": "Tagihan Pendaftaran + Cicilan Pertama Uang Pangkal KB a.n KB TEST",
  "tipe_tagihan": "open"
}
```

- Update **transaksi yang sama**: `total_biaya`, `description`, `pilihan_pembayaran`,
  `tipe_tagihan` (`opsi 2 = 'open'`, opsi 1/3 = `'fixed'`), `updated_at`.
- `judul_tagihan` = heading FE; jika tidak ada, FE fallback ke `description`.

Response gagal (HTTP 200, format error API yang ada) + field `kode`:

| kode | Kondisi |
|---|---|
| `pilihan_invalid` | `pilihan_pembayaran` bukan 1/2/3 |
| `sudah_terkunci` | VA sudah digenerate (`va_number` terisi) |
| `sudah_lunas` | `status_transaksi = 1` |
| `bukan_transaksi_pendaftaran` | Prefix bukan `JI-` (mis. tagihan daftar ulang) |
| `tidak_berhak` | Transaksi tidak ada / bukan milik pemanggil |

Contoh: `{"status":0,"kode":"sudah_terkunci","message":"Virtual Account sudah dibuat, pilihan tidak bisa diubah","data":[]}`

## C. Cek Data (endpoint yang sudah ada)

`POST /apiweb/siswa/cek_data` (dan `/apiapp/siswa/cek_data`) kini mengembalikan tambahan:

- top-level: `pilihan_pembayaran`, `description`, `judul_tagihan` (untuk jenjang KB/TK/SD,
  bila transaksi pendaftaran `LI-/JI-`)
- `detail_transaksi[]` (branch pendaftaran): `pilihan_pembayaran`, `judul_tagihan`
  (`description` sudah ada sebelumnya)

Field lama tidak berubah. Data lama (`pilihan_pembayaran=NULL`) → `1` + description lama.
Cek Data juga kini mengembalikan `riwayat_payment` untuk bank BNI (dari
`payment_response_bni`, bukan hanya BRI).

## Aturan kunci

Pilihan boleh diganti selama: tagihan **belum lunas** dan **VA belum digenerate**.
Setelah VA terbit / lunas → tolak (`sudah_terkunci` / `sudah_lunas`).
Perubahan harga/flag `biaya_pendaftaran` hanya berlaku untuk pilihan yang baru disimpan
(tagihan lama tidak dihitung ulang).

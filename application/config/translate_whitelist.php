<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * config/translate_whitelist.php — Whitelist field response API yang boleh
 * diterjemahkan (DeepL) pada fase translate switch bahasa.
 *
 * Kunci : "<role>/<controller>/<HTTP-METHOD>" — role = folder di apiapp
 *         ("root" = file langsung di application/controllers/apiapp/),
 *         controller = segment URL (lowercase), method = http method nyata.
 * Route : /apiapp/<role>/<controller>
 *
 * Nilai: path_field => array('tipe' => 'teks'|'label'|'html', 'bersama' => bool)
 *   'teks'  : teks natural plain
 *   'label' : kosakata pendek berulang (hari/status/jenis) — bergantung tabel override
 *   'html'  : teks berformat HTML (tag-preserving)
 *   bersama = TRUE: konten ADMIN/lembaga, sama untuk semua user (aman di-cache
 *             hasil translate sekali untuk semua user, per string).
 *   bersama = FALSE: konten per-user / bisa menyisip data personal — hasil
 *             translate TIDAK boleh di-cache lintas user.
 *
 * Path: notasi titik + wildcard array "data.*.<field>"; "data.<field>" utk
 * row tunggal.
 *
 * Keputusan review (lengkap di Berkas_sample/fase4_whitelist_report.md):
 * - MHCU        : seluruh endpoint Mhcu exclude (instrumen psikologi).
 * - UGC_PRIBADI : teks yang ditulis user (guru/ortu/siswa) tentang individu
 *                 (keterangan presensi/izin, catatan pelanggaran, KPI, prestasi
 *                 pribadi) — exclude, TIDAK boleh otomatis diterjemahkan.
 * - NAMA_LEMBAGA: nama sekolah/PT/alamat/oid identitas — exclude.
 * - TABEL_TIDAK_ADA : endpoint sumber tabelnya tidak ada di DB — exclude.
 *
 * Data pribadi (nama orang, NIS, email, no HP, VA, nominal, tanggal) TIDAK
 * pernah berada di sini. Struktur response tidak boleh berubah.
 */

$config['translate_whitelist'] = array(

    // ========================= SISWA =========================
    'siswa/agenda/POST' => array(
        'data.*.judul'         => array('tipe' => 'teks',  'bersama' => true),
        'data.*.deskripsi'     => array('tipe' => 'html',  'bersama' => true),
        'data.*.nama_kategori' => array('tipe' => 'teks',  'bersama' => true),
    ),
    'siswa/kategori_agenda/GET' => array(
        'data.*.nama_kategori' => array('tipe' => 'teks', 'bersama' => true),
    ),
    'siswa/detail_ekskul/POST' => array(
        'data.*.nama'   => array('tipe' => 'teks', 'bersama' => true),
        'data.*.judul'  => array('tipe' => 'teks', 'bersama' => true),
        'data.*.konten' => array('tipe' => 'html', 'bersama' => true),
    ),
    'siswa/get_ekskul/POST' => array(
        'data.*.nama'   => array('tipe' => 'teks', 'bersama' => true),
        'data.*.judul'  => array('tipe' => 'teks', 'bersama' => true),
        'data.*.konten' => array('tipe' => 'html', 'bersama' => true),
    ),
    'siswa/get_jadwal_ekskul/POST' => array(
        'data.*.nama_ekskul' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hari'        => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/jadwal_mapel_sd/POST' => array(
        'data.*.nama_mapel' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hari'       => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/jadwal_mapel_smp/POST' => array(
        'data.*.nama_mapel' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hari'       => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/jadwal_mapel_sma/POST' => array(
        'data.*.nama_mapel' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hari'       => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/jadwal_mapel_ft/POST' => array(
        'data.*.nama_mapel' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hari'       => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/jadwal_ujian_sd/GET' => array(
        'data.*.nama_ujian' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.nama_mapel' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hari'       => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/jadwal_ujian_smp/GET' => array(
        'data.*.nama_ujian' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.nama_mapel' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hari'       => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/jadwal_ujian_sma/GET' => array(
        'data.*.nama_ujian' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.nama_mapel' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hari'       => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/jadwal_ujian_ft/GET' => array(
        'data.*.nama_ujian' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.nama_mapel' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hari'       => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/export_kartu_ujian/GET' => array(
        'data.*.nama_ujian' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.nama_mapel' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hari'       => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/get_presensi/POST' => array(
        'data.*.status_absen' => array('tipe' => 'label', 'bersama' => true),
        'data.*.hari_absen'   => array('tipe' => 'label', 'bersama' => true),
        // keterangan & alasan_terlambat = teks ditulis guru/tentang individu
        // → EXCLUDED (UGC_PRIBADI), lihat translate_whitelist_excluded.
    ),
    'siswa/get_presensi_ekskul/POST' => array(
        'data.*.status_absen' => array('tipe' => 'label', 'bersama' => true),
        'data.*.hari_absen'   => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/list_ibadah_tipe/GET' => array(
        'data.*.ibadah'     => array('tipe' => 'label', 'bersama' => true), // dzuhur | ashar | dhuha
        'data.*.keterangan' => array('tipe' => 'teks',  'bersama' => true), // ket. admin per tipe ibadah
    ),
    'siswa/cek_ibadah_tipe/POST' => array(
        'data.*.ibadah' => array('tipe' => 'label', 'bersama' => true),
        // keterangan di query ini berasal dari tabel ibadah (entri individu)
        // → EXCLUDED (UGC_PRIBADI)
    ),
    'siswa/riwayat_presensi_ibadah/POST' => array(
        'data.*.ibadah' => array('tipe' => 'label', 'bersama' => true),
    ),
    'siswa/fasilitas/POST' => array(
        'data.*.nama_fasilitas' => array('tipe' => 'teks', 'bersama' => true),
        'data.*.deskripsi'      => array('tipe' => 'html', 'bersama' => true),
    ),

    // ========================= ORTU =========================
    'ortu/detail_ekskul/POST' => array(
        'data.*.nama'   => array('tipe' => 'teks', 'bersama' => true),
        'data.*.judul'  => array('tipe' => 'teks', 'bersama' => true),
        'data.*.konten' => array('tipe' => 'html', 'bersama' => true),
    ),
    'ortu/get_ekskul/GET' => array(
        'data.*.nama'   => array('tipe' => 'teks', 'bersama' => true),
        'data.*.judul'  => array('tipe' => 'teks', 'bersama' => true),
        'data.*.konten' => array('tipe' => 'html', 'bersama' => true),
    ),
    'ortu/get_jadwal_ekskul/POST' => array(
        'data.*.nama_ekskul' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hari'        => array('tipe' => 'label', 'bersama' => true),
    ),
    'ortu/get_presensi/POST' => array(
        'data.*.status_absen' => array('tipe' => 'label', 'bersama' => true),
        'data.*.hari_absen'   => array('tipe' => 'label', 'bersama' => true),
    ),
    'ortu/get_presensi_ekskul/POST' => array(
        'data.*.status_absen' => array('tipe' => 'label', 'bersama' => true),
        'data.*.hari_absen'   => array('tipe' => 'label', 'bersama' => true),
    ),

    // ========================= GURU =========================
    'guru/get_ekskul_guru/POST' => array(
        'data.*.nama_ekskul' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.keterangan'  => array('tipe' => 'teks',  'bersama' => true), // "Pembina" — preset admin ekskul_manajemen
    ),
    'guru/get_jadwal_ekskul_guru/POST' => array(
        'data.*.nama_ekskul'   => array('tipe' => 'teks',  'bersama' => true),
        'data.*.status_member' => array('tipe' => 'label', 'bersama' => true),
    ),
    'guru/get_jadwal_pelatih/POST' => array(
        'data.*.nama_ekskul'  => array('tipe' => 'teks', 'bersama' => true),
        'data.*.nama_pelatih' => array('tipe' => 'teks', 'bersama' => true), // nama pelatih/pembina dari ekskul_manajemen (admin); hati-hati bila berisi nama person
        'data.*.keterangan'   => array('tipe' => 'teks', 'bersama' => true),
    ),
    'guru/get_riwayat_ekskul_siswa/POST' => array(
        'data.*.nama_ekskul' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.status_absen'=> array('tipe' => 'label', 'bersama' => true),
    ),
    'guru/get_riwayat_ekskul_pelatih/POST' => array(
        'data.*.nama_ekskul' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.status_absen'=> array('tipe' => 'label', 'bersama' => true),
    ),
    'guru/get_presensi_siswa/POST' => array(
        'data.*.status_absen' => array('tipe' => 'label', 'bersama' => true),
        'data.*.hari_absen'   => array('tipe' => 'label', 'bersama' => true),
    ),
    'guru/get_presensi_ekskul_siswa/POST' => array(
        'data.*.status_absen' => array('tipe' => 'label', 'bersama' => true),
        'data.*.hari_absen'   => array('tipe' => 'label', 'bersama' => true),
    ),
    'guru/get_jenis_kpi/GET' => array(
        'data.*.nama_jenis' => array('tipe' => 'teks', 'bersama' => true), // master jenis KPI (admin)
    ),
    'guru/get_kategori_catatan/GET' => array(
        'data.*.nama_kategori' => array('tipe' => 'teks', 'bersama' => true), // Terlambat | Rambut panjang | ...
    ),
    'guru/presensi_pjok_detail/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.status_keaktifan' => array('tipe' => 'label', 'bersama' => true),
'data.*.deskripsi'        => array('tipe' => 'label', 'bersama' => true), // computed PHP: Sangat Baik | Baik | Cukup | Perlu Bimbingan | Perlu Pembinaan Intensif
    ),
    'guru/presensi_pjok_riwayat/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.status_keaktifan' => array('tipe' => 'label', 'bersama' => true),
'data.*.deskripsi'        => array('tipe' => 'label', 'bersama' => true), // computed PHP: Sangat Baik | Baik | Cukup | Perlu Bimbingan | Perlu Pembinaan Intensif
    ),
    'guru/presensi_pramuka_detail/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.status_kelengkapan' => array('tipe' => 'label', 'bersama' => true), // Lengkap | -
'data.*.deskripsi'          => array('tipe' => 'label', 'bersama' => true),
    ),
    'guru/presensi_pramuka_riwayat/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.status_kelengkapan' => array('tipe' => 'label', 'bersama' => true), // Lengkap | -
'data.*.deskripsi'          => array('tipe' => 'label', 'bersama' => true),
    ),
    'guru/presensi_tadarus_detail/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.deskripsi'        => array('tipe' => 'label', 'bersama' => true),
    ),
    'guru/presensi_tadarus_riwayat/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.deskripsi'        => array('tipe' => 'label', 'bersama' => true),
    ),

    // ========================= PEGAWAI =========================
    'pegawai/get_jenis_kpi/GET' => array(
        'data.*.nama_jenis' => array('tipe' => 'teks', 'bersama' => true),
    ),

    // ========================= PIMPINAN =========================
    'pimpinan/get_jenis_kpi/GET' => array(
        'data.*.nama_jenis' => array('tipe' => 'teks', 'bersama' => true),
    ),
    'pimpinan/get_all_kpi_guru/GET' => array(
        'data.*.nama_jenis' => array('tipe' => 'teks', 'bersama' => true),
    ),
    'pimpinan/get_anggaran/POST' => array(
        // proposal program sekolah — konten lembaga (admin), bukan tentang individu
        'data.*.nama_program'                 => array('tipe' => 'teks',  'bersama' => true),
        'data.*.jenis_kegiatan'               => array('tipe' => 'teks',  'bersama' => true),
        'data.*.tema_kegiatan'                => array('tipe' => 'teks',  'bersama' => true),
        'data.*.dasar_pelaksanaan_kegiatan'   => array('tipe' => 'teks',  'bersama' => true),
        'data.*.tujuan_kegiatan'              => array('tipe' => 'teks',  'bersama' => true),
        'data.*.gambaran_acara_kegiatan'      => array('tipe' => 'teks',  'bersama' => true),
        'data.*.mitra_kegiatan'               => array('tipe' => 'teks',  'bersama' => true),
        'data.*.hasil_yang_diharapkan'        => array('tipe' => 'teks',  'bersama' => true),
        'data.*.kepanitiaan'                  => array('tipe' => 'teks',  'bersama' => true),
        'data.*.tempat_dan_waktu_pelaksanaan' => array('tipe' => 'teks',  'bersama' => true),
        'data.*.status_laporan'               => array('tipe' => 'label', 'bersama' => true),
        'data.*.status_pengajuan'             => array('tipe' => 'label', 'bersama' => true),
        'data.*.sub_jenis_kegiatan'           => array('tipe' => 'teks',  'bersama' => true),
    ),
    'pimpinan/get_presensi_siswa/POST' => array(
        'data.*.status_absen' => array('tipe' => 'label', 'bersama' => true),
        'data.*.hari_absen'   => array('tipe' => 'label', 'bersama' => true),
    ),
    'pimpinan/presensi_pjok_detail/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.status_keaktifan' => array('tipe' => 'label', 'bersama' => true),
'data.*.deskripsi'        => array('tipe' => 'label', 'bersama' => true), // computed PHP: Sangat Baik | Baik | Cukup | Perlu Bimbingan | Perlu Pembinaan Intensif
    ),
    'pimpinan/presensi_pjok_riwayat/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.status_keaktifan' => array('tipe' => 'label', 'bersama' => true),
'data.*.deskripsi'        => array('tipe' => 'label', 'bersama' => true), // computed PHP: Sangat Baik | Baik | Cukup | Perlu Bimbingan | Perlu Pembinaan Intensif
    ),
    'pimpinan/presensi_pramuka_detail/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.status_kelengkapan' => array('tipe' => 'label', 'bersama' => true), // Lengkap | -
'data.*.deskripsi'          => array('tipe' => 'label', 'bersama' => true),
    ),
    'pimpinan/presensi_pramuka_riwayat/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.status_kelengkapan' => array('tipe' => 'label', 'bersama' => true), // Lengkap | -
'data.*.deskripsi'          => array('tipe' => 'label', 'bersama' => true),
    ),
    'pimpinan/presensi_tadarus_detail/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.deskripsi'        => array('tipe' => 'label', 'bersama' => true),
    ),
    'pimpinan/presensi_tadarus_riwayat/GET' => array(
'data.*.status_hadir'      => array('tipe' => 'label', 'bersama' => true),
'data.*.deskripsi'        => array('tipe' => 'label', 'bersama' => true),
    ),

    // ========================= ROOT (shared master / konten lembaga) =========================
    'root/ekstrakurikuler/POST' => array(
        'data.*.nama'   => array('tipe' => 'teks', 'bersama' => true),
        'data.*.judul'  => array('tipe' => 'teks', 'bersama' => true),
        'data.*.konten' => array('tipe' => 'html', 'bersama' => true),
    ),
    'root/informasi_psb/GET' => array(
        'data.*.judul'     => array('tipe' => 'teks', 'bersama' => true),
        'data.*.deskripsi' => array('tipe' => 'html', 'bersama' => true),
    ),
    'root/kalender_akademik/POST' => array(
        'data.*.label'     => array('tipe' => 'teks', 'bersama' => true),
        'data.*.nama_tipe' => array('tipe' => 'teks', 'bersama' => true),
    ),
    'root/jenis_prestasi/GET' => array(
        'data.*.jenis_prestasi' => array('tipe' => 'teks', 'bersama' => true),
    ),
    'root/jenis_lomba/GET' => array(
        'data.*.jenis_lomba' => array('tipe' => 'teks', 'bersama' => true),
    ),
    'root/keterangan_prestasi/GET' => array(
        'data.*.keterangan_prestasi' => array('tipe' => 'teks', 'bersama' => true),
    ),
    'root/list_notifikasi/POST' => array(
        'data.*.title'   => array('tipe' => 'teks', 'bersama' => false), // ups per-user; bisa menyisip nama individu
        'data.*.content' => array('tipe' => 'teks', 'bersama' => false),
    ),
    'root/home_notification_detail/GET' => array(
        'data.*.title' => array('tipe' => 'teks', 'bersama' => false), // mailbox per recipient
    ),
    'root/alasan_tertarik/GET' => array(
        'data.*.alasan' => array('tipe' => 'teks', 'bersama' => true),
    ),
    'root/pekerjaan_ortu/GET' => array(
        'data.*.nama_pekerjaan' => array('tipe' => 'label', 'bersama' => true), // PNS | Wiraswasta | ...
    ),
    'root/mata_pelajaran/GET' => array(
        'data.*.nama' => array('tipe' => 'teks', 'bersama' => true),
    ),
    'root/sejarah_sekolah/GET' => array(
        'data.*.judul' => array('tipe' => 'teks', 'bersama' => true),
        'data.*.konten'=> array('tipe' => 'html', 'bersama' => true),
    ),
    'root/visi_misi/GET' => array(
        'data.*.isi' => array('tipe' => 'html', 'bersama' => true),
    ),
    'root/galeri_foto/GET' => array(
        'data.*.label' => array('tipe' => 'teks', 'bersama' => true),
    ),
    'root/foto_pengajar/GET' => array(
        'data.*.jabatan' => array('tipe' => 'label', 'bersama' => true),
    ),
);

/**
 * Field yang belum pasti — TIDAK dipakai translator sampai di-review.
 * Setiap entry: alasan kenapa menunggu konfirmasi manusia.
 */
$config['translate_whitelist_pending_review'] = array(
    'root/home_notification/GET' => array(
        'alasan' => 'endpoint ini hanya meteran (notification_type + red_mark), tidak ada konten teks; tidak perlu whitelist.',
    ),
    'root/jenis_jenjang/GET' => array(
        'alasan' => 'kolom aktual tabel jenis_jenjang belum dikonfirmasi saat audit.',
    ),
    'root/library/GET' => array(
        'alasan' => 'kolom library (judul/deskripsi?) belum dikonfirmasi.',
    ),
    'root/link_wa_grup/GET' => array(
        'alasan' => 'berisi label grup/link WA — link tidak diterjemahkan; label grup perlu review isinya.',
    ),
    'root/uks/GET' => array(
        'alasan' => 'kolom belum dikonfirmasi.',
    ),
    'root/apps_version/GET' => array(
        'alasan' => 'data teknis versi; tidak perlu whitelist tapi dicatat sudah dianalisis.',
    ),
    'siswa/cek_ibadah_tipe/POST' => array(
        'alasan' => 'field response menyertakan koordinat/no? perlu sample hidup POST dgn geolokasi — verifikasi manual.',
    ),
    'guru/get_jadwal_ekskul_guru/POST' => array(
        'alasan' => 'detail anggota dibangun dinamis (key data anggota datar) — nama field perlu verifikasi response nyata.',
    ),
    'nama-field-status-pjok-pramuka-tadarus/GET' => array(
        'alasan' => 'nama field status per tabel pjok/pramuka/tadarus (status_kehadiran vs status_hadir) perlu diverifikasi dari response nyata; konfig sekarang pakai asumsi kolom.',
    ),
);

/**
 * Field yang TIDAK AKAN diterjemahkan — sudah diputus Keluar dari whitelist.
 * alasan baku: MHCU | UGC_PRIBADI | NAMA_LEMBAGA | TABEL_TIDAK_ADA
 */
$config['translate_whitelist_excluded'] = array(
    // ===== MHCU =====
    'root/mhcu/instrument_get' => array(
        array('path' => 'data.*.instruksi',            'alasan' => 'MHCU'),
        array('path' => 'data.*.nama_instrument',       'alasan' => 'MHCU'),
        array('path' => 'data.*.demografi_pertanyaan',  'alasan' => 'MHCU'),
    ),
    'root/mhcu/demografi_get' => array(
        array('path' => 'data.*.demografi_pertanyaan',  'alasan' => 'MHCU'),
    ),

    // ===== TABEL_TIDAK_ADA =====
    'siswa/jadwal_sd/POST' => array(
        array('path' => 'data.*.nama_pelajaran', 'alasan' => 'TABEL_TIDAK_ADA'),
        array('path' => 'data.*.hari',           'alasan' => 'TABEL_TIDAK_ADA'),
    ),
    'siswa/jadwal_smp/POST' => array(
        array('path' => 'data.*.nama_pelajaran', 'alasan' => 'TABEL_TIDAK_ADA'),
        array('path' => 'data.*.hari',           'alasan' => 'TABEL_TIDAK_ADA'),
    ),
    'siswa/jadwal_sma/POST' => array(
        array('path' => 'data.*.nama_pelajaran', 'alasan' => 'TABEL_TIDAK_ADA'),
        array('path' => 'data.*.hari',           'alasan' => 'TABEL_TIDAK_ADA'),
    ),

    // ===== NAMA_LEMBAGA =====
    'root/list_sekolah_sd/GET' => array(
        array('path' => 'data.*.nama_sekolah', 'alasan' => 'NAMA_LEMBAGA'),
    ),
    'root/list_sekolah_smp/GET' => array(
        array('path' => 'data.*.nama_sekolah', 'alasan' => 'NAMA_LEMBAGA'),
    ),
    'root/list_sekolah_tk/GET' => array(
        array('path' => 'data.*.nama_sekolah', 'alasan' => 'NAMA_LEMBAGA'),
    ),
    'siswa/get_perguruan_tinggi/GET' => array(
        array('path' => 'data.*.deskripsi', 'alasan' => 'NAMA_LEMBAGA'), // nilai aktual = alamat PT
        array('path' => 'data.*.nama_pt',   'alasan' => 'NAMA_LEMBAGA'),
        array('path' => 'data.*.provinsi',  'alasan' => 'NAMA_LEMBAGA'),
        array('path' => 'data.*.kota',      'alasan' => 'NAMA_LEMBAGA'),
    ),
    'siswa/pilihan_ptn_list/POST' => array(
        array('path' => 'data.*.nama_pt',    'alasan' => 'NAMA_LEMBAGA'),
        array('path' => 'data.*.jurusan',    'alasan' => 'NAMA_LEMBAGA'),
    ),

    // ===== UGC_PRIBADI (teks bebas ditulis user tentang individu) =====
    // presensi / izin — keterangan, alasan, catatan
    'siswa/cek_ibadah_tipe/POST' => array(
        array('path' => 'data.*.keterangan', 'alasan' => 'UGC_PRIBADI'),
    ),
    'siswa/riwayat_presensi_ibadah/POST' => array(
        array('path' => 'data.*.keterangan', 'alasan' => 'UGC_PRIBADI'),
    ),
    'siswa/get_presensi/POST' => array(
        array('path' => 'data.*.keterangan',       'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.alasan_terlambat', 'alasan' => 'UGC_PRIBADI'),
    ),
    'siswa/get_presensi_ekskul/POST' => array(
        array('path' => 'data.*.keterangan_presensi', 'alasan' => 'UGC_PRIBADI'),
    ),
    'ortu/get_presensi/POST' => array(
        array('path' => 'data.*.keterangan',       'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.alasan_terlambat', 'alasan' => 'UGC_PRIBADI'),
    ),
    'ortu/get_presensi_ekskul/POST' => array(
        array('path' => 'data.*.keterangan_presensi', 'alasan' => 'UGC_PRIBADI'),
    ),
    'guru/get_presensi_siswa/POST' => array(
        array('path' => 'data.*.keterangan',       'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.alasan_terlambat', 'alasan' => 'UGC_PRIBADI'),
    ),
    'guru/get_presensi_ekskul_siswa/POST' => array(
        array('path' => 'data.*.keterangan_presensi', 'alasan' => 'UGC_PRIBADI'),
    ),
    'pimpinan/get_presensi_siswa/POST' => array(
        array('path' => 'data.*.keterangan',       'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.alasan_terlambat', 'alasan' => 'UGC_PRIBADI'),
    ),
    'ortu/get_izin/POST' => array(
        array('path' => 'data.*.keterangan', 'alasan' => 'UGC_PRIBADI'),
    ),
    'guru/get_izin/POST' => array(
        array('path' => 'data.*.keterangan', 'alasan' => 'UGC_PRIBADI'),
    ),
    // catatan pelajaran/pelanggaran per individu (guru menulis per siswa)
    'guru/presensi_siswa_catatan_detail/POST' => array(
        array('path' => 'data.*.keterangan',          'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.jenis_pelanggaran',   'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.jenis_pelanggaran_custom', 'alasan' => 'UGC_PRIBADI'),
    ),
    // KPI — guru/pegawai menulis capaian sendiri
    'guru/get_kpi/POST' => array(
        array('path' => 'data.*.nama_kpi',    'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.judul_kpi',   'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.keterangan',  'alasan' => 'UGC_PRIBADI'),
    ),
    'pegawai/get_kpi/POST' => array(
        array('path' => 'data.*.nama_kpi',    'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.judul_kpi',   'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.keterangan',  'alasan' => 'UGC_PRIBADI'),
    ),
    'pimpinan/get_kpi/POST' => array(
        array('path' => 'data.*.nama_kpi',    'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.judul_kpi',   'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.keterangan',  'alasan' => 'UGC_PRIBADI'),
    ),
    'pimpinan/get_all_kpi_guru/GET' => array(
        array('path' => 'data.*.nama_kpi',    'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.judul_kpi',   'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.keterangan',  'alasan' => 'UGC_PRIBADI'),
    ),
    // Prestasi — entri ditulis individu (siswa/guru/pegawai) lalu dikurasi
    'siswa/prestasi/POST' => array(
        array('path' => 'data.*.nama_prestasi', 'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.judul',         'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.konten',        'alasan' => 'UGC_PRIBADI'),
    ),
    'guru/get_prestasi/POST' => array(
        array('path' => 'data.*.nama_prestasi', 'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.judul',         'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.konten',        'alasan' => 'UGC_PRIBADI'),
    ),
    'pimpinan/get_prestasi/POST' => array(
        array('path' => 'data.*.nama_prestasi', 'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.judul',         'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.konten',        'alasan' => 'UGC_PRIBADI'),
    ),
    'pegawai/get_prestasi/POST' => array(
        array('path' => 'data.*.nama_prestasi', 'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.judul',         'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.konten',        'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.keterangan',    'alasan' => 'UGC_PRIBADI'),
    ),
    // detail personal (nama, alamat, data keluarga, gaji)
    'guru/detail_guru/POST' => array(
        array('path' => 'data.*', 'alasan' => 'UGC_PRIBADI'),
    ),
    'pegawai/detail_pegawai/POST' => array(
        array('path' => 'data.*', 'alasan' => 'UGC_PRIBADI'),
    ),
    'pimpinan/detail_pimpinan/POST' => array(
        array('path' => 'data.*', 'alasan' => 'UGC_PRIBADI'),
    ),
    'pimpinan/get_anggaran/POST' => array(
        // catatan laporan/pengajuan tentang satu program oleh penanggung jawab individu
        array('path' => 'data.*.catatan_laporan',   'alasan' => 'UGC_PRIBADI'),
        array('path' => 'data.*.catatan_pengajuan', 'alasan' => 'UGC_PRIBADI'),
    ),
);

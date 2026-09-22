<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mhcu_tambahan_kategori — Keyword matcher untuk pertanyaan 3 Instrumen 6
 * (Guru/Karyawan) yang isinya teks bebas "Harapan dukungan sekolah".
 *
 * Konfirmasi user (prompt-kategorisasi-instrumen6.md §3.1=a, §3.2=daftar baru):
 *   - Metode: keyword matching sederhana (a)
 *   - Daftar kategori: baru (10 kode)
 *
 * Sumber keyword: analisis 192 jawaban riil sesi 1-198 di mhcu_sesi_instrument
 * id_instrument_item=59 (Guru/Karyawan). Setiap jawaban bisa匹配 ke banyak
 * kategori (multi-tag), ditambah fallback "lainnya" kalau tidak ada match.
 *
 * Daftar kategori mengikuti tema dominan data riil:
 *   worklife, kompensasi, apresiasi, konseling, karier, komunikasi,
 *   wellbeing, supervisi, fasilitas, lainnya
 */
class Mhcu_tambahan_kategori {

    /**
     * Map kategori => pola regex (case-insensitive).
     * Penting: urutan regex dari yang PALING SPESIFIK ke yang generik,
     * supaya kata "psikolog" tidak tertangkap oleh "konseling" saja.
     * Multi-line di-handle dengan /ims flags (lihat match()).
     */
    private static $kategori = array(
        // Pola: pisahkan kata majemuk; jangan pakai \b di akhir yang langsung setelah whitespace+lookahead.
        // Lookbehind/lookahead di awal sudah cukup karena PHP regex konsisten.
        'worklife'     => '/(?<![A-Za-z])(work[\s-]?life|keseimbangan|menyeimbangkan|menyeimbangkan|beban[\s_-]?kerja|jam[\s_-]?kerja|di[\s_-]?luar[\s_-]?jam|deadline|kepanitiaan|over[\s_-]?time|lembur|me[\s_-]?time|proporsional|pendelegasian|waktu[\s_-]?luang|waktu[\s_-]?untuk[\s_-]?keluarga|refreshing)/ims',
        'kompensasi'   => '/(?<![A-Za-z])(gaji|tunjangan|cuti|libur|insentif|benefit|penghasilan|tunj[\s_-]?hari[\s_-]?tua|honor|honorer|kesejahteraan[\s_-]?ketenagakerja|asuransi)/ims',
        'apresiasi'    => '/(?<![A-Za-z])(apresiasi|reward|recognition|pengakuan|penghargaan|menghargai|menghargai)/ims',
        'konseling'    => '/(?<![A-Za-z])(psikolog|konseling|konsultan|terapi|kerahasiaan|privasi|dukungan[\s_-]?emosional|pendampingan[\s_-]?(?:khusus|mental)|seminar[\s_-]?psikolog|mental[\s_-]?(?:health|helath|check))/ims',
        'karier'       => '/(?<![A-Za-z])(pelatihan|studi[\s_-]?banding|dialog[\s_-]?dengan[\s_-]?atasan|pengembangan[\s_-]?(?:karier|karir)|kapasitas[\s_-]?(?:diri|kerja)|coaching|karir|potential|potensi|pengembangan)/ims',
        'komunikasi'   => '/(?<![A-Za-z])(komunikasi|wali[\s_-]?kelas|orang[\s_-]?tua|rekan[\s_-]?kerja|koordinasi|sosialisasi|persaudaraan|kepedulian|mendengar[\s_-]?masukan|forum[\s_-]?bersama|mengedepankan)/ims',
        'wellbeing'    => '/(?<![A-Za-z])(gathering|rekreasi|jalan[\s-]?jalan|jalan[\s_-]?jalan|family[\s_-]?gathering|healing|wellbeing|hiburan[\s_-]?berkala|team[\s_-]?building|refreshing)/ims',
        'supervisi'    => '/(?<![A-Za-z])(supervisi|coaching[\s_-]?kerja|evaluasi[\s_-]?kinerja|umpan[\s_-]?balik|feedback|rapatan[\s_-]?bulanan)/ims',
        'fasilitas'    => '/(?<![A-Za-z])(ruang[\s_-]?(?:istirahat|kumpul|guru)|fasilitas|nyaman|kenyamanan|lindung)/ims',
    );

    /**
     * Cocokkan teks jawaban ke daftar kategori. Return array kode kategori
     * unik (lowercase). Kalau kosong -> array('lainnya').
     */
    public static function match($text)
    {
        if (empty(trim($text))) {
            return array('lainnya');
        }
        $tags = array();
        foreach (self::$kategori as $kode => $pattern) {
            if (preg_match($pattern, $text)) {
                $tags[] = $kode;
            }
        }
        if (empty($tags)) {
            $tags[] = 'lainnya';
        }
        return $tags;
    }

    /**
     * Label kategori untuk display. Dipakai di rekap & dashboard kalau perlu.
     */
    public static function label($kode)
    {
        $labels = array(
            'worklife'   => 'Work-Life Balance',
            'kompensasi' => 'Kompensasi & Tunjangan',
            'apresiasi'  => 'Penghargaan & Apresiasi',
            'konseling'  => 'Pendampingan Psikologis',
            'karier'     => 'Pengembangan Karier',
            'komunikasi' => 'Komunikasi & Relasi',
            'wellbeing'  => 'Program Wellbeing',
            'supervisi'  => 'Supervisi & Dukungan Kerja',
            'fasilitas'  => 'Fasilitas & Lingkungan Kerja',
            'lainnya'    => 'Lainnya',
        );
        return isset($labels[$kode]) ? $labels[$kode] : $kode;
    }
}
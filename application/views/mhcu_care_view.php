<?php
/**
 * MHCU Care — halaman publik multi-step (error / info / flow / sukses).
 * State dikontrol dari controller Mhcu_care::index() via $page_state.
 * Data: $error_message | $sesi, $peringatan, $booking, $jadwal, $token
 * Teks persetujuan: sumber resmi dari
 * "Informed consent & consent to release information MHCU Care.docx" (2026-09-04).
 */

if (!function_exists('care_tgl_indo')) {
	function care_tgl_indo($tanggal)
	{
		$hari  = array('Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu');
		$bulan = array(1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
		$ts = strtotime($tanggal);
		return $hari[date('l', $ts)] . ', ' . date('j', $ts) . ' ' . $bulan[date('n', $ts)] . ' ' . date('Y', $ts);
	}
}
if (!function_exists('care_wa_url')) {
	function care_wa_url($nama)
	{
		$msg = 'Halo, Saya "' . $nama . '" ingin konfirmasi pengisian jadwal konsultasi MHCU Care sudah dilakukan.';
		return 'https://wa.me/6282133762818?text=' . rawurlencode($msg);
	}
}
if (!function_exists('care_jam')) {
	function care_jam($jam)
	{
		return substr($jam, 0, 5);
	}
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MHCU Care — Labschool Cibubur</title>
<link rel="icon" type="image/png" href="<?= base_url('uploads/logo_labschool_2025.png'); ?>">
<style>
:root{
	--brand:#C2410C; --brand-dark:#A8350A; --brand-soft:#FFF1EA;
	--ink:#1F1A17; --muted:#7A716C; --line:#EFE4DD;
	--ok:#15803D; --danger:#B91C1C;
	--radius:14px; --shadow:0 10px 40px rgba(31,26,23,.10);
}
*{box-sizing:border-box;margin:0;padding:0}
html{-webkit-text-size-adjust:100%}
body{
	font-family:'Segoe UI',system-ui,-apple-system,sans-serif;
	background:linear-gradient(160deg,#FFF8F4 0%,#FDF0E8 55%,#FBE7DA 100%);
	min-height:100vh;color:var(--ink);
}
.wrap{max-width:860px;margin:0 auto;padding:24px 20px 64px}
header.top{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:8px}
header.top img.logo-full{height:44px}
header.top img.logo-mini{display:none;height:44px}
.page-title{margin:18px 0 4px;font-size:26px;font-weight:700;letter-spacing:-.3px}
.page-sub{color:var(--muted);font-size:14.5px;margin-bottom:22px}
.card{background:#fff;border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);padding:28px}
.peserta-chip{display:inline-flex;align-items:center;gap:10px;background:var(--brand-soft);border:1px solid #F6D9C8;color:var(--brand-dark);
	border-radius:999px;padding:7px 16px;font-size:13.5px;font-weight:600;margin-bottom:20px}
.peserta-chip .dot{width:8px;height:8px;border-radius:50%;background:var(--brand)}

/* steps */
.step{display:none}
.step.active{display:block;animation:fadeIn .45s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.step-head{display:flex;align-items:center;gap:12px;margin-bottom:16px}
.step-badge{width:34px;height:34px;border-radius:10px;background:var(--brand);color:#fff;font-weight:700;
	display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0}
.step-head h2{font-size:19px}
.illus{display:block;margin:2px auto 14px;width:200px;max-width:60%;height:auto}
.doc-text{
	background:#FDFBFA;border:1px solid var(--line);border-radius:10px;padding:20px 22px;
	font-size:14px;line-height:1.75;max-height:340px;overflow-y:auto;
}
.doc-text h3{font-size:15px;margin-bottom:10px;text-align:center}
.doc-text h4{font-size:14px;margin:14px 0 6px}
.doc-text p{margin-bottom:9px}
.doc-text ul{margin:6px 0 10px 20px}
.doc-text li{margin-bottom:5px}
.doc-text .sign-row{display:flex;justify-content:space-between;gap:20px;margin-top:16px;font-size:13px;color:var(--muted)}

.consent-check{display:flex;gap:11px;align-items:flex-start;background:var(--brand-soft);border:1px solid #F6D9C8;
	border-radius:10px;padding:14px 16px;margin:16px 0 4px;cursor:pointer;font-size:14px;line-height:1.5}
.consent-check input{margin-top:2px;width:18px;height:18px;accent-color:var(--brand);flex-shrink:0;cursor:pointer}
.btn-row{display:flex;justify-content:space-between;gap:12px;margin-top:22px}
.btn{border:0;border-radius:10px;padding:13px 26px;font-size:15px;font-weight:600;cursor:pointer;transition:background .2s,transform .15s}
.btn:active{transform:scale(.97)}
.btn-primary{background:var(--brand);color:#fff}
.btn-primary:hover:not(:disabled){background:var(--brand-dark)}
.btn-primary:disabled{background:#E8D5C9;color:#fff;cursor:not-allowed}
.btn-ghost{background:transparent;color:var(--muted);border:1px solid var(--line)}
.btn-ghost:hover{background:#FAF5F2}

/* kalender */
.cal-legend{display:flex;gap:18px;flex-wrap:wrap;margin-bottom:14px;font-size:12.5px;color:var(--muted)}
.cal-legend span{display:inline-flex;align-items:center;gap:7px}
.swatch{width:14px;height:14px;border-radius:4px;display:inline-block}
.sw-free{background:#fff;border:2px solid var(--brand)}
.sw-full{background:#EDE7E3}
.sw-sel{background:var(--brand)}
.cal-scroll{overflow-x:auto}
.cal-grid{display:grid;grid-template-columns:150px repeat(3,1fr);gap:8px;min-width:560px}
.cal-grid .cal-dayhead{font-weight:700;text-align:center;padding:9px 4px;background:var(--brand-soft);border-radius:8px;font-size:13px}
.slot-day{display:flex;flex-direction:column;justify-content:center;background:#FDFBFA;border:1px solid var(--line);
	border-radius:8px;padding:8px 10px;font-size:12.5px;line-height:1.45}
.slot-day b{font-size:13px}
.slot-day .left{color:var(--ok);font-weight:600;font-size:11.5px}
.slot-day .left.empty{color:var(--danger)}
.slot{border:2px solid var(--brand);background:#fff;border-radius:8px;padding:10px 6px;font-size:13px;font-weight:600;
	color:var(--brand-dark);cursor:pointer;transition:background .15s,transform .15s;text-align:center}
.slot:hover:not(:disabled):not(.selected){background:var(--brand-soft);transform:translateY(-1px)}
.slot:disabled{border-color:#EDE7E3;background:#EDE7E3;color:#B7ACA5;cursor:not-allowed;text-decoration:line-through}
.slot.selected{background:var(--brand);border-color:var(--brand);color:#fff}
.slot small{display:block;margin-top:4px;font-size:10px;font-weight:700;line-height:1.2}
.sel-banner{margin-top:16px;background:var(--brand-soft);border:1px solid #F6D9C8;border-radius:10px;padding:13px 16px;
	font-size:14px;display:none}
.sel-banner.show{display:block;animation:fadeIn .3s ease}
.sel-banner b{color:var(--brand-dark)}

/* konfirmasi */
.summary{background:#FDFBFA;border:1px solid var(--line);border-radius:10px;padding:18px 20px;margin:14px 0}
.summary .row{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px dashed var(--line);font-size:14px}
.summary .row:last-child{border-bottom:0}
.summary .row .lbl{color:var(--muted)}
.summary .row .val{font-weight:600;text-align:right}
.final-check{margin-top:14px}
.loading{display:none;align-items:center;gap:10px;color:var(--muted);font-size:14px;margin-top:14px}
.loading.show{display:flex}
.spinner{width:18px;height:18px;border:3px solid var(--line);border-top-color:var(--brand);border-radius:50%;
	animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

/* animasi sukses (step 5) */
.done-ring{transform-origin:100px 100px;animation:donePulse 2s ease-in-out infinite}
@keyframes donePulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.07);opacity:.85}}
.done-check{stroke-dasharray:110;stroke-dashoffset:0;animation:doneDraw 2.4s ease infinite}
@keyframes doneDraw{0%{stroke-dashoffset:110}55%,100%{stroke-dashoffset:0}}
.done-hearts{display:flex;justify-content:center;gap:14px;margin:-6px 0 10px}
.done-hearts svg{width:22px;height:22px;animation:doneFloat 2.4s ease-in-out infinite}
.done-hearts svg:nth-child(2){animation-delay:.4s;width:28px;height:28px}
.done-hearts svg:nth-child(3){animation-delay:.8s}
@keyframes doneFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}

/* state halaman penuh (error/info/sukses) */
.center-card{text-align:center;padding:44px 30px}
.center-card .illus{width:230px;max-width:75%}
.center-card h1{font-size:23px;margin-bottom:10px}
.center-card p{color:var(--muted);font-size:14.5px;line-height:1.7;max-width:520px;margin:0 auto 8px}
.center-card .summary{margin:22px auto 6px;max-width:480px;text-align:left}
.hi-name{font-size:17px;font-weight:700;margin-top:2px}

footer.foot{margin-top:30px;text-align:center;color:#B7ACA5;font-size:12px}

@media (max-width:720px){
	.wrap{padding:16px 14px 48px}
	header.top img.logo-full{display:none}
	header.top img.logo-mini{display:block;height:40px}
	.page-title{font-size:21px}
	.card{padding:20px 16px}
	/* kalender: grid jadi list per tanggal */
	.cal-grid{grid-template-columns:1fr;min-width:0;gap:10px}
	.cal-grid .cal-dayhead{display:none}
	.slot-day{flex-direction:row;align-items:center;justify-content:space-between;gap:8px}
	.slot-day b{font-size:12.5px}
	.slot{padding:11px 8px;font-size:12.5px}
	.btn{padding:12px 18px;font-size:14px}
	.btn-row{flex-direction:column-reverse}
	.btn-row .btn{width:100%}
}
</style>
</head>
<body>
<div class="wrap">
	<header class="top">
		<img class="logo-full" src="<?= base_url('uploads/logo_labschool_cibubur_new.png'); ?>" alt="Labschool Cibubur">
		<img class="logo-mini" src="<?= base_url('uploads/logo_labschool_2025.png'); ?>" alt="Labschool Cibubur">
	</header>

<?php if ($page_state === 'error') { ?>
	<div class="card center-card">
		<svg class="illus" viewBox="0 0 200 200" aria-hidden="true">
			<circle cx="100" cy="100" r="78" fill="#F6D9C8"/>
			<path d="M60 78 L92 110 M92 78 L60 110" stroke="#C2410C" stroke-width="9" stroke-linecap="round"/>
			<circle cx="100" cy="100" r="78" fill="none" stroke="#C2410C" stroke-width="6"/>
		</svg>
		<h1>Tautan Tidak Tersedia</h1>
		<p><?= isset($error_message) ? htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') : 'Tautan tidak valid.'; ?></p>
		<p>Silakan gunakan tautan resmi yang dikirimkan kepada Anda, atau hubungi tim MHCU Labschool Cibubur jika Anda merasa menerima tautan ini secara sah.</p>
	</div>

<?php } elseif ($page_state === 'info') { ?>
	<div class="card center-card">
		<svg class="illus" viewBox="0 0 200 200" aria-hidden="true">
			<rect x="40" y="46" width="120" height="112" rx="14" fill="#fff" stroke="#C2410C" stroke-width="5"/>
			<rect x="66" y="30" width="68" height="30" rx="8" fill="#C2410C"/>
			<path d="M66 108 L92 132 L136 88" fill="none" stroke="#15803D" stroke-width="9" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
		<h1>Anda Sudah Menjadwalkan Sesi</h1>
		<p class="hi-name">Halo, <?= htmlspecialchars($sesi->nama_lengkap, ENT_QUOTES, 'UTF-8'); ?></p>
		<p>Berikut ringkasan jadwal konseling psikologis yang sudah Anda pilih.</p>
		<div class="summary">
			<div class="row"><span class="lbl">Tanggal</span><span class="val"><?= care_tgl_indo($booking->tanggal); ?></span></div>
			<div class="row"><span class="lbl">Sesi</span><span class="val">Sesi <?= intval($booking->sesi_ke); ?> (<?= care_jam($booking->jam_mulai); ?> &ndash; <?= care_jam($booking->jam_selesai); ?> WIB)</span></div>
			<div class="row"><span class="lbl">Psikolog</span><span class="val"><?= htmlspecialchars($booking->nama_psikolog, ENT_QUOTES, 'UTF-8'); ?></span></div>
			<div class="row"><span class="lbl">Status</span><span class="val">Terjadwal</span></div>
			<div class="row"><span class="lbl">Lokasi</span><span class="val">Ruang Psikolog Labschool Cibubur</span></div>
		</div>
		<p>Jika anda perlu mengubah jadwal, silakan hubungi psikolog Labschool Cibubur ke nomor berikut <a href="<?= care_wa_url($sesi->nama_lengkap); ?>" target="_blank" rel="noopener"><b>082133762818 (Bu Dewi)</b></a>. Terima kasih.</p>
	</div>

<?php } else { /* flow */ ?>
	<p class="page-title" style="margin-top:14px">MHCU Care</p>
	<p class="page-sub">Layanan Konseling Psikologis &mdash; tindak lanjut Mental Health Check-Up</p>
	<div class="peserta-chip"><span class="dot"></span><?= htmlspecialchars($sesi->nama_lengkap, ENT_QUOTES, 'UTF-8'); ?></div>

	<div class="card">
		<!-- ============ STEP 1: Persetujuan Konseling ============ -->
		<section class="step active" id="step1">
			<div class="step-head"><div class="step-badge">1</div><h2>Persetujuan Konsultasi Psikolog</h2></div>
			<svg class="illus" viewBox="0 0 200 160" aria-hidden="true">
				<rect x="52" y="24" width="96" height="116" rx="12" fill="#fff" stroke="#C2410C" stroke-width="5"/>
				<rect x="52" y="24" width="96" height="26" rx="12" fill="#F6D9C8"/>
				<path d="M70 72 H130 M70 90 H130 M70 108 H112" stroke="#E5BBA4" stroke-width="6" stroke-linecap="round"/>
				<circle cx="122" cy="126" r="26" fill="#C2410C"/>
				<path d="M110 126 L118 134 L134 118" fill="none" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
			<div class="doc-text">
				<h3>LEMBAR PERSETUJUAN<br>MHCU CARE &mdash; LAYANAN KONSELING PSIKOLOGIS</h3>
				<p>Saya yang bertanda tangan di bawah ini (<b><?= htmlspecialchars($sesi->nama_lengkap, ENT_QUOTES, 'UTF-8'); ?></b>, <?= htmlspecialchars($jabatan, ENT_QUOTES, 'UTF-8'); ?>, <?= care_tgl_indo(date('Y-m-d')); ?>), menyatakan bahwa saya telah memperoleh penjelasan mengenai layanan konseling psikologis sebagai tindak lanjut hasil Mental Health Check-Up (MHCU).</p>
				<h4>Saya memahami bahwa:</h4>
				<ul>
					<li>Konseling bertujuan membantu saya memahami dan mengatasi permasalahan atau kondisi psikologis yang sedang saya hadapi.</li>
					<li>Layanan diberikan oleh Psikolog Klinis/profesional psikologi yang berwenang.</li>
					<li>Konseling bersifat sukarela dan rahasia.</li>
					<li>Isi pembicaraan konseling tidak diberikan kepada pihak sekolah/organisasi tanpa persetujuan saya, kecuali dalam kondisi tertentu yang berkaitan dengan keselamatan diri/orang lain atau kewajiban hukum/etik.</li>
					<li>Hasil MHCU merupakan skrining awal dan bukan diagnosis.</li>
					<li>Berdasarkan hasil konseling, psikolog dapat merekomendasikan konseling lanjutan atau rujukan kepada tenaga profesional lain apabila diperlukan.</li>
				</ul>
			</div>
			<label class="consent-check"><input type="checkbox" id="c1a">
				<span>Saya <b>bersedia</b> mengikuti layanan konseling psikologis.</span></label>
			<label class="consent-check"><input type="checkbox" id="c1b">
				<span>Saya telah memperoleh kesempatan untuk bertanya dan memahami penjelasan mengenai layanan ini.</span></label>
			<div class="btn-row">
				<span></span>
				<button class="btn btn-primary" id="btn1" disabled>Setuju &amp; Lanjutkan</button>
			</div>
		</section>

		<!-- ============ STEP 2: Consent to Release Information ============ -->
		<section class="step" id="step2">
			<div class="step-head"><div class="step-badge">2</div><h2>Persetujuan Pemberian Informasi</h2></div>
			<svg class="illus" viewBox="0 0 200 160" aria-hidden="true">
				<rect x="46" y="30" width="88" height="110" rx="10" fill="#fff" stroke="#C2410C" stroke-width="5"/>
				<path d="M62 56 H118 M62 74 H118 M62 92 H100" stroke="#E5BBA4" stroke-width="6" stroke-linecap="round"/>
				<path d="M118 128 L132 142 L158 112" fill="none" stroke="#15803D" stroke-width="9" stroke-linecap="round" stroke-linejoin="round"/>
				<circle cx="138" cy="128" r="34" fill="none" stroke="#15803D" stroke-width="5"/>
			</svg>
			<div class="doc-text">
				<h3>PERSETUJUAN PEMBERIAN INFORMASI<br>CONSENT TO RELEASE INFORMATION &ndash; MHCU</h3>
				<p>Saya memahami bahwa informasi yang saya sampaikan dalam konseling bersifat <b>rahasia</b>.</p>
				<p>Saya memberikan persetujuan kepada Psikolog Klinis untuk menyampaikan kepada <b>Pihak Manajemen Labschool Cibubur</b> informasi berikut:</p>
				<ul>
					<li>Konfirmasi bahwa saya telah mengikuti konseling.</li>
					<li>Rekomendasi umum terkait dukungan/penyesuaian di lingkungan kerja.</li>
					<li>Rekomendasi tindak lanjut layanan.</li>
					<li>Informasi lain yang secara khusus saya setujui.</li>
				</ul>
				<p>Saya <b>tidak</b> memberikan persetujuan untuk menyampaikan isi percakapan konseling, catatan klinis, maupun informasi pribadi lainnya, kecuali dalam kondisi yang berkaitan dengan keselamatan atau kewajiban hukum/etik.</p>
				<p>Persetujuan ini berlaku sampai tanggal yang ditentukan kemudian. Saya memahami bahwa saya dapat mencabut persetujuan ini secara tertulis.</p>
			</div>
			<label class="consent-check"><input type="checkbox" id="c2a">
				<span>Saya menyetujui <b>Persetujuan Pemberian Informasi</b> di atas.</span></label>
			<div class="btn-row">
				<button class="btn btn-ghost" id="back2">&larr; Kembali</button>
				<button class="btn btn-primary" id="btn2" disabled>Setuju &amp; Lanjutkan</button>
			</div>
		</section>

		<!-- ============ STEP 3: Pilih Jadwal ============ -->
		<section class="step" id="step3">
			<div class="step-head"><div class="step-badge">3</div><h2>Pilih Jadwal Sesi Konseling</h2></div>
			<svg class="illus" viewBox="0 0 200 160" aria-hidden="true">
				<rect x="40" y="34" width="120" height="104" rx="12" fill="#fff" stroke="#C2410C" stroke-width="5"/>
				<rect x="40" y="34" width="120" height="24" rx="12" fill="#C2410C"/>
				<circle cx="100" cy="104" r="26" fill="none" stroke="#15803D" stroke-width="6"/>
				<path d="M100 90 V104 L110 112" stroke="#15803D" stroke-width="6" fill="none" stroke-linecap="round"/>
			</svg>
			<p style="font-size:14px;color:var(--muted);margin-bottom:14px">
				Pilih <b>tepat satu slot</b> (tanggal + sesi). Setiap slot hanya untuk satu peserta &mdash;
				slot yang sudah terisi tidak dapat dipilih.</p>
			<div class="cal-legend">
				<span><span class="swatch sw-free"></span>Tersedia</span>
				<span><span class="swatch sw-full"></span>Penuh</span>
				<span><span class="swatch sw-sel"></span>Dipilih Anda</span>
			</div>
			<div class="cal-scroll"><div class="cal-grid" id="calGrid"></div></div>
			<div class="sel-banner" id="selBanner"></div>
			<div class="btn-row">
				<button class="btn btn-ghost" id="back3">&larr; Kembali</button>
				<button class="btn btn-primary" id="btn3" disabled>Lanjut ke Konfirmasi</button>
			</div>
		</section>

		<!-- ============ STEP 4: Konfirmasi ============ -->
		<section class="step" id="step4">
			<div class="step-head"><div class="step-badge">4</div><h2>Konfirmasi Akhir</h2></div>
			<svg class="illus" viewBox="0 0 200 160" aria-hidden="true">
				<rect x="55" y="26" width="90" height="118" rx="10" fill="#fff" stroke="#C2410C" stroke-width="5"/>
				<rect x="78" y="16" width="44" height="20" rx="6" fill="#C2410C"/>
				<line x1="70" y1="58" x2="130" y2="58" stroke="#EDE7E3" stroke-width="5" stroke-linecap="round"/>
				<line x1="70" y1="76" x2="130" y2="76" stroke="#EDE7E3" stroke-width="5" stroke-linecap="round"/>
				<line x1="70" y1="94" x2="112" y2="94" stroke="#EDE7E3" stroke-width="5" stroke-linecap="round"/>
				<path d="M76 112 L92 128 L126 94" fill="none" stroke="#15803D" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
			<div class="summary">
				<div class="row"><span class="lbl">Nama</span><span class="val"><?= htmlspecialchars($sesi->nama_lengkap, ENT_QUOTES, 'UTF-8'); ?></span></div>
				<div class="row"><span class="lbl">Tanggal Sesi</span><span class="val" id="sumTanggal">&ndash;</span></div>
				<div class="row"><span class="lbl">Sesi</span><span class="val" id="sumSesi">&ndash;</span></div>
				<div class="row"><span class="lbl">Psikolog</span><span class="val" id="sumPsikolog">&ndash;</span></div>
				<div class="row"><span class="lbl">Layanan</span><span class="val">Konseling Psikologis (MHCU Care)</span></div>
				<div class="row"><span class="lbl">Lokasi</span><span class="val">Ruang Psikolog Labschool Cibubur</span></div>
			</div>
			<label class="consent-check final-check"><input type="checkbox" id="cFinal">
				<span>Dengan ini, saya secara sadar dan bertanggung jawab atas keputusan yang saya lakukan.</span></label>
			<div class="loading" id="loadRow"><div class="spinner"></div>Menyimpan jadwal&hellip;</div>
			<div class="btn-row">
				<button class="btn btn-ghost" id="back4">&larr; Kembali</button>
				<button class="btn btn-primary" id="btnSubmit" disabled>Konfirmasi &amp; Kirim</button>
			</div>
		</section>

		<!-- ============ SUKSES ============ -->
		<section class="step" id="stepDone">
			<div class="center-card" style="padding:20px 0">
				<svg class="illus" viewBox="0 0 200 200" aria-hidden="true">
					<circle class="done-ring" cx="100" cy="100" r="78" fill="#DCFCE7"/>
					<circle cx="100" cy="100" r="78" fill="none" stroke="#15803D" stroke-width="5"/>
					<path class="done-check" d="M64 102 L90 128 L138 76" fill="none" stroke="#15803D" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<div class="done-hearts" aria-hidden="true">
					<svg viewBox="0 0 24 24"><path d="M12 21s-7.5-4.9-10-9.2C.3 8.7 2 5 5.5 5 8 5 9.5 6.7 12 9.3 14.5 6.7 16 5 18.5 5 22 5 23.7 8.7 22 11.8 19.5 16.1 12 21 12 21z" fill="#C2410C"/></svg>
					<svg viewBox="0 0 24 24"><path d="M12 21s-7.5-4.9-10-9.2C.3 8.7 2 5 5.5 5 8 5 9.5 6.7 12 9.3 14.5 6.7 16 5 18.5 5 22 5 23.7 8.7 22 11.8 19.5 16.1 12 21 12 21z" fill="#F97316"/></svg>
					<svg viewBox="0 0 24 24"><path d="M12 21s-7.5-4.9-10-9.2C.3 8.7 2 5 5.5 5 8 5 9.5 6.7 12 9.3 14.5 6.7 16 5 18.5 5 22 5 23.7 8.7 22 11.8 19.5 16.1 12 21 12 21z" fill="#15803D"/></svg>
				</div>
				<h1>Terima Kasih!</h1>
				<p>Jadwal sesi konseling psikologis Anda berhasil disimpan.</p>
				<div class="summary">
					<div class="row"><span class="lbl">Tanggal</span><span class="val" id="doneTanggal">&ndash;</span></div>
					<div class="row"><span class="lbl">Sesi</span><span class="val" id="doneSesi">&ndash;</span></div>
					<div class="row"><span class="lbl">Psikolog</span><span class="val" id="donePsikolog">&ndash;</span></div>
				</div>
				<p>Mohon hadir tepat waktu.<br>Silahkan konfirmasi jadwal sesi konseling kepada psikolog Labschool Cibubur ke nomor berikut <a href="<?= care_wa_url($sesi->nama_lengkap); ?>" target="_blank" rel="noopener"><b>082133762818 (Bu Dewi)</b></a>.<br>Terima kasih.</p>
			</div>
		</section>
	</div>
<?php } ?>

	<footer class="foot">&copy; <?= date('Y'); ?> Labschool Cibubur &mdash; MHCU Care</footer>
</div>

<?php if ($page_state === 'flow') { ?>
<script>
var CARE = <?= json_encode(array(
	'token'   => $token,
	'id_sesi' => intval($sesi->id_mhcu_sesi),
	'jadwal'  => $jadwal
)); ?>;

var BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
var HARI  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

function tglIndo(s){
	var d = new Date(s + 'T00:00:00');
	return HARI[d.getDay()] + ', ' + d.getDate() + ' ' + BULAN[d.getMonth()] + ' ' + d.getFullYear();
}
function jam5(t){ return t.substring(0, 5); }
function escapeHtml(s){ return String(s || '').replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]; }); }
function sesiLabel(j){ return 'Sesi ' + j.sesi_ke + ' (' + jam5(j.jam_mulai) + '\u2013' + jam5(j.jam_selesai) + ' WIB)'; }
function psikologLabel(j){ return j.nama_psikolog || '-'; }

/* ---------- step navigation ---------- */
var steps = ['step1','step2','step3','step4','stepDone'];
function goStep(id){
	steps.forEach(function(s){ document.getElementById(s).classList.toggle('active', s === id); });
	window.scrollTo({top: 0, behavior: 'smooth'});
}
function bindCheck(checkboxIds, btnId){
	var btn = document.getElementById(btnId);
	function upd(){
		var all = checkboxIds.every(function(id){ return document.getElementById(id).checked; });
		btn.disabled = !all;
	}
	checkboxIds.forEach(function(id){
		document.getElementById(id).addEventListener('change', upd);
	});
}
bindCheck(['c1a','c1b'], 'btn1');
bindCheck(['c2a'], 'btn2');
document.getElementById('btn1').addEventListener('click', function(){ goStep('step2'); });
document.getElementById('btn2').addEventListener('click', function(){ goStep('step3'); });
document.getElementById('back2').addEventListener('click', function(){ goStep('step1'); });
document.getElementById('back3').addEventListener('click', function(){ goStep('step2'); });
document.getElementById('back4').addEventListener('click', function(){ goStep('step3'); });

/* ---------- kalender ---------- */
var selectedSlot = null;
function renderCalendar(){
	var grid = document.getElementById('calGrid');
	grid.innerHTML = '';
	grid.appendChild(el('div', 'cal-dayhead', 'Tanggal'));
	['Sesi 1<br>11.00\u201312.30','Sesi 2<br>13.00\u201314.30','Sesi 3<br>14.30\u201316.00'].forEach(function(h){
		var d = el('div', 'cal-dayhead', h); d.style.fontSize = '12px'; grid.appendChild(d);
	});
	var byDate = {};
	CARE.jadwal.forEach(function(j){
		if (!byDate[j.tanggal]) byDate[j.tanggal] = [];
		byDate[j.tanggal].push(j);
	});
	Object.keys(byDate).sort().forEach(function(tgl){
		var slots = byDate[tgl];
		// taken dari mysqli berupa string ("0"/"1") — "0" truthy di JS, wajib Number()
		var free = slots.filter(function(j){ return Number(j.taken) !== 1; }).length;
		var day = el('div', 'slot-day');
		day.innerHTML = '<b>' + tglIndo(tgl) + '</b>' +
			'<span class="left' + (free === 0 ? ' empty' : '') + '">' +
			(free === 0 ? 'Penuh' : free + ' slot tersisa') + '</span>';
		grid.appendChild(day);
		slots.forEach(function(j){
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'slot';
			b.innerHTML = jam5(j.jam_mulai) + ' \u2013 ' + jam5(j.jam_selesai) + '<small>' + escapeHtml(j.nama_psikolog) + '</small>';
			b.title = (j.nama_psikolog || '') + ' — ' + sesiLabel(j);
			if (Number(j.taken) === 1) { b.disabled = true; b.title = 'Slot penuh'; }
			if (selectedSlot && selectedSlot.id_jadwal === j.id_jadwal) b.classList.add('selected');
			b.addEventListener('click', function(){ pickSlot(j, b); });
			grid.appendChild(b);
		});
	});
}
function el(tag, cls, html){
	var d = document.createElement(tag);
	d.className = cls; d.innerHTML = html;
	return d;
}
function pickSlot(j, btn){
	selectedSlot = j;
	document.querySelectorAll('.slot.selected').forEach(function(x){ x.classList.remove('selected'); });
	btn.classList.add('selected');
	var banner = document.getElementById('selBanner');
	banner.innerHTML = 'Slot dipilih: <b>' + tglIndo(j.tanggal) + '</b> &mdash; <b>' + sesiLabel(j) + '</b><br><b>' + escapeHtml(psikologLabel(j)) + '</b>';
	banner.classList.add('show');
	document.getElementById('btn3').disabled = false;
}
renderCalendar();
document.getElementById('btn3').addEventListener('click', function(){
	if (!selectedSlot) return;
	document.getElementById('sumTanggal').textContent = tglIndo(selectedSlot.tanggal);
	document.getElementById('sumSesi').textContent = sesiLabel(selectedSlot);
	document.getElementById('sumPsikolog').textContent = psikologLabel(selectedSlot);
	goStep('step4');
});

/* ---------- konfirmasi & submit ---------- */
bindCheck(['cFinal'], 'btnSubmit');
document.getElementById('btnSubmit').addEventListener('click', function(){
	if (!selectedSlot) return;
	var btn = this;
	btn.disabled = true;
	document.getElementById('loadRow').classList.add('show');

	var fd = new FormData();
	fd.append('token', CARE.token);
	fd.append('id_sesi', CARE.id_sesi);
	fd.append('id_jadwal', selectedSlot.id_jadwal);

	fetch('<?= site_url('mhcu_care/submit'); ?>', {method: 'POST', body: fd})
		.then(function(r){ return r.json(); })
		.then(function(res){
			document.getElementById('loadRow').classList.remove('show');
			if (res.status === 1) {
				var d = res.data;
				document.getElementById('doneTanggal').textContent = tglIndo(d.tanggal);
				document.getElementById('doneSesi').textContent =
					'Sesi ' + d.sesi_ke + ' (' + jam5(d.jam_mulai) + '\u2013' + jam5(d.jam_selesai) + ' WIB)';
				document.getElementById('donePsikolog').textContent = d.nama_psikolog || '-';
				goStep('stepDone');
			} else {
				alert(res.message || 'Gagal menyimpan jadwal.');
				btn.disabled = false;
				// slot bisa saja barusan terisi — refresh availability
				refreshSlots();
			}
		})
		.catch(function(){
			document.getElementById('loadRow').classList.remove('show');
			alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
			btn.disabled = false;
		});
});

function refreshSlots(){
	fetch('<?= site_url('mhcu_care/slots'); ?>?token=' + encodeURIComponent(CARE.token) + '&id_sesi=' + CARE.id_sesi)
		.then(function(r){ return r.json(); })
		.then(function(res){
			if (res.status === 1) { CARE.jadwal = res.data.jadwal; renderCalendar(); }
		})
		.catch(function(){ /* abaikan — submit tetap di-guard server */ });
}


</script>
<?php } ?>
</body>
</html>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** Inisial nama untuk avatar (labs-name-cell). */
if ( ! function_exists('career_kandidat_inisial')) {
function career_kandidat_inisial($nama)
{
	$nama = trim((string) $nama);
	if ($nama === '') return '?';
	$potongan = preg_split('/\s+/', $nama);
	if (count($potongan) >= 2) {
		return strtoupper(mb_substr($potongan[0], 0, 1) . mb_substr($potongan[count($potongan) - 1], 0, 1));
	}
	return strtoupper(mb_substr($potongan[0], 0, 2));
}
}
?>
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<style>
/* Info boxes (pola ala siswa_sd_list) */
.info-box-custom { min-height:90px; border-radius:6px; margin-bottom:15px; display:flex; align-items:center; padding:15px 20px; box-shadow:0 2px 4px rgba(0,0,0,0.08); color:#fff; position:relative; overflow:hidden; }
.info-box-custom .info-icon { font-size:40px; opacity:0.35; position:absolute; right:15px; top:50%; transform:translateY(-50%); }
.info-box-custom .info-label { font-size:12px; text-transform:uppercase; letter-spacing:0.5px; opacity:0.9; margin-bottom:5px; display:block; font-weight:500; }
.info-box-custom .info-value { font-size:28px; font-weight:700; line-height:1.2; display:block; color:#fff; }
.info-box-custom .info-detail { font-size:11px; opacity:0.85; margin-top:4px; display:block; color:#fff; }
.bg-grad-blue { background:linear-gradient(135deg,#3c8dbc 0%,#367fa9 100%); }
.bg-grad-green { background:linear-gradient(135deg,#00a65a 0%,#008d4c 100%); }
.bg-grad-purple { background:linear-gradient(135deg,#605ca8 0%,#555299 100%); }
.bg-grad-orange { background:linear-gradient(135deg,#f39c12 0%,#db8b0a 100%); }
</style>

<section class="content-header">
	<h1>
		Kandidat Karier
		<small class="labs-text-muted">Kelola akun kandidat</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Kandidat Karier</li>
	</ol>
</section>

<section class="content">
<div id="career_kandidat">
	<!-- Info boxes (ala siswa_sd) -->
	<div class="row">
		<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="info-box-custom bg-grad-blue">
				<i class="fa fa-users info-icon"></i>
				<div>
					<span class="info-label">Total Kandidat</span>
					<span class="info-value"><?= number_format($infobox['total']); ?></span>
					<span class="info-detail">Seluruh akun kandidat terdaftar</span>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="info-box-custom bg-grad-green">
				<i class="fa fa-check-circle info-icon"></i>
				<div>
					<span class="info-label">Kandidat Aktif</span>
					<span class="info-value"><?= number_format($infobox['aktif']); ?></span>
					<span class="info-detail">Akun aktif, bisa login</span>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="info-box-custom bg-grad-purple">
				<i class="fa fa-ban info-icon"></i>
				<div>
					<span class="info-label">Kandidat Nonaktif</span>
					<span class="info-value"><?= number_format($infobox['nonaktif']); ?></span>
					<span class="info-detail">Akun tidak aktif</span>
				</div>
			</div>
		</div>
		<div class="col-md-3 col-sm-6 col-xs-12">
			<div class="info-box-custom bg-grad-orange">
				<i class="fa fa-file-text info-icon"></i>
				<div>
					<span class="info-label">Punya Lamaran</span>
					<span class="info-value"><?= number_format($infobox['punya_lamaran']); ?></span>
					<span class="info-detail">Kandidat dengan >=1 lamaran</span>
				</div>
			</div>
		</div>
	</div>

	<div class="labs-card">
		<div class="labs-card__header">
			<h3 class="labs-card__title">
				<i class="fa fa-users"></i>
				Daftar Kandidat
				<span class="labs-badge labs-badge--default"><?= (int) $jumlah; ?> kandidat</span>
			</h3>
			<div>
				<button type="button" id="btn_buka_tambah" class="labs-btn labs-btn--primary labs-btn--sm"
					title="Membuat akun kandidat baru; password ditampilkan sekali setelah sukses">
					<i class="fa fa-user-plus"></i> Tambah Kandidat
				</button>
			</div>
		</div>

		<div class="labs-card__body">
			<!-- Filter cari (compact, wrap rapi di mobile) -->
			<form method="get" action="<?= site_url('administrator/career_kandidat'); ?>"
				class="labs-filter-inline mb-3">
				<div class="labs-filter-inline__search">
					<i class="fa fa-search labs-filter-inline__search-icon"></i>
					<input type="text" name="q" value="<?= html_escape($q); ?>"
						placeholder="Cari nama / email&hellip;">
				</div>
				<button type="submit" class="labs-btn labs-btn--primary labs-btn--sm" title="Cari">
					<i class="fa fa-filter"></i>
				</button>
				<?php if ($q !== ''): ?>
					<a href="<?= site_url('administrator/career_kandidat'); ?>"
						class="labs-btn labs-btn--default labs-btn--sm" title="Reset pencarian">
						<i class="fa fa-undo"></i>
					</a>
				<?php endif; ?>
			</form>

			<!-- Tabel: wrap + scroll agar tetap terbaca di layar kecil -->
			<div class="labs-table-wrap">
				<div class="labs-table-scroll">
					<table class="labs-table">
						<?php $sort_base = site_url('administrator/career_kandidat') . ($base_query !== '' ? '?' . $base_query : ''); ?>
						<thead>
							<tr>
								<th class="labs-cell-numeric"><?= career_sort_link('No.', 'id', $sort_col, $sort_dir, $sort_base, 'asc'); ?></th>
								<th><?= career_sort_link('Kandidat', 'nama', $sort_col, $sort_dir, $sort_base); ?></th>
								<th><?= career_sort_link('Email', 'email', $sort_col, $sort_dir, $sort_base); ?></th>
								<th><?= career_sort_link('Telepon', 'telepon', $sort_col, $sort_dir, $sort_base); ?></th>
								<th class="labs-cell-numeric"><?= career_sort_link('Lamaran', 'lamaran', $sort_col, $sort_dir, $sort_base); ?></th>
								<th><?= career_sort_link('Login terakhir', 'login', $sort_col, $sort_dir, $sort_base); ?></th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php if (empty($kandidats)): ?>
							<tr>
								<td colspan="7">
									<div class="labs-empty">
										<i class="fa fa-user-secret"></i>
										<p>Tidak ada kandidat<?= $q !== '' ? ' yang cocok dengan pencarian' : ''; ?>.</p>
									</div>
								</td>
							</tr>
						<?php endif; ?>
						<?php foreach ($kandidats as $k): ?>
							<tr class="kandidat-row">
								<td class="labs-cell-numeric labs-cell-muted"><?= (int) $k->id_candidate; ?></td>
								<td>
									<div class="labs-name-cell">
										<span class="labs-avatar"><?= html_escape(career_kandidat_inisial($k->full_name)); ?></span>
										<div>
											<div class="labs-fw-semi"><?= html_escape($k->full_name); ?></div>
											<div class="labs-fs-xs labs-text-muted">ID: <?= (int) $k->id_candidate; ?></div>
										</div>
									</div>
								</td>
								<td><?= html_escape($k->email); ?></td>
								<td class="labs-cell-muted"><?= html_escape($k->phone !== NULL ? $k->phone : '&mdash;'); ?></td>
								<td><span class="labs-badge labs-badge--info"><?= (int) $k->jumlah_lamaran; ?></span></td>
								<td class="labs-cell-muted"><?php echo $k->last_login_at !== NULL
									? html_escape($k->last_login_at) : 'belum pernah'; ?></td>
								<td>
									<div class="labs-row-actions">
										<button type="button"
											class="btn btn-sm btn-outline-warning kandidat-reset"
											data-url="<?php echo site_url('administrator/career_kandidat/reset_password/' . (int) $k->id_candidate); ?>">Reset password
										</button>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

			<div class="mt-3 d-flex justify-content-between align-items-center">
				<span class="labs-text-muted labs-fs-xs">Total <?= (int) $jumlah; ?> kandidat</span>
			</div>
			<?php echo $pagination; ?>
		</div>
	</div>
</div>

<span id="career_csrf_holder" class="career-csrf hidden"><?php echo $csrf_field; ?></span>
</section>

<!-- Modal tambah kandidat -->
<div class="modal fade labs-modal" id="modal-tambah-kandidat" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-user-plus"></i> Tambah Kandidat</h4>
				<button type="button" class="close" data-dismiss="modal" id="btn-tambah-x">&times;</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="tambah-email">Email <span class="text-danger">*</span></label>
					<input type="email" id="tambah-email" class="form-control" maxlength="190"
						placeholder="nama@example.com" autocomplete="off">
				</div>
				<div class="form-group">
					<label for="tambah-nama">Nama lengkap <span class="text-danger">*</span></label>
					<input type="text" id="tambah-nama" class="form-control" maxlength="150"
						placeholder="Nama lengkap kandidat" autocomplete="off">
				</div>
				<div class="form-group" style="margin-bottom:0;">
					<label for="tambah-telepon">Telepon (opsional)</label>
					<input type="text" id="tambah-telepon" class="form-control" maxlength="30"
						placeholder="Contoh: 0812xxxxxxx" autocomplete="off">
				</div>
				<p class="help-block" style="margin-top:10px;">
					Password dibuat otomatis oleh server (acak) dan
					<strong>ditampilkan sekali</strong> setelah kandidat berhasil dibuat.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="labs-btn labs-btn--default" data-dismiss="modal">Batal</button>
				<button type="button" id="btn-simpan-kandidat" class="labs-btn labs-btn--primary">
					<i class="fa fa-save"></i> Simpan Kandidat
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal hasil (tambah / reset): password acak server ditampilkan SEKALI di sini;
	tidak tersimpan di halaman/log/audit. Tutup modal = password tak tampil lagi. -->
<div class="modal fade labs-modal" id="modal-reset-hasil" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><i class="fa fa-key"></i> Sandi baru kandidat</h4>
			</div>
			<div class="modal-body">
				<p style="margin-bottom:12px;">
					Dibuat otomatis oleh server. <strong>Catatan berlaku sekali</strong> &mdash;
					setelah ditutup tidak bisa dilihat lagi. Bagikan lewat saluran administrasi
					(BUKAN email &mdash; keputusan D2); sarankan kandidat menggantinya setelah masuk.
				</p>
				<p style="margin:0;">
					<code id="sandi-baru-tampil"
						style="font-size:1.25rem; display:block; user-select:all; word-break:break-all;"></code>
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-modal-salin" class="labs-btn labs-btn--default labs-btn--sm">
					<i class="fa fa-copy"></i> Salin
				</button>
				<button type="button" id="btn-modal-tutup" class="labs-btn labs-btn--primary labs-btn--sm">
					Sudah dicatat &mdash; muat ulang
				</button>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var holder = document.getElementById('career_csrf_holder');
	if (holder) { document.body.appendChild(holder); holder.style.display = 'none'; }

	var modalHasil = document.getElementById('modal-reset-hasil');
	var sandiEl = document.getElementById('sandi-baru-tampil');
	var sudahTampil = false; // hasil tambah/reset hanya ditampilkan sekali per aksi
	var sandiBaru = null;

	function csrfToken() {
		var el = document.querySelector('#career_csrf_holder input[name="career_csrf"]');
		return el ? el.value : '';
	}

	function tutupModal() {
		$('#modal-reset-hasil').modal('hide');
		sandiEl.textContent = '';
		sudahTampil = false;
		sandiBaru = null;
		window.location.reload(); // sandi tak disimpan setelah modal ditutup
	}

	function hasilSukses(json) {
		if (json && json.success && json.data && json.data.password && !sudahTampil) {
			sudahTampil = true;
			sandiBaru = String(json.data.password);
			sandiEl.textContent = sandiBaru;
			$('#modal-reset-hasil').modal('show'); // modal punya backdrop static: hanya bisa lewat tombol
			return true;
		}
		window.alert((json && json.message) ? json.message : 'Gagal.');
		return true;
	}

	var tombolSalin = document.getElementById('btn-modal-salin');
	var tombolTutup = document.getElementById('btn-modal-tutup');
	if (tombolSalin) tombolSalin.addEventListener('click', function () {
		if (sandiBaru && navigator.clipboard) { navigator.clipboard.writeText(sandiBaru); }
	});
	if (tombolTutup) tombolTutup.addEventListener('click', tutupModal);

	// ---- Aksi reset password (per baris) ----
	document.querySelectorAll('.kandidat-reset').forEach(function (btn) {
		btn.addEventListener('click', function () {
			if (!window.confirm('Reset password kandidat ini? Sistem akan membuat password BARU otomatis (acak); password lama tidak berlaku lagi, sesi/aplikasi kandidat yang aktif ikut berhenti. Password baru ditampilkan SEKALI setelah aksi sukses.')) { return; }
			var fd = new FormData();
			fd.append('career_csrf', csrfToken());
			btn.disabled = true;
			btn.textContent = 'Memproses…';
			fetch(btn.getAttribute('data-url'), {
				method: 'POST',
				body: fd,
				credentials: 'same-origin'
			}).then(function (r) {
				return r.json().then(function (j) { return j; });
			})
			.then(function (json) { hasilSukses(json); })
			.catch(function () { window.alert('Gagal — koneksi.'); })
			.finally(function () {
				btn.disabled = false;
				btn.textContent = 'Reset password';
			});
		});
	});

	// ---- Aksi tambah kandidat (baru) ----
	var modalTambah = document.getElementById('modal-tambah-kandidat');
	var btnBuka = document.getElementById('btn_buka_tambah');
	if (btnBuka) btnBuka.addEventListener('click', function () {
		$('#modal-tambah-kandidat').modal('show');
	});

	var btnSimpan = document.getElementById('btn-simpan-kandidat');
	if (btnSimpan) btnSimpan.addEventListener('click', function () {
		var email   = document.getElementById('tambah-email').value.trim();
		var nama    = document.getElementById('tambah-nama').value.trim();
		var telepon = document.getElementById('tambah-telepon').value.trim();
		if (email === '' || nama === '') {
			window.alert('Email dan nama wajib diisi.');
			return;
		}
		var fd = new FormData();
		fd.append('career_csrf', csrfToken());
		fd.append('email', email);
		fd.append('full_name', nama);
		fd.append('phone', telepon);
		btnSimpan.disabled = true;
		btnSimpan.textContent = 'Menyimpan…';
		fetch('<?php echo site_url('administrator/career_kandidat/add'); ?>', {
			method: 'POST',
			body: fd,
			credentials: 'same-origin'
		}).then(function (r) {
			return r.json().then(function (j) { return j; });
		})
		.then(function (json) {
			if (json && json.success) {
				$('#modal-tambah-kandidat').modal('hide');
				hasilSukses(json);
			} else {
				window.alert((json && json.message) ? json.message : 'Gagal menyimpan kandidat.');
			}
		})
		.catch(function () { window.alert('Gagal — koneksi.'); })
		.finally(function () {
			btnSimpan.disabled = false;
			btnSimpan.innerHTML = '<i class="fa fa-save"></i> Simpan Kandidat';
		});
	});
});
</script>

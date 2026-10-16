<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div id="career_kandidat">
	<h3 class="mb-3">Kandidat Karier (<?php echo (int) $jumlah; ?>)</h3>

	<form method="get" action="<?php echo site_url('administrator/career_kandidat'); ?>" class="mb-3 form-inline">
		<input type="text" name="q" value="<?php echo html_escape($q); ?>"
			placeholder="Cari nama / email…" class="form-control form-control-sm mr-2">
		<button type="submit" class="btn btn-sm btn-primary">Cari</button>
		<?php if ($q !== ''): ?>
			<a href="<?php echo site_url('administrator/career_kandidat'); ?>"
				class="btn btn-sm btn-outline-secondary ml-1">Reset</a>
		<?php endif; ?>
	</form>

	<div class="table-responsive">
		<table class="table table-sm table-bordered">
			<thead>
				<tr>
					<th>#</th>
					<th>Nama</th>
					<th>Email</th>
					<th>Telepon</th>
					<th>Lamaran</th>
					<th>Login terakhir</th>
					<th style="width:1%">Aksi</th>
				</tr>
			</thead>
			<tbody>
			<?php if (empty($kandidats)): ?>
				<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada kandidat.</td></tr>
			<?php endif; ?>
			<?php foreach ($kandidats as $k): ?>
				<tr class="kandidat-row">
					<td><?php echo (int) $k->id_candidate; ?></td>
					<td><?php echo html_escape($k->full_name); ?></td>
					<td><?php echo html_escape($k->email); ?></td>
					<td><?php echo html_escape($k->phone !== NULL ? $k->phone : '—'); ?></td>
					<td><span class="badge badge-info badge-pill"><?php echo (int) $k->jumlah_lamaran; ?></span></td>
					<td><?php echo $k->last_login_at !== NULL
						? html_escape($k->last_login_at) : '<span class="text-muted">belum pernah</span>'; ?></td>
					<td>
						<button type="button"
							class="btn btn-sm btn-outline-warning kandidat-reset"
							data-url="<?php echo site_url('administrator/career_kandidat/reset_password/' . (int) $k->id_candidate); ?>">Reset password
						</button>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<?php echo $pagination; ?>

	<span id="career_csrf_holder" class="career-csrf hidden"><?php echo $csrf_field; ?></span>
</div>

<!-- Modal hasil reset (6B.1): password acak server ditampilkan SEKALI di sini;
	tidak tersimpan di halaman/log/audit. Tutup modal = password tak tampil lagi. -->
<div id="modal-reset-hasil" class="hidden" style="display:none;
	position:fixed; inset:0; z-index:1050; background:rgba(0,0,0,.5);">
	<div style="max-width:520px; margin:10vh auto; background:#fff; border-radius:8px;
		padding:20px; box-shadow:0 10px 30px rgba(0,0,0,.3);">
		<h5 class="mb-3">Sandi baru kandidat</h5>
		<p class="mb-2">Buat otomatis oleh server. <strong>Catatan berlaku sekali</strong> —
			setelah ditutup tidak bisa dilihat lagi. Bagikan lewat saluran administrasi
			(BUKAN email — keputusan D2); sarankan kandidat menggantinya setelah masuk.</p>
		<p class="mb-3"><code id="sandi-baru-tampil" style="font-size:1.25rem; user-select:all;"></code></p>
		<button type="button" id="btn-modal-salin" class="btn btn-sm btn-outline-secondary mr-2">Salin</button>
		<button type="button" id="btn-modal-tutup" class="btn btn-sm btn-primary">Sudah dicatat — muat ulang</button>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var holder = document.getElementById('career_csrf_holder');
	if (holder) { document.body.appendChild(holder); holder.style.display = 'none'; }

	var modal = document.getElementById('modal-reset-hasil');
	var sandiEl = document.getElementById('sandi-baru-tampil');
	var sudahTampil = false; // hasil reset hanya ditampilkan sekali per aksi

	function tutupModal() {
		modal.style.display = 'none';
		sandiEl.textContent = '';
		sudahTampil = false;
		sandiBaru = null;
		window.location.reload(); // sandi tak disimpan setelah modal ditutup
	}

	var tombolSalin = document.getElementById('btn-modal-salin');
	var tombolTutup = document.getElementById('btn-modal-tutup');
	var sandiBaru = null;
	if (tombolSalin) tombolSalin.addEventListener('click', function () {
		if (sandiBaru && navigator.clipboard) { navigator.clipboard.writeText(sandiBaru); }
	});
	if (tombolTutup) tombolTutup.addEventListener('click', tutupModal);

	document.querySelectorAll('.kandidat-reset').forEach(function (btn) {
		btn.addEventListener('click', function () {
			if (!window.confirm('Reset password kandidat ini? Sistem akan membuat password BARU otomatis (acak); password lama tidak berlaku lagi, sesi/aplikasi kandidat yang aktif ikut berhenti. password baru ditampilkan SEKALI setelah aksi sukses.')) { return; }
			var csrf = document.querySelector('#career_csrf_holder input[name="career_csrf"]');
			var fd = new FormData();
			fd.append('career_csrf', csrf ? csrf.value : '');
			btn.disabled = true;
			btn.textContent = 'Memproses…';
			fetch(btn.getAttribute('data-url'), {
				method: 'POST',
				body: fd,
				credentials: 'same-origin'
			}).then(function (r) { return r.json().then(function (j) { return { status: r.status, json: j }; }); })
			.then(function (res) {
				if (res.json && res.json.success && res.json.data && res.json.data.password && !sudahTampil) {
					sudahTampil = true;
					sandiBaru = String(res.json.data.password);
					sandiEl.textContent = sandiBaru;
					modal.style.display = 'block';
				} else {
					window.alert(res.json.message || 'Gagal.');
				}
			}).catch(function () { window.alert('Gagal — koneksi.'); }).finally(function () {
				btn.disabled = false;
				btn.textContent = 'Reset password';
			});
		});
	});
});
</script>

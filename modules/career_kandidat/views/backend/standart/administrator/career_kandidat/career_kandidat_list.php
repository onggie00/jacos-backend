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
							data-url="<?php echo site_url('administrator/career_kandidat/reset_password/' . (int) $k->id_candidate); ?>">
							Reset password
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

<script>
document.addEventListener('DOMContentLoaded', function () {
	var holder = document.getElementById('career_csrf_holder');
	if (holder) { document.body.appendChild(holder); holder.style.display = 'none'; }
	document.querySelectorAll('.kandidat-reset').forEach(function (btn) {
		btn.addEventListener('click', function () {
			if (!window.confirm('Reset password kandidat ini? Password lama kehilangan akses; admin harus menginformasikan password baru ke kandidat (tanpa email).')) { return; }
			var password = window.prompt('Password baru untuk kandidat (minimal 8 karakter):');
			if (password === null) { return; }
			if (password.length < 8) {
				window.alert('Password minimal 8 karakter.');
				return;
			}
			var csrf = document.querySelector('#career_csrf_holder input[name="career_csrf"]');
			var fd = new FormData();
			fd.append('career_csrf', csrf ? csrf.value : '');
			fd.append('password_baru', password);
			btn.disabled = true;
			btn.textContent = 'Menyimpan…';
			fetch(btn.getAttribute('data-url'), {
				method: 'POST',
				body: fd,
				credentials: 'same-origin'
			}).then(function (r) { return r.json().then(function (j) { return { status: r.status, json: j }; }); })
			.then(function (res) {
				window.alert(res.json.message || (res.json.success ? 'Berhasil.' : 'Gagal.'));
				if (res.json.success) { window.location.reload(); }
			}).catch(function () { window.alert('Gagal — koneksi.'); }).finally(function () {
				btn.disabled = false;
				btn.textContent = 'Reset password';
			});
		});
	});
});
</script>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<section class="content-header">
	<h1>
		Guru TK
		<small class="labs-text-muted">Kelola data guru KB</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="javascript:;">Guru TK</a></li>
		<li class="active">Data Guru</li>
	</ol>
</section>

<section class="content">
<div id="guru_tk">
	<div class="labs-card">
		<div class="labs-card__header">
			<h3 class="labs-card__title">
				<i class="fa fa-users"></i>
				Daftar Guru TK
				<span class="labs-badge labs-badge--default"><?= (int) $guru_tk_counts; ?> guru</span>
			</h3>
			<div>
				<?php if ( ! empty($bisa_add)): ?>
					<a href="<?= site_url('administrator/guru_tk/add'); ?>" class="labs-btn labs-btn--primary labs-btn--sm">
						<i class="fa fa-user-plus"></i> Tambah Guru TK
					</a>
				<?php endif; ?>
				<?php if ( ! empty($bisa_export)): ?>
					<a href="<?= site_url('administrator/guru_tk/export'); ?>" class="labs-btn labs-btn--default labs-btn--sm">
						<i class="fa fa-download"></i> Export CSV
					</a>
				<?php endif; ?>
			</div>
		</div>

		<div class="labs-card__body">
			<form method="get" action="<?= site_url('administrator/guru_tk'); ?>" class="labs-filter-inline mb-3">
				<div class="labs-filter-inline__search">
					<i class="fa fa-search labs-filter-inline__search-icon"></i>
					<input type="text" name="q" value="<?= html_escape($this->input->get('q')); ?>" placeholder="Cari nama / emp_code&hellip;">
				</div>
				<button type="submit" class="labs-btn labs-btn--primary labs-btn--sm" title="Cari">
					<i class="fa fa-filter"></i>
				</button>
			</form>

			<div class="labs-table-wrap">
				<div class="labs-table-scroll">
					<table class="labs-table">
						<thead>
							<tr>
								<th class="labs-cell-numeric">No.</th>
								<th>Guru</th>
								<th>Emp Code</th>
								<th>Email</th>
								<th>Posisi</th>
								<th>Status Kepegawaian</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php if (empty($guru_tks)): ?>
							<tr>
								<td colspan="7">
									<div class="labs-empty">
										<i class="fa fa-user-secret"></i>
										<p>Tidak ada data guru KB.</p>
									</div>
								</td>
							</tr>
						<?php endif; ?>
						<?php foreach ($guru_tks as $k): ?>
							<tr>
								<td class="labs-cell-numeric labs-cell-muted"><?= (int) $k->id_guru; ?></td>
								<td>
									<div class="labs-name-cell">
										<?php if ( ! empty($k->foto_profil)): ?>
											<span class="labs-avatar">
												<img src="<?= base_url('uploads/guru_tk/' . $k->foto_profil); ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
											</span>
										<?php else: ?>
											<span class="labs-avatar"><?= html_escape(strtoupper(mb_substr(trim((string) $k->nama_lengkap), 0, 2))); ?></span>
										<?php endif; ?>
										<div>
											<div class="labs-fw-semi"><?= html_escape($k->nama_lengkap); ?></div>
											<div class="labs-fs-xs labs-text-muted">ID: <?= (int) $k->id_guru; ?></div>
										</div>
									</div>
								</td>
								<td><?= html_escape($k->emp_code); ?></td>
								<td class="labs-cell-muted"><?= html_escape($k->email); ?></td>
								<td><?= html_escape($k->posisi_guru_nama_posisi); ?></td>
								<td><?= html_escape($k->status_kepegawaian); ?></td>
								<td>
									<div class="labs-row-actions">
										<?php if ( ! empty($bisa_view)): ?>
											<a href="<?= site_url('administrator/guru_tk/view/' . (int) $k->id_guru); ?>" class="btn btn-sm btn-outline-primary">Detail</a>
										<?php endif; ?>
										<?php if ( ! empty($bisa_update)): ?>
											<a href="<?= site_url('administrator/guru_tk/edit/' . (int) $k->id_guru); ?>" class="btn btn-sm btn-outline-info">Ubah</a>
										<?php endif; ?>
										<?php if ( ! empty($bisa_delete)): ?>
											<a href="#" data-hapus="<?= (int) $k->id_guru; ?>"
												data-nama="<?= html_escape($k->nama_lengkap); ?>"
												data-url="<?= site_url('administrator/guru_tk/delete/' . (int) $k->id_guru); ?>"
												class="btn btn-sm btn-outline-danger">Hapus</a>
										<?php endif; ?>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

			<div class="mt-3 d-flex justify-content-between align-items-center">
				<span class="labs-text-muted labs-fs-xs">Total <?= (int) $guru_tk_counts; ?> guru KB</span>
			</div>
			<?php echo $pagination; ?>
		</div>
	</div>
</div>
</section>

<?php $csrf = array(
	'name' => $this->security->get_csrf_token_name(),
	'hash' => $this->security->get_csrf_hash(),
); ?>
<form id="form_hapus_kb" method="post" action="">
	<input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>">
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('[data-hapus]').forEach(function (a) {
		a.addEventListener('click', function (e) {
			e.preventDefault();
			if (!window.confirm('Hapus guru "' + a.dataset.nama + '"? Data tidak akan tampil di daftar lagi (arsip).')) {
				return;
			}
			var form = document.getElementById('form_hapus_kb');
			form.action = a.dataset.url;
			form.submit();
		});
	});
});
</script>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$g = isset($guru_tk) ? $guru_tk : null;
if ( ! $g || (property_exists($g, 'deleted_at') && $g->deleted_at !== NULL)) {
	?>
	<div class="alert alert-danger">Data guru tidak ditemukan.</div>
	<a href="<?= site_url('administrator/guru_tk'); ?>" class="btn btn-default">&larr; Kembali</a>
	<?php
	return;
}

function guru_label_tabel($a)
{
	return is_array($a) && isset($a, $a['id_guru']) ? $a : null;
}
?>
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<section class="content-header">
	<h1>
		Detail Guru TK &mdash; <?= html_escape($g->nama_lengkap); ?>
		<small class="labs-text-muted">#<?= (int) $g->id_guru; ?></small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="<?= site_url('administrator/guru_tk'); ?>">Guru TK</a></li>
		<li class="active">Detail</li>
	</ol>
</section>

<section class="content">
<div class="row">
	<div class="col-md-4">
		<div class="labs-card">
			<div class="labs-card__body" style="text-align:center;">
				<?php if ( ! empty($g->foto_profil)): ?>
					<img src="<?= base_url('uploads/guru_tk/' . $g->foto_profil); ?>" alt="Foto <?= html_escape($g->nama_lengkap); ?>" style="width:110px;height:110px;border-radius:50%;object-fit:cover;">
				<?php else: ?>
					<span class="labs-avatar" style="width:110px;height:110px;font-size:38px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:#eef3f7;color:#365f80;font-weight:700;">
						<?= html_escape(strtoupper(mb_substr(trim((string) $g->nama_lengkap), 0, 2))); ?>
					</span>
				<?php endif; ?>
				<h3 style="margin:12px 0 4px;"><?= html_escape($g->nama_lengkap); ?></h3>
				<span class="labs-badge labs-badge--default"><?= html_escape($g->emp_code); ?></span>
				<div style="margin-top:10px;" class="labs-text-muted"><?= html_escape($g->email); ?></div>
				<div class="mt-3">
					<a href="<?= site_url('administrator/guru_tk/edit/' . (int) $g->id_guru); ?>" class="labs-btn labs-btn--primary labs-btn--sm">Ubah</a>
					<a href="<?= site_url('administrator/guru_tk'); ?>" class="labs-btn labs-btn--default labs-btn--sm">&larr; Daftar</a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-8">
		<div class="labs-card">
			<div class="labs-card__header"><h3 class="labs-card__title"><i class="fa fa-info-circle"></i> Data Lengkap</h3></div>
			<div class="labs-card__body">
				<div class="labs-table-wrap">
					<div class="labs-table-scroll">
						<table class="labs-table">
							<tbody>
							<tr><th style="width:200px;">NIK</th><td><?= html_escape($g->nik); ?></td></tr>
							<tr><th>NUPTK</th><td><?= html_escape($g->nuptk); ?></td></tr>
							<tr><th>NPP</th><td><?= html_escape($g->npp); ?></td></tr>
							<tr><th>NPWP</th><td><?= html_escape($g->npwp); ?></td></tr>
							<tr><th>Tempat / Tgl Lahir</th><td><?= html_escape($g->tempat_lahir); ?>, <?= html_escape($g->tgl_lahir); ?></td></tr>
							<tr><th>Jenis Kelamin</th><td><?= $g->jenis_kelamin === 'L' ? 'Laki-laki' : ($g->jenis_kelamin === 'P' ? 'Perempuan' : html_escape($g->jenis_kelamin)); ?></td></tr>
							<tr><th>Agama</th><td><?= html_escape($g->agama); ?></td></tr>
							<tr><th>Status Menikah</th><td><?= html_escape($g->status_menikah); ?></td></tr>
							<tr><th>Jumlah Anak</th><td><?= html_escape($g->jumlah_anak); ?></td></tr>
							<tr><th>No. KK</th><td><?= html_escape($g->no_kk); ?></td></tr>
							<tr><th>Alamat</th><td><?= html_escape($g->alamat); ?></td></tr>
							<tr><th>No. Telp</th><td><?= html_escape($g->no_telp); ?></td></tr>
							<tr><th>Email</th><td><?= html_escape($g->email); ?></td></tr>
							<tr><th>Email MS Office</th><td><?= html_escape($g->email_ms_office); ?></td></tr>
							<tr><th>Satuan Pendidikan</th><td><?= html_escape($g->satuan_pendidikan); ?></td></tr>
							<tr><th>Unit</th><td><?= html_escape($g->unit); ?></td></tr>
							<tr><th>Posisi</th><td><?= html_escape($g->posisi_guru_nama_posisi); ?></td></tr>
							<tr><th>Status Kepegawaian</th><td><?= html_escape($g->status_kepegawaian); ?></td></tr>
							<tr><th>Informasi Kepala Pimpinan</th><td><?= html_escape($g->informasi_kepala_pimpinan); ?></td></tr>
							<tr><th>Keterangan Jabatan</th><td><?= html_escape($g->keterangan_jabatan); ?></td></tr>
							<tr><th>Jam Ajar</th><td><?= html_escape($g->jam_ajar); ?></td></tr>
							<tr><th>Pendidikan Terakhir</th><td><?= html_escape($g->pendidikan_terakhir); ?></td></tr>
							<tr><th>Universitas</th><td><?= html_escape($g->universitas); ?></td></tr>
							<tr><th>Jurusan</th><td><?= html_escape($g->jurusan); ?></td></tr>
							<tr><th>Tahun Lulus</th><td><?= html_escape($g->tahun_lulus); ?></td></tr>
							<tr><th>Presensi Role</th><td><?= html_escape($g->presensi_role); ?></td></tr>
							<tr><th>Dibuat</th><td><?= html_escape($g->created_at); ?></td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</section>

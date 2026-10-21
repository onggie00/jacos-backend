<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Form konversi pelamar → guru (GURU.1 Bagian B-3 langkah 1).
 * Pratinjau data kandidat + form field admin; pelamar tidak ditampilkan
 * NIK / NPWP / no_kk — open decision.
 */
$app = isset($app) ? $app : null;
if ( ! $app) { return; }

?>
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<section class="content-header">
	<h1>
		Konversi Pelamar &arrow; Guru <?= strtoupper($jenjang_pilihan); ?>
		<small class="labs-text-muted">Salin data kandidat ke data guru</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="<?= site_url('administrator/career_pelamar'); ?>">Pelamar Karier</a></li>
		<li><a href="<?= site_url('administrator/career_pelamar/view/' . (int) $app->id_application); ?>">Detail</a></li>
		<li class="active">Konversi</li>
	</ol>
</section>

<section class="content">
<div id="career_konversi">

	<?php if ( ! empty($peringatan)): ?>
		<div class="alert alert-warning"><?= html_escape($peringatan); ?></div>
	<?php endif; ?>

	<div class="labs-card">
		<div class="labs-card__header">
			<h3 class="labs-card__title"><i class="fa fa-exchange"></i> Data pelamar diterima</h3>
		</div>
		<div class="labs-card__body">
			<div class="labs-table-wrap">
				<div class="labs-table-scroll">
					<table class="labs-table">
						<tbody>
						<tr><th style="width:200px;">Lamaran</th><td>#<?= (int) $app->id_application; ?></td></tr>
						<tr><th>Kandidat</th><td><?= html_escape($app->full_name); ?></td></tr>
						<tr><th>Email kandidat</th><td><?= html_escape($app->email); ?></td></tr>
						<tr><th>Status</th><td><span class="labs-badge labs-badge--info"><?= html_escape($app->status); ?></span></td></tr>
						<tr><th>Diajukan</th><td><?= html_escape($app->submitted_at); ?></td></tr>
						<tr><th>Telepon (profil)</th><td><?= html_escape($profil ? $profil->phone : '&mdash;'); ?></td></tr>
						<tr><th>Tempat / Tgl Lahir</th><td><?= html_escape($profil ? $profil->place_of_birth : '&mdash;'); ?>, <?= html_escape($profil ? $profil->date_of_birth : '&mdash;'); ?></td></tr>
						<tr><th>Jenis kelamin</th><td><?= ($profil && $profil->gender) == 'male' ? 'Laki-laki' : (($profil && $profil->gender != NULL) ? 'Perempuan' : '&mdash;'); ?></td></tr>
						<tr><th>Pendidikan terbaru</th><td>
							<?php if ($pendidikan_terbaru): ?>
								<?= html_escape($pendidikan_terbaru->title); ?>
								<?php if ($pendidikan_terbaru->institution): ?>&mdash; <?= html_escape($pendidikan_terbaru->institution); ?><?php endif; ?>
								<?php if ($pendidikan_terbaru->end_date): ?>(<?= html_escape($pendidikan_terbaru->end_date); ?>)<?php endif; ?>
							<?php else: ?>
								<span class="labs-text-muted">Tidak ada item pendidikan.</span>
							<?php endif; ?>
						</td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<?php echo form_open('', ['id' => 'form_konversi_guru', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']); ?>
	<input type="hidden" name="id_application" value="<?= (int) $app->id_application; ?>">
	<input type="hidden" name="jenjang_pilihan" value="<?= html_escape($jenjang_pilihan); ?>">
	<?= career_csrf_field(); ?>

	<div class="labs-card">
		<div class="labs-card__header">
			<h3 class="labs-card__title"><i class="fa fa-user-plus"></i> Form pembekal guru</h3>
		</div>
		<div class="labs-card__body">
			<div class="row">
				<div class="col-md-6">
					<h5><b>Data kepegawaian (wajib)</b></h5>
					<div class="alert alert-info" style="font-size:12.5px;">
						Data sudah diambil dari snapshot kandidat diperiksa admin.
					</div>

					<div class="form-group">
						<label for="kg_emp_code">Emp Code <span class="text-danger">*</span></label>
						<input type="text" name="emp_code" id="kg_emp_code" class="form-control" maxlength="255" required value="<?= html_escape(set_value('kg_emp_code')); ?>">
						<span class="help-block">Wajib unik lintas guru_* (SD/SMP/SMA/FT/KB/TK).</span>
					</div>
					<div class="form-group">
						<label for="kg_jenjang">Jenjang tujuan <span class="text-danger">*</span></label>
						<select name="jenjang" id="kg_jenjang" class="form-control" required>
							<option value="">— pilih —</option>
							<?php foreach (array('SD', 'KB', 'TK') as $j): ?>
								<option value="<?= $j; ?>"><?= $j === 'SD' ? 'Guru SD' : ($j === 'KB' ? 'Guru KB' : 'Guru TK'); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="kg_id_posisi">Posisi</label>
						<select name="id_posisi" id="kg_id_posisi" class="form-control">
							<?php foreach ($posisi as $p): ?>
								<option value="<?= (int) $p->id_posisi; ?>" <?= ((int) $p->id_posisi === $ps_default) ? 'selected' : ''; ?>><?= html_escape($p->nama_posisi); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="kg_npp">NPP &mdash; opsional</label>
						<input type="text" name="npp" id="kg_npp" class="form-control" maxlength="255">
					</div>
					<div class="form-group">
						<label for="kg_email_ms_office">Email MS Office (login aplikasi guru) &mdash; opsional</label>
						<input type="email" name="email_ms_office" id="kg_email_ms_office" class="form-control" maxlength="255">
					</div>
					<div class="form-group">
						<label for="kg_status_kepegawaian">Status kepegawaian</label>
						<input type="text" name="status_kepegawaian" id="kg_status_kepegawaian" class="form-control" maxlength="255">
					</div>
					<div class="form-group">
						<label for="kg_keterangan_jabatan">Keterangan jabatan</label>
						<input type="text" name="keterangan_jabatan" id="kg_keterangan_jabatan" class="form-control" maxlength="50">
					</div>

					<?php if (strtoupper($jenjang_pilihan) === 'SD'): ?>
						<div class="form-group">
							<label for="kg_id_mapel">Mapel</label>
							<select name="kg_id_mapel" id="kg_id_mapel" class="form-control">
								<option value="">— pilih —</option>
								<?php foreach ($mapel as $m): ?>
									<option value="<?= (int) $m->id_mapel; ?>"><?= html_escape($m->nama_mapel); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php else: ?>
						<div class="alert alert-info" style="font-size:12px;">Mapel tidak diperlukan untuk jenjang KB/TK.</div>
					<?php endif; ?>
				</div>

				<div class="col-md-6">
					<h5><b>Data yang disalin dari profil kandidat</b></h5>
					<div class="alert alert-info" style="font-size:12px;">
						Field ini DISALIN (tidak di-prev hacking — data di career cukup utuh).
					</div>

					<div class="form-group">
						<label for="kg_email">Email guru <span class="text-danger">*</span></label>
						<input type="email" name="email" id="kg_email" class="form-control" maxlength="255" required value="<?= html_escape(strtolower(trim($app->email))); ?>">
						<span class="help-block">Wajib unik lintas email & MS Office semua jenjang.</span>
					</div>
					<div class="form-group">
						<label for="kg_unit">Unit</label>
						<input type="text" name="unit" id="kg_unit" class="form-control" maxlength="255">
					</div>
					<div class="form-group">
						<label for="kg_status_kepegawaian2">Status kepegawaian</label>
						<input type="text" id="kg_status_kepegawaian2" name="kg_status_kepegawaian2" class="form-control" maxlength="255">
					</div>
					<div class="form-group">
						<label for="kg_pendidikan_terakhir">Pendidikan terakhir</label>
						<input type="text" name="pendidikan_terakhir" id="kg_pendidikan_terakhir" class="form-control" maxlength="50">
					</div>
					<div class="form-group">
						<label for="kg_universitas">Universitas</label>
						<input type="text" name="universitas" id="kg_universitas" class="form-control" maxlength="200">
					</div>
					<div class="form-group">
						<label for="kg_jurusan">Jurusan</label>
						<input type="text" name="jurusan" id="kg_jurusan" class="form-control" maxlength="150">
					</div>
					<div class="form-group">
						<label for="kg_tahun_lulus">Tahun lulus</label>
						<input type="number" name="tahun_lulus" id="kg_tahun_lulus" class="form-control" min="1900" max="2100">
					</div>

					<div class="form-group" id="warning_area" style="display: none;">
						<div class="checkbox" style="background:#fffbe6;border:1px solid #f5deb3;border-radius:6px;padding:12px;">
							<label style="font-weight:normal;">
								<input type="checkbox" name="periksa_dipastikan" value="1">
								<span style="font-weight:600;">Saya sudah memeriksa dengan hati-hati &mdash; guru lama yang saya terlihat di daftar (nama + tanggal lahir sama) adalah orang lain</span>
							</label>
						</div>
						<p class="text-warning" style="font-size:12px;">
							Cek ini dibutuhkan bila sistem mendeteksi tabrakan nama + tanggal lahir. Tanpa centang,
							sistem menolak menyimpan.
						</p>
					</div>
				</div>
			</div>

			<div class="row" style="margin-top: 15px;">
				<div class="col-md-12">
					<button type="submit" class="labs-btn labs-btn--primary" id="btn_simpan">
						<i class="fa fa-save"></i> Simpan Konversi
					</button>
					<a href="<?= site_url('administrator/career_pelamar/view/' . (int) $app->id_application); ?>" class="labs-btn labs-btn--default">Batal</a>
				</div>
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var form = document.getElementById('form_konversi_guru');
	var areaPeriksa = document.getElementById('warning_area');
	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var btn = document.getElementById('btn_simpan');
		var fd = new FormData(form);
		btn.disabled = true;
		btn.textContent = 'Menyimpan…';
		fetch('<?= site_url('administrator/career_guru/konversi_simpan_post/' . (int) $app->id_application); ?>', {
			method: 'POST',
			body: fd,
			credentials: 'same-origin'
		}).then(function (r) { return r.json(); }).then(function (j) {
			if (j && j.success) {
				window.location.href = j.redirect || '<?= site_url('administrator/career_pelamar/view/' . (int) $app->id_application); ?>';
				return;
			}
			var pesan = (j && j.message) ? j.message : 'Gagal menyimpan.';
			if (j && j.tahan === true) {
				areaPeriksa.style.display = 'block';
			}
			window.alert(pesan);
		}).catch(function () {
			window.alert('Gagal — koneksi.');
		}).finally(function () {
			btn.disabled = false;
			btn.innerHTML = '<i class="fa fa-save"></i> Simpan Konversi';
		});
	});
});
</script>

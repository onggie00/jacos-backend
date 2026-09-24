<div class="row">
	<div class="col-sm-12">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title"><i class="fa fa-upload"></i> Import Buat Baru — Siswa TK Aktif (Data Historis)</h3>
				<div class="box-tools pull-right">
					<a class="btn btn-sm btn-default" href="<?= site_url('administrator/siswa_tk_aktif'); ?>"><i class="fa fa-arrow-left"></i> Kembali</a>
				</div>
			</div>
			<div class="box-body">
				<?php if ($this->session->flashdata('success')): ?>
					<div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
				<?php endif; ?>
				<?php if ($this->session->flashdata('error')): ?>
					<div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
				<?php endif; ?>

				<div class="alert alert-warning">
					<i class="fa fa-exclamation-triangle"></i>
					<b>Untuk migrasi data historis:</b> baris akan dibuat BARU di <code>siswa_tk_aktif</code>
					dengan <code>id_siswa_tk = 0</code> (tidak terhubung record PSB).
					Tahun ajaran dipilih per-batch. Selalu cek preview sebelum konfirmasi.
				</div>

				<?php if (empty($preview)): ?>
					<form action="<?= base_url('administrator/siswa_tk_aktif/import_create_parse'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
						<div class="form-group">
							<label class="control-label col-sm-2">Tahun Ajaran <i class="required">*</i></label>
							<div class="col-sm-4">
								<select name="id_tahun_ajaran" class="form-control" required>
									<option value="">- Pilih Tahun Ajaran -</option>
									<?php foreach ($list_tahun_ajaran as $ta): ?>
										<option value="<?= $ta->id_tahun_ajaran; ?>"><?= $ta->label; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2">File Excel <i class="required">*</i></label>
							<div class="col-sm-4">
								<input name="file_import" class="form-control" type="file" accept=".xls,.xlsx" required>
							</div>
							<div class="col-sm-4">
								<a class="btn btn-sm btn-default" href="<?= site_url('administrator/siswa_tk_aktif/template_import_create'); ?>"><i class="fa fa-download"></i> Download Template</a>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-4">
								<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Upload &amp; Preview</button>
							</div>
						</div>
					</form>
					<div style="margin-top:10px;padding:10px;background:#f8f9fa;border-radius:3px">
						<small><b>Kolom template:</b> NO, NAMA LENGKAP, NIS, KELAS (label kelas_kb), TINGKATAN (label tingkatan_kb),
						KEWARGANEGARAAN, NIK, GOLONGAN DARAH, TELP, PENDIDIKAN AYAH/IBU, PENGHASILAN AYAH/IBU,
						TGL LAHIR AYAH/IBU (YYYY-MM-DD), TGL LAHIR SISWA (YYYY-MM-DD), NOMOR PESERTA UJIAN.</small><br>
						<small><b>Validasi:</b> NIS duplikat (DB maupun di dalam file) di-skip, kelas tidak ditemukan di-skip,
						nama/NIS kosong di-skip — semua dilaporkan di preview.</small>
					</div>
				<?php else: ?>
					<div class="alert alert-info">
						File: <b><?= htmlspecialchars($preview['file']); ?></b> —
						Tahun Ajaran terpilih: <b>
						<?php foreach ($list_tahun_ajaran as $ta) { if ($ta->id_tahun_ajaran == $preview['id_tahun_ajaran']) { echo $ta->label; } } ?>
						</b> —
						<b><?= count($preview['rows']); ?></b> baris siap insert,
						<b><?= count($preview['skipped']); ?></b> baris di-skip.
					</div>

					<?php if (count($preview['rows'])): ?>
						<table class="table table-bordered table-striped table-condensed">
							<thead><tr><th>#</th><th>Nama</th><th>NIS</th><th>Kelas</th><th>Tingkatan</th><th>NIK</th><th>Tgl Lahir</th></tr></thead>
							<tbody>
								<?php $no = 1; foreach ($preview['rows'] as $r): ?>
								<tr>
									<td><?= $no++; ?></td>
									<td><?= htmlspecialchars($r['nama_lengkap']); ?></td>
									<td><?= htmlspecialchars($r['nis']); ?></td>
									<td><?= $r['id_kelas']; ?></td>
									<td><?= $r['id_tingkatan']; ?></td>
									<td><?= htmlspecialchars($r['nik']); ?></td>
									<td><?= $r['tgl_lahir']; ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>

					<?php if (count($preview['peringatan'])): ?>
						<h4>Catatan (baris tetap masuk)</h4>
						<table class="table table-condensed">
							<thead><tr><th>Baris</th><th>Nama</th><th>Catatan</th></tr></thead>
							<tbody>
								<?php foreach ($preview['peringatan'] as $p): ?>
								<tr class="warning"><td><?= $p['row']; ?></td><td><?= htmlspecialchars($p['nama']); ?></td><td><?= htmlspecialchars($p['catatan']); ?></td></tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>

					<?php if (count($preview['skipped'])): ?>
						<h4>Baris Di-skip (tidak akan di-insert)</h4>
						<table class="table table-condensed">
							<thead><tr><th>Baris</th><th>Nama</th><th>Alasan</th></tr></thead>
							<tbody>
								<?php foreach ($preview['skipped'] as $s): ?>
								<tr class="danger"><td><?= $s['row']; ?></td><td><?= htmlspecialchars($s['nama']); ?></td><td><?= htmlspecialchars($s['alasan']); ?></td></tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>

					<form action="<?= base_url('administrator/siswa_tk_aktif/import_create_commit'); ?>" method="post" class="form-inline" style="margin-top:10px">
						<button type="submit" class="btn btn-success" onclick="return confirm('Insert <?= count($preview['rows']); ?> baris ke siswa_tk_aktif? Data historis — pastikan preview sudah benar.');"><i class="fa fa-check"></i> Konfirmasi &amp; Insert <?= count($preview['rows']); ?> Baris</button>
						<a class="btn btn-default" href="<?= site_url('administrator/siswa_tk_aktif/import_create_cancel'); ?>">Batal</a>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

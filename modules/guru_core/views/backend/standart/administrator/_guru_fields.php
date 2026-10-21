<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Field umum Guru (dipakai tampilan add/update guru_kb & guru_tk; file ini
 * di-include lewat require_once dari views jenjang).
 *
 * Variabel tersedia:
 *  $g        (stdClass|null) baris guru mode edit; null mode add
 *  $prefix   'kb' atau 'tk'
 *  $posisi   array (id_posisi => nama_posisi)
 */

if ( ! function_exists('guru_fields')) {
function guru_fields($g, $prefix, $posisi = [])
{
	$default_posisi = ($prefix === 'kb') ? 5 : 6;
	?>
	<div class="row">
		<div class="col-md-6">
			<div class="card box">
				<div class="box-header with-border"><b>Data Pribadi</b></div>
				<div class="box-body">
					<div class="form-group">
						<label>Nama Lengkap <span class="text-danger">*</span></label>
						<input type="text" name="nama_lengkap" class="form-control" maxlength="255" value="<?= $g ? html_escape($g->nama_lengkap) : ''; ?>" required>
					</div>
					<div class="form-group">
						<label>NIK &mdash; opsional</label>
						<input type="text" name="nik" class="form-control" maxlength="16" value="<?= $g ? html_escape($g->nik) : ''; ?>" placeholder="16 digit">
					</div>
					<div class="form-group">
						<label>NUPTK</label>
						<input type="text" name="nuptk" class="form-control" maxlength="50" value="<?= $g ? html_escape($g->nuptk) : ''; ?>">
					</div>
					<div class="form-group">
						<label>NPP</label>
						<input type="text" name="npp" class="form-control" maxlength="50" value="<?= $g ? html_escape($g->npp) : ''; ?>">
					</div>
					<div class="form-group">
						<label>NPWP</label>
						<input type="text" name="npwp" class="form-control" maxlength="50" value="<?= $g ? html_escape($g->npwp) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Tempat Lahir</label>
						<input type="text" name="tempat_lahir" class="form-control" maxlength="50" value="<?= $g ? html_escape($g->tempat_lahir) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Tanggal Lahir</label>
						<input type="date" name="tgl_lahir" class="form-control" value="<?= $g ? html_escape($g->tgl_lahir) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Jenis Kelamin</label>
						<select name="jenis_kelamin" class="form-control">
							<option value="">— pilih —</option>
							<option value="L" <?= ($g && $g->jenis_kelamin === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
							<option value="P" <?= ($g && $g->jenis_kelamin === 'P') ? 'selected' : ''; ?>>Perempuan</option>
						</select>
					</div>
					<div class="form-group">
						<label>Agama</label>
						<input type="text" name="agama" class="form-control" maxlength="20" value="<?= $g ? html_escape($g->agama) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Status Menikah</label>
						<select name="status_menikah" class="form-control">
							<option value="">— pilih —</option>
							<?php foreach (array('Sudah Menikah', 'Belum Menikah') as $opt): ?>
								<option value="<?= $opt; ?>" <?= ($g && $g->status_menikah === $opt) ? 'selected' : ''; ?>><?= $opt; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Jumlah Anak</label>
						<input type="number" name="jumlah_anak" class="form-control" min="0" max="99" value="<?= $g ? html_escape($g->jumlah_anak) : ''; ?>">
					</div>
					<div class="form-group">
						<label>No. KK</label>
						<input type="text" name="no_kk" class="form-control" maxlength="50" value="<?= $g ? html_escape($g->no_kk) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Alamat</label>
						<textarea name="alamat" class="form-control" rows="2"><?= $g ? html_escape($g->alamat) : ''; ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card box">
				<div class="box-header with-border"><b>Data Pekerjaan (<?= strtoupper($prefix); ?>)</b></div>
				<div class="box-body">
					<div class="form-group">
						<label>Emp Code <span class="text-danger">*</span></label>
						<input type="text" name="emp_code" class="form-control" maxlength="255" value="<?= $g ? html_escape($g->emp_code) : ''; ?>" required>
						<span class="help-block">Wajib unik lintas semua jenjang guru (SD/SMP/SMA/FT/<?= strtoupper($prefix); ?>).</span>
					</div>
					<div class="form-group">
						<label>Email pribadi</label>
						<input type="email" name="email" class="form-control" maxlength="255" value="<?= $g ? html_escape($g->email) : ''; ?>">
						<span class="help-block">Wajib unik lintas email & email MS Office semua jenjang.</span>
					</div>
					<div class="form-group">
						<label>Email MS Office (login aplikasi guru) &mdash; opsional</label>
						<input type="email" name="email_ms_office" class="form-control" maxlength="255" value="<?= $g ? html_escape($g->email_ms_office) : ''; ?>">
					</div>
					<div class="form-group">
						<label>No. Telp</label>
						<input type="text" name="no_telp" class="form-control" maxlength="25" value="<?= $g ? html_escape($g->no_telp) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Posisi</label>
						<select name="id_posisi" class="form-control">
							<?php foreach ($posisi as $id => $nama): ?>
								<option value="<?= (int) $id; ?>" <?= ($g && (int) $g->id_posisi === (int) $id) ? 'selected' : (!$g && (int) $id === $default_posisi ? 'selected' : ''); ?>><?= html_escape($nama); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label>Satuan Pendidikan</label>
						<input type="text" class="form-control" value="<?= $g ? html_escape($g->satuan_pendidikan) : strtoupper($prefix); ?>" disabled>
						<span class="help-block">Di-set otomatis server (<?= strtoupper($prefix); ?>).</span>
					</div>
					<div class="form-group">
						<label>Unit</label>
						<input type="text" name="unit" class="form-control" maxlength="255" value="<?= $g ? html_escape($g->unit) : strtoupper($prefix); ?>">
					</div>
					<div class="form-group">
						<label>Status Kepegawaian</label>
						<input type="text" name="status_kepegawaian" class="form-control" maxlength="255" value="<?= $g ? html_escape($g->status_kepegawaian) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Informasi Kepala Pimpinan</label>
						<input type="text" name="informasi_kepala_pimpinan" class="form-control" maxlength="255" value="<?= $g ? html_escape($g->informasi_kepala_pimpinan) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Keterangan Jabatan</label>
						<input type="text" name="keterangan_jabatan" class="form-control" maxlength="50" value="<?= $g ? html_escape($g->keterangan_jabatan) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Jam Ajar</label>
						<input type="text" name="jam_ajar" class="form-control" maxlength="50" value="<?= $g ? html_escape($g->jam_ajar) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Presensi Role</label>
						<input type="text" class="form-control" value="<?= $g ? html_escape($g->presensi_role) : 'GURU' . strtoupper($prefix); ?>" disabled>
						<span class="help-block">Di-set otomatis server (GURU<?= strtoupper($prefix); ?>).</span>
					</div>
				</div>
			</div>

			<div class="card box">
				<div class="box-header with-border"><b>Pendidikan</b></div>
				<div class="box-body">
					<div class="form-group">
						<label>Pendidikan Terakhir</label>
						<input type="text" name="pendidikan_terakhir" class="form-control" maxlength="50" value="<?= $g ? html_escape($g->pendidikan_terakhir) : ''; ?>" placeholder="S1 / S2 / D3 / SMA / SMK">
					</div>
					<div class="form-group">
						<label>Universitas</label>
						<input type="text" name="universitas" class="form-control" maxlength="200" value="<?= $g ? html_escape($g->universitas) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Jurusan</label>
						<input type="text" name="jurusan" class="form-control" maxlength="150" value="<?= $g ? html_escape($g->jurusan) : ''; ?>">
					</div>
					<div class="form-group">
						<label>Tahun Lulus</label>
						<input type="number" name="tahun_lulus" class="form-control" min="1900" max="2100" value="<?= $g ? html_escape($g->tahun_lulus) : ''; ?>">
					</div>
				</div>
			</div>

			<div class="card box">
				<div class="box-header with-border"><b>Foto Profil</b></div>
				<div class="box-body">
					<?php if ($g && ! empty($g->foto_profil)): ?>
						<p><img src="<?= base_url('uploads/' . ($prefix === 'kb' ? 'guru_kb' : 'guru_tk') . '/' . $g->foto_profil); ?>" alt="Foto" style="max-width:120px;max-height:120px;border-radius:8px;"></p>
						<span class="help-block">Biarkan kosong untuk mempertahankan foto lama.</span>
					<?php endif; ?>
					<input type="file" name="foto_profil" accept=".jpg,.jpeg,.png,.webp" class="form-control">
					<span class="help-block">JPG/JPEG/PNG/WebP, maksimal 2 MB.</span>
				</div>
			</div>
		</div>
	</div>
	<?php
}
}

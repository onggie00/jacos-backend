<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$DIR_VIEWS = dirname(__DIR__);
require_once $DIR_VIEWS . '/../../../../../guru_core/views/backend/standart/administrator/_guru_fields.php';
if ( ! function_exists('guru_fields')) {
	exit('Helper tampilan guru tidak ditemukan');
}
?>
<link rel="stylesheet" href="<?= BASE_ASSET; ?>css/labs-ui/labs-ui.css">
<script src="<?= BASE_ASSET; ?>js/labs-ui/labs-ui-init.js"></script>

<section class="content-header">
	<h1>
		Tambah Guru KB
		<small class="labs-text-muted">Data guru KB baru</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?= base_url('administrator'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="<?= site_url('administrator/guru_kb'); ?>">Guru KB</a></li>
		<li class="active">Tambah</li>
	</ol>
</section>

<section class="content">
<div id="guru_kb_add">
	<?php echo form_open('', ['id' => 'form_guru_kb', 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data']); ?>
	<?php guru_fields(null, 'kb', isset($posisi) ? $posisi : []); ?>
	<div class="row" style="margin-top: 15px;">
		<div class="col-md-12">
			<button type="submit" class="labs-btn labs-btn--primary" id="btn-simpan-guru">
				<i class="fa fa-save"></i> Simpan Guru KB
			</button>
			<a href="<?= site_url('administrator/guru_kb'); ?>" class="labs-btn labs-btn--default">
				Batal
			</a>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var form = document.getElementById('form_guru_kb');
	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var btn = document.getElementById('btn-simpan-guru');
		var fd = new FormData(form);
		btn.disabled = true;
		btn.textContent = 'Menyimpan…';
		fetch('<?= site_url('administrator/guru_kb/add_save'); ?>', {
			method: 'POST',
			body: fd,
			credentials: 'same-origin'
		}).then(function (r) { return r.json(); }).then(function (j) {
			if (j && j.success) {
				window.location.href = j.redirect || '<?= site_url('administrator/guru_kb'); ?>';
			} else {
				var pesan = (j && j.message) ? j.message : 'Gagal menyimpan.';
				if (j && j.errors) {
					var first = Object.values(j.errors)[0];
					if (first) pesan += ' — ' + first;
				}
				window.alert(pesan);
			}
		}).catch(function () {
			window.alert('Gagal — koneksi.');
		}).finally(function () {
			btn.disabled = false;
			btn.innerHTML = '<i class="fa fa-save"></i> Simpan Guru KB';
		});
	});
});
</script>

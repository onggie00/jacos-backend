

<style>
.sertifikat-overlay { z-index: 10; user-select: none; }
.sertifikat-overlay.dragging { opacity: 0.8; z-index: 20; }
#sertifikat_preview_container { overflow: hidden; }
</style>

<!-- Fine Uploader Gallery CSS file
    ====================================================================== -->
<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<!-- Fine Uploader jQuery JS file
    ====================================================================== -->
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Acara        <small>Edit Acara</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/acara'); ?>">Acara</a></li>
        <li class="active">Edit</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row" >
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-body ">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header ">
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="User Avatar">
                            </div>
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username">Acara</h3>
                            <h5 class="widget-user-desc">Edit Acara</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/acara/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_acara', 
                            'class'   => 'form-horizontal form-step', 
                            'id'      => 'form_acara', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="peserta_acara" class="col-sm-2 control-label">Acara Untuk 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="peserta_acara[]" id="peserta_acara" data-placeholder="Select Acara Untuk" multiple >
                                    <option value=""></option>
                                    <option <?= in_array('guru_sd', explode(',', $acara->peserta_acara)) ? 'selected' : ''; ?>  value="guru_sd">Guru SD</option>
                                    <option <?= in_array('guru_smp', explode(',', $acara->peserta_acara)) ? 'selected' : ''; ?>  value="guru_smp">Guru SMP</option>
                                    <option <?= in_array('guru_sma', explode(',', $acara->peserta_acara)) ? 'selected' : ''; ?>  value="guru_sma">Guru SMA</option>
                                    <option <?= in_array('guru_ft', explode(',', $acara->peserta_acara)) ? 'selected' : ''; ?>  value="guru_ft">Guru FT</option>
                                    <option <?= in_array('pegawai', explode(',', $acara->peserta_acara)) ? 'selected' : ''; ?>  value="pegawai">Pegawai</option>
                                    <option <?= in_array('pimpinan', explode(',', $acara->peserta_acara)) ? 'selected' : ''; ?>  value="pimpinan">Pimpinan</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="nama_acara" class="col-sm-2 control-label">Nama 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_acara" id="nama_acara" placeholder="Nama" value="<?= set_value('nama_acara', $acara->nama_acara); ?>">
                                <small class="info help-block">
                                <b>Input Nama Acara</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="unique_code" class="col-sm-2 control-label">Code In
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="unique_code" id="unique_code" placeholder="Code" value="<?= set_value('unique_code', $acara->unique_code); ?>">
                                <small class="info help-block">
                                <b>Input Unique Code</b> Max Length : 50.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="unique_code_finish" class="col-sm-2 control-label">Code Out
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="unique_code_finish" id="unique_code_finish" placeholder="Code" value="<?= set_value('unique_code_finish', $acara->unique_code_finish); ?>">
                                <small class="info help-block">
                                <b>Input Unique Code Out</b> Max Length : 50.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="narasumber" class="col-sm-2 control-label">Narasumber 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="narasumber" id="narasumber" placeholder="Narasumber" value="<?= set_value('narasumber', $acara->narasumber); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="keterangan" class="col-sm-2 control-label">Keterangan 
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan" value="<?= set_value('keterangan', $acara->keterangan); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="waktu_mulai" class="col-sm-2 control-label">Waktu Mulai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="waktu_mulai"  placeholder="Waktu Mulai" id="waktu_mulai" value="<?= set_value('waktu_mulai', $acara->waktu_mulai); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="waktu_selesai" class="col-sm-2 control-label">Waktu Selesai 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="waktu_selesai"  placeholder="Waktu Selesai" id="waktu_selesai" value="<?= set_value('waktu_selesai', $acara->waktu_selesai); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="lokasi" class="col-sm-2 control-label">Lokasi 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="lokasi" id="lokasi" placeholder="Lokasi" value="<?= set_value('lokasi', $acara->lokasi); ?>">
                                <small class="info help-block">
                                <b>Input Lokasi</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="evaluation_url" class="col-sm-2 control-label">Evaluation URL 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="evaluation_url" id="evaluation_url" placeholder="https://google.com" value="<?= set_value('evaluation_url', $acara->evaluation_url); ?>">
                                <small class="info help-block">
                                <b>Input Evaluation URL</b></small>
                            </div>
                        </div>
                                                <div class="form-group ">
                            <label for="is_certificated" class="col-sm-2 control-label">Certificated? 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select" name="is_certificated" id="is_certificated" data-placeholder="Select Certificated?" >
                                    <option value=""></option>
                                    <option <?= $acara->is_certificated == "0" ? 'selected' :''; ?> value="0">Tidak</option>
                                    <option <?= $acara->is_certificated == "1" ? 'selected' :''; ?> value="1">Ya</option>
                                    </select>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="no_certificate" class="col-sm-2 control-label">Nomor Sertifikat
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="no_certificate" id="no_certificate" placeholder="Nomor Sertifikat" value="<?= set_value('no_certificate', $acara->no_certificate); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="start_number_certificate" class="col-sm-2 control-label">Mulai Nomor Dari
                            </label>
                            <div class="col-sm-4">
                                <input type="number" min="1" class="form-control" name="start_number_certificate" id="start_number_certificate" placeholder="Contoh: 20" value="<?= set_value('start_number_certificate', isset($acara->start_number_certificate) ? $acara->start_number_certificate : ''); ?>">
                                <small class="info help-block">
                                    Kosongkan jika nomor urut mulai dari 1.
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="file_certificate" class="col-sm-2 control-label">Certificate Template 
                            </label>
                            <div class="col-sm-8">
                                <div id="acara_file_certificate_galery"></div>
                                <input class="data_file data_file_uuid" name="acara_file_certificate_uuid" id="acara_file_certificate_uuid" type="hidden" value="<?= set_value('acara_file_certificate_uuid'); ?>">
                                <input class="data_file" name="acara_file_certificate_name" id="acara_file_certificate_name" type="hidden" value="<?= set_value('acara_file_certificate_name', $acara->file_certificate); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="file_certificate_back" class="col-sm-2 control-label">Certificate Template Back 
                            </label>
                            <div class="col-sm-8">
                                <div id="acara_file_certificate_back_galery"></div>
                                <input class="data_file data_file_uuid" name="acara_file_certificate_back_uuid" id="acara_file_certificate_back_uuid" type="hidden" value="<?= set_value('acara_file_certificate_back_uuid'); ?>">
                                <input class="data_file" name="acara_file_certificate_back_name" id="acara_file_certificate_back_name" type="hidden" value="<?= set_value('acara_file_certificate_back_name', $acara->file_certificate_back); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>

                        <!-- Live Preview Sertifikat -->
                        <div class="form-group" id="sertifikat_preview_section" <?= (!empty($acara->file_certificate) || !empty($acara->file_certificate_back)) ? '' : 'style="display:none;"'; ?>">
                            <label class="col-sm-2 control-label">Preview Sertifikat</label>
                            <div class="col-sm-10">
                                <div style="overflow-x:auto;overflow-y:hidden;">
                                <div class="btn-group" style="margin-bottom:10px;">
                                    <button type="button" class="btn btn-sm btn-primary btn-preview-side" data-side="depan"><i class="fa fa-image"></i> Depan</button>
                                    <button type="button" class="btn btn-sm btn-default btn-preview-side" data-side="belakang"><i class="fa fa-image"></i> Belakang</button>
                                </div>
                                <div id="sertifikat_preview_container" style="position:relative;display:inline-block;width:1080px;height:764px;border:1px solid #ddd;background:#f5f5f5;overflow:hidden;">
                                    <?php if (!empty($acara->file_certificate)): ?>
                                        <img id="sertifikat_preview_img_depan" src="<?= base_url('uploads/acara/' . $acara->file_certificate); ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:contain;">
                                    <?php else: ?>
                                        <img id="sertifikat_preview_img_depan" src="" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:contain;display:none;">
                                    <?php endif; ?>
                                    <?php if (!empty($acara->file_certificate_back)): ?>
                                        <img id="sertifikat_preview_img_belakang" src="<?= base_url('uploads/acara/' . $acara->file_certificate_back); ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:contain;display:none;">
                                    <?php else: ?>
                                        <img id="sertifikat_preview_img_belakang" src="" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:contain;display:none;">
                                    <?php endif; ?>
                                    <div id="sertifikat_preview_placeholder" style="width:100%;height:100%;display:none;align-items:center;justify-content:center;color:#999;font-size:14px;">Upload template sertifikat untuk melihat preview</div>
                                    <div id="overlay_nomor" class="sertifikat-overlay" style="position:absolute;top:19%;left:23.1%;cursor:move;<?= empty($acara->file_certificate) ? 'display:none;' : '' ?>">
                                        <span style="color:#000;font-size:11px;white-space:nowrap;">164.001</span>
                                    </div>
                                    <div id="overlay_nama" class="sertifikat-overlay" style="position:absolute;top:13.1%;left:13%;cursor:move;<?= empty($acara->file_certificate) ? 'display:none;' : '' ?>">
                                        <span style="color:#000;font-size:11px;white-space:nowrap;">DUMMY PESERTA</span>
                                    </div>
                                    <div id="overlay_instansi" class="sertifikat-overlay" style="position:absolute;top:18%;left:13%;cursor:move;">
                                        <span style="color:#000;font-size:10px;white-space:nowrap;">SD LABSCHOOL CIBUBUR</span>
                                    </div>
                                    <div id="overlay_npp" class="sertifikat-overlay" style="position:absolute;top:20.9%;left:13%;cursor:move;display:none;">
                                        <span style="color:#000;font-size:11px;white-space:nowrap;">00.00.000</span>
                                    </div>
                                </div>
                                <div style="margin-top:8px;font-size:12px;color:#666;">
                                    <i class="fa fa-info-circle"></i> Drag teks untuk mengatur posisi (hanya sisi Depan). Nilai otomatis terisi di input di bawah.<br>
                                    <span style="color:#0078d7;">■</span> Nomor &nbsp; <span style="color:#dc3545;">■</span> Nama &nbsp; <span style="color:#6f42c1;">■</span> Instansi &nbsp; <span style="color:#28a745;">■</span> NPP (jika aktif)
                                </div>
                                </div>
                            </div>
                        </div>

                        <div id="sertifikat_posisi_fields" style="display:none;">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Posisi Atas Nomor Sertifikat (px)</label>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control sertifikat-pos-input" step="0.01" name="sertifikat_no_pos_top" id="sertifikat_no_pos_top" placeholder="Default: 19.0" value="<?= set_value('sertifikat_no_pos_top', $acara->sertifikat_no_pos_top); ?>">
                                </div>
                                <label class="col-sm-2 control-label">Posisi Kiri Nomor Sertifikat (px)</label>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control sertifikat-pos-input" step="0.01" name="sertifikat_no_pos_left" id="sertifikat_no_pos_left" placeholder="Default: 23.1" value="<?= set_value('sertifikat_no_pos_left', $acara->sertifikat_no_pos_left); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Ukuran Font Nomor Sertifikat (pt)</label>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control sertifikat-font-input" name="sertifikat_no_font_size" id="sertifikat_no_font_size" placeholder="Default: 16" value="<?= set_value('sertifikat_no_font_size', $acara->sertifikat_no_font_size); ?>">
                                </div>
                                <label class="col-sm-2 control-label">Gaya Font Nomor</label>
                                <div class="col-sm-2">
                                    <?php $cur = set_value('sertifikat_no_font_style', $acara->sertifikat_no_font_style); ?>
                                                                        <select class="form-control sertifikat-fontstyle-input" name="sertifikat_no_font_style" id="sertifikat_no_font_style">
                                        <option value="normal" <?= $cur == "normal" ? "selected" : ""; ?>>Normal</option><option value="bold" <?= $cur == "bold" ? "selected" : ""; ?>>Tebal</option><option value="italic" <?= $cur == "italic" ? "selected" : ""; ?>>Miring</option><option value="bold_italic" <?= $cur == "bold_italic" ? "selected" : ""; ?>>Tebal + Miring</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Posisi Atas Nama Peserta (px)</label>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control sertifikat-pos-input" step="0.01" name="sertifikat_nama_pos_top" id="sertifikat_nama_pos_top" placeholder="Default: 13.1" value="<?= set_value('sertifikat_nama_pos_top', $acara->sertifikat_nama_pos_top); ?>">
                                </div>
                                <label class="col-sm-2 control-label">Posisi Kiri Nama Peserta (px)</label>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control sertifikat-pos-input" step="0.01" name="sertifikat_nama_pos_left" id="sertifikat_nama_pos_left" placeholder="Default: 13.0" value="<?= set_value('sertifikat_nama_pos_left', $acara->sertifikat_nama_pos_left); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Ukuran Font Nama Peserta (pt)</label>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control sertifikat-font-input" name="sertifikat_nama_font_size" id="sertifikat_nama_font_size" placeholder="Default: 30" value="<?= set_value('sertifikat_nama_font_size', $acara->sertifikat_nama_font_size); ?>">
                                </div>
                                <label class="col-sm-2 control-label">Gaya Font Nama</label>
                                <div class="col-sm-2">
                                    <?php $cur = set_value('sertifikat_nama_font_style', $acara->sertifikat_nama_font_style); ?>
                                                                        <select class="form-control sertifikat-fontstyle-input" name="sertifikat_nama_font_style" id="sertifikat_nama_font_style">
                                        <option value="normal" <?= $cur == "normal" ? "selected" : ""; ?>>Normal</option><option value="bold" <?= $cur == "bold" ? "selected" : ""; ?>>Tebal</option><option value="italic" <?= $cur == "italic" ? "selected" : ""; ?>>Miring</option><option value="bold_italic" <?= $cur == "bold_italic" ? "selected" : ""; ?>>Tebal + Miring</option>
                                    </select>
                                </div>
                            </div>
                            <hr style="border-color:#ddd;">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Tampilkan NPP?</label>
                                <div class="col-sm-3">
                                    <label style="font-weight:normal;cursor:pointer;">
                                        <input type="checkbox" name="sertifikat_show_npp" id="sertifikat_show_npp" value="1" <?= !empty($acara->sertifikat_show_npp) ? 'checked' : ''; ?>> Ya, tampilkan NPP di sertifikat
                                    </label>
                                </div>
                            </div>
                            <div id="sertifikat_npp_fields" style="display:none;">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Posisi Atas NPP (px)</label>
                                    <div class="col-sm-3">
                                        <input type="number" class="form-control sertifikat-pos-input" step="0.01" name="sertifikat_npp_pos_top" id="sertifikat_npp_pos_top" placeholder="Default: 20.9" value="<?= set_value('sertifikat_npp_pos_top', $acara->sertifikat_npp_pos_top); ?>">
                                    </div>
                                    <label class="col-sm-2 control-label">Posisi Kiri NPP (px)</label>
                                    <div class="col-sm-3">
                                        <input type="number" class="form-control sertifikat-pos-input" step="0.01" name="sertifikat_npp_pos_left" id="sertifikat_npp_pos_left" placeholder="Default: 13.0" value="<?= set_value('sertifikat_npp_pos_left', $acara->sertifikat_npp_pos_left); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Ukuran Font NPP (pt)</label>
                                    <div class="col-sm-3">
                                        <input type="number" class="form-control sertifikat-font-input" name="sertifikat_npp_font_size" id="sertifikat_npp_font_size" placeholder="Default: 12" value="<?= set_value('sertifikat_npp_font_size', $acara->sertifikat_npp_font_size); ?>">
                                    </div>
                                    <label class="col-sm-2 control-label">Gaya Font NPP</label>
                                    <div class="col-sm-2">
                                        <?php $cur = set_value('sertifikat_npp_font_style', $acara->sertifikat_npp_font_style); ?>
                                                                            <select class="form-control sertifikat-fontstyle-input" name="sertifikat_npp_font_style" id="sertifikat_npp_font_style">
                                        <option value="normal" <?= $cur == "normal" ? "selected" : ""; ?>>Normal</option><option value="bold" <?= $cur == "bold" ? "selected" : ""; ?>>Tebal</option><option value="italic" <?= $cur == "italic" ? "selected" : ""; ?>>Miring</option><option value="bold_italic" <?= $cur == "bold_italic" ? "selected" : ""; ?>>Tebal + Miring</option>
                                    </select>
                                    </div>
                                </div>
                            </div>
                            <hr style="border-color:#ddd;">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Tampilkan Instansi?</label>
                                <div class="col-sm-3">
                                    <label style="font-weight:normal;cursor:pointer;">
                                        <input type="checkbox" name="sertifikat_show_instansi" id="sertifikat_show_instansi" value="1" <?= !empty($acara->sertifikat_show_instansi) ? 'checked' : ''; ?>> Ya, tampilkan Instansi di sertifikat
                                    </label>
                                </div>
                            </div>
                            <div id="sertifikat_instansi_fields">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Posisi Atas Instansi (%)</label>
                                    <div class="col-sm-3">
                                        <input type="number" step="0.01" class="form-control sertifikat-pos-input" step="0.01" name="sertifikat_instansi_pos_top" id="sertifikat_instansi_pos_top" placeholder="Default: 18.0" value="<?= set_value('sertifikat_instansi_pos_top', $acara->sertifikat_instansi_pos_top); ?>">
                                    </div>
                                    <label class="col-sm-2 control-label">Posisi Kiri Instansi (%)</label>
                                    <div class="col-sm-3">
                                        <input type="number" step="0.01" class="form-control sertifikat-pos-input" step="0.01" name="sertifikat_instansi_pos_left" id="sertifikat_instansi_pos_left" placeholder="Default: 13.0" value="<?= set_value('sertifikat_instansi_pos_left', $acara->sertifikat_instansi_pos_left); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Ukuran Font Instansi (pt)</label>
                                    <div class="col-sm-3">
                                        <input type="number" class="form-control sertifikat-font-input" name="sertifikat_instansi_font_size" id="sertifikat_instansi_font_size" placeholder="Default: 18" value="<?= set_value('sertifikat_instansi_font_size', $acara->sertifikat_instansi_font_size); ?>">
                                    </div>
                                    <label class="col-sm-2 control-label">Gaya Font Instansi</label>
                                    <div class="col-sm-2">
                                        <?php $cur = set_value('sertifikat_instansi_font_style', $acara->sertifikat_instansi_font_style); ?>
                                        <select class="form-control sertifikat-fontstyle-input" name="sertifikat_instansi_font_style" id="sertifikat_instansi_font_style">
                                            <option value="normal" <?= $cur == "normal" ? "selected" : ""; ?>>Normal</option><option value="bold" <?= $cur == "bold" ? "selected" : ""; ?>>Tebal</option><option value="italic" <?= $cur == "italic" ? "selected" : ""; ?>>Miring</option><option value="bold_italic" <?= $cur == "bold_italic" ? "selected" : ""; ?>>Tebal + Miring</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                                                 
                                                 <div class="message"></div>
                                                <div class="row-fluid col-md-7 container-button-bottom">
                            <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="<?= cclang('save_button'); ?> (Ctrl+s)">
                            <i class="fa fa-save"></i> <?= cclang('save_button'); ?>
                            </button>
                            <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" data-stype='back' title="<?= cclang('save_and_go_the_list_button'); ?> (Ctrl+d)">
                            <i class="ion ion-ios-list-outline"></i> <?= cclang('save_and_go_the_list_button'); ?>
                            </a>
                            <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="<?= cclang('cancel_button'); ?> (Ctrl+x)">
                            <i class="fa fa-undo"></i> <?= cclang('cancel_button'); ?>
                            </a>
                            <span class="loading loading-hide">
                            <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg"> 
                            <i><?= cclang('loading_saving_data'); ?></i>
                            </span>
                        </div>
                                                 <?= form_close(); ?>
                    </div>
                </div>
                <!--/box body -->
            </div>
            <!--/box -->
        </div>
    </div>
</section>
<!-- /.content -->
<!-- Page script -->
<script>
    $(document).ready(function(){

      // Toggle sertifikat posisi fields
      function toggleSertifikatPosisi() {
        if ($('#is_certificated').val() == '1') {
          $('#sertifikat_posisi_fields').slideDown();
        } else {
          $('#sertifikat_posisi_fields').slideUp();
        }
      }
      $('#is_certificated').change(toggleSertifikatPosisi);
      toggleSertifikatPosisi();

      // === LIVE PREVIEW SERTIFIKAT ===
      var previewImgDepan = document.getElementById('sertifikat_preview_img_depan');
      var previewImgBelakang = document.getElementById('sertifikat_preview_img_belakang');
      var placeholder = document.getElementById('sertifikat_preview_placeholder');
      var overlayNomor = document.getElementById('overlay_nomor');
      var overlayNama = document.getElementById('overlay_nama');
      var overlayNpp = document.getElementById('overlay_npp');
      var overlayInstansi = document.getElementById('overlay_instansi');
      var container = document.getElementById('sertifikat_preview_container');
      var previewSection = document.getElementById('sertifikat_preview_section');
      var currentSide = 'depan';

      // Toggle NPP fields
      function toggleNppFields() {
        if ($('#sertifikat_show_npp').is(':checked')) {
          $('#sertifikat_npp_fields').slideDown();
          if (overlayNpp) overlayNpp.style.display = 'block';
        } else {
          $('#sertifikat_npp_fields').slideUp();
          if (overlayNpp) overlayNpp.style.display = 'none';
        }
      }
      $('#sertifikat_show_npp').change(toggleNppFields);
      toggleNppFields();

      // Toggle Instansi fields
      function toggleInstansiFields() {
        if ($('#sertifikat_show_instansi').is(':checked')) {
          $('#sertifikat_instansi_fields').slideDown();
          if (typeof overlayInstansi !== 'undefined' && overlayInstansi) overlayInstansi.style.display = 'block';
        } else {
          $('#sertifikat_instansi_fields').slideUp();
          if (typeof overlayInstansi !== 'undefined' && overlayInstansi) overlayInstansi.style.display = 'none';
        }
      }
      $('#sertifikat_show_instansi').change(toggleInstansiFields);
      toggleInstansiFields();

      function getDefaults() {
        var hasNomor = ($('#no_certificate').val() || '').trim() !== '';
        return {
          no_top: 19.0, no_left: 23.1, no_font: 16,
          nama_top: hasNomor ? 6.5 : 13.1, nama_left: 13.0, nama_font: 30,
          npp_top: 20.9, npp_left: 13.0, npp_font: 12,
          instansi_top: 18.0, instansi_left: 13.0, instansi_font: 18
        };
      }

      function valOrDef(id, defVal) {
        var v = $('#' + id).val();
        return (v !== '' && v !== null && v !== undefined) ? parseFloat(v) : defVal;
      }

      // Ambil area gambar yang ter-render (exclude letterbox object-fit:contain)
      function getImageRenderedRect() {
        var rect = { left: 0, top: 0, width: container.offsetWidth, height: container.offsetHeight };
        var img = currentSide === 'depan' ? previewImgDepan : previewImgBelakang;
        if (img && img.style.display !== 'none' && img.complete && img.naturalWidth > 0) {
          var cl = img.getBoundingClientRect();
          var parentRect = container.getBoundingClientRect();
          rect.left = cl.left - parentRect.left;
          rect.top = cl.top - parentRect.top;
          rect.width = cl.width;
          rect.height = cl.height;
        }
        return rect;
      }

      function syncOverlayFromInputs() {
        var def = getDefaults();
        var noTop = valOrDef('sertifikat_no_pos_top', def.no_top);
        var noLeft = valOrDef('sertifikat_no_pos_left', def.no_left);
        var noFont = valOrDef('sertifikat_no_font_size', def.no_font);
        var namaTop = valOrDef('sertifikat_nama_pos_top', def.nama_top);
        var namaLeft = valOrDef('sertifikat_nama_pos_left', def.nama_left);
        var namaFont = valOrDef('sertifikat_nama_font_size', def.nama_font);

        var ir = getImageRenderedRect();
        var cw = ir.width || 1080;
        var ch = ir.height || 400;
        var imgOffsetLeft = ir.left;
        var imgOffsetTop = ir.top;

        function applyPos(overlay, topPct, leftPct, fontSize, fontStyle) {
          overlay.style.top = (imgOffsetTop + (topPct / 100) * ch) + 'px';
          overlay.style.left = (imgOffsetLeft + (leftPct / 100) * cw) + 'px';
          var span = overlay.querySelector('span');
          span.style.fontSize = Math.max(8, Math.round(fontSize * cw / 1080)) + 'pt';
          span.style.fontWeight = (fontStyle === 'bold' || fontStyle === 'bold_italic') ? 'bold' : 'normal';
          span.style.fontStyle = (fontStyle === 'italic' || fontStyle === 'bold_italic') ? 'italic' : 'normal';
        }

        applyPos(overlayNomor, noTop, noLeft, noFont, $('#sertifikat_no_font_style').val() || 'normal');
        applyPos(overlayNama, namaTop, namaLeft, namaFont, $('#sertifikat_nama_font_style').val() || 'bold_italic');

        var nppTop = valOrDef('sertifikat_npp_pos_top', def.npp_top);
        var nppLeft = valOrDef('sertifikat_npp_pos_left', def.npp_left);
        var nppFont = valOrDef('sertifikat_npp_font_size', def.npp_font);
        applyPos(overlayNpp, nppTop, nppLeft, nppFont, $('#sertifikat_npp_font_style').val() || 'normal');

        var instansiTop = valOrDef('sertifikat_instansi_pos_top', def.instansi_top);
        var instansiLeft = valOrDef('sertifikat_instansi_pos_left', def.instansi_left);
        var instansiFont = valOrDef('sertifikat_instansi_font_size', def.instansi_font);
        applyPos(overlayInstansi, instansiTop, instansiLeft, instansiFont, $('#sertifikat_instansi_font_style').val() || 'normal');
      }

      function syncInputsFromOverlay(overlay, topId, leftId) {
        var ir = getImageRenderedRect();
        var cw = ir.width || 1080;
        var ch = ir.height || 400;
        var topPercent = ((parseFloat(overlay.style.top) - ir.top) / ch) * 100;
        var leftPercent = ((parseFloat(overlay.style.left) - ir.left) / cw) * 100;
        $('#' + topId).val(topPercent.toFixed(2));
        $('#' + leftId).val(leftPercent.toFixed(2));
      }

      function makeDraggable(el, topId, leftId) {
        var isDragging = false, startX, startY, origTop, origLeft;

        el.addEventListener('mousedown', function(e) {
          isDragging = true;
          el.classList.add('dragging');
          startX = e.clientX;
          startY = e.clientY;
          origTop = parseInt(el.style.top) || 0;
          origLeft = parseInt(el.style.left) || 0;
          e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
          if (!isDragging) return;
          var dx = e.clientX - startX;
          var dy = e.clientY - startY;
          var newTop = Math.max(0, origTop + dy);
          var newLeft = Math.max(0, origLeft + dx);
          var maxTop = container.offsetHeight - 30;
          var maxLeft = container.offsetWidth - 50;
          var ir = getImageRenderedRect();
          var minTop = ir.top;
          var minLeft = ir.left;
          maxTop = ir.top + ir.height - 20;
          maxLeft = ir.left + ir.width - 50;
          var newTop = Math.max(minTop, origTop + dy);
          var newLeft = Math.max(minLeft, origLeft + dx);
          if (newTop > maxTop) newTop = maxTop;
          if (newLeft > maxLeft) newLeft = maxLeft;
          el.style.top = newTop + 'px';
          el.style.left = newLeft + 'px';
          syncInputsFromOverlay(el, topId, leftId);
        });

        document.addEventListener('mouseup', function() {
          if (isDragging) {
            isDragging = false;
            el.classList.remove('dragging');
          }
        });
      }

      makeDraggable(overlayNomor, 'sertifikat_no_pos_top', 'sertifikat_no_pos_left');
      makeDraggable(overlayNama, 'sertifikat_nama_pos_top', 'sertifikat_nama_pos_left');
      makeDraggable(overlayNpp, 'sertifikat_npp_pos_top', 'sertifikat_npp_pos_left');
      makeDraggable(overlayInstansi, 'sertifikat_instansi_pos_top', 'sertifikat_instansi_pos_left');

      $('.sertifikat-pos-input, .sertifikat-font-input').on('input', function() {
        syncOverlayFromInputs();
      });
      $('.sertifikat-fontstyle-input').on('change', function() {
        syncOverlayFromInputs();
      });
      $('#no_certificate').on('input', function() {
        syncOverlayFromInputs();
      });

      // Toggle preview depan/belakang
      $('.btn-preview-side').click(function() {
        var side = $(this).data('side');
        currentSide = side;
        $('.btn-preview-side').removeClass('btn-primary').addClass('btn-default');
        $(this).removeClass('btn-default').addClass('btn-primary');
        if (side === 'depan') {
          previewImgDepan.style.display = 'block';
          previewImgBelakang.style.display = 'none';
          overlayNomor.style.display = previewImgDepan.src ? 'block' : 'none';
          overlayNama.style.display = previewImgDepan.src ? 'block' : 'none';
        } else {
          previewImgDepan.style.display = 'none';
          previewImgBelakang.style.display = 'block';
          overlayNomor.style.display = 'none';
          overlayNama.style.display = 'none';
        }
      });

      function showPreviewSection() {
        if (previewSection) previewSection.style.display = '';
      }

      // Helper: dapatkan URL asli file dari fineuploader
      function getUploadedFileUrl(uuidInputId, nameInputId) {
        var uuid = $('#' + uuidInputId).val();
        var name = $('#' + nameInputId).val();
        if (uuid && name) {
          return BASE_URL + 'uploads/tmp/' + uuid + '/' + name;
        }
        return null;
      }

      // Observer untuk gambar depan
      var galleryDepan = new MutationObserver(function() {
        var fileUrl = getUploadedFileUrl('acara_file_certificate_uuid', 'acara_file_certificate_name');
        if (fileUrl) {
          previewImgDepan.src = fileUrl;
          previewImgDepan.style.display = 'block';
          if (placeholder) placeholder.style.display = 'none';
          if (currentSide === 'depan') {
            overlayNomor.style.display = 'block';
            overlayNama.style.display = 'block';
            overlayInstansi.style.display = 'block';
          }
          showPreviewSection();
          previewImgDepan.onload = function() { syncOverlayFromInputs(); };
          setTimeout(syncOverlayFromInputs, 300);
        }
      });
      var galDepan = document.getElementById('acara_file_certificate_galery');
      if (galDepan) galleryDepan.observe(galDepan, { childList: true, subtree: true });

      // Observer untuk gambar belakang
      var galleryBelakang = new MutationObserver(function() {
        var fileUrl = getUploadedFileUrl('acara_file_certificate_back_uuid', 'acara_file_certificate_back_name');
        if (fileUrl) {
          previewImgBelakang.src = fileUrl;
          showPreviewSection();
        }
      });
      var galBelakang = document.getElementById('acara_file_certificate_back_galery');
      if (galBelakang) galleryBelakang.observe(galBelakang, { childList: true, subtree: true });

      // Init awal
      if (previewImgDepan.src && previewImgDepan.src !== window.location.href && previewImgDepan.src !== '') {
        overlayNomor.style.display = 'block';
        overlayNama.style.display = 'block';
        overlayInstansi.style.display = 'block';
        previewImgDepan.onload = function() { syncOverlayFromInputs(); };
        if (previewImgDepan.complete) setTimeout(syncOverlayFromInputs, 100);
      }
       
      
             
      $('#btn_cancel').click(function(){
        swal({
            title: "Are you sure?",
            text: "the data that you have created will be in the exhaust!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes!",
            cancelButtonText: "No!",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm){
            if (isConfirm) {
              window.location.href = BASE_URL + 'administrator/acara';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_acara = $('#form_acara');
        var data_post = form_acara.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_acara.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          $('form').find('.form-group').removeClass('has-error');
          $('form').find('.error-input').remove();
          $('.steps li').removeClass('error');
          if(res.success) {
            var id = $('#acara_image_galery').find('li').attr('qq-file-id');
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            $('.data_file_uuid').val('');
    
          } else {
            if (res.errors) {
               parseErrorField(res.errors);
            }
            $('.message').printMessage({message : res.message, type : 'warning'});
          }
    
        })
        .fail(function() {
          $('.message').printMessage({message : 'Error save data', type : 'warning'});
        })
        .always(function() {
          $('.loading').hide();
          $('html, body').animate({ scrollTop: $(document).height() }, 2000);
        });
    
        return false;
      }); /*end btn save*/
      
                                   var params = {};
       params[csrf] = token;

       $('#acara_file_certificate_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/acara/upload_file_certificate_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/acara/delete_file_certificate_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/acara/get_file_certificate_file/<?= $acara->id_acara; ?>',
             refreshOnRequest:true
           },
          multiple : false,
          validation: {
              allowedExtensions: ["*"],
              sizeLimit : 0,
                        },
          showMessage: function(msg) {
              toastr['error'](msg);
          },
          callbacks: {
              onComplete : function(id, name, xhr) {
                if (xhr.success) {
                   var uuid = $('#acara_file_certificate_galery').fineUploader('getUuid', id);
                   $('#acara_file_certificate_uuid').val(uuid);
                   $('#acara_file_certificate_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#acara_file_certificate_uuid').val();
                  $.get(BASE_URL + '/administrator/acara/delete_file_certificate_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#acara_file_certificate_uuid').val('');
                  $('#acara_file_certificate_name').val('');
                }
              }
          }
      }); /*end file_certificate galey*/
                            var params = {};
       params[csrf] = token;

       $('#acara_file_certificate_back_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/acara/upload_file_certificate_back_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/acara/delete_file_certificate_back_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/acara/get_file_certificate_back_file/<?= $acara->id_acara; ?>',
             refreshOnRequest:true
           },
          multiple : false,
          validation: {
              allowedExtensions: ["*"],
              sizeLimit : 0,
                        },
          showMessage: function(msg) {
              toastr['error'](msg);
          },
          callbacks: {
              onComplete : function(id, name, xhr) {
                if (xhr.success) {
                   var uuid = $('#acara_file_certificate_back_galery').fineUploader('getUuid', id);
                   $('#acara_file_certificate_back_uuid').val(uuid);
                   $('#acara_file_certificate_back_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#acara_file_certificate_back_uuid').val();
                  $.get(BASE_URL + '/administrator/acara/delete_file_certificate_back_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#acara_file_certificate_back_uuid').val('');
                  $('#acara_file_certificate_back_name').val('');
                }
              }
          }
      }); /*end file_certificate_back galey*/
              
       
       

      async function chain(){
      }
       
      chain();


    
    
    }); /*end doc ready*/
</script>
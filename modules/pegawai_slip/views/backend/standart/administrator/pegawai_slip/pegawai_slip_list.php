
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('pegawai_slip') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('pegawai_slip') ?></li>
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
                     <div class="row pull-right">
                        <a class="btn btn-flat btn-success" title="Import Data Gaji" data-toggle="modal" data-target="#modal_import_gaji"> Import Gaji Pegawai</a>
                        <a class="btn btn-flat btn-success" title="Import Data Tunjangan" data-toggle="modal" data-target="#modal_import_tunjangan"> Import Tunjangan Pegawai</a>
                        <?php is_allowed('pegawai_slip_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new hidden" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('pegawai_slip')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/pegawai_slip/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('pegawai_slip')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('pegawai_slip_export', function(){?>
                        <a class="btn btn-flat btn-success" title="Export Data Gaji" data-toggle="modal" data-target="#modal_export_gaji"><i class="fa fa-file-excel-o" ></i> Export Gaji Pegawai</a>
                        <a class="btn btn-flat btn-success" title="Export Data Tunjangan" data-toggle="modal" data-target="#modal_export_tunjangan"><i class="fa fa-file-excel-o" ></i> Export Tunjangan Pegawai</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('pegawai_slip') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('pegawai_slip')]); ?>  <i class="label bg-yellow"><?= $pegawai_slip_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_pegawai_slip" id="form_pegawai_slip" action="<?= base_url('administrator/pegawai_slip/index'); ?>">
                  
                  <?php
                     if (!empty($this->session->flashdata('success'))) {
                  ?>
                     <div class="alert alert-success alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Success!</strong> <?= $this->session->flashdata('success'); ?>
                     </div>
                  <?php
                     }
                  ?>
                  <?php
                     if (!empty($this->session->flashdata('failed'))) {
                  ?>
                     <div class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Gagal!</strong> <?= $this->session->flashdata('failed'); ?>
                     </div>
                  <?php
                     }
                  ?>

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                           <th> <?= cclang('id_slip') ?></th>
                           <th> <?= cclang('pegawai') ?></th>
                           <th> <?= cclang('npp') ?></th>
                           <th> <?= cclang('golongan') ?></th>
                           <th> <?= cclang('jabatan') ?></th>
                           <th> Periode Mulai</th>
                           <th> Periode Selesai</th>
                           <th>Action</th>
                                                   </tr>
                     </thead>
                     <tbody id="tbody_pegawai_slip">
                     <?php $no = 1; foreach($pegawai_slips as $pegawai_slip): ?>
                        <tr>
                           
                           <td><?=  $no; ?></td> 
                           <td><?= $pegawai_slip->nama_lengkap; ?> </td>
                           
                           <td><?= _ent($pegawai_slip->npp); ?></td> 
                           <td><?= _ent($pegawai_slip->golongan); ?></td> 
                           <td><?= _ent($pegawai_slip->jabatan); ?></td> 
                           <td><?= formatTanggal($pegawai_slip->periode_mulai); ?></td> 
                           <td><?= formatTanggal($pegawai_slip->periode_selesai); ?></td>
                           
                           <td width="250">
                              <?php is_allowed('pegawai_view', function() use ($pegawai_slip){?>
                              <a class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal_export_gaji<?= $pegawai_slip->id_slip; ?>"><i class="fa fa-eye"></i> </a>
                              <?php }) ?>
                              <?php is_allowed('pegawai_update', function() use ($pegawai_slip){?>
                              <a href="<?= site_url('administrator/pegawai_slip/edit/' . $pegawai_slip->id_slip); ?>" class="btn btn-sm btn-warning"><i class="fa fa-pencil-square-o "></i> </a>
                              <?php }) ?>
                                 <?php 
                                       is_allowed('pegawai_slip_delete', function() use ($pegawai_slip){
                                 ?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/pegawai_slip/delete/' . $pegawai_slip->id_slip); ?>" class="btn btn-sm btn-danger remove-data" title="delete pegawai_slip"><i class="fa fa-trash-o"></i> </a>
                                 <?php
                                    }) 
                                 ?>
                           </td>
                        </tr>
                     <?php $no++; endforeach; ?>
                     <?php if ($pegawai_slip_counts == 0) :?>
                        <tr>
                           <td colspan="100">
                           Slip Gaji & Tunjangan Pegawai data is not available
                           </td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>
               <hr>
               <!-- /.widget-user -->
               <div class="row">
                  <div class="col-md-8">
                     <div class="col-sm-2 padd-left-0 " >
                        <select type="text" class="form-control chosen chosen-select" name="bulk" id="bulk" placeholder="Site Email" >
                           <option value="">Bulk</option>
                                                  </select>
                     </div>
                     <div class="col-sm-2 padd-left-0 ">
                        <button type="button" class="btn btn-flat" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>"><?= cclang('apply_button'); ?></button>
                     </div>
                     <div class="col-sm-3 padd-left-0  " >
                        <input type="text" class="form-control" name="q" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('q'); ?>">
                     </div>
                     <div class="col-sm-3 padd-left-0 " >
                        <select type="text" class="form-control chosen chosen-select" name="f" id="field" >
                           <option value=""><?= cclang('all'); ?></option>
                        </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/pegawai_slip');?>" title="<?= cclang('reset_filter'); ?>">
                        <i class="fa fa-undo"></i>
                        </a>
                     </div>
                  </div>
                  </form>                  <div class="col-md-4">
                     <div class="dataTables_paginate paging_simple_numbers pull-right" id="example2_paginate" >
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
            </div>
            <!--/box body -->
         </div>
         <!--/box -->
      </div>
   </div>
</section>
<!-- /.content -->

<?php
foreach ($pegawai_slips as $pegawai_slip) {
?>
<!-- ============ MODAL Detail  =============== -->
<div class="modal fade" id="modal_export_gaji<?= $pegawai_slip->id_slip; ?>" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Detail Gaji Pegawai</h3>
         </div>
            <div class="modal-body" >               
                  <div class="form-group">
                     <label class="control-label col-md-3" style="margin-top:10px;">Nama Lengkap</label>
                     <div class="col-md-9" style="margin-top:10px;">
                        <input type="text" name="nama_lengkap_modal" class="form-control" value="<?= $pegawai_slip->nama_lengkap; ?>" readonly>
                     </div>
                  </div>
                  <div class="form-group" >
                     <label class="control-label col-md-3" style="margin-top:10px;">NPP</label>
                     <div class="col-md-9" style="margin-top:10px;">
                        <input type="text" name="npp_modal" class="form-control" value="<?= $pegawai_slip->npp; ?>" readonly>
                     </div>
                  </div>
                  <div class="form-group" >
                     <label class="control-label col-md-3" style="margin-top:10px;">Periode</label>
                     <div class="col-md-4" style="margin-top:10px;">
                        <input class="form-control" type="text" value="<?php echo formatTanggal($pegawai_slip->periode_mulai); ?> " readonly />
                     </div>
                     <div class="col-md-1 text-center" style="font-size: 24pt;"> - </div>
                     <div class="col-md-4" style="margin-top:10px;">
                        <input class="form-control" type="text" value="<?php echo formatTanggal($pegawai_slip->periode_selesai); ?> " readonly />
                     </div>
                  </div>
                  <div class="form-group col-xs-12" >
                     <label class="control-label col-md-3" style="margin-top:10px;">Slip</label>
                     <div class="col-md-1">&nbsp;</div>
                     <div class="col-md-2" style="margin-top:10px;">
                        <a href="<?= base_url("apiapp/export_slip_gaji_tunjangan?tipe=gaji&id=").$pegawai_slip->id_slip; ?>" target="_blank" class="btn btn-success" style="width:100%;" >Gaji</a>
                     </div>
                     <div class="col-md-2" style="margin-top:10px;">
                        <a href="<?= base_url("apiapp/export_slip_gaji_tunjangan?tipe=tunjangan&id=").$pegawai_slip->id_slip; ?>" target="_blank" class="btn btn-info" style="width:100%;" >Tunjangan</a>
                     </div>
                     <div class="col-md-2" style="margin-top:10px;">
                        <a href="<?= base_url("apiapp/export_slip_gaji_tunjangan?tipe=gaji_kosong&id=").$pegawai_slip->id_slip; ?>" target="_blank" class="btn btn-warning" style="width:100%;" >Gaji (Kosong)</a>
                     </div>
                     <div class="col-md-2" style="margin-top:10px;">
                        <a href="<?= base_url("apiapp/export_slip_gaji_tunjangan?tipe=tunjangan_kosong&id=").$pegawai_slip->id_slip; ?>" target="_blank" class="btn btn-danger" style="width:100%;" >Tunjangan (Kosong)</a>
                     </div>
                  </div>
                  <div class="col-xs-12">
                     <div>
                        <label class="control-label col-md-12 " style="font-size: 16pt;">GAJI</label>
                        <hr>
                        <label class="control-label col-md-12">KWITANSI : <?= $pegawai_slip->kwitansi_gaji; ?></label>
                     </div>
                     <div class="col-xs-12 table-responsive">
                        <table class="table table-bordered table-striped table-condensed bg-success">
                           <thead>
                              <tr>
                                 <th class="text-right">Gaji Pokok</th>
                                 <th class="text-right">Tunjangan Istri</th>
                                 <th class="text-right">Tunjangan Anak</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->gaji_pokok); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_istri); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_anak); ?></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="col-xs-3" style="font-weight: bold;">Total penghasilan gaji</div>
                     <div class="col-xs-9">
                        <p class="text-right" style="font-weight: bold;"><?= formatIDR($pegawai_slip->total_penghasilan_gaji); ?></p>
                     </div>
                  </div>
                  <div class="col-xs-12">
                     <div>
                        <label class="control-label col-md-12 " style="font-size: 16pt;">TUNJANGAN</label>
                        <hr>
                        <label class="control-label col-md-12">KWITANSI : <?= $pegawai_slip->kwitansi_tunjangan; ?></label>
                     </div>
                     <div class="col-md-12 table-responsive">
                        <table style="" class="table table-bordered table-striped table-condensed bg-info">
                           <thead>
                              <tr>
                                 <th class="text-right">Tunjangan Pengelolaan</th>
                                 <th class="text-right">Tunjangan Jabatan</th>
                                 <th class="text-right">Tunjangan Kesejahteraan</th>
                                 <th class="text-right">Tunjangan Masa Kerja</th>
                                 <th class="text-right">Tunjangan Fungsional</th>
                                 <th class="text-right">Tunjangan Kehadiran</th>
                                 <th class="text-right">Tunjangan Mengajar</th>
                                 <th class="text-right">Tunjangan Piket</th>
                                 <th class="text-right">Tunjangan Wali Kelas</th>
                                 <th class="text-right">Tunjangan Pembina</th>
                                 <th class="text-right">Insentif FT</th>
                                 <th class="text-right">Tunjangan Insentif</th>
                                 <th class="text-right">Bonus</th>
                                 <th class="text-right">Honor</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_pengelolaan); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_jabatan); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_kesejahteraan); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_masa_kerja); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_fungsional); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_kehadiran); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_mengajar); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_piket); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_wali_kelas); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_pembina); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->insentif_ft); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->tunjangan_insentif); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->bonus); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->honor); ?></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="col-xs-3" style="font-weight: bold;">Total Tunjangan</div>
                     <div class="col-xs-9">
                        <p class="text-right" style="font-weight: bold;"><?= formatIDR($pegawai_slip->total_penghasilan_tunjangan); ?></p>
                     </div>
                  </div>
                  <div class="col-xs-12">
                     <div>
                        <label class="control-label col-md-12"  style="font-size: 16pt;">POTONGAN</label>
                        <hr>
                     </div>
                     <div class="col-md-12 table-responsive">
                        <table style="" class="table table-bordered table-striped table-condensed bg-danger">
                           <thead>
                              <tr>
                                 <th class="text-right">BPJS Kesehatan</th>
                                 <th class="text-right">BPJS Ketenagakerjaan</th>
                                 <th class="text-right">BPJS Pensiun</th>
                                 <th class="text-right">Iuran DPLK BNI</th>
                                 <th class="text-right">Simpanan wajib koperasi</th>
                                 <th class="text-right">Pinjaman uang koperasi</th>
                                 <th class="text-right">Pinjaman barang koperasi</th>
                                 <th class="text-right">PPH 21</th>
                                 <?= (!empty($pegawai_slip->keterangan_lain1)) ? '<th class="text-right">' . $pegawai_slip->keterangan_lain1 . '</th>' : "" ; ?>
                                 <?= (!empty($pegawai_slip->keterangan_lain2)) ? '<th class="text-right">' . $pegawai_slip->keterangan_lain2 . '</th>' : "" ; ?>
                                 <?= (!empty($pegawai_slip->keterangan_lain3)) ? '<th class="text-right">' . $pegawai_slip->keterangan_lain3 . '</th>' : "" ; ?>
                                 <?= (!empty($pegawai_slip->keterangan_lain4)) ? '<th class="text-right">' . $pegawai_slip->keterangan_lain4 . '</th>' : "" ; ?>
                                 <?= (!empty($pegawai_slip->keterangan_lain5)) ? '<th class="text-right">' . $pegawai_slip->keterangan_lain5 . '</th>' : "" ; ?>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->bpjs_kesehatan); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->bpjs_ketenagakerjaan); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->bpjs_pensiun); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->iuran_dplk_bni); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->simpanan_wajib_koperasi); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->pinjaman_uang_koperasi); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->pinjaman_barang_koperasi); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip->pph_21); ?></td>
                                 <?= (!empty($pegawai_slip->nominal_lain1)) ? '<td class="text-right">' . formatIDR($pegawai_slip->nominal_lain1) . '</td>' : "" ; ?>
                                 <?= (!empty($pegawai_slip->nominal_lain2)) ? '<td class="text-right">' . formatIDR($pegawai_slip->nominal_lain2) . '</td>' : "" ; ?>
                                 <?= (!empty($pegawai_slip->nominal_lain3)) ? '<td class="text-right">' . formatIDR($pegawai_slip->nominal_lain3) . '</td>' : "" ; ?>
                                 <?= (!empty($pegawai_slip->nominal_lain4)) ? '<td class="text-right">' . formatIDR($pegawai_slip->nominal_lain4) . '</td>' : "" ; ?>
                                 <?= (!empty($pegawai_slip->nominal_lain5)) ? '<td class="text-right">' . formatIDR($pegawai_slip->nominal_lain5) . '</td>' : "" ; ?>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="col-xs-3" style="font-weight: bold;">Total potongan</div>
                     <div class="col-xs-9">
                        <p class="text-right" style="font-weight: bold;"><?= formatIDR($pegawai_slip->total_potongan_tunjangan); ?></p>
                     </div>
                     <div class="col-xs-3" style="font-weight: bold;">Total diterima</div>
                     <div class="col-xs-9">
                           <p class="text-right" style="font-weight: bold;"><?= formatIDR($pegawai_slip->total_diterima_tunjangan); ?></p>
                     </div>
                  </div>
            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Tutup</button>
            </div>
      </div>
   </div>
</div>
<!--END Detail -->
<?php
}
?>

<!-- ============ MODAL IMPORT GAJI  =============== -->
<div class="modal fade" id="modal_import_gaji" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Import Data Gaji Pegawai</h3>
         </div>
         <form action="<?= base_url('administrator/pegawai_slip/import_gaji'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Upload File Excel</label>
                  <div class="col-xs-8">
                     <input type="file" class="form-control" name="file_upload" required>
                     <small class="info help-block">
                        Semua data akan diganti dengan data yang diupload, jika belum tersedia maka akan menambahkan data baru
                     </small>
                  </div>
               </div>

               <div class="form-group">
                  <label class="control-label col-xs-3">Periode Mulai</label>
                     <div class="col-xs-6">
                        <input type="date" class="form-control" name="periode_mulai" required>
                     </div>
               </div>

               <div class="form-group">
                  <label class="control-label col-xs-3">Periode Selesai</label>
                     <div class="col-xs-6">
                        <input type="date" class="form-control" name="periode_selesai" required>
                        <small class="info help-block">
                           Periode mulai dan selesai harus sama dengan periode gaji
                        </small>
                     </div>
               </div>

            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
               <button type="submit" class="btn btn-info btn_save">Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END IMPORT GAJI-->

<!-- ============ MODAL IMPORT TUNJANGAN =============== -->
<div class="modal fade" id="modal_import_tunjangan" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Import Data Tunjangan Pegawai</h3>
         </div>
         <form action="<?= base_url('administrator/pegawai_slip/import_tunjangan'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Upload File Excel</label>
                  <div class="col-xs-8">
                     <input type="file" class="form-control" name="file_upload" required>
                     <small class="info help-block">
                        Semua data akan diganti dengan data yang diupload, jika belum tersedia maka akan menambahkan data baru
                     </small>
                  </div>
               </div>

               <div class="form-group">
                  <label class="control-label col-xs-3">Periode Mulai</label>
                     <div class="col-xs-6">
                        <input type="date" class="form-control" name="periode_mulai" required>
                     </div>
               </div>

               <div class="form-group">
                  <label class="control-label col-xs-3">Periode Selesai</label>
                     <div class="col-xs-6">
                        <input type="date" class="form-control" name="periode_selesai" required>
                        <small class="info help-block">
                           Periode mulai dan selesai harus sama dengan periode Tunjangan
                        </small>
                     </div>
               </div>

            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
               <button type="submit" class="btn btn-info btn_save">Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END IMPORT TUNJANGAN -->

<!-- ============ MODAL EXPORT GAJI  =============== -->
<div class="modal fade" id="modal_export_gaji" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Export Data Gaji Pegawai</h3>
         </div>
         <form action="<?= base_url('administrator/pegawai_slip/export_gaji'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">

               <div class="form-group">
                  <label class="control-label col-xs-3">Periode Mulai</label>
                     <div class="col-xs-6">
                        <input type="date" class="form-control" name="periode_mulai" required>
                     </div>
               </div>

               <div class="form-group">
                  <label class="control-label col-xs-3">Periode Selesai</label>
                     <div class="col-xs-6">
                        <input type="date" class="form-control" name="periode_selesai" required>
                        <small class="info help-block">
                           Periode mulai dan selesai harus sama dengan periode gaji
                        </small>
                     </div>
               </div>
            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
               <button type="submit" class="btn btn-info btn_save">Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END EXPORT GAJI -->

<!-- ============ MODAL EXPORT TUNJANGAN  =============== -->
<div class="modal fade" id="modal_export_tunjangan" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Export Data Tunjangan Pegawai</h3>
         </div>
         <form action="<?= base_url('administrator/pegawai_slip/export_tunjangan'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">

               <div class="form-group">
                  <label class="control-label col-xs-3">Periode Mulai</label>
                     <div class="col-xs-6">
                        <input type="date" class="form-control" name="periode_mulai" required>
                     </div>
               </div>

               <div class="form-group">
                  <label class="control-label col-xs-3">Periode Selesai</label>
                     <div class="col-xs-6">
                        <input type="date" class="form-control" name="periode_selesai" required>
                        <small class="info help-block">
                           Periode mulai dan selesai harus sama dengan periode Tunjangan
                        </small>
                     </div>
               </div>

            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
               <button type="submit" class="btn btn-info btn_save">Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END EXPORT TUNJANGAN -->

<!-- Page script -->
<script>
  $(document).ready(function(){
   
    $('.remove-data').click(function(){

      var url = $(this).attr('data-href');

      swal({
          title: "<?= cclang('are_you_sure'); ?>",
          text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
          cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
          closeOnConfirm: true,
          closeOnCancel: true
        },
        function(isConfirm){
          if (isConfirm) {
            document.location.href = url;            
          }
        });

      return false;
    });


    $('#apply').click(function(){

      var bulk = $('#bulk');
      var serialize_bulk = $('#form_pegawai_slip').serialize();

      if (bulk.val() == 'delete') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
            cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/pegawai_slip/delete?' + serialize_bulk;      
            }
          });

        return false;

      } else if(bulk.val() == '')  {
          swal({
            title: "Upss",
            text: "<?= cclang('please_choose_bulk_action_first'); ?>",
            type: "warning",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Okay!",
            closeOnConfirm: true,
            closeOnCancel: true
          });

        return false;
      }

      return false;

    });/*end appliy click*/


    //check all
    var checkAll = $('#check_all');
    var checkboxes = $('input.check');

    checkAll.on('ifChecked ifUnchecked', function(event) {   
        if (event.type == 'ifChecked') {
            checkboxes.iCheck('check');
        } else {
            checkboxes.iCheck('uncheck');
        }
    });

    checkboxes.on('ifChanged', function(event){
        if(checkboxes.filter(':checked').length == checkboxes.length) {
            checkAll.prop('checked', 'checked');
        } else {
            checkAll.removeProp('checked');
        }
        checkAll.iCheck('update');
    });

  }); /*end doc ready*/
</script>
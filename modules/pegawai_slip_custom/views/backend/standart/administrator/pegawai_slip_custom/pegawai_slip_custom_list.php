
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('pegawai_slip_custom') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('pegawai_slip_custom') ?></li>
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
                     <a class="btn btn-flat btn-success" title="Import Data Slip" data-toggle="modal" data-target="#modal_import_slip"> Import Slip THR / Gaji ke 14</a>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('pegawai_slip_custom') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('pegawai_slip_custom')]); ?>  <i class="label bg-yellow"><?= $pegawai_slip_custom_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_pegawai_slip_custom" id="form_pegawai_slip_custom" action="<?= base_url('administrator/pegawai_slip_custom/index'); ?>">
                  
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
                     <?php $no = 1; foreach($pegawai_slip_customs as $pegawai_slip_custom): ?>
                        <tr>
                           
                           <td><?=  $no; ?></td> 
                           <td><?= $pegawai_slip_custom->nama_lengkap; ?> </td>
                           
                           <td><?= _ent($pegawai_slip_custom->npp); ?></td> 
                           <td><?= _ent($pegawai_slip_custom->golongan); ?></td> 
                           <td><?= _ent($pegawai_slip_custom->jabatan); ?></td> 
                           <td><?= formatTanggal($pegawai_slip_custom->periode_mulai); ?></td> 
                           <td><?= formatTanggal($pegawai_slip_custom->periode_selesai); ?></td>
                           
                           <td width="250">
                              <?php is_allowed('pegawai_view', function() use ($pegawai_slip_custom){?>
                              <a class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal_export_slip<?= $pegawai_slip_custom->id_slip; ?>"><i class="fa fa-eye"></i> </a>
                              <?php }) ?>
                              <?php is_allowed('pegawai_update', function() use ($pegawai_slip_custom){?>
                              <a href="<?= site_url('administrator/pegawai_slip_custom/edit/' . $pegawai_slip_custom->id_slip); ?>" class="btn btn-sm btn-warning"><i class="fa fa-pencil-square-o "></i> </a>
                              <?php }) ?>
                                 <?php 
                                       is_allowed('pegawai_slip_delete', function() use ($pegawai_slip_custom){
                                 ?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/pegawai_slip_custom/delete/' . $pegawai_slip_custom->id_slip); ?>" class="btn btn-sm btn-danger remove-data" title="delete pegawai_slip"><i class="fa fa-trash-o"></i> </a>
                                 <?php
                                    }) 
                                 ?>
                           </td>
                        </tr>
                     <?php $no++; endforeach; ?>
                     <?php if ($pegawai_slip_custom_counts == 0) :?>
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
                                                     <option value="delete">Delete</option>
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
                            <option <?= $this->input->get('f') == 'nama_slip' ? 'selected' :''; ?> value="nama_slip">Nama Slip</option>
                           <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' :''; ?> value="nama_lengkap">Nama Lengkap</option>
                           <option <?= $this->input->get('f') == 'npp' ? 'selected' :''; ?> value="npp">NPP</option>
                           <option <?= $this->input->get('f') == 'golongan' ? 'selected' :''; ?> value="golongan">Golongan</option>
                           <option <?= $this->input->get('f') == 'jabatan' ? 'selected' :''; ?> value="jabatan">Jabatan</option>
                           <option <?= $this->input->get('f') == 'gaji_pokok' ? 'selected' :''; ?> value="gaji_pokok">Gaji Pokok</option>
                           <option <?= $this->input->get('f') == 'tunjangan_istri' ? 'selected' :''; ?> value="tunjangan_istri">Tunjangan Istri</option>
                           <option <?= $this->input->get('f') == 'tunjangan_anak' ? 'selected' :''; ?> value="tunjangan_anak">Tunjangan Anak</option>
                           <option <?= $this->input->get('f') == 'tunjangan_pengelolaan' ? 'selected' :''; ?> value="tunjangan_pengelolaan">Tunjangan Pengelolaan</option>
                           <option <?= $this->input->get('f') == 'tunjangan_jabatan' ? 'selected' :''; ?> value="tunjangan_jabatan">Tunjangan Jabatan</option>
                           <option <?= $this->input->get('f') == 'tunjangan_kesejahteraan' ? 'selected' :''; ?> value="tunjangan_kesejahteraan">Tunjangan Kesejahteraan</option>
                           <option <?= $this->input->get('f') == 'tunjangan_masa_kerja' ? 'selected' :''; ?> value="tunjangan_masa_kerja">Tunjangan Masa Kerja</option>
                           <option <?= $this->input->get('f') == 'tunjangan_fungsional' ? 'selected' :''; ?> value="tunjangan_fungsional">Tunjangan Fungsional</option>
                           <option <?= $this->input->get('f') == 'total_kehadiran' ? 'selected' :''; ?> value="total_kehadiran">Total Kehadiran</option>
                           <option <?= $this->input->get('f') == 'tunjangan_kehadiran' ? 'selected' :''; ?> value="tunjangan_kehadiran">Tunjangan Kehadiran</option>
                           <option <?= $this->input->get('f') == 'total_mengajar' ? 'selected' :''; ?> value="total_mengajar">Total Mengajar</option>
                           <option <?= $this->input->get('f') == 'tunjangan_mengajar' ? 'selected' :''; ?> value="tunjangan_mengajar">Tunjangan Mengajar</option>
                           <option <?= $this->input->get('f') == 'total_piket' ? 'selected' :''; ?> value="total_piket">Total Piket</option>
                           <option <?= $this->input->get('f') == 'rupiah_per_piket' ? 'selected' :''; ?> value="rupiah_per_piket">Rupiah Per Piket</option>
                           <option <?= $this->input->get('f') == 'tunjangan_piket' ? 'selected' :''; ?> value="tunjangan_piket">Tunjangan Piket</option>
                           <option <?= $this->input->get('f') == 'tunjangan_wali_kelas' ? 'selected' :''; ?> value="tunjangan_wali_kelas">Tunjangan Wali Kelas</option>
                           <option <?= $this->input->get('f') == 'tunjangan_pembina' ? 'selected' :''; ?> value="tunjangan_pembina">Tunjangan Pembina</option>
                           <option <?= $this->input->get('f') == 'insentif_ft' ? 'selected' :''; ?> value="insentif_ft">Insentif FT</option>
                           <option <?= $this->input->get('f') == 'tunjangan_insentif' ? 'selected' :''; ?> value="tunjangan_insentif">Tunjangan Insentif</option>
                           <option <?= $this->input->get('f') == 'bonus' ? 'selected' :''; ?> value="bonus">Bonus</option>
                           <option <?= $this->input->get('f') == 'honor' ? 'selected' :''; ?> value="honor">Honor</option>
                           <option <?= $this->input->get('f') == 'thr' ? 'selected' :''; ?> value="thr">THR</option>
                           <option <?= $this->input->get('f') == 'gaji14' ? 'selected' :''; ?> value="gaji14">Gaji Ke 14</option>
                           <option <?= $this->input->get('f') == 'pph21' ? 'selected' :''; ?> value="pph21">PPH21</option>
                           <option <?= $this->input->get('f') == 'total_penghasilan' ? 'selected' :''; ?> value="total_penghasilan">Total Penghasilan</option>
                           <option <?= $this->input->get('f') == 'total_potongan' ? 'selected' :''; ?> value="total_potongan">Total Potongan</option>
                           <option <?= $this->input->get('f') == 'total_diterima' ? 'selected' :''; ?> value="total_diterima">Total Diterima</option>
                           <option <?= $this->input->get('f') == 'kwitansi' ? 'selected' :''; ?> value="kwitansi">Kwitansi</option>
                           <option <?= $this->input->get('f') == 'periode_mulai' ? 'selected' :''; ?> value="periode_mulai">Periode Mulai</option>
                           <option <?= $this->input->get('f') == 'periode_selesai' ? 'selected' :''; ?> value="periode_selesai">Periode Selesai</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/pegawai_slip_custom');?>" title="<?= cclang('reset_filter'); ?>">
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
foreach ($pegawai_slip_customs as $pegawai_slip_custom) {
?>
<!-- ============ MODAL Detail  =============== -->
<div class="modal fade" id="modal_export_slip<?= $pegawai_slip_custom->id_slip; ?>" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Detail Slip THR / Gaji ke 14</h3>
         </div>
            <div class="modal-body" >               
                  <div class="form-group">
                     <label class="control-label col-md-3" style="margin-top:10px;">Nama Lengkap</label>
                     <div class="col-md-9" style="margin-top:10px;">
                        <input type="text" name="nama_lengkap_modal" class="form-control" value="<?= $pegawai_slip_custom->nama_lengkap; ?>" readonly>
                     </div>
                  </div>
                  <div class="form-group" >
                     <label class="control-label col-md-3" style="margin-top:10px;">NPP</label>
                     <div class="col-md-9" style="margin-top:10px;">
                        <input type="text" name="npp_modal" class="form-control" value="<?= $pegawai_slip_custom->npp; ?>" readonly>
                     </div>
                  </div>
                  <div class="form-group" >
                     <label class="control-label col-md-3" style="margin-top:10px;">Periode</label>
                     <div class="col-md-4" style="margin-top:10px;">
                        <input class="form-control" type="text" value="<?php echo formatTanggal($pegawai_slip_custom->periode_mulai); ?> " readonly />
                     </div>
                     <div class="col-md-1 text-center" style="font-size: 24pt;"> - </div>
                     <div class="col-md-4" style="margin-top:10px;">
                        <input class="form-control" type="text" value="<?php echo formatTanggal($pegawai_slip_custom->periode_selesai); ?> " readonly />
                     </div>
                  </div>
                  <div class="form-group col-xs-12" >
                     <label class="control-label col-md-3" style="margin-top:10px;">Slip</label>
                     <div class="col-md-5">&nbsp;</div>
                     <div class="col-md-2" style="margin-top:10px;">
                        <a href="<?= base_url("apiapp/export_slip_tunjangan?tipe=gaji&id=").$pegawai_slip_custom->id_slip; ?>" target="_blank" class="btn btn-success" style="width:100%;" >Gaji</a>
                     </div>
                     <div class="col-md-2" style="margin-top:10px;">
                        <a href="<?= base_url("apiapp/export_slip_tunjangan?tipe=tunjangan&id=").$pegawai_slip_custom->id_slip; ?>" target="_blank" class="btn btn-info" style="width:100%;" >Tunjangan</a>
                     </div>
                  </div>
                  <div class="col-xs-12">
                     <div>
                        <label class="control-label col-md-12 " style="font-size: 16pt;">GAJI</label>
                        <hr>
                        <label class="control-label col-md-12">KWITANSI : <?= $pegawai_slip_custom->kwitansi; ?></label>
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
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->gaji_pokok); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_istri); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_anak); ?></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="col-xs-3" style="font-weight: bold;">Total penghasilan gaji</div>
                     <div class="col-xs-9">
                        <p class="text-right" style="font-weight: bold;"><?= formatIDR($pegawai_slip_custom->total_penghasilan); ?></p>
                     </div>
                  </div>
                  <div class="col-xs-12">
                     <div>
                        <label class="control-label col-md-12 " style="font-size: 16pt;">TUNJANGAN</label>
                        <hr>
                        <label class="control-label col-md-12">KWITANSI : <?= $pegawai_slip_custom->kwitansi; ?></label>
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
                                 <?php
                                    if(!empty($pegawai_slip_custom->thr)){
                                       echo '<th class="text-right">THR</th>';
                                    }
                                    if(!empty($pegawai_slip_custom->gaji14)){
                                       echo '<th class="text-right">Gaji ke 14</th>';
                                    }
                                 ?>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_pengelolaan); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_jabatan); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_kesejahteraan); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_masa_kerja); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_fungsional); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_kehadiran); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_mengajar); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_piket); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_wali_kelas); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_pembina); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->insentif_ft); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->tunjangan_insentif); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->bonus); ?></td>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->honor); ?></td>
                                 <?php
                                    if(!empty($pegawai_slip_custom->thr)){
                                       echo '<td class="text-right">' . formatIDR($pegawai_slip_custom->thr) . '</td>';
                                    }
                                    if(!empty($pegawai_slip_custom->gaji14)){
                                       echo '<td class="text-right">' . formatIDR($pegawai_slip_custom->gaji14) . '</td>';
                                    }
                                 ?>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="col-xs-3" style="font-weight: bold;">Total Tunjangan</div>
                     <div class="col-xs-9">
                        <p class="text-right" style="font-weight: bold;"><?= formatIDR($pegawai_slip_custom->total_penghasilan); ?></p>
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
                                 <th class="text-right">PPH 21</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td class="text-right"><?= formatIDR($pegawai_slip_custom->pph21); ?></td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="col-xs-3" style="font-weight: bold;">Total potongan</div>
                     <div class="col-xs-9">
                        <p class="text-right" style="font-weight: bold;"><?= formatIDR($pegawai_slip_custom->total_potongan); ?></p>
                     </div>
                     <div class="col-xs-3" style="font-weight: bold;">Total diterima</div>
                     <div class="col-xs-9">
                           <p class="text-right" style="font-weight: bold;"><?= formatIDR($pegawai_slip_custom->total_diterima); ?></p>
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
<div class="modal fade" id="modal_import_slip" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Import Data Slip THR / Gaji 14</h3>
         </div>
         <form action="<?= base_url('administrator/pegawai_slip_custom/import_slip'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
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

               <div class="form-group">
                  <label class="control-label col-xs-3">Nama Slip</label>
                     <div class="col-xs-8">
                        <input type="input" class="form-control" name="nama_slip" required placeholder="TUNJANGAN HARI RAYA <?= date("Y") ?>">
                        <small class="info help-block">
                           Contoh : THR / Gaji ke 14 (akan dimunculkan di slip sebagai judul slip)
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
      var serialize_bulk = $('#form_pegawai_slip_custom').serialize();

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
               document.location.href = BASE_URL + '/administrator/pegawai_slip_custom/delete?' + serialize_bulk;      
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
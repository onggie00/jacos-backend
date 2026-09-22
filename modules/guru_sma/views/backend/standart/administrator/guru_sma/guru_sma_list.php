
<script type="text/javascript">
<?php if ($this->session->flashdata('success')) { ?>
      toastr.success("<?php echo $this->session->flashdata('success'); ?>");
   <?php } else if ($this->session->flashdata('error')) {  ?>
      toastr.error("<?php echo $this->session->flashdata('error'); ?>");
   <?php } else if ($this->session->flashdata('warning')) {  ?>
      toastr.warning("<?php echo $this->session->flashdata('warning'); ?>");
   <?php } else if ($this->session->flashdata('info')) {  ?>
      toastr.info("<?php echo $this->session->flashdata('info'); ?>");
   <?php } ?>
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('guru_sma') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('guru_sma') ?></li>
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
                        <?php is_allowed('guru_sma_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('guru_sma')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/guru_sma/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('guru_sma')]); ?></a>
                        <a style="display:none" class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('guru_sma')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/guru_sma/add_slip_gaji'); ?>"><i class="fa fa-plus-square-o" ></i> Tambah Slip Gaji</a>
                        <a style="display:none" class="btn btn-flat btn-success" title="Import Slip Gaji Multiple" data-toggle="modal" data-target="#modal_import"><i class="fa fa-file-pdf-o"></i> <?= Import ?> Slip Gaji</a>
                        <a class="btn btn-flat btn-success" title="Import Excel" data-toggle="modal" data-target="#modal_add_new"><i class="fa fa-file-pdf-o"></i> <?= Import ?> Guru</a>

                        <?php }) ?>
                        <?php is_allowed('guru_sma_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('guru_sma') ?>']); ?>" href="<?= site_url('administrator/guru_sma/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('guru_sma_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('guru_sma') ?>']); ?>" href="<?= site_url('administrator/guru_sma/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('guru_sma') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('guru_sma')]); ?>  <i class="label bg-yellow"><?= $guru_sma_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_guru_sma" id="form_guru_sma" action="<?= base_url('administrator/guru_sma/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                           <th> Action</th>
                           <th> <?= cclang('nama_lengkap') ?></th>
                           <th> <?= cclang('nik') ?></th>
                           <th> <?= cclang('nuptk') ?></th>
                           <th> <?= cclang('npp') ?></th>
                           <th> <?= cclang('npwp') ?></th>
                           <th> <?= cclang('alamat') ?></th>
                           <th> <?= cclang('agama') ?></th>
                           <th> <?= cclang('jenis_kelamin') ?></th>
                           <th> <?= cclang('status_menikah') ?></th>
                           <th> <?= cclang('jumlah_anak') ?></th>
                           <th> <?= cclang('no_telp') ?></th>
                           <th> <?= cclang('email') ?></th>
                           <th> <?= cclang('id_posisi') ?></th>
                           <th> <?= cclang('satuan_pendidikan') ?></th>
                           <th> <?= cclang('unit') ?></th>
                           <th> <?= cclang('id_mapel') ?></th>
                           <th> <?= cclang('status_kepegawaian') ?></th>
                           <th> <?= cclang('informasi_kepala_pimpinan') ?></th>
                           <th> <?= cclang('emp_code') ?></th>
                           <th> <?= cclang('token') ?></th>
                           <th> <?= cclang('token_expired') ?></th>
                           <th> <?= cclang('email_ms_office') ?></th>
                           <th> <?= cclang('foto_profil') ?></th>
                           <th> <?= cclang('no_kk') ?></th>
                           <th> <?= cclang('tempat_lahir') ?></th>
                           <th> <?= cclang('tgl_lahir') ?></th>
                           </tr>
                     </thead>
                     <tbody id="tbody_guru_sma">
                     <?php foreach($guru_smas as $guru_sma): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $guru_sma->id_guru; ?>">
                           </td>
                           <td width="200">
                           
                           <?php is_allowed('guru_sma_view', function() use ($guru_sma){?>
                              <a href="<?= site_url('administrator/guru_sma/view/' . $guru_sma->id_guru); ?>" class="btn btn-success"><i class="fa fa-eye"></i> </a>
                           <?php }) ?>
                           <?php is_allowed('guru_sma_update', function() use ($guru_sma){?>
                              <a href="<?= site_url('administrator/guru_sma/edit/' . $guru_sma->id_guru); ?>" class="btn btn-info"><i class="fa fa-edit "></i> </a>
                           <?php }) ?> 
                           <?php is_allowed('guru_sma_delete', function() use ($guru_sma){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/guru_sma/delete/' . $guru_sma->id_guru); ?>" class="btn btn-danger remove-data"><i class="fa fa-close"></i> </a>
                           <?php }) ?>
                           <a style="margin-top:5px;" class="btn btn-primary btn_ubah_password" data-toggle="modal" data-target="#modal_update_password" data-id="<?= $guru_sma->id_guru; ?>" data-email="<?= $guru_sma->email_ms_office; ?>"><i class="fa fa-key"></i >Ubah Password</a>

                           </td>
                           <td><?= _ent($guru_sma->nama_lengkap); ?></td> 
                           <td><?= _ent($guru_sma->nik); ?></td> 
                           <td><?= _ent($guru_sma->nuptk); ?></td> 
                           <td><?= _ent($guru_sma->npp); ?></td> 
                           <td><?= _ent($guru_sma->npwp); ?></td> 
                           <td><?= _ent($guru_sma->alamat); ?></td> 
                           <td><?= _ent($guru_sma->agama); ?></td> 
                           <td><?= _ent($guru_sma->jenis_kelamin); ?></td> 
                           <td><?= _ent($guru_sma->status_menikah); ?></td> 
                           <td><?= _ent($guru_sma->jumlah_anak); ?></td> 
                           <td><?= _ent($guru_sma->no_telp); ?></td> 
                           <td><?= _ent($guru_sma->email); ?></td> 
                           <td><?php if  ($guru_sma->id_posisi) {

                              echo anchor('administrator/posisi_guru/view/'.$guru_sma->id_posisi.'?popup=show', $guru_sma->posisi_guru_nama_posisi, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($guru_sma->satuan_pendidikan); ?></td> 
                           <td><?= _ent($guru_sma->unit); ?></td> 
                           <td><?php if  ($guru_sma->id_mapel) {

                              echo anchor('administrator/mata_pelajaran_sma/view/'.$guru_sma->id_mapel.'?popup=show', $guru_sma->mata_pelajaran_sma_nama_mapel, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?= _ent($guru_sma->status_kepegawaian); ?></td> 
                           <td><?= _ent($guru_sma->informasi_kepala_pimpinan); ?></td> 
                           <td><?= _ent($guru_sma->emp_code); ?></td> 
                           <td><?= _ent($guru_sma->token); ?></td> 
                           <td><?= _ent($guru_sma->token_expired); ?></td> 
                           <td><?= _ent($guru_sma->email_ms_office); ?></td> 
                           <td>
                              <?php if (!empty($guru_sma->foto_profil)): ?>
                                <?php if (is_image($guru_sma->foto_profil)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/guru_sma/' . $guru_sma->foto_profil; ?>">
                                  <img src="<?= BASE_URL . 'uploads/guru_sma/' . $guru_sma->foto_profil; ?>" class="image-responsive" alt="image guru_sma" title="foto_profil guru_sma" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/guru_sma/' . $guru_sma->foto_profil; ?>">
                                   <img src="<?= get_icon_file($guru_sma->foto_profil); ?>" class="image-responsive image-icon" alt="image guru_sma" title="foto_profil <?= $guru_sma->foto_profil; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td><?= _ent($guru_sma->no_kk); ?></td> 
                           <td><?= _ent($guru_sma->tempat_lahir); ?></td> 
                           <td><?= _ent($guru_sma->tgl_lahir); ?></td> 

                           </tr>
                      <?php endforeach; ?>
                      <?php if ($guru_sma_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Guru Sma data is not available
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
                     <div class="col-sm-2 padd-left-0  " >
                        <input type="text" class="form-control" name="q" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('q'); ?>">
                     </div>
                     <div class="col-sm-2 padd-left-0 " >
                        <select type="text" class="form-control chosen chosen-select" name="f" id="field" >
                           <option value=""><?= cclang('all'); ?></option>
                            <option <?= $this->input->get('f') == 'nama_lengkap' ? 'selected' :''; ?> value="nama_lengkap">Nama Lengkap</option>
                           <option <?= $this->input->get('f') == 'nik' ? 'selected' :''; ?> value="nik">Nik</option>
                           <option <?= $this->input->get('f') == 'nuptk' ? 'selected' :''; ?> value="nuptk">Nuptk</option>
                           <option <?= $this->input->get('f') == 'npp' ? 'selected' :''; ?> value="npp">Npp</option>
                           <option <?= $this->input->get('f') == 'npwp' ? 'selected' :''; ?> value="npwp">Npwp</option>
                           <option <?= $this->input->get('f') == 'alamat' ? 'selected' :''; ?> value="alamat">Alamat</option>
                           <option <?= $this->input->get('f') == 'agama' ? 'selected' :''; ?> value="agama">Agama</option>
                           <option <?= $this->input->get('f') == 'jenis_kelamin' ? 'selected' :''; ?> value="jenis_kelamin">Jenis Kelamin</option>
                           <option <?= $this->input->get('f') == 'status_menikah' ? 'selected' :''; ?> value="status_menikah">Status Menikah</option>
                           <option <?= $this->input->get('f') == 'jumlah_anak' ? 'selected' :''; ?> value="jumlah_anak">Jumlah Anak</option>
                           <option <?= $this->input->get('f') == 'no_telp' ? 'selected' :''; ?> value="no_telp">No Telp</option>
                           <option <?= $this->input->get('f') == 'email' ? 'selected' :''; ?> value="email">Email</option>
                           <option <?= $this->input->get('f') == 'id_posisi' ? 'selected' :''; ?> value="id_posisi">Id Posisi</option>
                           <option <?= $this->input->get('f') == 'satuan_pendidikan' ? 'selected' :''; ?> value="satuan_pendidikan">Satuan Pendidikan</option>
                           <option <?= $this->input->get('f') == 'unit' ? 'selected' :''; ?> value="unit">Unit</option>
                           <option <?= $this->input->get('f') == 'id_mapel' ? 'selected' :''; ?> value="id_mapel">Id Mapel</option>
                           <option <?= $this->input->get('f') == 'status_kepegawaian' ? 'selected' :''; ?> value="status_kepegawaian">Status Kepegawaian</option>
                           <option <?= $this->input->get('f') == 'informasi_kepala_pimpinan' ? 'selected' :''; ?> value="informasi_kepala_pimpinan">Informasi Kepala Pimpinan</option>
                           <option <?= $this->input->get('f') == 'emp_code' ? 'selected' :''; ?> value="emp_code">Emp Code</option>
                           <option <?= $this->input->get('f') == 'token' ? 'selected' :''; ?> value="token">Token</option>
                           <option <?= $this->input->get('f') == 'token_expired' ? 'selected' :''; ?> value="token_expired">Token Expired</option>
                           <option <?= $this->input->get('f') == 'email_ms_office' ? 'selected' :''; ?> value="email_ms_office">Email Ms Office</option>
                           <option <?= $this->input->get('f') == 'foto_profil' ? 'selected' :''; ?> value="foto_profil">Foto Profil</option>
                           <option <?= $this->input->get('f') == 'no_kk' ? 'selected' :''; ?> value="no_kk">No Kk</option>
                           <option <?= $this->input->get('f') == 'tempat_lahir' ? 'selected' :''; ?> value="tempat_lahir">Tempat Lahir</option>
                           <option <?= $this->input->get('f') == 'tgl_lahir' ? 'selected' :''; ?> value="tgl_lahir">Tgl Lahir</option>
                           <option <?= $this->input->get('f') == 'slip_gaji' ? 'selected' :''; ?> value="slip_gaji">Slip Gaji</option>
                          </select>
                     </div>
                     <div class="col-sm-2 padd-left-0 ">
                        <select type="text" class="form-control chosen chosen-select" name="s" id="sort">
                           <option value="">Sort By</option>
                           <option <?= $this->input->get('s') == 'id_guru' ? 'selected' : ''; ?> value="id_guru">ID Guru</option>
                           <option <?= $this->input->get('s') == 'nama_lengkap' ? 'selected' : ''; ?> value="nama_lengkap">Nama Lengkap</option>
                        </select>
                     </div>
                     <div class="col-sm-2 padd-left-0 ">
                        <select type="text" class="form-control chosen chosen-select" name="d" id="sort_type">
                           <option <?= $this->input->get('d') == 'desc' ? 'selected' : ''; ?> value="desc">Descending</option>
                           <option <?= $this->input->get('d') == 'asc' ? 'selected' : ''; ?> value="asc">Ascending</option>
                        </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/guru_sma');?>" title="<?= cclang('reset_filter'); ?>">
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
<!-- ============ MODAL ADD BARANG =============== -->
<div class="modal fade" id="modal_add_new" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Tambah Guru Baru</h3>
         </div>
         <form action="<?= base_url('administrator/guru_sma/import/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">

               <div class="form-group">
                  <label class="control-label col-xs-3">File</label>
                  <div class="col-xs-8">
                     <input name="file_guru" class="form-control" type="file"  accept=".xls,.xlsx" required>
                     <span>*Silahkan isikan excel sesuai dengan format yang tersedia, berikut contoh format excel
                        <a class="text-danger" href="<?= base_url('uploads/example/format_guru_sma.xlsx'); ?>">download disini</a> </span>
                  </div>
               </div>


            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Tutup</button>
               <button class="btn btn-info btn_save">Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL ADD BARANG-->
<!-- ============ MODAL ADD  =============== -->
<div class="modal fade" id="modal_import" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Upload Slip Gaji Multiple</h3>
         </div>
         <form action="<?= base_url('administrator/guru_sma/upload_slip_gaji/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">File</label>
                  <div class="col-xs-8">
                     <input name="file_slip_gaji[]" class="form-control" type="file"  accept=".pdf" required multiple>
                     <span>*Dapat upload slip gaji lebih dari satu file (pastikan nama file sama dengan NPP guru masing-masing) </span>
                  </div>
               </div>


            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Tutup</button>
               <button class="btn btn-info btn_save">Simpan</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL ADD -->

<!-- Modal Update Password -->
<div class="modal fade" id="modal_update_password" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Ubah / Reset Password</h3>
         </div>
         <form action="<?= base_url('administrator/guru_sma/update_password/'); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-4" style="margin-top: 10px;">User Microsoft ID</label>
                  <div class="col-xs-7" style="text-align: left;margin-top: 10px;">
                     <input name="user_microsoft_id" id="user_microsoft_id" class="form-control" type="text" readonly/>
                  </div>
                  <label class="control-label col-xs-4" style="margin-top: 10px;">User Microsoft Email</label>
                  <div class="col-xs-7" style="text-align: left;margin-top: 10px;">
                     <input name="user_microsoft_mail" id="user_microsoft_mail" class="form-control" type="text" readonly/>
                  </div>
                  <label class="control-label col-xs-4" style="margin-top: 10px;">Password</label>
                  <div class="col-xs-7" style="text-align: left;margin-top: 10px;">
                     <input name="password" class="form-control" type="password" required>
                  </div>
               </div>
               <small>*Jika Reset Password akan diisi otomatis <b>Labschool123456</b>.</small><br/>
               <small>**Jika tidak mempunyai access_token dan refresh_token atau token telah kadaluarsa, silahkan login ulang.</small><br/>
               <textarea style="height: 100px;" class="form-control" disabled><?php echo "Microsoft Session Token : ".$this->session->userdata('access_token'); ?></textarea>
               <br/>
               <textarea style="height: 100px;" class="form-control" disabled><?php echo "Microsoft Session Refresh Token : ".$this->session->userdata('refresh_token'); ?></textarea>
            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
               <button class="btn btn-info btn_ubah">Ubah</button>
               <a id="reset_password" class="btn btn-danger" href="">Reset Password</a>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- Modal Update Password -->
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
      var serialize_bulk = $('#form_guru_sma').serialize();

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
               document.location.href = BASE_URL + '/administrator/guru_sma/delete?' + serialize_bulk;      
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

   $('.btn_ubah_password').click(function(){
      <?php
         $db_refresh_token = $this->mymodel->withquery("select name_setting, value from pengaturan_akun where name_setting = 'administrator_access_token'","row")->value;   
      ?>
      const access_token = "<?= (empty($this->session->userdata('access_token')) ? $db_refresh_token : $this->session->userdata('access_token')) ? : $this->session->userdata('access_token'); ?>";
      const email = $(this).attr('data-email');

      $.ajax({
         url: `https://graph.microsoft.com/v1.0/users/${email}`,
         method: "GET",
         headers: {
            "Authorization": "Bearer " + access_token
         },
         success: function(res) {
            $('#user_microsoft_id').val(res.id);
            $('#user_microsoft_mail').val(res.userPrincipalName);
            $('#reset_password').attr('href', `<?php echo base_url('administrator/guru_sma/reset_password'); ?>?user_microsoft_id=${res.id}`);
            // console.log("User ID:", res.id);
            // console.log("UPN:", res.userPrincipalName);
         },
         error: function(err) {
            console.error(err);
         }
      });
   });

  }); /*end doc ready*/
</script>
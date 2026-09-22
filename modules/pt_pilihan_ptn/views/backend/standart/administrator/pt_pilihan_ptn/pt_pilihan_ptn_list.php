
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('pt_pilihan_ptn') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('pt_pilihan_ptn') ?></li>
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
                        <?php is_allowed('pt_pilihan_ptn_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" style="display:none;" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('pt_pilihan_ptn')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/pt_pilihan_ptn/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('pt_pilihan_ptn')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('pt_pilihan_ptn_export', function(){?>
                           <a class="btn btn-flat btn-success" data-toggle="modal" data-target="#modal_export" href="javascript:void(0);">
                              <i class="fa fa-file-excel-o"></i> <?= cclang('export'); ?> XLS
                           </a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('pt_pilihan_ptn') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('pt_pilihan_ptn')]); ?>  <i class="label bg-yellow"><?= $pt_pilihan_ptn_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_pt_pilihan_ptn" id="form_pt_pilihan_ptn" action="<?= base_url('administrator/pt_pilihan_ptn/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                                                    <th> <?= cclang('jenjang') ?></th>
                           <th style="width:200px;"> <?= cclang('id_siswa_aktif') ?></th>
                           <th style="width: 100px;"><?= "NIS" ?></th>
                           <th><?= "Kelas"?></th>
                           <th style="text-align:center;"><?= "UTBK 1" ?></th>
                           <th style="text-align:center;"><?= "UTBK 2" ?></th>
                           <th style="text-align:center;"><?= "UTBK 3" ?></th>
                           <th style="text-align:center;"><?= "UTBK 4" ?></th>
                           <th> <?= cclang('id_pt') ?></th>
                           <th> <?= cclang('id_jurusan') ?></th>
                           <th> <?= 'Tanggal Pilih' ?></th>
                           <th> <?= 'Tanggal Ubah' ?></th>
                           <th style="text-align:center;"><?= "Persentase UTBK 1" ?></th>
                           <th style="text-align:center;"><?= "Persentase UTBK 2" ?></th>
                           <th style="text-align:center;"><?= "Persentase UTBK 3" ?></th>
                           <th style="text-align:center;"><?= "Persentase UTBK 4" ?></th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_pt_pilihan_ptn">
                     <?php foreach($pt_pilihan_ptns as $pt_pilihan_ptn): ?>
                        <?php
                           $get_nilai_utbk = $this->mymodel->withquery("select * from pt_to_siswa where jenjang = '".$pt_pilihan_ptn->jenjang."' and id_siswa_aktif = '".$pt_pilihan_ptn->id_siswa_aktif."'", "row");
                           $persentase1 = ($get_nilai_utbk->nilai_utbk1 * 100)/$pt_pilihan_ptn->pt_jurusan_passing_grade;
                           $persentase2 = ($get_nilai_utbk->nilai_utbk2 * 100)/$pt_pilihan_ptn->pt_jurusan_passing_grade;
                           $persentase3 = ($get_nilai_utbk->nilai_utbk3 * 100)/$pt_pilihan_ptn->pt_jurusan_passing_grade;
                           $persentase4 = ($get_nilai_utbk->nilai_utbk4 * 100)/$pt_pilihan_ptn->pt_jurusan_passing_grade;
                        ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $pt_pilihan_ptn->id; ?>">
                           </td>
                                                       
                           <td><?= _ent($pt_pilihan_ptn->jenjang); ?></td> 
                           <td><?php if  ($pt_pilihan_ptn->id_siswa_aktif) {
                              $nama_tabel = "siswa_".$pt_pilihan_ptn->jenjang."_aktif";
                              $nama_lengkap = "siswa_".$pt_pilihan_ptn->jenjang."_aktif_nama_lengkap";
                              echo anchor('administrator/'.$nama_tabel.'/view/'.$pt_pilihan_ptn->id_siswa_aktif.'?popup=show', $pt_pilihan_ptn->$nama_lengkap, ['class' => 'popup-view']); }?> </td>
                           <td><?php 
                           $kolom_nis = "siswa_".$pt_pilihan_ptn->jenjang."_aktif_nis";
                           echo $pt_pilihan_ptn->$kolom_nis;
                           ?></td>
                           <td><?php 
                           $nama_kolom = "kelas_".$pt_pilihan_ptn->jenjang."_label";
                           echo $pt_pilihan_ptn->$nama_kolom;
                           ?></td>
                           <td><?= $get_nilai_utbk->nilai_utbk1; ?></td>
                           <td><?= $get_nilai_utbk->nilai_utbk2; ?></td>
                           <td><?= $get_nilai_utbk->nilai_utbk3; ?></td>
                           <td><?= $get_nilai_utbk->nilai_utbk4; ?></td>
                           <td><?php if  ($pt_pilihan_ptn->id_pt) {

                              echo anchor('administrator/pt_perguruan_tinggi/view/'.$pt_pilihan_ptn->id_pt.'?popup=show', $pt_pilihan_ptn->pt_perguruan_tinggi_nama_pt, ['class' => 'popup-view']); }?> </td>
                             
                           <td><?php if  ($pt_pilihan_ptn->id_jurusan) {

                              echo anchor('administrator/pt_jurusan/view/'.$pt_pilihan_ptn->id_jurusan.'?popup=show', $pt_pilihan_ptn->pt_jurusan_jurusan, ['class' => 'popup-view']); }?> </td>
                           <td><?= date('d-m-Y H:i', strtotime($pt_pilihan_ptn->created_at)); ?></td>
                           <td><?= date('d-m-Y H:i', strtotime($pt_pilihan_ptn->updated_at)); ?></td>
                           <td><?= number_format($persentase1,2,".","")."%"; ?></td>
                           <td><?= number_format($persentase2,2,".","")."%"; ?></td>
                           <td><?= number_format($persentase3,2,".","")."%"; ?></td>
                           <td><?= number_format($persentase4,2,".","")."%"; ?></td>
                             
                           <td width="200">
                            
                              <?php is_allowed('pt_pilihan_ptn_update', function() use ($pt_pilihan_ptn){?>
                              <a href="<?= site_url('administrator/pt_pilihan_ptn/edit/' . $pt_pilihan_ptn->id); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('pt_pilihan_ptn_delete', function() use ($pt_pilihan_ptn){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/pt_pilihan_ptn/delete/' . $pt_pilihan_ptn->id); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($pt_pilihan_ptn_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Pilihan PTN data is not available
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
                           <option value=""><?php echo cclang('all'); ?></option>
                           <option <?= $this->input->get('f') == 'id_kelas' ? 'selected' :''; ?> value="id_kelas">Kelas</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/pt_pilihan_ptn');?>" title="<?= cclang('reset_filter'); ?>">
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

<!-- ============ MODAL EXPORT DATA PT SISWA  =============== -->
<div class="modal fade" id="modal_export" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 class="modal-title" id="myModalLabel">Export Data Pilihan PTN Siswa</h3>
         </div>
         <form action="<?= base_url('administrator/pt_pilihan_ptn/export/'); ?>" method="get">
            <div class="modal-body">
               <div class="form-group">
                  <label class="control-label col-xs-3">Tahun Ajaran</label>
                  <div class="col-xs-8">
                     <select name="q" id="select_tahun_ajaran" class="form-control" required>
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <?php
                           $tahun_ajaran = $this->mymodel->withquery("SELECT * FROM tahun_ajaran ORDER BY tanggal_mulai ASC", "result");
                           foreach ($tahun_ajaran as $row) {
                              $selected = ($this->input->get('q') == $row->id_tahun_ajaran) ? 'selected' : '';
                              echo "<option value='{$row->id_tahun_ajaran}' {$selected}>{$row->label}</option>";
                           }
                        ?>
                     </select>
                  </div>
               </div>
<br/>
               <div class="form-group">
                  <label class="control-label col-xs-3">Kelas</label>
                  <div class="col-xs-8">
                     <select name="id_kelas" id="select_kelas" class="form-control chosen chosen-select">
                        <option value="">-- Semua Kelas (per Sheet) --</option>
                        <optgroup label="FT">
                        <?php
                           $kelas_ft = $this->mymodel->withquery("SELECT id_kelas_ft AS id_kelas, label FROM kelas_ft ORDER BY label ASC", "result");
                           foreach ($kelas_ft as $row) {
                              echo "<option value='{$row->id_kelas}'>[FT] {$row->label}</option>";
                           }
                        ?>
                        </optgroup>
                        <optgroup label="SMA">
                        <?php
                           $kelas_sma = $this->mymodel->withquery("SELECT id_kelas_sma AS id_kelas, label FROM kelas_sma ORDER BY label ASC", "result");
                           foreach ($kelas_sma as $row) {
                              echo "<option value='{$row->id_kelas}'>[SMA] {$row->label}</option>";
                           }
                        ?>
                        </optgroup>
                     </select>
                     <small class="text-muted">Biarkan kosong untuk export semua kelas (tiap kelas = 1 sheet)</small>
                  </div>
               </div>

               <!-- hidden field untuk menentukan mode filter -->
               <input type="hidden" name="f" value="tahun_ajaran">
            </div>

            <div class="modal-footer">
               <button class="btn" data-dismiss="modal" aria-hidden="true">Tutup</button>
               <button type="submit" class="btn btn-success btn_save">
                  <i class="fa fa-file-excel-o"></i> Export XLS
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
<!--END MODAL EXPORT DATA PT SISWA -->

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
      var serialize_bulk = $('#form_pt_pilihan_ptn').serialize();

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
               document.location.href = BASE_URL + '/administrator/pt_pilihan_ptn/delete?' + serialize_bulk;      
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
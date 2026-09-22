
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('nilai_raport_sma') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('nilai_raport_sma') ?></li>
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
                        <?php is_allowed('nilai_raport_sma_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> <?= cclang('nilai_raport_sma') ?>']); ?>" href="<?= site_url('administrator/nilai_raport_sma/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('nilai_raport_sma_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> pdf <?= cclang('nilai_raport_sma') ?>']); ?>" href="<?= site_url('administrator/nilai_raport_sma/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username"><?= cclang('nilai_raport_sma') ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', [cclang('nilai_raport_sma')]); ?>  <i class="label bg-yellow"><?= $nilai_raport_sma_counts; ?>  <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_nilai_raport_sma" id="form_nilai_raport_sma" action="<?= base_url('administrator/nilai_raport_sma/index'); ?>">
                  

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                                                     <th>
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                           <th> Nama Lengkap</th>
                           <th> B.INGGRIS 7/1</th>
                           <th> B.INGGRIS 7/2</th>
                           <th> B.INGGRIS 8/1</th>
                           <th> B.INGGRIS 8/2</th>
                           <th> B.INDO 7/1</th>
                           <th> B.INDO 7/2</th>
                           <th> B.INDO 8/1</th>
                           <th> B.INDO 8/2</th>
                           <th> IPA 7/1</th>
                           <th> IPA 7/2</th>
                           <th> IPA 8/1</th>
                           <th> IPA 8/2</th>
                           <th> IPS 7/1</th>
                           <th> IPS 7/2</th>
                           <th> IPS 8/1</th>
                           <th> IPS 8/2</th>
                           <th> MTK 7/1</th>
                           <th> MTK 7/2</th>
                           <th> MTK 8/1</th>
                           <th> MTK 8/2</th>
                           <th> RAPORT 7/1</th>
                           <th> RAPORT 7/2</th>
                           <th> RAPORT 8/1</th>
                           <th> RAPORT 8/2</th>
                           <th>Action</th>                        </tr>
                     </thead>
                     <tbody id="tbody_nilai_raport_sma">
                     <?php foreach($nilai_raport_smas as $nilai_raport_sma): ?>
                        <tr>
                                                       <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $nilai_raport_sma->id_nilai_raport_sma; ?>">
                           </td>
                                                       
                           <td><?= _ent($nilai_raport_sma->nama_lengkap); ?></td> 
                           <td><?= _ent($nilai_raport_sma->b_inggris); ?></td> 
                           <td><?= _ent($nilai_raport_sma->b_inggris2); ?></td> 
                           <td><?= _ent($nilai_raport_sma->b_inggris3); ?></td> 
                           <td><?= _ent($nilai_raport_sma->b_inggris4); ?></td> 
                           <td><?= _ent($nilai_raport_sma->b_indonesia); ?></td> 
                           <td><?= _ent($nilai_raport_sma->b_indonesia2); ?></td> 
                           <td><?= _ent($nilai_raport_sma->b_indonesia3); ?></td> 
                           <td><?= _ent($nilai_raport_sma->b_indonesia4); ?></td> 
                           <td><?= _ent($nilai_raport_sma->ipa); ?></td> 
                           <td><?= _ent($nilai_raport_sma->ipa2); ?></td> 
                           <td><?= _ent($nilai_raport_sma->ipa3); ?></td> 
                           <td><?= _ent($nilai_raport_sma->ipa4); ?></td> 
                           <td><?= _ent($nilai_raport_sma->ips); ?></td> 
                           <td><?= _ent($nilai_raport_sma->ips2); ?></td> 
                           <td><?= _ent($nilai_raport_sma->ips3); ?></td> 
                           <td><?= _ent($nilai_raport_sma->ips4); ?></td> 
                           <td><?= _ent($nilai_raport_sma->matematika); ?></td> 
                           <td><?= _ent($nilai_raport_sma->matematika2); ?></td> 
                           <td><?= _ent($nilai_raport_sma->matematika3); ?></td> 
                           <td><?= _ent($nilai_raport_sma->matematika4); ?></td> 
                           <td>
                              <?php if (!empty($nilai_raport_sma->file_raport)): ?>
                                <?php if (is_image($nilai_raport_sma->file_raport)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport; ?>">
                                  <img src="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport; ?>" class="image-responsive" alt="image nilai_raport_sma" title="file_raport nilai_raport_sma" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport; ?>">
                                   <img src="<?= get_icon_file($nilai_raport_sma->file_raport); ?>" class="image-responsive image-icon" alt="image nilai_raport_sma" title="file_raport <?= $nilai_raport_sma->file_raport; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td>
                              <?php if (!empty($nilai_raport_sma->file_raport2)): ?>
                                <?php if (is_image($nilai_raport_sma->file_raport2)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport2; ?>">
                                  <img src="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport2; ?>" class="image-responsive" alt="image nilai_raport_sma" title="file_raport2 nilai_raport_sma" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport2; ?>">
                                   <img src="<?= get_icon_file($nilai_raport_sma->file_raport2); ?>" class="image-responsive image-icon" alt="image nilai_raport_sma" title="file_raport2 <?= $nilai_raport_sma->file_raport2; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td>
                              <?php if (!empty($nilai_raport_sma->file_raport3)): ?>
                                <?php if (is_image($nilai_raport_sma->file_raport3)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport3; ?>">
                                  <img src="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport3; ?>" class="image-responsive" alt="image nilai_raport_sma" title="file_raport3 nilai_raport_sma" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport3; ?>">
                                   <img src="<?= get_icon_file($nilai_raport_sma->file_raport3); ?>" class="image-responsive image-icon" alt="image nilai_raport_sma" title="file_raport3 <?= $nilai_raport_sma->file_raport3; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td>
                              <?php if (!empty($nilai_raport_sma->file_raport4)): ?>
                                <?php if (is_image($nilai_raport_sma->file_raport4)): ?>
                                <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport4; ?>">
                                  <img src="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport4; ?>" class="image-responsive" alt="image nilai_raport_sma" title="file_raport4 nilai_raport_sma" width="40px">
                                </a>
                                <?php else: ?>
                                  <a href="<?= BASE_URL . 'uploads/siswa_sma/raport/' . $nilai_raport_sma->file_raport4; ?>">
                                   <img src="<?= get_icon_file($nilai_raport_sma->file_raport4); ?>" class="image-responsive image-icon" alt="image nilai_raport_sma" title="file_raport4 <?= $nilai_raport_sma->file_raport4; ?>" width="40px"> 
                                 </a>
                                <?php endif; ?>
                              <?php endif; ?>
                           </td>
                            
                           <td width="200">
                            
                                                              <?php is_allowed('nilai_raport_sma_view', function() use ($nilai_raport_sma){?>
                                 <a href="<?= site_url('administrator/nilai_raport_sma/single_pdf/' .$nilai_raport_sma->id_nilai_raport_sma); ?>" class="label-default"><i class="fa fa-file-pdf-o"></i> <?= cclang('PDF') ?>
                              <a href="<?= site_url('administrator/nilai_raport_sma/view/' . $nilai_raport_sma->id_nilai_raport_sma); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?>
                              <?php }) ?>
                              <?php is_allowed('nilai_raport_sma_update', function() use ($nilai_raport_sma){?>
                              <a href="<?= site_url('administrator/nilai_raport_sma/edit/' . $nilai_raport_sma->id_nilai_raport_sma); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('nilai_raport_sma_delete', function() use ($nilai_raport_sma){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/nilai_raport_sma/delete/' . $nilai_raport_sma->id_nilai_raport_sma); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                               <?php }) ?>

                           </td>                        </tr>
                      <?php endforeach; ?>
                      <?php if ($nilai_raport_sma_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Nilai Raport Sma data is not available
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
                            <option <?= $this->input->get('f') == 'id_siswa' ? 'selected' :''; ?> value="id_siswa">Id Siswa</option>
                           <option <?= $this->input->get('f') == 'b_inggris' ? 'selected' :''; ?> value="b_inggris">B Inggris</option>
                           <option <?= $this->input->get('f') == 'b_inggris2' ? 'selected' :''; ?> value="b_inggris2">B Inggris2</option>
                           <option <?= $this->input->get('f') == 'b_inggris3' ? 'selected' :''; ?> value="b_inggris3">B Inggris3</option>
                           <option <?= $this->input->get('f') == 'b_inggris4' ? 'selected' :''; ?> value="b_inggris4">B Inggris4</option>
                           <option <?= $this->input->get('f') == 'b_indonesia' ? 'selected' :''; ?> value="b_indonesia">B Indonesia</option>
                           <option <?= $this->input->get('f') == 'b_indonesia2' ? 'selected' :''; ?> value="b_indonesia2">B Indonesia2</option>
                           <option <?= $this->input->get('f') == 'b_indonesia3' ? 'selected' :''; ?> value="b_indonesia3">B Indonesia3</option>
                           <option <?= $this->input->get('f') == 'b_indonesia4' ? 'selected' :''; ?> value="b_indonesia4">B Indonesia4</option>
                           <option <?= $this->input->get('f') == 'ipa' ? 'selected' :''; ?> value="ipa">Ipa</option>
                           <option <?= $this->input->get('f') == 'ipa2' ? 'selected' :''; ?> value="ipa2">Ipa2</option>
                           <option <?= $this->input->get('f') == 'ipa3' ? 'selected' :''; ?> value="ipa3">Ipa3</option>
                           <option <?= $this->input->get('f') == 'ipa4' ? 'selected' :''; ?> value="ipa4">Ipa4</option>
                           <option <?= $this->input->get('f') == 'ips' ? 'selected' :''; ?> value="ips">Ips</option>
                           <option <?= $this->input->get('f') == 'ips2' ? 'selected' :''; ?> value="ips2">Ips2</option>
                           <option <?= $this->input->get('f') == 'ips3' ? 'selected' :''; ?> value="ips3">Ips3</option>
                           <option <?= $this->input->get('f') == 'ips4' ? 'selected' :''; ?> value="ips4">Ips4</option>
                           <option <?= $this->input->get('f') == 'matematika' ? 'selected' :''; ?> value="matematika">Matematika</option>
                           <option <?= $this->input->get('f') == 'matematika2' ? 'selected' :''; ?> value="matematika2">Matematika2</option>
                           <option <?= $this->input->get('f') == 'matematika3' ? 'selected' :''; ?> value="matematika3">Matematika3</option>
                           <option <?= $this->input->get('f') == 'matematika4' ? 'selected' :''; ?> value="matematika4">Matematika4</option>
                           <option <?= $this->input->get('f') == 'file_raport' ? 'selected' :''; ?> value="file_raport">File Raport</option>
                           <option <?= $this->input->get('f') == 'file_raport2' ? 'selected' :''; ?> value="file_raport2">File Raport2</option>
                           <option <?= $this->input->get('f') == 'file_raport3' ? 'selected' :''; ?> value="file_raport3">File Raport3</option>
                           <option <?= $this->input->get('f') == 'file_raport4' ? 'selected' :''; ?> value="file_raport4">File Raport4</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/nilai_raport_sma');?>" title="<?= cclang('reset_filter'); ?>">
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
      var serialize_bulk = $('#form_nilai_raport_sma').serialize();

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
               document.location.href = BASE_URL + '/administrator/nilai_raport_sma/delete?' + serialize_bulk;      
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
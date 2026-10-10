
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('kuesioner_psb') ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator'); ?>">Pengaturan PSB</a></li>
      <li class="active"><?= cclang('kuesioner_psb') ?></li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row" >

      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-body ">

               <div class="row info-boxes">
                  <div class="col-md-3 col-sm-6">
                     <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-question-circle"></i></span>
                        <div class="info-box-content">
                           <span class="info-box-text">Total Pertanyaan</span>
                           <span class="info-box-number"><?= $total_pertanyaan; ?></span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-6">
                     <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-list-ol"></i></span>
                        <div class="info-box-content">
                           <span class="info-box-text">Tampil di halaman ini</span>
                           <span class="info-box-number"><?= $web_psb_pertanyaan_counts; ?></span>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="box box-widget widget-user-2">
                  <div class="widget-user-header ">
                     <div class="row pull-right">
                        <?php is_allowed('web_psb_pertanyaan_add', function(){?>
                        <a class="btn btn-flat btn-primary" title="<?= cclang('add_button'); ?> <?= cclang('kuesioner_psb') ?>" href="<?= site_url('administrator/web_psb_pertanyaan/add'); ?>"><i class="fa fa-plus"></i> <?= cclang('add_button'); ?></a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="Kuesioner PSB">
                     </div>
                     <h3 class="widget-user-username"><?= cclang('kuesioner_psb') ?></h3>
                     <h5 class="widget-user-desc">Pertanyaan kuesioner pendaftaran (step 5) — diisi calon siswa di form web PSB</h5>
                  </div>

                  <form name="form_web_psb_pertanyaan" id="form_web_psb_pertanyaan" action="<?= base_url('administrator/web_psb_pertanyaan/index'); ?>">

                  <div class="table-responsive">
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                          <th width="60">No Urut</th>
                          <th>Pertanyaan</th>
                          <th width="60">Step</th>
                          <th width="120">Tipe Input</th>
                          <th width="100">Jenjang</th>
                          <th width="70">PPSBB</th>
                          <th width="70">Jawaban</th>
                          <th width="140">Min/Max Input</th>
                          <th width="160">Action</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_web_psb_pertanyaan">
                     <?php foreach($web_psb_pertanyaans as $web_psb_pertanyaan): ?>
                        <tr>
                           <td><?= _ent($web_psb_pertanyaan->no_urut); ?></td>
                           <td>
                              <?= character_limiter(_ent($web_psb_pertanyaan->pertanyaan), 100); ?>
                              <?php if ((int) $web_psb_pertanyaan->jumlah_item > 0): ?>
                                 <span class="label label-info"><?= (int) $web_psb_pertanyaan->jumlah_item; ?> jawaban</span>
                              <?php endif; ?>
                           </td>
                           <td><?= _ent($web_psb_pertanyaan->step); ?></td>
                           <td>
                              <?php if ($web_psb_pertanyaan->tipe_input == 'check'): ?>
                                 <span class="label label-warning">check</span>
                              <?php else: ?>
                                 <span class="label label-primary">radio</span>
                              <?php endif; ?>
                           </td>
                           <td><?= strtoupper(_ent($web_psb_pertanyaan->jenjang)); ?></td>
                           <td>
                              <?php if ($web_psb_pertanyaan->is_ppsbb == 1): ?>
                                 <span class="label label-success">Ya</span>
                              <?php else: ?>
                                 <span class="label label-default">-</span>
                              <?php endif; ?>
                           </td>
                           <td><?= (int) $web_psb_pertanyaan->jumlah_item; ?></td>
                           <td><?= _ent($web_psb_pertanyaan->min_input); ?> / <?= _ent($web_psb_pertanyaan->max_input); ?></td>
                           <td>
                              <?php is_allowed('web_psb_pertanyaan_view', function() use ($web_psb_pertanyaan){?>
                                 <a href="<?= site_url('administrator/web_psb_pertanyaan/view/' . $web_psb_pertanyaan->id_step_pertanyaan); ?>" class="label-default" title="Detail & jawaban"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('web_psb_pertanyaan_update', function() use ($web_psb_pertanyaan){?>
                                 <a href="<?= site_url('administrator/web_psb_pertanyaan/edit/' . $web_psb_pertanyaan->id_step_pertanyaan); ?>" class="label-default" title="<?= cclang('update_button'); ?>"><i class="fa fa-edit"></i></a>
                              <?php }) ?>
                              <?php is_allowed('web_psb_pertanyaan_delete', function() use ($web_psb_pertanyaan){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/web_psb_pertanyaan/delete/' . $web_psb_pertanyaan->id_step_pertanyaan); ?>" class="label-default remove-data" title="<?= cclang('remove_button'); ?>"><i class="fa fa-close"></i></a>
                              <?php }) ?>
                           </td>
                        </tr>
                      <?php endforeach; ?>
                      <?php if ($web_psb_pertanyaan_counts == 0) :?>
                         <tr>
                           <td colspan="9">Pertanyaan kuesioner tidak tersedia</td>
                         </tr>
                      <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>
               <hr>
               <div class="row">
                  <div class="col-md-8">
                     <div class="col-sm-3 padd-left-0 " >
                        <input type="text" class="form-control" name="q" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('q'); ?>">
                     </div>
                     <div class="col-sm-3 padd-left-0 " >
                        <select type="text" class="form-control chosen chosen-select" name="f" id="field" >
                           <option value=""><?= cclang('all'); ?></option>
                            <option <?= $this->input->get('f') == 'pertanyaan' ? 'selected' :''; ?> value="pertanyaan">Pertanyaan</option>
                            <option <?= $this->input->get('f') == 'step' ? 'selected' :''; ?> value="step">Step</option>
                            <option <?= $this->input->get('f') == 'jenjang' ? 'selected' :''; ?> value="jenjang">Jenjang</option>
                          </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 " >
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">Filter</button>
                     </div>
                     <div class="col-sm-1 padd-left-0 " >
                        <a class="btn btn-default btn-flat" name="reset" id="reset" href="<?= base_url('administrator/web_psb_pertanyaan');?>" title="<?= cclang('reset_filter'); ?>"><i class="fa fa-undo"></i></a>
                     </div>
                  </div>
                  </form>
                  <div class="col-md-4">
                     <div class="dataTables_paginate paging_simple_numbers pull-right" id="example2_paginate" >
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
            </div>
            <!--/box body -->
         </div>
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
          text: "Pertanyaan yang masih memiliki jawaban tidak bisa dihapus (hapus jawabannya dulu).",
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
  });
</script>

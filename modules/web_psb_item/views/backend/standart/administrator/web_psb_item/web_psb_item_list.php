
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Jawaban Kuesioner PSB <small>Detail Pertanyaan</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/web_psb_pertanyaan'); ?>">Kuesioner PSB</a></li>
      <li class="active">Jawaban</li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row" >

      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-body ">

               <div class="callout callout-info" style="margin-bottom:10px">
                  <h4>Pertanyaan #<?= _ent($pertanyaan->id_step_pertanyaan); ?></h4>
                  <p style="margin-bottom:0"><?= nl2br(_ent($pertanyaan->pertanyaan)); ?></p>
                  <small>Step <?= _ent($pertanyaan->step); ?> &middot; <?= strtoupper(_ent($pertanyaan->jenjang)); ?><?= $pertanyaan->is_ppsbb == 1 ? ' &middot; PPSBB' : ''; ?> &middot; tipe: <?= _ent($pertanyaan->tipe_input); ?> (min <?= _ent($pertanyaan->min_input); ?> / max <?= _ent($pertanyaan->max_input); ?>)</small>
               </div>

               <div class="box box-widget widget-user-2">
                  <div class="widget-user-header ">
                     <div class="row pull-right">
                        <?php is_allowed('web_psb_item_add', function() use ($pertanyaan){?>
                        <a class="btn btn-flat btn-primary" href="<?= site_url('administrator/web_psb_item/add?id_pertanyaan=' . $pertanyaan->id_step_pertanyaan); ?>"><i class="fa fa-plus"></i> <?= cclang('add_button'); ?></a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default" href="<?= site_url('administrator/web_psb_pertanyaan/view/' . $pertanyaan->id_step_pertanyaan); ?>"><i class="fa fa-arrow-left"></i> Kembali ke Pertanyaan</a>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="Jawaban">
                     </div>
                     <h3 class="widget-user-username">Daftar Jawaban</h3>
                     <h5 class="widget-user-desc">Pilihan jawaban yang tampil di web PSB <i class="label bg-yellow"><?= $web_psb_item_counts; ?> jawaban</i></h5>
                  </div>

                  <div class="table-responsive">
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                           <th width="50">No</th>
                           <th>Jawaban</th>
                           <th width="110">Isi Sendiri</th>
                           <th width="120">Action</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_web_psb_item">
                     <?php $n = 0; foreach($web_psb_items as $web_psb_item): $n++; ?>
                        <tr>
                           <td><?= $n; ?></td>
                           <td><?= _ent($web_psb_item->jawaban); ?></td>
                           <td>
                              <?php if ($web_psb_item->is_lainnya == 1): ?>
                                 <span class="label label-warning">Lainnya (isian bebas)</span>
                              <?php else: ?>
                                 <span class="label label-default">-</span>
                              <?php endif; ?>
                           </td>
                           <td>
                              <?php is_allowed('web_psb_item_update', function() use ($web_psb_item){?>
                                 <a href="<?= site_url('administrator/web_psb_item/edit/' . $web_psb_item->id_web_psb_item . '?id_pertanyaan=' . $web_psb_item->id_web_psb_pertanyaan); ?>" class="label-default" title="Edit"><i class="fa fa-edit"></i></a>
                              <?php }) ?>
                              <?php is_allowed('web_psb_item_delete', function() use ($web_psb_item){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/web_psb_item/delete/' . $web_psb_item->id_web_psb_item); ?>" class="label-default remove-data" title="Hapus"><i class="fa fa-close"></i></a>
                              <?php }) ?>
                           </td>
                        </tr>
                      <?php endforeach; ?>
                      <?php if ($web_psb_item_counts == 0) :?>
                         <tr>
                           <td colspan="4">Belum ada jawaban</td>
                         </tr>
                      <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>
               <hr>
               <div class="row">
                  <div class="col-md-8">
                     <form name="form_web_psb_item" id="form_web_psb_item" action="<?= base_url('administrator/web_psb_item/index'); ?>" method="get">
                        <input type="hidden" name="id_pertanyaan" value="<?= _ent($pertanyaan->id_step_pertanyaan); ?>">
                        <div class="col-sm-4 padd-left-0 " >
                           <input type="text" class="form-control" name="q" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('q'); ?>">
                        </div>
                        <div class="col-sm-2 padd-left-0 " >
                           <button type="submit" class="btn btn-flat" name="sbtn" value="Apply">Filter</button>
                        </div>
                        <div class="col-sm-1 padd-left-0 " >
                           <a class="btn btn-default btn-flat" href="<?= site_url('administrator/web_psb_item/?id_pertanyaan=' . $pertanyaan->id_step_pertanyaan); ?>" title="<?= cclang('reset_filter'); ?>"><i class="fa fa-undo"></i></a>
                        </div>
                     </form>
                  </div>
                  <div class="col-md-4">
                     <div class="dataTables_paginate paging_simple_numbers pull-right" id="example2_paginate" >
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
            </div>
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
          text: "Jawaban akan dihapus dari pilihan web PSB!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Ya, hapus!",
          cancelButtonText: "Batal",
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


<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Kuesioner PSB <small>Detail Pertanyaan</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/web_psb_pertanyaan'); ?>">Kuesioner PSB</a></li>
        <li class="active">Detail</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row" >
     <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-body ">

               <div class="box box-widget widget-user-2">
                  <div class="widget-user-header ">
                     <div class="row pull-right">
                        <?php is_allowed('web_psb_pertanyaan_update', function() use ($web_psb_pertanyaan){?>
                        <a class="btn btn-flat btn-warning" title="Edit pertanyaan" href="<?= site_url('administrator/web_psb_pertanyaan/edit/' . $web_psb_pertanyaan->id_step_pertanyaan); ?>"><i class="fa fa-edit"></i> Edit Pertanyaan</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/view.png" alt="Detail">
                     </div>
                     <h3 class="widget-user-username">Pertanyaan #<?= _ent($web_psb_pertanyaan->id_step_pertanyaan); ?></h3>
                     <h5 class="widget-user-desc">Step <?= _ent($web_psb_pertanyaan->step); ?> &middot; <?= strtoupper(_ent($web_psb_pertanyaan->jenjang)); ?><?= $web_psb_pertanyaan->is_ppsbb == 1 ? ' &middot; Jalur PPSBB' : ''; ?></h5>
                     <hr>
                  </div>

                  <div class="form-horizontal">
                    <div class="form-group ">
                        <label class="col-sm-2 control-label">Pertanyaan</label>
                        <div class="col-sm-9">
                           <p style="margin-top:5px"><?= nl2br(_ent($web_psb_pertanyaan->pertanyaan)); ?></p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label class="col-sm-2 control-label">Tipe Input</label>
                        <div class="col-sm-8">
                           <?= isset($tipe_input_list[$web_psb_pertanyaan->tipe_input]) ? $tipe_input_list[$web_psb_pertanyaan->tipe_input] : _ent($web_psb_pertanyaan->tipe_input); ?>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label class="col-sm-2 control-label">No Urut</label>
                        <div class="col-sm-8">
                           <?= _ent($web_psb_pertanyaan->no_urut); ?>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label class="col-sm-2 control-label">Min / Max Input</label>
                        <div class="col-sm-8">
                           <?= _ent($web_psb_pertanyaan->min_input); ?> / <?= _ent($web_psb_pertanyaan->max_input); ?>
                        </div>
                    </div>
                  </div>

                  <hr>
                  <h4 style="margin-left:15px">Daftar Jawaban</h4>
                  <div class="row" style="margin:0 0 10px 0">
                     <div class="col-md-12" style="margin-left:10px">
                        <?php is_allowed('web_psb_item_add', function() use ($web_psb_pertanyaan){?>
                        <a class="btn btn-flat btn-primary btn-sm" href="<?= site_url('administrator/web_psb_item/add?id_pertanyaan=' . $web_psb_pertanyaan->id_step_pertanyaan); ?>"><i class="fa fa-plus"></i> Tambah Jawaban</a>
                        <?php }) ?>
                     </div>
                  </div>
                  <div class="table-responsive" style="margin:0 15px">
                     <table class="table table-bordered table-striped dataTable">
                        <thead>
                           <tr>
                              <th width="50">No</th>
                              <th>Jawaban</th>
                              <th width="90">Isi Sendiri</th>
                              <th width="120">Action</th>
                           </tr>
                        </thead>
                        <tbody>
                        <?php $n = 0; foreach($items as $item): $n++; ?>
                           <tr>
                              <td><?= $n; ?></td>
                              <td><?= _ent($item->jawaban); ?></td>
                              <td>
                                 <?php if ($item->is_lainnya == 1): ?>
                                    <span class="label label-warning">Lainnya (isian bebas)</span>
                                 <?php else: ?>
                                    <span class="label label-default">-</span>
                                 <?php endif; ?>
                              </td>
                              <td>
                                 <?php is_allowed('web_psb_item_update', function() use ($item){?>
                                    <a href="<?= site_url('administrator/web_psb_item/edit/' . $item->id_web_psb_item . '?id_pertanyaan=' . $item->id_web_psb_pertanyaan); ?>" class="label-default" title="Edit"><i class="fa fa-edit"></i></a>
                                 <?php }) ?>
                                 <?php is_allowed('web_psb_item_delete', function() use ($item){?>
                                    <a href="javascript:void(0);" data-href="<?= site_url('administrator/web_psb_item/delete/' . $item->id_web_psb_item); ?>" class="label-default remove-item" title="Hapus"><i class="fa fa-close"></i></a>
                                 <?php }) ?>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                        <?php if (empty($items)): ?>
                           <tr><td colspan="4">Belum ada jawaban</td></tr>
                        <?php endif; ?>
                        </tbody>
                     </table>
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
    $('.remove-item').click(function(){
      var url = $(this).attr('data-href');
      swal({
          title: "<?= cclang('are_you_sure'); ?>",
          text: "Jawaban akan dihapus!",
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

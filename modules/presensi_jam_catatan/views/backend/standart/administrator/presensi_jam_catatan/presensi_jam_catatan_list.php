
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      <?= cclang('presensi_jam_catatan'); ?><small><?= cclang('list_all'); ?></small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?= cclang('presensi_jam_catatan'); ?></li>
   </ol>
</section>
<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning">
            <div class="box-body">
               <div class="box box-widget widget-user-2">
                  <div class="widget-user-header">
                     <div class="row pull-right">
                        <?php is_allowed('presensi_jam_catatan_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', cclang('presensi_jam_catatan')); ?>" href="<?= site_url('administrator/presensi_jam_catatan/add'); ?>"><i class="fa fa-plus-square-o"></i> <?= cclang('add_new_button', cclang('presensi_jam_catatan')); ?></a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <h3 class="widget-user-username"><?= cclang('presensi_jam_catatan'); ?></h3>
                     <h5 class="widget-user-desc"><?= cclang('list_all', cclang('presensi_jam_catatan')); ?> <i class="label bg-yellow"><?= $presensi_jam_catatan_counts; ?> <?= cclang('items'); ?></i></h5>
                  </div>

                  <form name="form_presensi_jam_catatan" id="form_presensi_jam_catatan" action="<?= base_url('administrator/presensi_jam_catatan/index'); ?>">
                  <div class="table-responsive">
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                           <th width="5"><input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all"></th>
                           <th><?= cclang('id_jam_catatan'); ?></th>
                           <th><?= cclang('jam'); ?></th>
                           <th><?= cclang('is_lainnya'); ?></th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_presensi_jam_catatan">
                     <?php foreach($presensi_jam_catatans as $presensi_jam_catatan): ?>
                        <tr>
                           <td width="5"><input type="checkbox" class="flat-red check" name="id[]" value="<?= $presensi_jam_catatan->id_jam_catatan; ?>"></td>
                           <td><?= $presensi_jam_catatan->id_jam_catatan; ?></td>
                           <td><?= _ent($presensi_jam_catatan->jam); ?></td>
                           <td><?= (int)$presensi_jam_catatan->is_lainnya === 1 ? '<span class="label label-info">Lainnya</span>' : '-'; ?></td>
                           <td width="200">
                              <?php is_allowed('presensi_jam_catatan_view', function() use ($presensi_jam_catatan){?>
                              <a href="<?= site_url('administrator/presensi_jam_catatan/view/'.$presensi_jam_catatan->id_jam_catatan); ?>" class="btn btn-flat btn-default btn_action" title="View"><i class="fa fa-eye"></i></a>
                              <?php }) ?>
                              <?php is_allowed('presensi_jam_catatan_update', function() use ($presensi_jam_catatan){?>
                              <a href="<?= site_url('administrator/presensi_jam_catatan/edit/'.$presensi_jam_catatan->id_jam_catatan); ?>" class="btn btn-flat btn-warning btn_action" title="Edit"><i class="fa fa-edit"></i></a>
                              <?php }) ?>
                              <?php is_allowed('presensi_jam_catatan_delete', function() use ($presensi_jam_catatan){?>
                              <a href="javascript:void(0);" data-href="<?= site_url('administrator/presensi_jam_catatan/remove/'.$presensi_jam_catatan->id_jam_catatan); ?>" class="btn btn-flat btn-danger btn_action" id="btn_remove" title="Hapus"><i class="fa fa-trash"></i></a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     </tbody>
                  </table>
                  </div>
                  </form>
                  <div class="row">
                     <div class="col-md-12 text-center">
                        <?= $pagination; ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
<script>
$(document).ready(function(){
   $('#btn_remove').on('click', function(e){
      e.preventDefault();
      var url = $(this).data('href');
      swal({
         title: "Hapus jam ini?",
         text: "Jam akan dihapus permanen dari master data.",
         type: "warning",
         showCancelButton: true,
         confirmButtonColor: "#DD6B55",
         confirmButtonText: "Ya, hapus!",
         cancelButtonText: "Batal",
         closeOnConfirm: true
      }, function(isConfirm){
         if (isConfirm) {
            window.location.href = url;
         }
      });
      return false;
   });
});
</script>


<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Pengaturan Diskon SPP        <small><?= cclang('list', ['Pengaturan Diskon SPP']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengaturan Diskon SPP</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row" >
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-body ">
                    <div class="row" >
                        <div class="col-sm-8">
                            <div class="form-inline">
                                <div class="form-group">
                                    <?= form_input('', '', ['name' => 'q', 'id' => 'filter_q', 'class' => 'form-control input-sm', 'placeholder' => cclang('filter') . ' ' . cclang('pengaturan_diskon_spp')]); ?>
                                </div>
                                <div class="form-group">
                                    <select class="form-control input-sm" name="f" id="filter_field">
                                        <option value="jenjang">Jenjang</option>
                                    </select>
                                </div>
                                <button class="btn btn-sm btn-default" id="btn_filter"><i class="fa fa-search"></i> <?= cclang('filter'); ?></button>
                            </div>
                        </div>
                        <div class="col-sm-4 labs-pull-right">
                            <div class="btn-group labs-pull-right">
                                <?php is_allowed('pengaturan_diskon_spp_add', function(){?>
                                <a class="btn btn-flat btn-success btn_add" id="btn_add" title="<?= cclang('add', ['Pengaturan Diskon SPP']); ?> (Ctrl+n)" href="<?= site_url('administrator/pengaturan_diskon_spp/add'); ?>"><i class="fa fa-plus"></i> <?= cclang('add', ['Pengaturan Diskon SPP']); ?></a>
                                <?php }) ?>
                            </div>
                        </div>
                    </div>
                    <hr>
                                                
                    <table class="labs-table dataTable" id="pengaturan_diskon_spp_table">
                     <thead>
                        <tr>
                           <th width="30"><input type="checkbox" class="flat-red" id="check_all" name="check_all" title="check all"></th>
                           <th>Jenjang</th>
                           <th>Tingkatan</th>
                           <th>Min. Bulan</th>
                           <th>Persen Diskon</th>
                           <th>Status</th>
                           <th width="220" class="labs-text-center">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_pengaturan_diskon_spp">
                     <?php if($pengaturan_diskon_spp_counts > 0): ?>
                     <?php foreach($pengaturan_diskon_spps as $pengaturan_diskon_spp): ?>
                        <tr>
                           <td class="labs-text-center">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $pengaturan_diskon_spp->id_setting; ?>">
                           </td>
                           <td><span class="labs-badge labs-badge--primary labs-badge--solid"><?= _ent(strtoupper($pengaturan_diskon_spp->jenjang)); ?></span></td>
                           <td>
                              <?php if(!empty($pengaturan_diskon_spp->id_tingkatan)):
                                 $ting = $this->model_pengaturan_diskon_spp->db->query("select label from tingkatan_".$pengaturan_diskon_spp->jenjang." where id_tingkatan = '".(int) $pengaturan_diskon_spp->id_tingkatan."'")->row(); ?>
                                 <span class="labs-fw-semi"><?= _ent($ting ? $ting->label : $pengaturan_diskon_spp->id_tingkatan); ?></span>
                              <?php else: ?>
                                 <span class="labs-text-light">Semua tingkatan</span>
                              <?php endif; ?>
                           </td>
                           <td><?= _ent($pengaturan_diskon_spp->min_pembayaran); ?> bulan</td>
                           <td><span class="labs-fw-semi"><?= _ent($pengaturan_diskon_spp->persen_diskon); ?>%</span></td>
                           <td>
                              <?php if($pengaturan_diskon_spp->aktif == '1'): ?>
                                 <span class="labs-badge labs-badge--success">Aktif</span>
                              <?php else: ?>
                                 <span class="labs-badge labs-badge--danger">Nonaktif</span>
                              <?php endif; ?>
                           </td>
                           <td class="labs-text-center">
                              <?php is_allowed('pengaturan_diskon_spp_view', function() use ($pengaturan_diskon_spp){?>
                              <a class="btn btn-flat btn-default btn_sm" title="view pengaturan_diskon_spp" href="<?= site_url('administrator/pengaturan_diskon_spp/view/'.$pengaturan_diskon_spp->id_setting); ?>"><i class="fa fa-eye"></i></a>
                              <?php }) ?>
                              <?php is_allowed('pengaturan_diskon_spp_update', function() use ($pengaturan_diskon_spp){?>
                              <a class="btn btn-flat btn-info btn_sm" title="edit pengaturan_diskon_spp" href="<?= site_url('administrator/pengaturan_diskon_spp/edit/'.$pengaturan_diskon_spp->id_setting); ?>"><i class="fa fa-edit"></i></a>
                              <?php }) ?>
                              <?php is_allowed('pengaturan_diskon_spp_delete', function() use ($pengaturan_diskon_spp){?>
                              <a class="btn btn-flat btn-danger btn_sm btn_delete" title="delete pengaturan_diskon_spp" href="<?= site_url('administrator/pengaturan_diskon_spp/delete/'.$pengaturan_diskon_spp->id_setting); ?>"><i class="fa fa-trash"></i></a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                     <?php else: ?>
                        <tr>
                           <td colspan="7" class="labs-text-center labs-text-light">Tidak ada data pengaturan diskon SPP</td>
                        </tr>
                     <?php endif; ?>
                     </tbody>
                  </table>
                  <div class="row">
                     <div class="col-sm-5"><?= $pagination; ?></div>
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

      $('#btn_filter').click(function(){
         var q = $('#filter_q').val();
         var f = $('#filter_field').val();
         window.location.href = '<?= site_url('administrator/pengaturan_diskon_spp'); ?>?q=' + encodeURIComponent(q) + '&f=' + f;
         return false;
      });

      $('#check_all').click(function(){
         $('.check').prop('checked', $(this).prop('checked'));
      });

      $('.btn_delete').click(function(){
         var href = $(this).attr('href');
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
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
              window.location.href = href;
            }
          });
         return false;
      });

   }); /*end doc ready*/
</script>


<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Pengaturan Diskon SPP        <small><?= cclang('detail', ['Pengaturan Diskon SPP']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/pengaturan_diskon_spp'); ?>">Pengaturan Diskon SPP</a></li>
        <li class="active"><?= cclang('detail'); ?></li>
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
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="User Avatar">
                            </div>
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username">Pengaturan Diskon SPP</h3>
                            <h5 class="widget-user-desc"><?= cclang('detail', ['Pengaturan Diskon SPP']); ?></h5>
                            <hr>
                        </div>
                        <div class="box-body">
                                          
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Jenjang </label>

                        <div class="col-sm-8">
                           <?= _ent(strtoupper($pengaturan_diskon_spp->jenjang)); ?>
                        </div>
                    </div>
                                        
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Tingkatan </label>

                        <div class="col-sm-8">
                           <?php if(!empty($pengaturan_diskon_spp->id_tingkatan)):
                              $ting = $this->model_pengaturan_diskon_spp->db->query("select label from tingkatan_".$pengaturan_diskon_spp->jenjang." where id_tingkatan = '".(int) $pengaturan_diskon_spp->id_tingkatan."'")->row();
                              echo _ent($ting ? $ting->label : $pengaturan_diskon_spp->id_tingkatan);
                           else: ?>
                              Semua tingkatan
                           <?php endif; ?>
                        </div>
                    </div>
                                        
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Minimal Bulan </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_diskon_spp->min_pembayaran); ?> bulan
                        </div>
                    </div>
                                        
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Persen Diskon </label>

                        <div class="col-sm-8">
                           <?= _ent($pengaturan_diskon_spp->persen_diskon); ?>%
                        </div>
                    </div>
                                        
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status </label>

                        <div class="col-sm-8">
                           <?= ($pengaturan_diskon_spp->aktif == '1') ? 'Aktif' : 'Nonaktif'; ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pengaturan_diskon_spp_update', function() use ($pengaturan_diskon_spp){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pengaturan_diskon_spp (Ctrl+e)" href="<?= site_url('administrator/pengaturan_diskon_spp/edit/'.$pengaturan_diskon_spp->id_setting); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pengaturan Diskon SPP']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pengaturan_diskon_spp/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pengaturan Diskon SPP']); ?></a>
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

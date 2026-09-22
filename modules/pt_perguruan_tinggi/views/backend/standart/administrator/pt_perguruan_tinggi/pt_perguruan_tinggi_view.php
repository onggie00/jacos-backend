
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Perguruan Tinggi      <small><?= cclang('detail', ['Perguruan Tinggi']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/pt_perguruan_tinggi'); ?>">Perguruan Tinggi</a></li>
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
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/view.png" alt="User Avatar">
                     </div>
                     <!-- /.widget-user-image -->
                     <h3 class="widget-user-username">Perguruan Tinggi</h3>
                     <h5 class="widget-user-desc">Detail Perguruan Tinggi</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_pt_perguruan_tinggi" id="form_pt_perguruan_tinggi" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_perguruan_tinggi->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Perguruan Tinggi </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_perguruan_tinggi->nama_pt); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Singkatan </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_perguruan_tinggi->inisial); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Provinsi </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_perguruan_tinggi->provinces_name); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kota </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_perguruan_tinggi->regencies_name); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label"> Logo PTN </label>
                        <div class="col-sm-8">
                             <?php if (is_image($pt_perguruan_tinggi->logo_ptn)): ?>
                              <a class="fancybox" rel="group" href="<?= BASE_URL . 'uploads/pt_perguruan_tinggi/' . $pt_perguruan_tinggi->logo_ptn; ?>">
                                <img src="<?= BASE_URL . 'uploads/pt_perguruan_tinggi/' . $pt_perguruan_tinggi->logo_ptn; ?>" class="image-responsive" alt="image pt_perguruan_tinggi" title="logo_ptn pt_perguruan_tinggi" width="40px">
                              </a>
                              <?php else: ?>
                              <label>
                                <a href="<?= BASE_URL . 'administrator/file/download/pt_perguruan_tinggi/' . $pt_perguruan_tinggi->logo_ptn; ?>">
                                 <img src="<?= get_icon_file($pt_perguruan_tinggi->logo_ptn); ?>" class="image-responsive" alt="image pt_perguruan_tinggi" title="logo_ptn <?= $pt_perguruan_tinggi->logo_ptn; ?>" width="40px"> 
                               <?= $pt_perguruan_tinggi->logo_ptn ?>
                               </a>
                               </label>
                              <?php endif; ?>
                        </div>
                    </div>
                                       
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Deskripsi </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_perguruan_tinggi->deskripsi); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Terakhir Di Update </label>

                        <div class="col-sm-8">
                           <?= _ent($pt_perguruan_tinggi->updated_at); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('pt_perguruan_tinggi_update', function() use ($pt_perguruan_tinggi){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit pt_perguruan_tinggi (Ctrl+e)" href="<?= site_url('administrator/pt_perguruan_tinggi/edit/'.$pt_perguruan_tinggi->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Pt Perguruan Tinggi']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/pt_perguruan_tinggi/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Pt Perguruan Tinggi']); ?></a>
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

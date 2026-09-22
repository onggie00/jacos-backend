
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Catatan Invoice      <small><?= cclang('detail', ['Catatan Invoice']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/catatan_invoice'); ?>">Catatan Invoice</a></li>
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
                     <h3 class="widget-user-username">Catatan Invoice</h3>
                     <h5 class="widget-user-desc">Detail Catatan Invoice</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_catatan_invoice" id="form_catatan_invoice" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id Catatan Invoice </label>

                        <div class="col-sm-8">
                           <?= _ent($catatan_invoice->id_catatan_invoice); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Catatan Mohon Dibaca </label>

                        <div class="col-sm-8">
                           <?= _ent($catatan_invoice->catatan_mohon_dibaca); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('catatan_invoice_update', function() use ($catatan_invoice){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit catatan_invoice (Ctrl+e)" href="<?= site_url('administrator/catatan_invoice/edit/'.$catatan_invoice->id_catatan_invoice); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Catatan Invoice']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/catatan_invoice/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Catatan Invoice']); ?></a>
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

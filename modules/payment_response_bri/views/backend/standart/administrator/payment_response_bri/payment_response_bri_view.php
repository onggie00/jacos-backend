
<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Payment Response Bri      <small><?= cclang('detail', ['Payment Response Bri']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/payment_response_bri'); ?>">Payment Response Bri</a></li>
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
                     <h3 class="widget-user-username">Payment Response Bri</h3>
                     <h5 class="widget-user-desc">Detail Payment Response Bri</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_payment_response_bri" id="form_payment_response_bri" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id </label>

                        <div class="col-sm-8">
                           <?= _ent($payment_response_bri->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Briva No </label>

                        <div class="col-sm-8">
                           <?= _ent($payment_response_bri->briva_no); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Bill Amount </label>

                        <div class="col-sm-8">
                           <?= _ent($payment_response_bri->bill_amount); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Transaction Date </label>

                        <div class="col-sm-8">
                           <?= _ent($payment_response_bri->transaction_date); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Journal Id </label>

                        <div class="col-sm-8">
                           <?= _ent($payment_response_bri->journal_id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Terminal Id </label>

                        <div class="col-sm-8">
                           <?= _ent($payment_response_bri->terminal_id); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('payment_response_bri_update', function() use ($payment_response_bri){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit payment_response_bri (Ctrl+e)" href="<?= site_url('administrator/payment_response_bri/edit/'.$payment_response_bri->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Payment Response Bri']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/payment_response_bri/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Payment Response Bri']); ?></a>
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


<script type="text/javascript">
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>
      Daftar Sekolah Asal TK      <small><?= cclang('detail', ['Daftar Sekolah Asal TK']); ?> </small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a  href="<?= site_url('administrator/list_sekolah_tk'); ?>">Daftar Sekolah Asal TK</a></li>
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
                     <h3 class="widget-user-username">Daftar Sekolah Asal TK</h3>
                     <h5 class="widget-user-desc">Detail Daftar Sekolah Asal TK</h5>
                     <hr>
                  </div>

                 
                  <div class="form-horizontal" name="form_list_sekolah_tk" id="form_list_sekolah_tk" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Id List Sekolah Tk </label>

                        <div class="col-sm-8">
                           <?= _ent($list_sekolah_tk->id_list_sekolah_tk); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Nama Sekolah </label>

                        <div class="col-sm-8">
                           <?= _ent($list_sekolah_tk->nama_sekolah); ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('list_sekolah_tk_update', function() use ($list_sekolah_tk){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit list_sekolah_tk (Ctrl+e)" href="<?= site_url('administrator/list_sekolah_tk/edit/'.$list_sekolah_tk->id_list_sekolah_tk); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['List Sekolah Tk']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/list_sekolah_tk/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['List Sekolah Tk']); ?></a>
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

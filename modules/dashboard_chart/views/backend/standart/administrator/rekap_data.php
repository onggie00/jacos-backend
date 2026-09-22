<style type="text/css">
   .widget-user-header {
      padding-left: 20px !important;
   }
</style>

<link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.css">

<section class="content-header">
    <h1>
        <?= cclang('dashboard') ?>
        <small>
            
        <?= cclang('chart') ?>
        </small>
    </h1>
    <ol class="breadcrumb">
        <li>
            <a href="#">
                <i class="fa fa-dashboard">
                </i>
                <?= cclang('home') ?>
            </a>
        </li>
        <li>
            <?= cclang('dashboard') ?>
        </li>
        <li class="active">
            <?= 'Rekap siswa aktif' ?>
        </li>
    </ol>
</section>

<section class="content">
   
     <div class="row">
        <div class="col-md-6 col-xs-12">
          <!-- BAR CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Siswa Aktif</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="chart" id="chart-siswa-aktif" style="height: 300px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <div class="col-md-6 col-xs-12">
          <!-- BAR CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Guru </h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="chart" id="chart-guru" style="height: 300px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col (RIGHT) -->
      </div>
      <!-- /.row -->

      <div class="row">
        <div class="col-xs-12">
          <!-- BAR CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Pegawai</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="chart" id="chart-pegawai" style="height: 300px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
</section>
<!-- /.content -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.min.js"></script>
<script>
  $(function () {
    "use strict";
    //Chart Total siswa aktif SD / SMP / SMA
    $.post("<?= site_url('administrator/dashboard_chart/chart_data_siswa'); ?>",
    {
      //jenjang: "sd",
    },
    function(data, status){
      //BAR CHART
      var bar = new Morris.Bar({
        element: 'chart-siswa-aktif',
        resize: true,
        data: JSON.parse(data),
        barColors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
        xkey: 'tahun_ajaran',
        ykeys: ['total_siswa_aktif_sd','total_siswa_aktif_smp','total_siswa_aktif_sma', 'total_siswa_aktif_ft'],
        labels: ['Total Siswa SD', 'Total Siswa SMP', 'Total Siswa SMA', 'Total Siswa FT'],
        hideHover: 'auto'
      });
    });

    //Chart Total guru SD / SMP / SMA
    $.post("<?= site_url('administrator/dashboard_chart/chart_data_guru'); ?>",
    {
      //jenjang: "sd",
    },
    function(data, status){
      //BAR CHART
      var bar = new Morris.Bar({
        element: 'chart-guru',
        resize: true,
        data: JSON.parse(data),
        barColors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
        xkey: 'y',
        ykeys: ['total_guru_sd', 'total_guru_smp', 'total_guru_sma', 'total_guru_ft'],
        labels: ['SD', 'SMP', 'SMA', 'FT'],
        hideHover: 'auto'
      });
    });

    //Chart Total pegawai
    $.post("<?= site_url('administrator/dashboard_chart/chart_data_pegawai'); ?>",
    {
      jenjang: "sma",
    },
    function(data, status){
      //BAR CHART
      var bar = new Morris.Bar({
        element: 'chart-pegawai',
        resize: true,
        data: JSON.parse(data),
        barColors: ['#7B83EB', '#04699D', '#00A65A', '#50CA5D', '#EA4335', '#C5221F'],
        xkey: 'posisi',
        ykeys: ['total_pegawai'],
        labels: ['Total Pegawai'],
        hideHover: 'auto'
      });
    });

  });
</script>
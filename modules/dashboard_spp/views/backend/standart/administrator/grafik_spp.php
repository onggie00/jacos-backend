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
            <?= 'Grafik spp' ?>
        </li>
    </ol>
</section>

<section class="content">
   
     <div class="row">
        <div class="col-md-6">
          <!-- BAR CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"> Grafik SPP</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="form-group row">
                <form id="form_filter" method="post">
                  <input id="jenjang" type="hidden" name="jenjang" value="<?= $jenjang; ?>">
                  <div class="col-md-4">
                    <label>Start Date</label>
                    <!-- <select class="form-control" name="start_date" id="start_date"></select> -->
                    <input type="date" name="start_date" id="start_date" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label>End Date</label>
                    <!-- <select class="form-control" name="end_date" id="end_date"></select> -->
                    <input type="date" name="end_date" id="end_date" class="form-control">
                  </div>
                  <div class="col-md-4" style="margin-top: 25px;">
                    <button id="btn_filter" class="btn btn-primary">Filter</button>
                    <button id="btn_export" class="btn btn-success">Export XLS</button>
                  </div>
                </form>
              </div>
              <div class="chart" id="chart-spp" style="height: 300px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col (RIGHT) -->
      </div>
      <!-- /.row -->

</section>
<!-- /.content -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.min.js"></script>
<script>
  $(function () {
    let startYear = 1800;
    let endYear = new Date().getFullYear()+1;
    for (i = endYear; i > startYear; i--)
    {
      $('#start_date').append($('<option />').val(i).html(i));
      $('#end_date').append($('<option />').val(i).html(i));
    }
    "use strict";
    //Chart Total siswa aktif SD / SMP / SMA
    $.post("<?= site_url('administrator/dashboard_spp/chart_spp'); ?>",
    {
      jenjang: $('#jenjang').val(),
    },
    function(data, status){
      //BAR CHART
      var bar = new Morris.Bar({
        element: 'chart-spp',
        resize: true,
        data: JSON.parse(data),
        barColors: ['#00A65A', '#4285F4', '#EA4335'],
        xkey: 'keterangan',
        ykeys: ['total_spp', 'total_spp_belum_dibayar', 'total_spp_lunas'],
        labels: ['Semua Status ', 'Belum Dibayar ', 'Lunas '],
        hideHover: 'false'
      });
    });

    $("#btn_filter").click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "<?= site_url('administrator/dashboard_spp/chart_spp'); ?>",
            data: {
                jenjang: $("#jenjang").val(),
                start_date: $("#start_date").val(),
                end_date: $("#end_date").val(),
            },
            success: function(data) {
                //alert('ok');
                $("#chart-spp").empty();
                //BAR CHART
                var bar = new Morris.Bar({
                  element: 'chart-spp',
                  resize: true,
                  data: JSON.parse(data),
                  barColors: ['#00A65A', '#4285F4', '#EA4335'],
                  xkey: 'keterangan',
                  ykeys: ['total_spp', 'total_spp_belum_dibayar', 'total_spp_lunas'],
                  labels: ['Semua Status ', 'Belum Dibayar ', 'Lunas '],
                  hideHover: 'false'
                });
            },
            error: function(data) {
                alert('error');
            }
        });
    });

    $("#btn_export").click(function(e) {
      e.preventDefault();
      window.location.href = '<?= base_url('administrator/dashboard_spp/export_chart_spp'); ?>?jenjang='+$("#jenjang").val()+'&start_date='+$("#start_date").val()+'&end_date='+$("#end_date").val();
        /*e.preventDefault();
        $.ajax({
            type: "POST",
            url: "<?= site_url('administrator/dashboard_spp/export_chart_spp'); ?>",
            data: {
                jenjang: $("#jenjang").val(),
                start_date: $("#start_date").val(),
                end_date: $("#end_date").val(),
            },
            success: function(data) {
                //alert('ok');
            },
            error: function(data) {
                alert('error');
            }
        });*/
    });

  });
</script>
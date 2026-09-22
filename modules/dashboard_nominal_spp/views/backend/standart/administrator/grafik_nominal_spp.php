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
      <div class="col-md-8 col-md-offset-2">
          <!-- BAR CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"> Total Nominal SPP (Keseluruhan)</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="form-group row">
                <form id="form_filter" method="post">
                  <input id="jenjang_overall" type="hidden" name="jenjang" value="sd">
                  <div class="col-md-4">
                    <label>Start Date</label>
                    <!-- <select class="form-control" name="start_date" id="start_date"></select> -->
                    <input type="date" name="start_date" id="start_date_overall" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label>End Date</label>
                    <!-- <select class="form-control" name="end_date" id="end_date"></select> -->
                    <input type="date" name="end_date" id="end_date_overall" class="form-control">
                  </div>
                  <div class="col-md-4" style="margin-top: 25px;">
                    <button id="btn_filter_overall" class="btn btn-primary">Filter</button>
                  </div>
                </form>
              </div>
              <div class="chart" id="chart-nominal-overall" style="height: 300px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
    </div>
     <div class="row">
        <div class="col-md-6">
          <!-- BAR CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"> Total Nominal SPP SD</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="form-group row">
                <form id="form_filter" method="post">
                  <input id="jenjang_sd" type="hidden" name="jenjang" value="sd">
                  <div class="col-md-4">
                    <label>Start Date</label>
                    <!-- <select class="form-control" name="start_date" id="start_date"></select> -->
                    <input type="date" name="start_date" id="start_date_sd" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label>End Date</label>
                    <!-- <select class="form-control" name="end_date" id="end_date"></select> -->
                    <input type="date" name="end_date" id="end_date_sd" class="form-control">
                  </div>
                  <div class="col-md-4" style="margin-top: 25px;">
                    <button id="btn_filter_sd" class="btn btn-primary">Filter</button>
                    <button id="btn_export_sd" class="btn btn-success">Export XLS</button>
                  </div>
                </form>
              </div>
              <div class="chart" id="chart-nominal-sd" style="height: 300px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col (RIGHT) -->
        <div class="col-md-6">
          <!-- BAR CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"> Total Nominal SPP SMP</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="form-group row">
                <form id="form_filter" method="post">
                  <input id="jenjang_smp" type="hidden" name="jenjang" value="smp">
                  <div class="col-md-4">
                    <label>Start Date</label>
                    <!-- <select class="form-control" name="start_date" id="start_date"></select> -->
                    <input type="date" name="start_date" id="start_date_smp" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label>End Date</label>
                    <!-- <select class="form-control" name="end_date" id="end_date"></select> -->
                    <input type="date" name="end_date" id="end_date_smp" class="form-control">
                  </div>
                  <div class="col-md-4" style="margin-top: 25px;">
                    <button id="btn_filter_smp" class="btn btn-primary">Filter</button>
                    <button id="btn_export_smp" class="btn btn-success">Export XLS</button>
                  </div>
                </form>
              </div>
              <div class="chart" id="chart-nominal-smp" style="height: 300px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col (RIGHT) -->
        <div class="col-md-6">
          <!-- BAR CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"> Total Nominal SPP SMA</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="form-group row">
                <form id="form_filter" method="post">
                  <input id="jenjang_sma" type="hidden" name="jenjang" value="sma">
                  <div class="col-md-4">
                    <label>Start Date</label>
                    <!-- <select class="form-control" name="start_date" id="start_date"></select> -->
                    <input type="date" name="start_date" id="start_date_sma" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label>End Date</label>
                    <!-- <select class="form-control" name="end_date" id="end_date"></select> -->
                    <input type="date" name="end_date" id="end_date_sma" class="form-control">
                  </div>
                  <div class="col-md-4" style="margin-top: 25px;">
                    <button id="btn_filter_sma" class="btn btn-primary">Filter</button>
                    <button id="btn_export_sma" class="btn btn-success">Export XLS</button>
                  </div>
                </form>
              </div>
              <div class="chart" id="chart-nominal-sma" style="height: 300px;"></div>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col (RIGHT) -->
        <div class="col-md-6">
          <!-- BAR CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"> Total Nominal SPP FT</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body chart-responsive">
              <div class="form-group row">
                <form id="form_filter" method="post">
                  <input id="jenjang_ft" type="hidden" name="jenjang" value="ft">
                  <div class="col-md-4">
                    <label>Start Date</label>
                    <!-- <select class="form-control" name="start_date" id="start_date"></select> -->
                    <input type="date" name="start_date" id="start_date_ft" class="form-control">
                  </div>
                  <div class="col-md-4">
                    <label>End Date</label>
                    <!-- <select class="form-control" name="end_date" id="end_date"></select> -->
                    <input type="date" name="end_date" id="end_date_ft" class="form-control">
                  </div>
                  <div class="col-md-4" style="margin-top: 25px;">
                    <button id="btn_filter_ft" class="btn btn-primary">Filter</button>
                    <button id="btn_export_ft" class="btn btn-success">Export XLS</button>
                  </div>
                </form>
              </div>
              <div class="chart" id="chart-nominal-ft" style="height: 300px;"></div>
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
    "use strict";
    //Chart Total Nominal siswa aktif SD
    $.post("<?= site_url('administrator/dashboard_nominal_spp/chart_nominal_spp'); ?>",
    {
      jenjang: $('#jenjang_sd').val(),
    },
    function(data, status){
      //BAR CHART
      var bar = new Morris.Bar({
        element: 'chart-nominal-sd',
        resize: true,
        data: JSON.parse(data),
        barColors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
        xkey: 'keterangan',
        ykeys: ['total_nominal_spp', 'total_nominal_spp_belum_bayar', 'total_nominal_spp_lunas'],
                  labels: ['Semua Status ', 'Belum Dibayar ', 'Lunas '],
        hideHover: 'false'
      });
    });

    $("#btn_filter_sd").click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "<?= site_url('administrator/dashboard_nominal_spp/chart_nominal_spp'); ?>",
            data: {
                jenjang: $("#jenjang_sd").val(),
                start_date: $("#start_date_sd").val(),
                end_date: $("#end_date_sd").val(),
            },
            success: function(data) {
                //alert('ok');
                $("#chart-nominal-sd").empty();
                //BAR CHART
                var bar = new Morris.Bar({
                  element: 'chart-nominal-sd',
                  resize: true,
                  data: JSON.parse(data),
                  barColors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
                  xkey: 'keterangan',
                  ykeys: ['total_nominal_spp', 'total_nominal_spp_belum_bayar', 'total_nominal_spp_lunas'],
                  labels: ['Semua Status ', 'Belum Dibayar ', 'Lunas '],
                  hideHover: 'false'
                });
            },
            error: function(data) {
                alert('error');
            }
        });
    });

    $("#btn_export_sd").click(function(e) {
      e.preventDefault();
      window.location.href = '<?= base_url('administrator/dashboard_nominal_spp/export_chart_spp'); ?>?jenjang='+$("#jenjang_sd").val()+'&start_date='+$("#start_date_sd").val()+'&end_date='+$("#end_date_sd").val();
    });

    //Chart Total Nominal siswa aktif SMP
    $.post("<?= site_url('administrator/dashboard_nominal_spp/chart_nominal_spp'); ?>",
    {
      jenjang: $('#jenjang_smp').val(),
    },
    function(data, status){
      //BAR CHART
      var bar = new Morris.Bar({
        element: 'chart-nominal-smp',
        resize: true,
        data: JSON.parse(data),
        barColors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
        xkey: 'keterangan',
        ykeys: ['total_nominal_spp', 'total_nominal_spp_belum_bayar', 'total_nominal_spp_lunas'],
                  labels: ['Semua Status ', 'Belum Dibayar ', 'Lunas '],
        hideHover: 'false'
      });
    });

    $("#btn_filter_smp").click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "<?= site_url('administrator/dashboard_nominal_spp/chart_nominal_spp'); ?>",
            data: {
                jenjang: $("#jenjang_smp").val(),
                start_date: $("#start_date_smp").val(),
                end_date: $("#end_date_smp").val(),
            },
            success: function(data) {
                //alert('ok');
                $("#chart-nominal-smp").empty();
                //BAR CHART
                var bar = new Morris.Bar({
                  element: 'chart-nominal-smp',
                  resize: true,
                  data: JSON.parse(data),
                  barColors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
                  xkey: 'keterangan',
                  ykeys: ['total_nominal_spp', 'total_nominal_spp_belum_bayar', 'total_nominal_spp_lunas'],
                  labels: ['Semua Status ', 'Belum Dibayar ', 'Lunas '],
                  hideHover: 'false'
                });
            },
            error: function(data) {
                alert('error');
            }
        });
    });

    $("#btn_export_smp").click(function(e) {
      e.preventDefault();
      window.location.href = '<?= base_url('administrator/dashboard_nominal_spp/export_chart_spp'); ?>?jenjang='+$("#jenjang_smp").val()+'&start_date='+$("#start_date_smp").val()+'&end_date='+$("#end_date_smp").val();
    });

    //Chart Total Nominal siswa aktif SMA
    $.post("<?= site_url('administrator/dashboard_nominal_spp/chart_nominal_spp'); ?>",
    {
      jenjang: $('#jenjang_sma').val(),
    },
    function(data, status){
      //BAR CHART
      var bar = new Morris.Bar({
        element: 'chart-nominal-sma',
        resize: true,
        data: JSON.parse(data),
        barColors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
        xkey: 'keterangan',
        ykeys: ['total_nominal_spp', 'total_nominal_spp_belum_bayar', 'total_nominal_spp_lunas'],
                  labels: ['Semua Status ', 'Belum Dibayar ', 'Lunas '],
        hideHover: 'false'
      });
    });

    $("#btn_filter_sma").click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "<?= site_url('administrator/dashboard_nominal_spp/chart_nominal_spp'); ?>",
            data: {
                jenjang: $("#jenjang_sma").val(),
                start_date: $("#start_date_sma").val(),
                end_date: $("#end_date_sma").val(),
            },
            success: function(data) {
                //alert('ok');
                $("#chart-nominal-sma").empty();
                //BAR CHART
                var bar = new Morris.Bar({
                  element: 'chart-nominal-sma',
                  resize: true,
                  data: JSON.parse(data),
                  barColors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
                  xkey: 'keterangan',
                  ykeys: ['total_nominal_spp', 'total_nominal_spp_belum_bayar', 'total_nominal_spp_lunas'],
                  labels: ['Semua Status ', 'Belum Dibayar ', 'Lunas '],
                  hideHover: 'false'
                });
            },
            error: function(data) {
                alert('error');
            }
        });
    });

    $("#btn_export_sma").click(function(e) {
      e.preventDefault();
      window.location.href = '<?= base_url('administrator/dashboard_nominal_spp/export_chart_spp'); ?>?jenjang='+$("#jenjang_sma").val()+'&start_date='+$("#start_date_sma").val()+'&end_date='+$("#end_date_sma").val();
    });

    //Chart Total Nominal siswa aktif FT
    $.post("<?= site_url('administrator/dashboard_nominal_spp/chart_nominal_spp'); ?>",
    {
      jenjang: $('#jenjang_ft').val(),
    },
    function(data, status){
      //BAR CHART
      var bar = new Morris.Bar({
        element: 'chart-nominal-ft',
        resize: true,
        data: JSON.parse(data),
        barColors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
        xkey: 'keterangan',
        ykeys: ['total_nominal_spp', 'total_nominal_spp_belum_bayar', 'total_nominal_spp_lunas'],
        labels: ['Semua Status ', 'Belum Dibayar ', 'Lunas '],
        hideHover: 'false'
      });
    });

    $("#btn_filter_ft").click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "<?= site_url('administrator/dashboard_nominal_spp/chart_nominal_spp'); ?>",
            data: {
                jenjang: $("#jenjang_ft").val(),
                start_date: $("#start_date_ft").val(),
                end_date: $("#end_date_ft").val(),
            },
            success: function(data) {
                //alert('ok');
                $("#chart-nominal-ft").empty();
                //BAR CHART
                var bar = new Morris.Bar({
                  element: 'chart-nominal-ft',
                  resize: true,
                  data: JSON.parse(data),
                  barColors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
                  xkey: 'keterangan',
                  ykeys: ['total_nominal_spp', 'total_nominal_spp_belum_bayar', 'total_nominal_spp_lunas'],
                  labels: ['Semua Status ', 'Belum Dibayar ', 'Lunas '],
                  hideHover: 'false'
                });
            },
            error: function(data) {
                alert('error');
            }
        });
    });

    $("#btn_export_ft").click(function(e) {
      e.preventDefault();
      window.location.href = '<?= base_url('administrator/dashboard_nominal_spp/export_chart_spp'); ?>?jenjang='+$("#jenjang_ft").val()+'&start_date='+$("#start_date_ft").val()+'&end_date='+$("#end_date_ft").val();
    });

    //Chart Total Nominal SPP Overall
    $.post("<?= site_url('administrator/dashboard_nominal_spp/chart_nominal_spp_overall'); ?>",
    {
      jenjang: $('#jenjang_overall').val(),
    },
    function(data, status){
      //DONUT CHART
      var donut = new Morris.Donut({
        element: 'chart-nominal-overall',
        resize: true,
        colors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
        data: JSON.parse(data),
        /* data: [
          {label: "Download Sales", value: 12},
          {label: "In-Store Sales", value: 30},
          {label: "Mail-Order Sales", value: 20}
        ], */
        hideHover: 'auto'
      });
    });

    $("#btn_filter_overall").click(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "<?= site_url('administrator/dashboard_nominal_spp/chart_nominal_spp_overall'); ?>",
            data: {
                jenjang: $("#jenjang_overall").val(),
                start_date: $("#start_date_overall").val(),
                end_date: $("#end_date_overall").val(),
            },
            success: function(data) {
                //alert('ok');
                $("#chart-nominal-overall").empty();
                //DONUT CHART
                var donut = new Morris.Donut({
                  element: 'chart-nominal-overall',
                  resize: true,
                  colors: ['#00A65A', '#4285F4', '#EA4335', '#FBBC04'],
                  data: JSON.parse(data),
                  /* data: [
                    {label: "Download Sales", value: 12},
                    {label: "In-Store Sales", value: 30},
                    {label: "Mail-Order Sales", value: 20}
                  ], */
                  hideHover: 'auto'
                });
            },
            error: function(data) {
                alert('error');
            }
        });
    });

  });
</script>
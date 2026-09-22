<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
</head>
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
            <?= 'Grafik presensi' ?>
        </li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
        <!-- BAR CHART -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Grafik Presensi per Kelas</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body chart-responsive">
            <div style="border: 1px solid #1E40AF; padding: 10px 20px;width: 200px;border-radius: 10px">Total Siswa : <span id="total_siswa"><b><?= $total_siswa; ?></b></span></div>
                <div class="form-group row">
                    <div class="col-md-2">
                        <label>Start Date</label>
                        <!-- <select class="form-control" name="start_date" id="start_date"></select> -->
                        <input type="text" name="start_date" id="start_date" class="form-control" value="<?= (!empty($this->session->userdata('start_date_unit'))) ?date("Y-m-d", strtotime($this->session->userdata('start_date_unit'))) : date("Y-m-d"); ?>">
                    </div>
                    <div class="col-md-2">
                        <label>End Date</label>
                        <!-- <select class="form-control" name="end_date" id="end_date"></select> -->
                        <input type="text" name="end_date" id="end_date" class="form-control" value="<?= (!empty($this->session->userdata('end_date_unit'))) ? date("Y-m-d", strtotime($this->session->userdata('end_date_unit'))) : date("Y-m-d"); ?>">
                    </div>
                    <div class="col-md-2">
                        <label>Unit</label>
                        <select class="form-control" name="unit" id="unit">
                        <option value="">-- Pilih unit dahulu --</option>
                        <option value="ft">FT</option>
                        <option value="sma">SMA</option>
                        <option value="smp">SMP</option>
                        <option value="sd">SD</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Tingkat</label>
                        <select class="form-control" name="tingkatan" id="tingkatan">
                        <option value="">-- Pilih unit dahulu --</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Kelas</label>
                        <select class="form-control" name="kelas" id="kelas">
                        <option value="">-- Pilih tingkatan dahulu --</option>
                        </select>
                    </div>
                    <div class="col-md-2" style="margin-top: 25px;">
                        <button id="btn_filter" class="btn btn-primary">Filter</button>
                        <button id="btn_export" class="btn btn-success hidden">Export XLS</button>
                    </div>
                    
                </div>

                <div class="chart" id="chart-presensi-kelas" style="height: 300px;"></div>
                <div>
                    <div class="col-md-12" id="table_view">
                    
                    </div>
                </div>
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
    $('#start_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        minViewMode: 0
    });
    $('#end_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        minViewMode: 0
    });


    let chart = new Morris.Bar({
        element: 'chart-presensi-kelas',
        data: [''],
        xkey: 'bulan',
        ykeys: ['total_hadir_dan_presensi', 'total_terlambat', 'total_alfa', 'total_sakit', 'total_izin'],
        labels: ['Hadir dan Presensi', 'Terlambat', 'Sakit', 'Izin' , 'Alfa'],
        barColors: ['#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6f42c1'],
        hideHover: 'auto',
        resize: true
    });

    $('#unit').change(function () {
        const unit = $(this).val();
        $.ajax({
            url: '<?= site_url('administrator/dashboard_presensi/get_tingkatan') ?>',
            method: 'POST',
            data: { unit: unit },
            dataType: 'json',
            success: function (response) {
                $('#tingkatan').empty();
                $('#tingkatan').append('<option value="">-- Pilih unit dahulu --</option>');
                $.each(response, function (index, value) {
                    $('#tingkatan').append('<option value="' + value.id_tingkatan + '">' + value.label + '</option>');
                });
            }
        });
    });

    $('#tingkatan').change(function () {
        const unit = $('#unit').val();
        const tingkatan = $('#tingkatan').val();
        $.ajax({
            url: '<?= site_url('administrator/dashboard_presensi/get_kelas') ?>',
            method: 'POST',
            data: { unit: unit , tingkatan: tingkatan},
            dataType: 'json',
            success: function (response) {
                $('#kelas').empty();
                $('#kelas').append('<option value="">-- Pilih tingkatan dahulu --</option>');
                $.each(response, function (index, value) {
                    $('#kelas').append('<option value="' + value.id_kelas + '">' + value.label + '</option>');
                });
            }
        });
    });

        $('#btn_filter').click(function () {
            const start = $('#start_date').val();
            const end = $('#end_date').val();
            const unit = $('#unit').val();
            const tingkatan = $('#tingkatan').val();
            const kelas = $('#kelas').val();

            if (!start || !end) {
                alert("Silakan isi tanggal mulai dan tanggal selesai.");
                return;
            }
            if (!tingkatan) {
                alert("Tingkat tidak boleh kosong.");
                return;
            }
            if (!tingkatan) {
                alert("Kelas tidak boleh kosong.");
                return;
            }

            $.ajax({
                url: '<?= site_url('administrator/dashboard_presensi/chart_presensi_kelas') ?>',
                method: 'POST',
                data: { start_date: start, end_date: end , unit: unit , tingkatan: tingkatan , kelas: kelas},
                dataType: 'json',
                success: function (response) {
                    chart.setData(response);
                    $('#total_siswa').html(response[0].total_siswa+" Siswa");
                    let htmlContent = '';
                    response.forEach(function(item, index) {
                        htmlContent += `
                        <div class="col-md-3">
                            <table class="table table-bordered table-striped mb-4">
                                <thead class="thead-dark">
                                    <tr>
                                        <th colspan="2">Bulan: ${item.bulan}</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        for (const key in item) {
                            if (item.hasOwnProperty(key) && key !== 'bulan' && key !== 'total_siswa' && key !== 'total_hari_aktif' && key !== 'tingkat_presensi_digital') {
                                // Format key: Ubah underscore menjadi spasi dan kapital awal
                                const formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                                const total_hari_presensi = item.total_siswa * item.total_hari_aktif;
                                let percentage = item[key] / total_hari_presensi * 100;
                                if (key === 'total_terlambat') {
                                    percentage = item[key] / item.total_hadir_dan_presensi * 100;
                                }
                                htmlContent += `<tr><td>${formattedKey}</td><td>${item[key]} (${percentage.toFixed(2)}%)</td></tr>`;
                            }
                            else if(key === 'total_siswa') {
                                const total_hari_presensi = item.total_siswa * item.total_hari_aktif;
                                htmlContent += `<tr><td>Total Siswa</td><td>${item[key]}</td></tr>`;
                                htmlContent += `<tr><td>Total Hari Presensi</td><td>${item.total_hari_aktif} (${total_hari_presensi})</td></tr>`;
                            }
                            else if(key === 'tingkat_presensi_digital') {
                                htmlContent += `<tr><td>Tingkat Presensi Digital</td><td>${item.tingkat_presensi_digital}%</td></tr>`;
                            }
                        }

                        htmlContent += `
                                </tbody>
                            </table>
                            </div>
                        `;
                    });

                    document.getElementById('table_view').innerHTML = htmlContent;
                    // Or with jQuery
                    $('#table_view').html(htmlContent);
                }
            });

        });
    </script>

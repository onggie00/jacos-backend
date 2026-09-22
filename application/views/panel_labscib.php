<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Install Labscib Apps</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #4e73df, #224abe);
      min-height: 100vh;
    }
    .card {
      border-radius: 16px;
    }
    .logo {
      max-height: 90px;
    }
    .btn-store {
      padding: 12px;
      font-size: 16px;
      font-weight: 500;
    }
  </style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-5">

      <div class="card shadow-lg border-0 p-4 text-center">

        <!-- LOGO -->
        <div class="mb-3">
          <img src="<?= base_url("uploads/logo_labschool_cibubur_new.png"); ?>" class="logo">
        </div>

        <!-- TEXT -->
        <h5 class="fw-bold mb-2">Install Aplikasi Labscib</h5>
        <p class="text-muted mb-4">
          Belum punya aplikasi? Download sekarang melalui platform pilihan kamu
        </p>

        <!-- BUTTON -->
        <div class="d-grid gap-3">

          <a href="https://play.google.com/store/apps/details?id=com.labschool.labscib"
             class="btn btn-success btn-store">
            📱 Download di Android
          </a>

          <a href="https://apps.apple.com/id/app/labscib-app/id1666453482?l=id"
             class="btn btn-outline-dark btn-store">
            🍎 Download di iOS
          </a>

        </div>

      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
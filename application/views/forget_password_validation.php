<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Validation</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
    body {
      background: linear-gradient(135deg, #1cc88a, #13855c);
      min-height: 100vh;
    }
    .card {
      border-radius: 15px;
    }
    .logo {
      max-height: 80px;
    }
  </style>
</head>

<body class="d-flex align-items-center justify-content-center">

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-5">

      <div class="card shadow-lg border-0 p-4">

        <div class="text-center mb-3">
          <img src="<?= base_url("uploads/logo_labschool_cibubur_new.png"); ?>" class="logo mb-2">
          <h5 class="fw-bold">Account Validation</h5>
          <small class="text-muted">
            <?= $validation_field === 'npp' ? 'Masukkan NPP untuk reset password' : 'Masukkan tanggal lahir siswa untuk reset password'; ?>
          </small>
        </div>

        <div class="alert alert-info text-center">
          Account: <b><?= html_escape($role); ?></b>
        </div>

        <?php if (!empty($failed)) { ?>
          <div class="alert alert-danger" role="alert">
            <?= html_escape($failed); ?>
          </div>
        <?php } ?>

        <!-- FORM -->
        <?php echo form_open('forget_password/reset_password', array('id' => 'formReset')); ?>
          <input type="hidden" name="validation_token" value="<?= html_escape($validation_token); ?>">

          <?php if ($validation_field === 'npp') { ?>
            <div class="mb-3">
              <label class="form-label">NPP</label>
              <input type="text" name="npp" class="form-control" required placeholder="Masukkan NPP">
            </div>
          <?php } else { ?>
            <div class="mb-3">
              <label class="form-label">Date of Birth (Student)</label>
              <input type="date" name="tgl_lahir" class="form-control" required placeholder="YYYY-MM-DD">
            </div>
          <?php } ?>

          <div class="d-grid">
            <button type="submit" id="btnSubmit" class="btn btn-success">
              <span class="text">Reset Password</span>
              <span class="spinner-border spinner-border-sm d-none"></span>
            </button>
          </div>

        </form>

      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$("#formReset").submit(function(){
  $("#btnSubmit .text").text("Processing...");
  $("#btnSubmit .spinner-border").removeClass("d-none");
  $("#btnSubmit").prop("disabled", true);
});
</script>

</body>
</html>

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
          <h5 class="fw-bold">Account Validation for Teacher & Staff</h5>
          <small class="text-muted">Please enter your birth date to reset your password</small>
        </div>

        <div class="alert alert-info text-center">
          Account: <b><?= $role; ?></b>
        </div>

        <!-- FORM -->
        <?php echo form_open('forget_password/reset_password', ['id' => 'formReset']); ?>

          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="tgl_lahir" class="form-control" required placeholder="YYYY-MM-DD ">
            <input type="hidden" name="email" value="<?= $email; ?>">
            <input type="hidden" name="role" value="<?= $role; ?>">
          </div>

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
const tgl_lahir_server = "<?= $tgl_lahir; ?>"; // dari backend (format Y-m-d)

// Normalisasi tanggal: hasilkan string YYYY-MM-DD dari berbagai format input
function normalizeDate(str) {
  if (!str) return '';
  let s = String(str).trim();
  // buang bagian waktu jika ada (YYYY-MM-DD HH:MM:SS atau YYYY-MM-DDTHH:MM:SS)
  s = s.split(' ')[0].split('T')[0];
  // dd/mm/yyyy → yyyy-mm-dd
  let m = s.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
  if (m) s = m[3] + '-' + m[2] + '-' + m[1];
  // dd-mm-yyyy → yyyy-mm-dd
  m = s.match(/^(\d{2})-(\d{2})-(\d{4})$/);
  if (m) s = m[3] + '-' + m[2] + '-' + m[1];
  return s;
}

$("#formReset").submit(function(e){
  let input_tgl = normalizeDate($("input[name='tgl_lahir']").val());
  let server_tgl = normalizeDate(tgl_lahir_server);

  // validasi tanggal lahir
  if(input_tgl === '' || input_tgl !== server_tgl){
    e.preventDefault(); // STOP submit
    $(".alert-danger-custom").remove();

    $(".card").prepend(`
      <div class="alert alert-danger alert-dismissible fade show alert-danger-custom">
        Tanggal lahir tidak sesuai
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `);
    return false;
  }

  // kalau sesuai → lanjut submit + loading
  $("#btnSubmit .text").text("Processing...");
  $("#btnSubmit .spinner-border").removeClass("d-none");
  $("#btnSubmit").prop("disabled", true);
});
</script>

</body>
</html>
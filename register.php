<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Registration Page (v2)</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="AdminLTE-3.2.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="AdminLTE-3.2.0/dist/css/adminlte.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="../../index2.html" class="h1"><b>To-Do</b>List</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Register a new membership</p>

      <?php 
      if (isset($_GET['pesan'])) {
          $pesan = $_GET['pesan'];
          $alert_style = 'alert alert-danger';
          $message = '';

          if ($pesan == "password_mismatch") {
              $message = "Konfirmasi password tidak cocok!";
          } else if ($pesan == "user_exists") {
              $message = "Username atau Email sudah terdaftar.";
          } else if ($pesan == "incomplete") {
              $message = "Semua kolom wajib diisi.";
          } else if ($pesan == "register_failed") {
              $message = "Registrasi gagal karena kesalahan server. Coba lagi!";
          }
          // Pesan Sukses (biasanya ditangani di index.php, tapi bisa juga di sini)
          // if ($pesan == "register_success") { ... }

          if (!empty($message)) {
             echo "<div class='$alert_style' role='alert'>$message</div>";
          }
      }
      ?>
      <form action="config/register_process.php" method="post"> 
        
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Username" name="username" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Email" name="email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Retype password" name="retype_password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="agreeTerms" name="terms" value="agree" required>
              <label for="agreeTerms">
               I agree to the <a href="#">terms</a>
              </label>
            </div>
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Register</button>
          </div>
          </div>
      </form>

      <a href="index.php" class="text-center">I already have a membership</a>
    </div>
    </div></div>
<script src="AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
<script src="AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
</body>
</html>
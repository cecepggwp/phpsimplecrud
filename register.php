<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registration Page | To-Do List</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="AdminLTE-3.2.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="AdminLTE-3.2.0/dist/css/adminlte.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="index.php" class="h1"><b>To-Do</b>List</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Daftar Akun Baru</p>

      <?php 
      if (isset($_GET['pesan'])) {
          $pesan = $_GET['pesan'];
          if ($pesan == "password_mismatch") {
              echo "<div class='alert alert-danger'>Konfirmasi password tidak cocok!</div>";
          } else if ($pesan == "incomplete") {
              echo "<div class='alert alert-danger'>Mohon lengkapi semua kolom!</div>";
          } else if ($pesan == "user_exists") {
              echo "<div class='alert alert-warning'>Username atau Email sudah terdaftar!</div>";
          } else if ($pesan == "register_failed") {
              echo "<div class='alert alert-danger'>Terjadi kesalahan teknis. Coba lagi.</div>";
          }
      }
      ?>

      <form action="config/register_process.php" method="post">
        
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Nama Lengkap" name="nama_lengkap" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user-circle"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Nomor Induk (NIM/NIP)" name="nmr_induk" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-id-card"></span>
            </div>
          </div>
        </div>

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
          <input type="password" class="form-control" placeholder="Ketik ulang password" name="retype_password" required>
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
               Saya setuju dengan <a href="#">syarat & ketentuan</a>
              </label>
            </div>
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
          </div>
        </div>
      </form>

      <a href="index.php" class="text-center">Sudah punya akun? Login di sini</a>
    </div>
  </div>
</div>

<script src="AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
<script src="AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
</body>
</html>
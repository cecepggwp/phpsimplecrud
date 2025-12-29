<?php
include_once 'config/class-master.php';
$master = new MasterData();
$prodiList = $master->getProdi(); 
$mkList = $master->getMk(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrasi | To-Do List</title>
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
          if ($pesan == "incomplete") echo "<div class='alert alert-danger'>Lengkapi semua kolom!</div>";
          else if ($pesan == "user_exists") echo "<div class='alert alert-warning'>User sudah terdaftar!</div>";
          else if ($pesan == "register_failed") echo "<div class='alert alert-danger'>Gagal mendaftar.</div>";
          else if ($pesan == "password_mismatch") echo "<div class='alert alert-danger'>Password tidak cocok.</div>";
      }
      ?>

      <form action="config/register_process.php" method="post">
        
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Nama Lengkap" name="nama_lengkap" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user-circle"></span></div></div>
        </div>

        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Nomor Induk (NIM/NIP)" name="nmr_induk" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-id-card"></span></div></div>
        </div>

        <div class="input-group mb-3">
            <select name="role" id="roleSelect" class="form-control" onchange="toggleForm()" required>
                <option value="3" selected>Daftar Sebagai Mahasiswa</option>
                <option value="2">Daftar Sebagai Dosen</option>
            </select>
            <div class="input-group-append"><div class="input-group-text"><span class="fas fa-users"></span></div></div>
        </div>

        <div class="input-group mb-3">
            <select name="id_prodi" id="prodiSelect" class="form-control" onchange="filterMatkul()" required>
                <option value="" selected disabled>-- Pilih Program Studi --</option>
                <?php foreach($prodiList as $p): ?>
                    <option value="<?= $p['id_prodi'] ?>"><?= $p['nm_prodi'] ?></option>
                <?php endforeach; ?>
            </select>
            <div class="input-group-append"><div class="input-group-text"><span class="fas fa-university"></span></div></div>
        </div>

        <div class="form-group mb-3" id="matkulDiv" style="display:none;">
            <label>Mata Kuliah Ampuan (Bisa Pilih Banyak):</label>
            <div class="card p-2" style="max-height: 200px; overflow-y: auto;">
                <?php foreach($mkList as $m): ?>
                    <div class="form-check matkul-option" data-prodi="<?= $m['id_prodi'] ?>">
                        <input class="form-check-input" type="checkbox" name="id_mk[]" value="<?= $m['id_mk'] ?>" id="mk_<?= $m['id_mk'] ?>">
                        <label class="form-check-label" for="mk_<?= $m['id_mk'] ?>">
                            <?= $m['nm_mk'] ?>
                        </label>
                    </div>
                <?php endforeach; ?>
                <div id="noMatkulMsg" class="text-muted small" style="display:none;">Pilih Prodi terlebih dahulu.</div>
            </div>
        </div>

        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Username" name="username" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
        </div>

        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Email" name="email" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
        </div>

        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>

        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Ketik ulang password" name="retype_password" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>

        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="agreeTerms" name="terms" value="agree" required>
              <label for="agreeTerms">Setuju dengan <a href="#">syarat & ketentuan</a></label>
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

<script>
    function toggleForm() {
        var role = document.getElementById("roleSelect").value;
        var matkulDiv = document.getElementById("matkulDiv");
        if(role == "2") { 
            matkulDiv.style.display = "block";
            filterMatkul(); // Refresh filter
        } else {
            matkulDiv.style.display = "none";
        }
    }

    function filterMatkul() {
        var selectedProdi = document.getElementById("prodiSelect").value;
        var options = document.getElementsByClassName("matkul-option");
        var visibleCount = 0;

        for (var i = 0; i < options.length; i++) {
            var dataProdi = options[i].getAttribute("data-prodi");
            var checkbox = options[i].querySelector("input[type=checkbox]");

            if (selectedProdi && dataProdi == selectedProdi) {
                options[i].style.display = "block";
                visibleCount++;
            } else {
                options[i].style.display = "none";
                checkbox.checked = false; // Uncheck jika disembunyikan
            }
        }
        
        var msg = document.getElementById("noMatkulMsg");
        if(visibleCount === 0 && selectedProdi) {
             msg.style.display = "block";
             msg.innerText = "Tidak ada mata kuliah di prodi ini.";
        } else if (!selectedProdi) {
             msg.style.display = "block";
             msg.innerText = "Pilih Prodi untuk melihat Mata Kuliah.";
        } else {
             msg.style.display = "none";
        }
    }
    
    window.onload = function() { toggleForm(); };
</script>
</body>
</html>
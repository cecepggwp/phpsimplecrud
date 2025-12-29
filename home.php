<?php
session_start();
if(!isset($_SESSION["id"])){
	header("Location: index.php");
    exit();
}

include_once 'config/class-master.php';
$master = new MasterData();

$id_user_login = $_SESSION['id'];
$role_login    = $_SESSION['role'];

// Cek Kelengkapan Data (0=Lengkap, 1=Prodi Kosong, 2=Matkul Kosong)
$dataStatus = $master->checkUserDataStatus($id_user_login, $role_login);

// Ambil data untuk form update jika diperlukan
$prodiList = [];
$mkList = [];
if($dataStatus != 0 && $role_login != '1'){
    $prodiList = $master->getProdi();
    if($role_login == '2') $mkList = $master->getMk();
}
?>
<!doctype html>
<html lang="en">
	<head>
		<?php include 'template/header.php'; ?>
	</head>

	<body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-open bg-body-tertiary">
		<div class="app-wrapper">
			<?php include 'template/navbar.php'; ?>
			<?php include 'template/sidebar.php'; ?>

			<main class="app-main">
				<div class="app-content-header">
					<div class="container-fluid"></div>
				</div>

				<div class="app-content">
					<div class="container-fluid">
						
                        <?php if($dataStatus != 0 && $role_login != '1'): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning border-start border-warning border-4" role="alert">
                                    <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Data Akademik Belum Lengkap!</h4>
                                    
                                    <?php if($role_login == '2'): ?>
                                        <p>Halo Dosen, sistem mendeteksi Anda belum melengkapi Program Studi atau Mata Kuliah Ampuan.</p>
                                        
                                        <form action="proses/proses-update-data-dosen.php" method="POST" class="mt-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label>Program Studi:</label>
                                                    <select name="id_prodi" id="prodiSelect" class="form-select" onchange="filterMatkul()" required>
                                                        <option value="" selected disabled>-- Pilih Prodi --</option>
                                                        <?php foreach($prodiList as $p): ?>
                                                            <option value="<?= $p['id_prodi'] ?>"><?= $p['nm_prodi'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label>Mata Kuliah (Pilih Banyak):</label>
                                                    <div class="card p-2" style="max-height: 150px; overflow-y: auto;">
                                                        <?php foreach($mkList as $m): ?>
                                                            <div class="form-check matkul-option" data-prodi="<?= $m['id_prodi'] ?>" style="display:none;">
                                                                <input class="form-check-input" type="checkbox" name="id_mk[]" value="<?= $m['id_mk'] ?>" id="mk_home_<?= $m['id_mk'] ?>">
                                                                <label class="form-check-label" for="mk_home_<?= $m['id_mk'] ?>">
                                                                    <?= $m['nm_mk'] ?>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <div id="noMatkulMsg" class="text-muted small">Pilih Prodi terlebih dahulu.</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="submit" class="btn btn-dark w-100">Simpan</button>
                                                </div>
                                            </div>
                                        </form>

                                    <?php else: ?>
                                        <p>Anda belum memilih Program Studi.</p>
                                        <form action="proses/proses-update-prodi.php" method="POST" class="mt-3">
                                            <div class="input-group">
                                                <select name="id_prodi" class="form-select" required>
                                                    <option value="" selected disabled>-- Pilih Program Studi --</option>
                                                    <?php foreach($prodiList as $p): ?>
                                                        <option value="<?= $p['id_prodi'] ?>"><?= $p['nm_prodi'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-dark">Simpan</button>
                                            </div>
                                        </form>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>

                        <?php else: ?>
                        <div class="row">
							<div class="col-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Selamat Datang <?=$_SESSION['user']?>!</h3>
									</div>
									<div class="card-body">
										<p>Halo! Dashboard Anda siap digunakan.</p>
										<a href="data-input.php" class="btn btn-primary btn-lg"><i class="bi bi-clipboard-data-fill"></i> Input Tugas Baru</a>
										<a href="data-list.php" class="btn btn-success btn-lg"><i class="bi bi-card-list"></i> Lihat Daftar Tugas Kamu</a>
									</div>
								</div>
							</div>
						</div>
                        <?php endif; ?>

					</div>
				</div>
			</main>
			<?php include 'template/footer.php'; ?>
		</div>
		<?php include 'template/script.php'; ?>
        
        <script>
        function filterMatkul() {
            var selectedProdi = document.getElementById("prodiSelect").value;
            var options = document.getElementsByClassName("matkul-option");
            var hasVisible = false;

            for (var i = 0; i < options.length; i++) {
                var dataProdi = options[i].getAttribute("data-prodi");
                var checkbox = options[i].querySelector("input");
                
                if (selectedProdi && dataProdi == selectedProdi) {
                    options[i].style.display = "block";
                    hasVisible = true;
                } else {
                    options[i].style.display = "none";
                    checkbox.checked = false; // Uncheck yang disembunyikan
                }
            }
            
            var msg = document.getElementById("noMatkulMsg");
            if(msg) msg.style.display = hasVisible ? "none" : "block";
        }
        </script>
	</body>
</html>
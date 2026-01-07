<?php 
session_start();

// 1. Cek sesi login
if(!isset($_SESSION["id"])){
    header("Location: index.php");
    exit(); 
}

include_once 'config/class-master.php';
$master = new MasterData();

// 2. Ambil Data Pendukung
$categoriesList = $master->getCategories();
$prodiList      = $master->getProdi(); 
$allMkList      = $master->getMk(); // PENTING: Data semua MK untuk Admin

// 3. Logika Role & Judul Halaman
$role = $_SESSION['role'];
$id_user = $_SESSION['id'];
$pageTitle = "Input Tugas Baru";
$infoText = "";
$badgeRole = "";
$dosenMatkulList = []; 

if($role == '2'){ // DOSEN
    $pageTitle = "Input Tugas Kuliah";
    $badgeRole = '<span class="badge bg-info text-dark">Mode Dosen</span>';
    $infoText = '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Info Dosen:</strong> Tugas ini akan otomatis dikirim ke semua Mahasiswa di Prodi Anda.
                 </div>';
    // Ambil Matkul khusus Dosen ini
    $dosenMatkulList = $master->getMatkulByProdi($id_user); 

} elseif($role == '3'){ // MAHASISWA
    $pageTitle = "Input Tugas Pribadi";
    $badgeRole = '<span class="badge bg-warning text-dark">Mode Mahasiswa</span>';
    $infoText = '<div class="alert alert-light border-warning fade show" role="alert">
                    <i class="bi bi-person-fill-lock me-2"></i>
                    <strong>Info Mahasiswa:</strong> Tugas ini bersifat <strong>Personal (Pribadi)</strong>. 
                 </div>';

} elseif($role == '1'){ // ADMIN
    $pageTitle = "Input & Distribusi Tugas (Admin)";
    $badgeRole = '<span class="badge bg-danger">Mode Admin</span>';
    $infoText = '<div class="alert alert-danger fade show" role="alert">
                    <i class="bi bi-shield-fill-check me-2"></i>
                    <strong>Admin Power:</strong> Anda dapat mendistribusikan tugas ke seluruh mahasiswa dalam satu jurusan.
                 </div>';
}

// Alert Status Gagal
if(isset($_GET['status'])){
    if($_GET['status'] == 'failed'){
        echo "<script>alert('Gagal menambahkan Tugas Baru. Silakan coba lagi.');</script>";
    }
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
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-6">
								<h3 class="mb-0"><?php echo $pageTitle; ?></h3>
							</div>
							<div class="col-sm-6">
								<ol class="breadcrumb float-sm-end">
									<li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
									<li class="breadcrumb-item active" aria-current="page">Input Tugas</li>
								</ol>
							</div>
						</div>
					</div>
				</div>

				<div class="app-content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12">
								<div class="card">
									<div class="card-header">
										<h3 class="card-title">Formulir Tugas &nbsp; <?php echo $badgeRole; ?></h3>
									</div>
                                    
                                    <form action="proses/proses-input.php" method="POST">
									    <div class="card-body">
                                            
                                            <?php echo $infoText; ?>

                                            <?php if($role == '1'): ?>
                                                <div class="mb-3 p-3 bg-light border rounded">
                                                    <h5 class="text-danger mb-3"><i class="bi bi-shield-lock"></i> Distribusi Tugas</h5>
                                                    
                                                    <div class="mb-3">
                                                        <label for="target_prodi" class="form-label fw-bold">1. Pilih Target Jurusan / Prodi (Wajib)</label>
                                                        <select class="form-select border-danger" id="target_prodi" name="target_prodi" onchange="filterMatkulAdmin()" required>
                                                            <option value="" selected disabled>-- Pilih Jurusan --</option>
                                                            <?php foreach ($prodiList as $p){
                                                                echo '<option value="'.$p['id_prodi'].'">'.$p['nm_prodi'].'</option>';
                                                            } ?>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="admin_matkul" class="form-label fw-bold">2. Pilih Mata Kuliah (Opsional)</label>
                                                        <select class="form-select" id="admin_matkul" name="id_matkul" disabled>
                                                            <option value="" selected>-- Pilih Prodi Terlebih Dahulu --</option>
                                                        </select>
                                                        <div class="form-text">Mata kuliah akan otomatis muncul sesuai Jurusan yang dipilih di atas.</div>
                                                    </div>
                                                </div>
                                                <hr>
                                            <?php endif; ?>
                                            <?php if($role == '2'): ?>
                                                <div class="mb-3">
                                                    <label for="id_matkul" class="form-label text-primary fw-bold">Pilih Mata Kuliah</label>
                                                    <select class="form-select border-primary" name="id_matkul" required>
                                                        <option value="" selected disabled>-- Pilih Mata Kuliah --</option>
                                                        <?php 
                                                        if(empty($dosenMatkulList)){
                                                            echo '<option disabled>Tidak ada mata kuliah di Prodi Anda.</option>';
                                                        } else {
                                                            foreach ($dosenMatkulList as $dm){
                                                                echo '<option value="'.$dm['id_mk'].'">'.$dm['nm_mk'].'</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <hr>
                                            <?php endif; ?>
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nama Tugas / Judul</label>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Contoh: Menyelesaikan Laporan Akhir" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="deskripsi" class="form-label">Deskripsi Detail</label>
                                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan detail tugas yang harus dikerjakan..." required></textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="category" class="form-label">Kategori Tugas</label>
                                                    <select class="form-select" id="category" name="category" required>
                                                        <option value="" selected disabled>-- Pilih Kategori --</option>
                                                        <?php 
                                                        foreach ($categoriesList as $category){
                                                            echo '<option value="'.$category['id'].'">'.$category['name'].'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="deadline" class="form-label">Batas Waktu (Deadline)</label>
                                                    <input type="date" class="form-control" id="deadline" name="deadline" required>
                                                </div>
                                            </div>

                                        </div>
									    <div class="card-footer">
                                            <button type="button" class="btn btn-danger me-2 float-start" onclick="window.location.href='data-list.php'"><i class="bi bi-arrow-left"></i> Batal</button>
                                            <button type="reset" class="btn btn-secondary me-2 float-start"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
                                            <button type="submit" class="btn btn-primary float-end"><i class="bi bi-save"></i> Simpan Tugas</button>
                                        </div>
                                    </form>
								</div>
							</div>
						</div>
					</div>
				</div>

			</main>
			<?php include 'template/footer.php'; ?>
		</div>
		<?php include 'template/script.php'; ?>

        <?php if($role == '1'): ?>
        <script>
            // 1. Simpan Data Mata Kuliah dari PHP ke Javascript Array
            const dataMataKuliah = [
                <?php foreach ($allMkList as $mk): ?>
                {
                    id: "<?= $mk['id_mk'] ?>",
                    nama: "<?= $mk['nm_mk'] ?>",
                    id_prodi: "<?= $mk['id_prodi'] ?>"
                },
                <?php endforeach; ?>
            ];

            // 2. Fungsi Filter saat Dropdown Prodi berubah
            function filterMatkulAdmin() {
                // Ambil elemen dropdown
                const prodiSelect = document.getElementById("target_prodi");
                const matkulSelect = document.getElementById("admin_matkul");
                
                // Ambil ID Prodi yang dipilih
                const selectedProdiId = prodiSelect.value;

                // Kosongkan Dropdown Matkul
                matkulSelect.innerHTML = '<option value="" selected>-- Pilih Mata Kuliah (Opsional) --</option>';
                
                // Filter Array Javascript
                const filteredMk = dataMataKuliah.filter(item => item.id_prodi === selectedProdiId);

                if(filteredMk.length > 0){
                    // Jika ada matkul yang cocok, masukkan ke dropdown
                    matkulSelect.disabled = false;
                    
                    filteredMk.forEach(item => {
                        const option = document.createElement("option");
                        option.value = item.id;
                        option.text = item.nama;
                        matkulSelect.appendChild(option);
                    });
                } else {
                    // Jika kosong
                    const option = document.createElement("option");
                    option.text = "Tidak ada Mata Kuliah di Prodi ini";
                    matkulSelect.appendChild(option);
                    matkulSelect.disabled = true;
                }
            }
        </script>
        <?php endif; ?>

	</body>
</html>
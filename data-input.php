<?php 
session_start();

// Cek sesi login
if(!isset($_SESSION["id"])){
    header("Location: index.php");
    exit(); 
}

include_once 'config/class-master.php';
$master = new MasterData();

// Mengambil daftar kategori dari database
$categoriesList = $master->getCategories();

// Logika Judul & Info Berdasarkan Role
$role = $_SESSION['role'];
$pageTitle = "Input Tugas Baru";
$infoText = "";
$badgeRole = "";

if($role == '2'){ // Dosen
    $pageTitle = "Input Tugas Kuliah (Untuk Mahasiswa)";
    $badgeRole = '<span class="badge bg-info text-dark">Mode Dosen</span>';
    $infoText = '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Info Dosen:</strong> Tugas yang Anda buat akan berstatus <strong>"Tugas Dosen"</strong>. 
                    Tugas ini akan muncul di daftar semua Mahasiswa dengan status <em>Read-Only</em> (Mahasiswa tidak bisa mengedit/menghapus tugas ini).
                 </div>';
} elseif($role == '3'){ // Mahasiswa
    $pageTitle = "Input Tugas Pribadi";
    $badgeRole = '<span class="badge bg-warning text-dark">Mode Mahasiswa</span>';
    $infoText = '<div class="alert alert-light border-warning fade show" role="alert">
                    <i class="bi bi-person-fill-lock me-2"></i>
                    <strong>Info Mahasiswa:</strong> Tugas ini bersifat <strong>Personal (Pribadi)</strong>. 
                    Hanya Anda yang dapat melihat, mengedit, dan menghapus tugas ini.
                 </div>';
} elseif($role == '1'){ // Admin
    $pageTitle = "Input Tugas (Admin)";
    $badgeRole = '<span class="badge bg-danger">Mode Admin</span>';
    $infoText = '<div class="alert alert-secondary fade show" role="alert">
                    <i class="bi bi-shield-lock-fill me-2"></i>
                    <strong>Info Admin:</strong> Anda memiliki akses penuh untuk membuat tugas tipe apapun.
                 </div>';
}

// Alert Status Gagal (jika redirect dari proses-input.php gagal)
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
										<div class="card-tools">
											<button type="button" class="btn btn-tool" data-lte-toggle="card-collapse" title="Collapse">
												<i data-lte-icon="expand" class="bi bi-plus-lg"></i>
												<i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
											</button>
										</div>
									</div>
                                    
                                    <form action="proses/proses-input.php" method="POST">
									    <div class="card-body">
                                            
                                            <?php echo $infoText; ?>

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

	</body>
</html>
<?php 
session_start();
if($_SESSION["role"] == 3){
    header("Location: index.php");
    exit(); // Pastikan menambahkan exit setelah redirect
}
include_once 'config/class-master.php';
include_once 'config/class-mahasiswa.php';
$master = new MasterData();
$mahasiswa = new Mahasiswa();
// Mengambil daftar program studi, provinsi, dan status mahasiswa
$categoryList = $master->getCategories();
// Mengambil daftar provinsi
$usernameList = $master->getUsername();
// Mengambil daftar status mahasiswa
$statusList = $master->getStatus();
// Mengambil data mahasiswa yang akan diedit berdasarkan id dari parameter GET
$dataMahasiswa = $mahasiswa->getTaskById($_GET['id']);
if(isset($_GET['status'])){
    if($_GET['status'] == 'failed'){
        echo "<script>alert('Gagal mengubah data mahasiswa. Silakan coba lagi.');</script>";
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
								<h3 class="mb-0">Edit Tugasmu</h3>
							</div>
							<div class="col-sm-6">
								<ol class="breadcrumb float-sm-end">
									<li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
									<li class="breadcrumb-item active" aria-current="page">Edit Tugasmu</li>
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
										<h3 class="card-title">Formulir Tugasmu</h3>
										<div class="card-tools">
											<button type="button" class="btn btn-tool" data-lte-toggle="card-collapse" title="Collapse">
												<i data-lte-icon="expand" class="bi bi-plus-lg"></i>
												<i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
											</button>
											<button type="button" class="btn btn-tool" data-lte-toggle="card-remove" title="Remove">
												<i class="bi bi-x-lg"></i>
											</button>
										</div>
									</div>
                                    <form action="proses/proses-edit.php" method="POST">
									    <div class="card-body">
                                            <input type="hidden" name="id" value="<?php echo $dataMahasiswa['id']; ?>">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nama Tugasmu</label>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan Nama Tugasmu" value="<?php echo $dataMahasiswa['name']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="deskripsi" class="form-label">Deskripsi Tugasmu</label>
                                                <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="Deskripsikan Tugasmu" value="<?php echo $dataMahasiswa['description']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="deadline" class="form-label">Deadline</label>
                                                <input type="date" class="form-control" id="deadline" name="deadline" value="<?php echo $dataMahasiswa['deadline']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Kategori</label>
                                                <select class="form-select" id="category" name="category" required>
                                                    <option value="" selected disabled>Pilih Kategori Tugasmu</option>
                                                    <?php 
                                                    // Iterasi daftar program studi dan menandai yang sesuai dengan data mahasiswa yang dipilih
                                                    foreach ($categoryList as $category){
                                                        // Menginisialisasi variabel kosong untuk menandai opsi yang dipilih
                                                        $selectedcategory = "";
                                                        // Mengecek apakah program studi saat ini sesuai dengan data mahasiswa
                                                        if($dataMahasiswa['category_id'] == $category['id']){
                                                            // Jika sesuai, tandai sebagai opsi yang dipilih
                                                            $selectedcategory = "selected";
                                                        }
                                                        // Menampilkan opsi program studi dengan penanda yang sesuai
                                                        echo '<option value="'.$category['id'].'" '.$selectedcategory.'>'.$category['name'].'</option>';
                                                    }
                                                    
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="" selected disabled>Pilih Status</option>
                                                    <?php 
                                                    // Iterasi daftar status mahasiswa dan menandai yang sesuai dengan data mahasiswa yang dipilih
                                                    foreach ($statusList as $status){
                                                        // Menginisialisasi variabel kosong untuk menandai opsi yang dipilih
                                                        $selectedStatus = "";
                                                        // Mengecek apakah status saat ini sesuai dengan data mahasiswa
                                                        if($dataMahasiswa['status'] == $status['value']){
                                                            // Jika sesuai, tandai sebagai opsi yang dipilih
                                                            $selectedStatus = "selected";
                                                        }
                                                        // Menampilkan opsi status dengan penanda yang sesuai
                                                        echo '<option value="'.$status['value'].'" '.$selectedStatus.'>'.$status['name'].'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
									    <div class="card-footer">
                                            <button type="button" class="btn btn-danger me-2 float-start" onclick="window.location.href='data-list.php'">Batal</button>
                                            <button type="submit" class="btn btn-warning float-end">Update Data</button>
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

<?php 
session_start();
// Proteksi halaman
if($_SESSION["role"] == 3 || ($_SESSION["role"] == 2)){
    header("Location: index.php");
    exit();
}

include_once 'config/class-master.php';
$master = new MasterData();

// Mengambil data prodi berdasarkan ID dari URL
$dataProdi = $master->getUpdateProdi($_GET['id']);

if(isset($_GET['status'])){
    if($_GET['status'] == 'failed'){
        echo "<script>alert('Gagal mengubah data Program Studi. Silakan coba lagi.');</script>";
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
								<h3 class="mb-0">Update Program Studi</h3>
							</div>
							<div class="col-sm-6">
								<ol class="breadcrumb float-sm-end">
									<li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
									<li class="breadcrumb-item active" aria-current="page">Update Prodi</li>
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
										<h3 class="card-title">Edit Program Studi</h3>
									</div>
                                    <form action="proses/proses-prodi.php?aksi=updateprodi" method="POST">
										<div class="card-body">
                                            <input type="hidden" name="id_prodi" value="<?php echo $dataProdi['id_prodi']; ?>">
											<div class="mb-3">
												<label for="nm_prodi" class="form-label">Nama Program Studi</label>
												<input type="text" class="form-control" id="nm_prodi" name="nm_prodi" value="<?php echo $dataProdi['nm_prodi']; ?>" required>
											</div>
                                        </div>
									    <div class="card-footer">
                                            <button type="button" class="btn btn-danger me-2 float-start" onclick="window.location.href='master-prodi-list.php'">Batal</button>
                                            <button type="submit" class="btn btn-warning float-end">Update Prodi</button>
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
<?php
session_start();
if($_SESSION["role"] == 3 || ($_SESSION["role"] == 2)){
    header("Location: index.php");
    exit();
}
include_once 'config/class-master.php';
$master = new MasterData();

if(isset($_GET['status'])){
	if($_GET['status'] == 'inputsuccess'){
		echo "<script>alert('Program Studi berhasil ditambahkan.');</script>";
	} else if($_GET['status'] == 'editsuccess'){
		echo "<script>alert('Program Studi berhasil diubah.');</script>";
	} else if($_GET['status'] == 'deletesuccess'){
		echo "<script>alert('Program Studi berhasil dihapus.');</script>";
	} else if($_GET['status'] == 'deletefailed'){
		echo "<script>alert('Gagal menghapus Program Studi.');</script>";
	}
}
$dataProdi = $master->getProdi();
?>
<!doctype html>
<html lang="en">
	<head><?php include 'template/header.php'; ?></head>
	<body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-open bg-body-tertiary">
		<div class="app-wrapper">
			<?php include 'template/navbar.php'; ?>
			<?php include 'template/sidebar.php'; ?>
			<main class="app-main">
				<div class="app-content-header">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-6"><h3 class="mb-0">Daftar Program Studi</h3></div>
							<div class="col-sm-6">
								<ol class="breadcrumb float-sm-end">
									<li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
									<li class="breadcrumb-item active">Master Prodi</li>
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
									<div class="card-header"><h3 class="card-title">List Program Studi</h3></div>
									<div class="card-body p-0 table-responsive">
										<table class="table table-striped">
											<thead>
												<tr>
													<th style="width: 10%">No</th>
													<th>Nama Prodi</th>
													<th class="text-center">Aksi</th>
												</tr>
											</thead>
											<tbody>
												<?php
													if(count($dataProdi) == 0){
													    echo '<tr><td colspan="3" class="text-center">Data kosong.</td></tr>';
													} else {
														foreach ($dataProdi as $index => $prodi){
															echo '<tr class="align-middle">
																<td>'.($index + 1).'</td>
																<td>'.$prodi['nm_prodi'].'</td>
																<td class="text-center">
																	<button class="btn btn-sm btn-warning" onclick="window.location.href=\'master-prodi-edit.php?id='.$prodi['id_prodi'].'\'"><i class="bi bi-pencil"></i> Edit</button>
																	<button class="btn btn-sm btn-danger" onclick="if(confirm(\'Hapus prodi ini?\')){window.location.href=\'proses/proses-prodi.php?aksi=deleteprodi&id='.$prodi['id_prodi'].'\'}"><i class="bi bi-trash"></i> Hapus</button>
																</td>
															</tr>';
														}
													}
												?>
											</tbody>
										</table>
									</div>
									<div class="card-footer">
										<button class="btn btn-primary" onclick="window.location.href='master-prodi-input.php'"><i class="bi bi-plus"></i> Tambah Prodi</button>
									</div>
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
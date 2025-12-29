<?php
session_start();
if(!isset($_SESSION["id"])){
    header("Location: index.php");
    exit(); 
}
include_once 'config/class-mahasiswa.php';
$mahasiswa = new Mahasiswa();

// Ambil ID dan Role Session
$id_user_login = $_SESSION['id'];
$role_login    = $_SESSION['role'];

// Menampilkan alert
if(isset($_GET['status'])){
	if($_GET['status'] == 'inputsuccess'){
		echo "<script>alert('Data tugas berhasil ditambahkan.');</script>";
	} else if($_GET['status'] == 'editsuccess'){
		echo "<script>alert('Data tugas berhasil diubah.');</script>";
	} else if($_GET['status'] == 'deletesuccess'){
		echo "<script>alert('Data tugas berhasil dihapus.');</script>";
	} else if($_GET['status'] == 'deletefailed'){
		echo "<script>alert('Gagal! Anda tidak memiliki izin untuk menghapus tugas ini.');</script>";
	}
}

// Ambil data task sesuai Role & ID User
$dataMahasiswa = $mahasiswa->getAllTasks($id_user_login, $role_login);
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
							<div class="col-sm-6"><h3 class="mb-0">Daftar Tugasmu</h3></div>
							<div class="col-sm-6">
								<ol class="breadcrumb float-sm-end">
									<li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
									<li class="breadcrumb-item active" aria-current="page">List Tugas</li>
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
										<h3 class="card-title">Tabel Tugas</h3>
									</div>
									<div class="card-body p-0 table-responsive">
										<table class="table table-striped" role="table">
											<thead>
												<tr>
													<th>No</th>
													<th>Nama</th>
													<th>Deskripsi</th>
													<th>Deadline</th>
													<th>Kategori</th>
													<th class="text-center">Status</th>
													<th class="text-center">Aksi</th>
												</tr>
											</thead>
											<tbody>
												<?php
													if(count($dataMahasiswa) == 0){
													    echo '<tr class="align-middle">
															<td colspan="7" class="text-center">Tidak ada tugas yang tersedia.</td>
														</tr>';
													} else {
														foreach ($dataMahasiswa as $index => $row){
                                                            // Badge Status
															if($row['status'] == "Completed"){
															    $statusBadge = '<span class="badge bg-success">Completed</span>';
															} else {
															    $statusBadge = '<span class="badge bg-secondary">Pending</span>';
															}

                                                            // Badge Tipe Tugas (Pembeda Visual)
                                                            if($row['task_type'] == 'Dosen'){
                                                                $typeBadge = '<span class="badge bg-info text-dark mb-1">Tugas Dosen</span>';
                                                            } else {
                                                                $typeBadge = '<span class="badge bg-warning text-dark mb-1">Pribadi</span>';
                                                            }

															echo '<tr class="align-middle">
																<td>'.($index + 1).'</td>
																<td>'.$typeBadge.'<br>'.$row['name'].'</td>
																<td>'.$row['description'].'</td>
																<td>'.$row['deadline'].'</td>
																<td>'.$row['category_name'].'</td>
																<td class="text-center">'.$statusBadge.'</td>
																<td class="text-center">';
                                                                
                                                                // LOGIKA BUTTON:
                                                                // Tombol tampil JIKA: User adalah Pembuat Tugas ATAU User adalah Admin
                                                                $isOwner = ($row['created_by'] == $id_user_login);
                                                                $isAdmin = ($role_login == '1');

                                                                if ($isOwner || $isAdmin) {
                                                                    echo '<button type="button" class="btn btn-sm btn-warning me-1" onclick="window.location.href=\'data-edit.php?id='.$row['id'].'\'"><i class="bi bi-pencil-fill"></i></button>
                                                                          <button type="button" class="btn btn-sm btn-danger" onclick="if(confirm(\'Yakin ingin menghapus data ini?\')){window.location.href=\'proses/proses-delete.php?id='.$row['id'].'\'}"><i class="bi bi-trash-fill"></i></button>';
                                                                } else {
                                                                    // Jika Mahasiswa melihat tugas Dosen
                                                                    echo '<span class="badge bg-light text-dark border"><i class="bi bi-lock"></i> Read Only</span>';
                                                                }

															echo '</td>
															</tr>';
														}
													}
												?>
											</tbody>
										</table>
									</div>
									<div class="card-footer">
										<button type="button" class="btn btn-primary" onclick="window.location.href='data-input.php'"><i class="bi bi-plus-lg"></i> Tambah Tugas</button>
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
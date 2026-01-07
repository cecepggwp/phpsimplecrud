<?php
session_start();
// Hanya Admin (1) dan Dosen (2) yang boleh akses
if(!isset($_SESSION["id"]) || ($_SESSION["role"] == '3')){
    header("Location: index.php");
    exit(); 
}

include_once 'config/class-mahasiswa.php';
$mahasiswa = new Mahasiswa();

// Ambil ID Tugas
$id_task = isset($_GET['id']) ? $_GET['id'] : 0;

// Ambil Detail Tugas
$taskInfo = $mahasiswa->getTaskById($id_task);
if(!$taskInfo){
    echo "<script>alert('Tugas tidak ditemukan!'); window.location.href='data-list.php';</script>";
    exit();
}

// Ambil Daftar Mahasiswa Penerima Tugas
$receivers = $mahasiswa->getTaskReceivers($id_task);
?>
<!doctype html>
<html lang="en">
	<head>
		<?php include 'template/header.php'; ?>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
	</head>

	<body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-open bg-body-tertiary">
		<div class="app-wrapper">
			<?php include 'template/navbar.php'; include 'template/sidebar.php'; ?>

			<main class="app-main">
				<div class="app-content-header">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-6">
                                <h3 class="mb-0">Verifikasi Tugas Mahasiswa</h3>
                            </div>
                            <div class="col-sm-6 text-end">
                                <a href="data-list.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                            </div>
						</div>
					</div>
				</div>

				<div class="app-content">
					<div class="container-fluid">
                        
                        <div class="card mb-4 card-outline card-primary">
                            <div class="card-body">
                                <h5><strong>Judul Tugas: <?= htmlspecialchars($taskInfo['name']) ?></strong></h5>
                                <p class="text-muted mb-1"><?= htmlspecialchars($taskInfo['description']) ?></p>
                                <span class="badge bg-warning text-dark"><i class="bi bi-calendar"></i> Deadline: <?= date('d M Y', strtotime($taskInfo['deadline'])) ?></span>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-light">
                                <h3 class="card-title"><i class="bi bi-check2-all"></i> Daftar Mahasiswa</h3>
                            </div>
                            <div class="card-body">
                                <form action="proses/proses-verifikasi.php" method="POST">
                                    <input type="hidden" name="id_task" value="<?= $id_task ?>">
                                    
                                    <div class="table-responsive">
                                        <table id="verifTable" class="table table-bordered table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center" style="width: 5%">No</th>
                                                    <th>Nama Mahasiswa</th>
                                                    <th>NIM</th>
                                                    <th>Prodi</th>
                                                    <th class="text-center">Status Saat Ini</th>
                                                    <th class="text-center" style="width: 15%">Verifikasi (Ceklis)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($receivers as $idx => $mhs): ?>
                                                <tr>
                                                    <td class="text-center"><?= $idx + 1 ?></td>
                                                    <td><?= htmlspecialchars($mhs['nm_mhs']) ?></td>
                                                    <td><?= htmlspecialchars($mhs['nim_mhs']) ?></td>
                                                    <td><?= htmlspecialchars($mhs['nm_prodi']) ?></td>
                                                    <td class="text-center">
                                                        <?php if($mhs['status'] == 'Completed'): ?>
                                                            <span class="badge bg-success">Selesai</span>
                                                            <br><small class="text-muted"><?= $mhs['finish_date'] ?></small>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Pending</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                                   name="completed_students[]" 
                                                                   value="<?= $mhs['id_mhs'] ?>" 
                                                                   style="width: 50px; height: 25px;"
                                                                   <?= ($mhs['status'] == 'Completed') ? 'checked' : '' ?>>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary float-end btn-lg">
                                            <i class="bi bi-save"></i> Simpan Status Verifikasi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

					</div>
				</div>
			</main>
			<?php include 'template/footer.php'; ?>
		</div>
		<?php include 'template/script.php'; ?>
        
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#verifTable').DataTable({
                    "paging": false, // Matikan paging agar form submit mengirim semua data
                    "info": false,
                    "language": { "search": "Cari Mahasiswa:" }
                });
            });
        </script>
	</body>
</html>
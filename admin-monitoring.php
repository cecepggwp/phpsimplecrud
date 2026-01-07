<?php
session_start();
// Hanya Admin (Role 1) yang boleh akses
if(!isset($_SESSION["id"]) || $_SESSION["role"] != '1'){
    header("Location: index.php");
    exit(); 
}

include_once 'config/class-mahasiswa.php';
$mahasiswa = new Mahasiswa();

// Ambil SEMUA data tugas (Personal + Distribusi)
$allTasks = $mahasiswa->getAllTasksForAdmin();
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
							<div class="col-sm-6"><h3 class="mb-0">Monitoring Seluruh Tugas</h3></div>
						</div>
					</div>
				</div>

				<div class="app-content">
					<div class="container-fluid">
						<div class="card card-outline card-danger">
                            <div class="card-header">
                                <h3 class="card-title">Data Tugas Mahasiswa (Global)</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="monitoringTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nama Mahasiswa</th>
                                                <th>Prodi</th>
                                                <th>Judul Tugas</th>
                                                <th>Deadline</th>
                                                <th>Tipe Tugas</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($allTasks as $row): ?>
                                            <tr>
                                                <td><i class="bi bi-person-circle"></i> <?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                                                <td><?= htmlspecialchars($row['nm_prodi']) ?></td>
                                                <td><?= htmlspecialchars($row['judul_tugas']) ?></td>
                                                <td>
                                                    <?= date('d M Y', strtotime($row['deadline'])) ?>
                                                    <?php 
                                                        if($row['deadline'] < date('Y-m-d') && $row['status'] == 'Pending'){
                                                            echo '<span class="badge bg-danger ms-1">Terlewat</span>';
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if($row['tipe'] == 'Personal'): ?>
                                                        <span class="badge bg-warning text-dark">Pribadi</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary">Dari Dosen/Admin</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($row['status'] == 'Completed'): ?>
                                                        <span class="badge bg-success">Selesai</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="proses/proses-delete.php?id=<?= $row['id_task'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('PERINGATAN ADMIN: Menghapus tugas ini akan menghilangkannya dari akun mahasiswa. Lanjutkan?')">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
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
                $('#monitoringTable').DataTable({
                    "language": {
                        "search": "Cari (Nama/Tugas):",
                        "lengthMenu": "Tampilkan _MENU_ data per halaman",
                        "zeroRecords": "Tidak ada data ditemukan",
                        "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
                        "infoEmpty": "Tidak ada data tersedia",
                        "paginate": { "first": "Pertama", "last": "Terakhir", "next": "Selanjutnya", "previous": "Sebelumnya" }
                    }
                });
            });
        </script>
	</body>
</html>
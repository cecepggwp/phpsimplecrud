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

// 1. Cek Kelengkapan Data
$dataStatus = $master->checkUserDataStatus($id_user_login, $role_login);

// 2. Ambil Statistik Dashboard (Hanya jika data lengkap)
$dashboardStats = [];
if($dataStatus == 0){
    $dashboardStats = $master->getDashboardStats($id_user_login, $role_login);
}

// Ambil data dropdown prodi jika needed (untuk user baru)
$prodiList = [];
if($dataStatus != 0 && $role_login != '1'){
    $prodiList = $master->getProdi();
}
?>
<!doctype html>
<html lang="en">
	<head>
		<?php include 'template/header.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	</head>

	<body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-open bg-body-tertiary">
		<div class="app-wrapper">
			<?php include 'template/navbar.php'; ?>
			<?php include 'template/sidebar.php'; ?>

			<main class="app-main">
				<div class="app-content-header">
					<div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6"><h3 class="mb-0">Dashboard</h3></div>
                        </div>
                    </div>
				</div>

				<div class="app-content">
					<div class="container-fluid">
						
                        <?php if($dataStatus != 0 && $role_login != '1'): ?>
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-warning border-start border-warning border-4" role="alert">
                                        <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Data Akademik Belum Lengkap!</h4>
                                        <p>Silakan lengkapi Program Studi Anda untuk melanjutkan.</p>
                                        
                                        <?php if($role_login == '2'): // DOSEN ?>
                                            <form action="proses/proses-update-data-dosen.php" method="POST" class="mt-3">
                                                <div class="row align-items-end">
                                                    <div class="col-md-8">
                                                        <label class="form-label fw-bold">Pilih Program Studi Anda:</label>
                                                        <select name="id_prodi" class="form-select" required>
                                                            <option value="" selected disabled>-- Pilih Prodi --</option>
                                                            <?php foreach($prodiList as $p): ?>
                                                                <option value="<?= $p['id_prodi'] ?>"><?= $p['nm_prodi'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4"><button type="submit" class="btn btn-dark w-100">Simpan</button></div>
                                                </div>
                                            </form>
                                        <?php else: // MAHASISWA ?>
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
                            <?php if($role_login == '1'): ?>
                            <div class="row">
                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner"><h3><?= $dashboardStats['counts']['total_user'] ?></h3><p>Total User Terdaftar</p></div>
                                        <div class="icon"><i class="bi bi-people-fill"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner"><h3><?= $dashboardStats['counts']['total_prodi'] ?></h3><p>Program Studi</p></div>
                                        <div class="icon"><i class="bi bi-building"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner"><h3><?= $dashboardStats['counts']['total_mk'] ?></h3><p>Mata Kuliah</p></div>
                                        <div class="icon"><i class="bi bi-book-half"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-primary card-outline">
                                        <div class="card-header"><h3 class="card-title">Komposisi Pengguna</h3></div>
                                        <div class="card-body">
                                            <canvas id="adminChart" style="height: 250px; max-width: 100%;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php elseif($role_login == '2'): ?>
                            <h5 class="mb-3 text-secondary"><i class="bi bi-speedometer2"></i> Statistik Tugas Anda</h5>
                            <div class="row">
                                <div class="col-lg-4 col-12">
                                    <div class="small-box bg-primary">
                                        <div class="inner"><h3><?= $dashboardStats['counts']['my_tasks'] ?></h3><p>Total Tugas Diberikan</p></div>
                                        <div class="icon"><i class="bi bi-briefcase"></i></div>
                                        <a href="data-list.php" class="small-box-footer">Lihat Detail <i class="bi bi-arrow-right"></i></a>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3><?= $dashboardStats['counts']['mhs_selesai'] ?> <span style="font-size: 16px; font-weight:normal;">Orang</span></h3>
                                            <p>Mahasiswa Sudah Mengerjakan</p>
                                        </div>
                                        <div class="icon"><i class="bi bi-person-check-fill"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3><?= $dashboardStats['counts']['mhs_belum'] ?> <span style="font-size: 16px; font-weight:normal;">Orang</span></h3>
                                            <p>Mahasiswa Belum Mengerjakan</p>
                                        </div>
                                        <div class="icon"><i class="bi bi-person-x-fill"></i></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-info card-outline">
                                        <div class="card-header"><h3 class="card-title">Grafik Progress Mahasiswa</h3></div>
                                        <div class="card-body">
                                            <canvas id="dosenChart" style="height: 250px; max-width: 100%;"></canvas>
                                            <p class="mt-2 text-muted small text-center">Persentase akumulasi dari seluruh tugas.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php elseif($role_login == '3'): ?>
                            <div class="row">
                                <div class="col-lg-6 col-6">
                                    <div class="small-box bg-danger">
                                        <div class="inner"><h3><?= $dashboardStats['counts']['total_pending'] ?></h3><p>Tugas Belum Selesai</p></div>
                                        <div class="icon"><i class="bi bi-hourglass-split"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner"><h3><?= $dashboardStats['counts']['total_completed'] ?></h3><p>Tugas Selesai</p></div>
                                        <div class="icon"><i class="bi bi-check-circle-fill"></i></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="card card-success card-outline">
                                        <div class="card-header"><h3 class="card-title">Grafik Produktivitas</h3></div>
                                        <div class="card-body">
                                            <canvas id="mhsChart" style="height: 250px; max-width: 100%;"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="card card-warning card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="bi bi-list-task"></i> 5 Tugas Pending (Prioritas)</h3>
                                        </div>
                                        <div class="card-body p-0 table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Tugas</th>
                                                        <th>Deadline</th>
                                                        <th>Status</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(empty($dashboardStats['lists'])): ?>
                                                        <tr><td colspan="4" class="text-center text-muted py-4">Hore! Tidak ada tugas pending.</td></tr>
                                                    <?php else: ?>
                                                        <?php foreach($dashboardStats['lists'] as $tugas): 
                                                            // Hitung sisa hari
                                                            $deadline = new DateTime($tugas['deadline']);
                                                            $today = new DateTime();
                                                            $diff = $today->diff($deadline);
                                                            $sisaHari = $diff->format("%r%a"); // %r untuk tanda +/-
                                                            
                                                            $badgeTime = "";
                                                            if($sisaHari < 0) $badgeTime = "<span class='badge bg-danger'>Terlewat</span>";
                                                            elseif($sisaHari == 0) $badgeTime = "<span class='badge bg-warning text-dark'>Hari Ini!</span>";
                                                            else $badgeTime = "<span class='badge bg-info'>$sisaHari hari lagi</span>";
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?= htmlspecialchars($tugas['name']) ?>
                                                                <br><small class="text-muted"><?= $tugas['sumber'] ?></small>
                                                            </td>
                                                            <td>
                                                                <?= date('d M Y', strtotime($tugas['deadline'])) ?>
                                                                <br><?= $badgeTime ?>
                                                            </td>
                                                            <td><span class="badge bg-secondary">Pending</span></td>
                                                            <td>
                                                                <a href="data-edit.php?id=<?= $tugas['id_task'] ?>" class="btn btn-sm btn-primary">
                                                                    <i class="bi bi-pencil-square"></i> Update
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer text-center">
                                            <a href="data-list.php" class="text-secondary">Lihat Semua Tugas <i class="bi bi-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                        <?php endif; ?>
					</div>
				</div>
			</main>
			<?php include 'template/footer.php'; ?>
		</div>
		<?php include 'template/script.php'; ?>

        <?php if($dataStatus == 0): ?>
        <script>
            // CONFIG CHART
            <?php if($role_login == '1'): ?>
            new Chart(document.getElementById('adminChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Admin', 'Dosen', 'Mahasiswa'],
                    datasets: [{
                        data: [<?= $dashboardStats['charts']['roles']['Admin'] ?>, <?= $dashboardStats['charts']['roles']['Dosen'] ?>, <?= $dashboardStats['charts']['roles']['Mahasiswa'] ?>],
                        backgroundColor: ['#dc3545', '#198754', '#0d6efd']
                    }]
                }
            });
            <?php endif; ?>

            <?php if($role_login == '2'): ?>
            new Chart(document.getElementById('dosenChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Selesai', 'Belum Selesai'],
                    datasets: [{
                        data: [<?= $dashboardStats['charts']['progress']['Completed'] ?>, <?= $dashboardStats['charts']['progress']['Pending'] ?>],
                        backgroundColor: ['#198754', '#dc3545']
                    }]
                }
            });
            <?php endif; ?>

            <?php if($role_login == '3'): ?>
            new Chart(document.getElementById('mhsChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Pending', 'Completed'],
                    datasets: [{
                        label: 'Jumlah Tugas',
                        data: [<?= $dashboardStats['charts']['overall']['Pending'] ?>, <?= $dashboardStats['charts']['overall']['Completed'] ?>],
                        backgroundColor: ['#dc3545', '#198754']
                    }]
                },
                options: { scales: { y: { beginAtZero: true } } }
            });
            <?php endif; ?>
        </script>
        <?php endif; ?>

	</body>
</html>
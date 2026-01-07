<?php
session_start();
if(!isset($_SESSION["id"])){
    header("Location: index.php");
    exit(); 
}
include_once 'config/class-mahasiswa.php';
$mahasiswa = new Mahasiswa();

$id_user_login = $_SESSION['id'];
$role_login    = $_SESSION['role'];

// LOGIKA PENGAMBILAN DATA BERDASARKAN ROLE
if($role_login == '3') {
    // === UNTUK MAHASISWA (DATA KANBAN) ===
    $tasks = $mahasiswa->getAllTasks($id_user_login, $role_login);
    $pendingTasks = [];
    $completedTasks = [];
    foreach($tasks as $t){
        if($t['status'] == 'Pending') $pendingTasks[] = $t;
        else $completedTasks[] = $t;
    }
} else {
    // === UNTUK DOSEN & ADMIN (DATA TABLE LIST) ===
    $tasksDosen = $mahasiswa->getTasksWithStats($id_user_login);
}

// Alert System
if(isset($_GET['status'])){
	if($_GET['status'] == 'inputsuccess') echo "<script>alert('Data tugas berhasil ditambahkan.');</script>";
	else if($_GET['status'] == 'editsuccess') echo "<script>alert('Data tugas berhasil diubah.');</script>";
	else if($_GET['status'] == 'deletesuccess') echo "<script>alert('Data tugas berhasil dihapus.');</script>";
	else if($_GET['status'] == 'deletefailed') echo "<script>alert('Gagal! Anda tidak memiliki izin untuk menghapus tugas ini.');</script>";
}
?>
<!doctype html>
<html lang="en">
	<head>
		<?php include 'template/header.php'; ?>
        <?php if($role_login == '3'): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
        <style>
            .kanban-col { min-height: 500px; background-color: #f4f6f9; border-radius: 8px; padding: 15px; border: 2px dashed #d1d3e2; }
            .kanban-card { transition: transform 0.2s, box-shadow 0.2s; margin-bottom: 15px; }
            
            .kanban-card.personal { 
                border-left: 5px solid #ffc107; 
                cursor: grab; 
            }
            .kanban-card.personal:active { 
                cursor: grabbing; transform: scale(1.02); box-shadow: 0 10px 20px rgba(0,0,0,0.1); 
            }

            .kanban-card.dosen { 
                border-left: 5px solid #0d6efd; 
                cursor: not-allowed; 
                opacity: 0.9;
            }
            
            .badge-deadline { font-size: 0.8rem; }
            .badge-matkul { font-size: 0.75rem; background-color: #e2e3e5; color: #333; margin-bottom: 8px; display: inline-block; }
        </style>
        <?php endif; ?>
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
                                <?php if($role_login == '3'): ?>
                                    <h3 class="mb-0">Kanban Board Tugas</h3>
                                <?php else: ?>
                                    <h3 class="mb-0">Manajemen Tugas Yang Diberikan</h3>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6 text-end">
                                <a href="data-input.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Tugas Baru</a>
                            </div>
						</div>
					</div>
				</div>

				<div class="app-content">
					<div class="container-fluid">
                        
                        <?php if($role_login == '3'): ?>
                            
                            <div class="mb-3">
                                <span class="badge bg-warning text-dark me-2">🟨 Tugas Personal (Geser manual)</span>
                                <span class="badge bg-primary">🟦 Tugas Dosen (Verifikasi Dosen)</span>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card card-danger card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title fw-bold"><i class="bi bi-hourglass-split"></i> PENDING</h3>
                                            <div class="card-tools"><span class="badge bg-danger"><?= count($pendingTasks) ?></span></div>
                                        </div>
                                        <div class="card-body">
                                            <div id="pendingList" class="kanban-col" data-status="Pending">
                                                <?php foreach($pendingTasks as $row): 
                                                    $styleClass = ($row['created_by'] == $id_user_login) ? 'personal' : 'dosen';
                                                    $sumber     = ($row['created_by'] == $id_user_login) ? 'Personal' : 'Dari Dosen/Admin';
                                                    $lockIcon   = ($row['created_by'] != $id_user_login) ? '<i class="bi bi-lock-fill float-end text-muted" title="Menunggu Verifikasi Dosen"></i>' : '';
                                                    $matkul     = !empty($row['mata_kuliah']) ? $row['mata_kuliah'] : '';
                                                ?>
                                                <div class="card kanban-card <?= $styleClass ?>" data-id="<?= $row['id'] ?>">
                                                    <div class="card-body p-3">
                                                        <?= $lockIcon ?>
                                                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($row['name']) ?></h6>
                                                        
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <small class="text-muted"><i class="bi bi-person"></i> <?= $sumber ?></small>
                                                        </div>

                                                        <?php if($matkul): ?>
                                                            <span class="badge badge-matkul"><i class="bi bi-book"></i> <?= htmlspecialchars($matkul) ?></span>
                                                        <?php endif; ?>

                                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                                            <span class="badge bg-light text-dark border badge-deadline">
                                                                <i class="bi bi-calendar-event"></i> <?= date('d M', strtotime($row['deadline'])) ?>
                                                            </span>
                                                            <div>
                                                                <a href="data-edit.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                                                <?php if($styleClass == 'personal'): ?>
                                                                <a href="proses/proses-delete.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-outline-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card card-success card-outline">
                                        <div class="card-header">
                                            <h3 class="card-title fw-bold"><i class="bi bi-check-circle-fill"></i> SELESAI</h3>
                                            <div class="card-tools"><span class="badge bg-success"><?= count($completedTasks) ?></span></div>
                                        </div>
                                        <div class="card-body">
                                            <div id="completedList" class="kanban-col" data-status="Completed">
                                                <?php foreach($completedTasks as $row): 
                                                    $styleClass = ($row['created_by'] == $id_user_login) ? 'personal' : 'dosen';
                                                    $sumber     = ($row['created_by'] == $id_user_login) ? 'Personal' : 'Dari Dosen/Admin';
                                                    $lockIcon   = ($row['created_by'] != $id_user_login) ? '<i class="bi bi-lock-fill float-end text-success" title="Terverifikasi"></i>' : '';
                                                    $matkul     = !empty($row['mata_kuliah']) ? $row['mata_kuliah'] : '';
                                                ?>
                                                <div class="card kanban-card <?= $styleClass ?>" data-id="<?= $row['id'] ?>">
                                                    <div class="card-body p-3">
                                                        <?= $lockIcon ?>
                                                        <h6 class="fw-bold mb-1 text-decoration-line-through text-muted"><?= htmlspecialchars($row['name']) ?></h6>
                                                        
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <small class="text-muted d-block"><i class="bi bi-person"></i> <?= $sumber ?></small>
                                                        </div>

                                                        <?php if($matkul): ?>
                                                            <span class="badge badge-matkul text-decoration-line-through text-muted"><i class="bi bi-book"></i> <?= htmlspecialchars($matkul) ?></span>
                                                        <?php endif; ?>

                                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                                            <span class="badge bg-success badge-deadline"><i class="bi bi-check"></i> Selesai</span>
                                                            <div>
                                                                <a href="data-edit.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                                                <?php if($styleClass == 'personal'): ?>
                                                                <a href="proses/proses-delete.php?id=<?= $row['id'] ?>" class="btn btn-xs btn-outline-danger" onclick="return confirm('Hapus?')"><i class="bi bi-trash"></i></a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Daftar Tugas yang Anda Berikan</h3>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-striped table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th style="width: 25%">Nama Tugas</th>
                                                <th style="width: 15%">Deadline</th>
                                                <th style="width: 15%">Kategori</th>
                                                <th style="width: 25%">Progress Mahasiswa</th>
                                                <th style="width: 15%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(empty($tasksDosen)): ?>
                                                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada tugas yang Anda buat.</td></tr>
                                            <?php else: ?>
                                                <?php foreach($tasksDosen as $idx => $t): 
                                                    // Hitung Persentase Progress
                                                    $total = $t['total_assignee'];
                                                    $done  = $t['total_completed'];
                                                    $percent = ($total > 0) ? round(($done / $total) * 100) : 0;
                                                    
                                                    // Warna Progress Bar
                                                    $barColor = 'bg-primary';
                                                    if($percent == 100) $barColor = 'bg-success';
                                                    else if($percent < 50) $barColor = 'bg-warning';
                                                ?>
                                                <tr>
                                                    <td><?= $idx + 1 ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($t['name']) ?></strong>
                                                        <br><small class="text-muted"><?= substr($t['description'], 0, 50) ?>...</small>
                                                    </td>
                                                    <td>
                                                        <?= date('d M Y', strtotime($t['deadline'])) ?>
                                                        <?php if($t['deadline'] < date('Y-m-d')) echo '<br><span class="badge bg-danger">Expired</span>'; ?>
                                                    </td>
                                                    <td><span class="badge bg-secondary"><?= $t['category_name'] ?></span></td>
                                                    <td>
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <small>Selesai: <strong><?= $done ?>/<?= $total ?></strong></small>
                                                        </div>
                                                        <div class="progress" style="height: 10px;">
                                                            <div class="progress-bar <?= $barColor ?>" role="progressbar" style="width: <?= $percent ?>%" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="task-details.php?id=<?= $t['id_task'] ?>" class="btn btn-sm btn-info text-white mb-1" title="Cek Mahasiswa">
                                                            <i class="bi bi-people-fill"></i> Cek & Verifikasi
                                                        </a>
                                                        <div>
                                                            <a href="data-edit.php?id=<?= $t['id_task'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                                            <a href="proses/proses-delete.php?id=<?= $t['id_task'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('PERINGATAN: Menghapus tugas ini akan menghilangkannya dari semua Mahasiswa. Lanjutkan?')" title="Hapus"><i class="bi bi-trash-fill"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        <?php endif; ?>
                        </div>
				</div>
			</main>
			<?php include 'template/footer.php'; ?>
		</div>
		<?php include 'template/script.php'; ?>

        <?php if($role_login == '3'): ?>
        <script>
            // CONFIG PENDING: HANYA BOLEH DRAG YG KELASNYA 'personal'
            new Sortable(document.getElementById('pendingList'), {
                group: 'kanban', 
                animation: 150, 
                ghostClass: 'bg-light',
                draggable: ".personal", // <--- INI KUNCINYA (Hanya personal yg bisa ditarik)
                onEnd: function (evt) { updateTaskStatus(evt.item, 'Pending'); }
            });

            // CONFIG COMPLETED: HANYA BOLEH DRAG YG KELASNYA 'personal'
            new Sortable(document.getElementById('completedList'), {
                group: 'kanban', 
                animation: 150, 
                ghostClass: 'bg-light',
                draggable: ".personal", // <--- INI KUNCINYA
                onEnd: function (evt) { updateTaskStatus(evt.item, 'Completed'); }
            });

            function updateTaskStatus(item, newStatus) {
                let parentId = item.parentElement.id;
                let targetStatus = (parentId === 'pendingList') ? 'Pending' : 'Completed';
                let taskId = item.getAttribute('data-id');
                
                // Visual Effect
                let titleEl = item.querySelector('h6');
                let badgeEl = item.querySelector('.badge-deadline');
                let matkulEl = item.querySelector('.badge-matkul'); // Badge matkul (optional)

                if(targetStatus === 'Completed'){
                    titleEl.classList.add('text-decoration-line-through', 'text-muted');
                    badgeEl.className = 'badge bg-success badge-deadline';
                    badgeEl.innerHTML = '<i class="bi bi-check"></i> Selesai';
                    if(matkulEl) matkulEl.classList.add('text-decoration-line-through', 'text-muted');
                } else {
                    titleEl.classList.remove('text-decoration-line-through', 'text-muted');
                    badgeEl.className = 'badge bg-light text-dark border badge-deadline';
                    badgeEl.innerHTML = '<i class="bi bi-clock"></i> Diproses';
                    if(matkulEl) matkulEl.classList.remove('text-decoration-line-through', 'text-muted');
                }

                // AJAX Update
                fetch('proses/api-update-status.php', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_task: taskId, status: targetStatus })
                });
            }
        </script>
        <?php endif; ?>
	</body>
</html>
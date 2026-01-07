<?php 
session_start();

// 1. Cek Login
if(!isset($_SESSION["id"])){
    header("Location: index.php");
    exit(); 
}

include_once 'config/class-master.php';
include_once 'config/class-mahasiswa.php';

$master = new MasterData();
$mahasiswa = new Mahasiswa();

// 2. Ambil ID dari URL
$id_task = isset($_GET['id']) ? $_GET['id'] : 0;

// 3. Ambil Data Tugas dari Database
$dataTask = $mahasiswa->getTaskById($id_task);

// Jika data tidak ditemukan
if(!$dataTask){
    echo "<script>alert('Data tugas tidak ditemukan!'); window.location.href='data-list.php';</script>";
    exit();
}

// 4. KEAMANAN: Cek Hak Akses (RBAC)
$id_user_login = $_SESSION['id'];
$role_login    = $_SESSION['role'];

// User boleh edit JIKA: Dia Pemilik Tugas (isOwner) ATAU Dia Admin (Role 1)
$isOwner = ($dataTask['created_by'] == $id_user_login);
$isAdmin = ($role_login == '1');

if(!$isOwner && !$isAdmin && $role_login != '3'){
    echo "<script>alert('Anda tidak memiliki akses untuk mengedit tugas ini.'); window.location.href='data-list.php';</script>";
    exit();
}

// Ambil Data Pendukung untuk Dropdown
$categoriesList = $master->getCategories();
$statusList     = $master->getStatus();
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
								<h3 class="mb-0">Edit Data Tugas</h3>
							</div>
							<div class="col-sm-6">
								<ol class="breadcrumb float-sm-end">
									<li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
									<li class="breadcrumb-item"><a href="data-list.php">Daftar Tugas</a></li>
									<li class="breadcrumb-item active" aria-current="page">Edit</li>
								</ol>
							</div>
						</div>
					</div>
				</div>

				<div class="app-content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12">
								<div class="card card-warning card-outline">
									<div class="card-header">
										<h3 class="card-title">Formulir Edit Tugas</h3>
									</div>
                                    
                                    <form action="proses/proses-edit.php" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $dataTask['id_task']; ?>">
                                        
									    <div class="card-body">
                                            
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Nama Tugas / Judul</label>
                                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $dataTask['name']; ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="deskripsi" class="form-label">Deskripsi Detail</label>
                                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required><?php echo $dataTask['description']; ?></textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="category" class="form-label">Kategori Tugas</label>
                                                    <select class="form-select" id="category" name="category" required>
                                                        <option value="" disabled>-- Pilih Kategori --</option>
                                                        <?php 
                                                        foreach ($categoriesList as $category){
                                                            $selected = ($dataTask['category_id'] == $category['id']) ? "selected" : "";
                                                            echo '<option value="'.$category['id'].'" '.$selected.'>'.$category['name'].'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="deadline" class="form-label">Batas Waktu (Deadline)</label>
                                                    <input type="date" class="form-control" id="deadline" name="deadline" value="<?php echo $dataTask['deadline']; ?>" required>
                                                </div>
                                            </div>

                                            <?php if($role_login == '3' && $dataTask['task_type'] == 'Personal'): ?>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="status" class="form-label">Status Penyelesaian</label>
                                                    <select class="form-select" id="status" name="status">
                                                        <?php 
                                                        foreach ($statusList as $st){
                                                            $selected = ($dataTask['status'] == $st['value']) ? "selected" : "";
                                                            echo '<option value="'.$st['value'].'" '.$selected.'>'.$st['name'].'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                        </div>
                                        
									    <div class="card-footer">
                                            <button type="button" class="btn btn-danger me-2 float-start" onclick="window.location.href='data-list.php'"><i class="bi bi-arrow-left"></i> Batal</button>
                                            <button type="submit" class="btn btn-warning float-end"><i class="bi bi-pencil-square"></i> Simpan Perubahan</button>
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
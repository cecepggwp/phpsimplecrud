<?php
session_start();
// Proteksi halaman: Hanya Admin (Role 1) yang boleh masuk
if($_SESSION["role"] != 1){
    header("Location: index.php"); 
    exit();
}

include_once 'config/class-master.php';
$master = new MasterData();

// Ambil data user berdasarkan ID yang dikirim melalui URL
$user = $master->getUserById($_GET['id']);
$dataProdi = $master->getProdi();
// Tidak butuh $dataMk lagi
?>
<!doctype html>
<html lang="en">
<head>
    <?php include 'template/header.php'; ?>
</head>
<body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php include 'template/navbar.php'; include 'template/sidebar.php'; ?>
        
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6"><h3 class="mb-0">Atur Role & Detail User</h3></div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Kategorikan User: <strong><?= $user['username'] ?></strong></h3>
                        </div>
                        
                        <form action="proses/proses-user.php?aksi=setrole" method="POST">
                            <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">
                            
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label">Pilih Kategori (Role)</label>
                                    <select name="role" id="roleSelector" class="form-select" onchange="toggleForm()" required>
                                        <option value="">-- Pilih Role --</option>
                                        <option value="2" <?= ($user['role'] == '2') ? 'selected' : '' ?>>Dosen</option>
                                        <option value="3" <?= ($user['role'] == '3') ? 'selected' : '' ?>>Mahasiswa</option>
                                    </select>
                                    <small class="text-muted">Mengubah role akan memperbarui data di tabel Dosen/Mahasiswa.</small>
                                </div>

                                <div id="formDosen" style="display: none;" class="border p-4 bg-light rounded">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-person-badge"></i> Data Detail Dosen</h5>
                                    <div class="mb-3">
                                        <label>NIP Dosen</label>
                                        <input type="text" name="nip" class="form-control" value="<?= $user['nmr_induk'] ?>" placeholder="Masukkan NIP">
                                    </div>
                                    <div class="mb-3">
                                        <label>Nama Lengkap Dosen</label>
                                        <input type="text" name="nama_dosen" class="form-control" value="<?= $user['nama_lengkap'] ?>" placeholder="Masukkan Nama Lengkap">
                                    </div>
                                    <div class="mb-3">
                                        <label>Program Studi</label>
                                        <select name="id_prodi_dosen" class="form-select">
                                            <option value="">-- Pilih Prodi --</option>
                                            <?php foreach($dataProdi as $p): ?>
                                                <option value="<?= $p['id_prodi'] ?>"><?= $p['nm_prodi'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    </div>

                                <div id="formMhs" style="display: none;" class="border p-4 bg-light rounded">
                                    <h5 class="mb-3 text-success"><i class="bi bi-mortarboard"></i> Data Detail Mahasiswa</h5>
                                    <div class="mb-3">
                                        <label>NIM Mahasiswa</label>
                                        <input type="text" name="nim" class="form-control" value="<?= $user['nmr_induk'] ?>" placeholder="Masukkan NIM">
                                    </div>
                                    <div class="mb-3">
                                        <label>Nama Lengkap Mahasiswa</label>
                                        <input type="text" name="nama_mhs" class="form-control" value="<?= $user['nama_lengkap'] ?>" placeholder="Masukkan Nama Lengkap">
                                    </div>
                                    <div class="mb-3">
                                        <label>Program Studi</label>
                                        <select name="id_prodi_mhs" class="form-select">
                                            <option value="">-- Pilih Prodi --</option>
                                            <?php foreach($dataProdi as $p): ?>
                                                <option value="<?= $p['id_prodi'] ?>"><?= $p['nm_prodi'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <a href="master-user-list.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include 'template/script.php'; ?>

    <script>
    function toggleForm() {
        var role = document.getElementById("roleSelector").value;
        var formDosen = document.getElementById("formDosen");
        var formMhs = document.getElementById("formMhs");

        if (role == "2") {
            formDosen.style.display = "block";
            formMhs.style.display = "none";
        } else if (role == "3") {
            formDosen.style.display = "none";
            formMhs.style.display = "block";
        } else {
            formDosen.style.display = "none";
            formMhs.style.display = "none";
        }
    }
    window.onload = toggleForm;
    </script>
</body>
</html>
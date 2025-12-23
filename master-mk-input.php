<?php
session_start();
include_once 'config/class-master.php';
$master = new MasterData();
$dataProdi = $master->getProdi(); // Ambil daftar prodi untuk dropdown
?>
<!doctype html>
<html lang="en">
<head><?php include 'template/header.php'; ?></head>
<body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php include 'template/navbar.php'; include 'template/sidebar.php'; ?>
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid mt-4">
                    <div class="card">
                        <div class="card-header"><h3>Input Mata Kuliah</h3></div>
                        <form action="proses/proses-mk.php?aksi=inputmk" method="POST">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Program Studi</label>
                                    <select name="id_prodi" class="form-select" required>
                                        <option value="">-- Pilih Prodi --</option>
                                        <?php foreach($dataProdi as $p): ?>
                                            <option value="<?= $p['id_prodi'] ?>"><?= $p['nm_prodi'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama Mata Kuliah</label>
                                    <input type="text" name="nm_mk" class="form-control" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary float-end">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
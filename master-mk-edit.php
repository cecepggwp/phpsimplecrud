<?php
session_start();
include_once 'config/class-master.php';
$master = new MasterData();
$dataMk = $master->getUpdateMk($_GET['id']);
$dataProdi = $master->getProdi();
?>
<!doctype html>
<html lang="en">
<head><?php include 'template/header.php'; ?></head>
<body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php include 'template/navbar.php'; include 'template/sidebar.php'; ?>
        <main class="app-main">
            <div class="container-fluid mt-4">
                <div class="card">
                    <div class="card-header"><h3>Edit Mata Kuliah</h3></div>
                    <form action="proses/proses-mk.php?aksi=updatemk" method="POST">
                        <input type="hidden" name="id_mk" value="<?= $dataMk['id_mk'] ?>">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Program Studi</label>
                                <select name="id_prodi" class="form-select" required>
                                    <?php foreach($dataProdi as $p): ?>
                                        <option value="<?= $p['id_prodi'] ?>" <?= ($p['id_prodi'] == $dataMk['id_prodi']) ? 'selected' : '' ?>>
                                            <?= $p['nm_prodi'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Mata Kuliah</label>
                                <input type="text" name="nm_mk" class="form-control" value="<?= $dataMk['nm_mk'] ?>" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-warning float-end">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
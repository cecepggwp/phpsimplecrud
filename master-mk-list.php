<?php
session_start();
if($_SESSION["role"] == 3 || ($_SESSION["role"] == 2)){
    header("Location: index.php"); exit();
}
include_once 'config/class-master.php';
$master = new MasterData();
$dataMk = $master->getMk();
?>
<!doctype html>
<html lang="en">
<head><?php include 'template/header.php'; ?></head>
<body class="layout-fixed fixed-header fixed-footer sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <?php include 'template/navbar.php'; include 'template/sidebar.php'; ?>
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6"><h3 class="mb-0">Master Mata Kuliah</h3></div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Daftar Mata Kuliah</h3></div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Kuliah</th>
                                        <th>Program Studi</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dataMk as $index => $row): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= $row['nm_mk'] ?></td>
                                        <td><?= $row['nm_prodi'] ?></td>
                                        <td class="text-center">
                                            <a href="master-mk-edit.php?id=<?= $row['id_mk'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="proses/proses-mk.php?aksi=deletemk&id=<?= $row['id_mk'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary" onclick="window.location.href='master-mk-input.php'">Tambah Mata Kuliah</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include 'template/script.php'; ?>
</body>
</html>
<?php
session_start();
include_once 'config/class-master.php';
$master = new MasterData();

$search = isset($_GET['search']) ? $_GET['search'] : null;
$users = $master->getUsers($search);
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
                    <div class="card-header">
                        <h3 class="card-title">Manajemen User & Role</h3>
                        <div class="card-tools">
                            <form action="" method="GET" class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control" placeholder="Cari username/email..." value="<?= $search ?>">
                                <button type="submit" class="btn btn-default"><i class="bi bi-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role Saat Ini</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($users as $u): ?>
                                <tr>
                                    <td><?= $u['id_user'] ?></td>
                                    <td><?= $u['username'] ?></td>
                                    <td><?= $u['email'] ?></td>
                                    <td>
                                        <?php 
                                            if($u['role'] == '1') echo '<span class="badge bg-danger">Admin</span>';
                                            elseif($u['role'] == '2') echo '<span class="badge bg-success">Dosen</span>';
                                            elseif($u['role'] == '3') echo '<span class="badge bg-primary">Mahasiswa</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <a href="master-user-set-role.php?id=<?= $u['id_user'] ?>" class="btn btn-sm btn-info">Atur Role & Detail</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
        <?php include 'template/footer.php'; ?>
    </div>
    <?php include 'template/script.php'; ?>
</body>
</html>
<?php
include '../config/class-master.php';
$master = new MasterData();

if($_GET['aksi'] == 'inputmk'){
    $data = [
        'nm_mk' => $_POST['nm_mk'],
        'id_prodi' => $_POST['id_prodi']
    ];
    $master->inputMk($data) ? header("Location: ../master-mk-list.php?status=success") : header("Location: ../master-mk-input.php?status=failed");

} elseif($_GET['aksi'] == 'updatemk'){
    $data = [
        'id_mk' => $_POST['id_mk'],
        'nm_mk' => $_POST['nm_mk'],
        'id_prodi' => $_POST['id_prodi']
    ];
    $master->updateMk($data) ? header("Location: ../master-mk-list.php?status=success") : header("Location: ../master-mk-edit.php?id=".$_POST['id_mk']."&status=failed");

} elseif($_GET['aksi'] == 'deletemk'){
    $master->deleteMk($_GET['id']) ? header("Location: ../master-mk-list.php?status=success") : header("Location: ../master-mk-list.php?status=failed");
}
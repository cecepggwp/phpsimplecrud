<?php
include '../config/class-master.php';
$master = new MasterData();

if($_GET['aksi'] == 'inputprodi'){
    $data = [
        'nm_prodi' => $_POST['nm_prodi']
    ];
    $input = $master->inputProdi($data);
    if($input){
        header("Location: ../master-prodi-list.php?status=inputsuccess");
    } else {
        header("Location: ../master-prodi-input.php?status=failed");
    }

} elseif($_GET['aksi'] == 'updateprodi'){
    $data = [
        'id_prodi' => $_POST['id_prodi'],
        'nm_prodi' => $_POST['nm_prodi']
    ];
    $update = $master->updateProdi($data);
    if($update){
        header("Location: ../master-prodi-list.php?status=editsuccess");
    } else {
        header("Location: ../master-prodi-edit.php?id=".$dataProdi['id_prodi']."&status=failed");
    }

} elseif($_GET['aksi'] == 'deleteprodi'){
    $id = $_GET['id'];
    $delete = $master->deleteProdi($id);
    if($delete){
        header("Location: ../master-prodi-list.php?status=deletesuccess");
    } else {
        header("Location: ../master-prodi-list.php?status=deletefailed");
    }
}
?>
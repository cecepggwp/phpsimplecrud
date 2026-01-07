<?php
session_start();
// Pastikan user login dan role Dosen (2)
if(!isset($_SESSION['id']) || $_SESSION['role'] != '2'){
    header("Location: ../index.php");
    exit();
}

include_once '../config/class-master.php';
$master = new MasterData();

$id_user  = $_SESSION['id'];
$id_prodi = $_POST['id_prodi'];

// Panggil fungsi updateDosenData (yang sekarang cuma butuh 2 parameter)
$update = $master->updateDosenData($id_user, $id_prodi);

if($update){
    header("Location: ../home.php");
} else {
    echo "<script>alert('Gagal update data.'); window.location.href='../home.php';</script>";
}
?>
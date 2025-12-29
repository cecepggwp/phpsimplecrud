<?php
session_start();
if(!isset($_SESSION['id'])){
    header("Location: ../index.php");
    exit();
}

include_once '../config/class-master.php';
$master = new MasterData();

$id_user  = $_SESSION['id'];
$role     = $_SESSION['role'];
$id_prodi = $_POST['id_prodi'];

// Panggil fungsi update (Pastikan method updateUserProdi ada di class-master.php kamu sesuai instruksi sebelumnya)
$update = $master->updateUserProdi($id_user, $role, $id_prodi);

if($update){
    // Jika sukses, kembali ke home (form warning akan hilang otomatis)
    header("Location: ../home.php");
} else {
    echo "<script>alert('Gagal memperbarui Program Studi.'); window.location.href='../home.php';</script>";
}
?>
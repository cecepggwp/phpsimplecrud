<?php
session_start();
if(!isset($_SESSION["id"])){
    header("Location: ../index.php");
    exit();
}

include_once '../config/class-mahasiswa.php';
$mahasiswa = new Mahasiswa();

$id        = $_GET['id'];
$user_id   = $_SESSION['id'];
$role_user = $_SESSION['role'];

// Pass user_id dan role. Jika Mahasiswa mencoba hapus tugas Dosen, 
// fungsi deleteTask akan return false karena ID tidak cocok dengan created_by
$delete = $mahasiswa->deleteTask($id, $user_id, $role_user);

if($delete){
    header("Location: ../data-list.php?status=deletesuccess");
} else {
    header("Location: ../data-list.php?status=deletefailed");
}
?>
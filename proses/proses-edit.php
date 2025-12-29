<?php
session_start();
if(!isset($_SESSION["id"])){
    header("Location: ../index.php");
    exit();
}

include_once '../config/class-mahasiswa.php';
$mahasiswa = new Mahasiswa();

$dataMahasiswa = [
    'id' => $_POST['id'],
    'name' => $_POST['name'],
    'description' => $_POST['deskripsi'], // Pastikan name di form edit adalah 'deskripsi'
    'deadline' => $_POST['deadline'],
    'category' => $_POST['category'],
    'status' => $_POST['status'],
];

$user_id   = $_SESSION['id'];
$role_user = $_SESSION['role'];

// Pass user_id dan role untuk validasi kepemilikan di dalam query
$edit = $mahasiswa->updateTask($dataMahasiswa, $user_id, $role_user);

if($edit){
    header("Location: ../data-list.php?status=editsuccess");
} else {
    header("Location: ../data-edit.php?id=".$_POST['id']."&status=failed");
}
?>
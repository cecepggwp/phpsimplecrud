<?php
session_start();
// Pastikan user sudah login
if(!isset($_SESSION["id"])){
    header("Location: ../index.php");
    exit();
}

include '../config/class-mahasiswa.php';
$tasklist = new Mahasiswa();

$datatasklist = [
    'name' => $_POST['name'],
    'deskripsi' => $_POST['deskripsi'],
    'deadline' => $_POST['deadline'],
    'category' => $_POST['category'],
];

// Mengambil ID User dan Role dari Session
$user_id   = $_SESSION['id']; // ID User yang login
$role_user = $_SESSION['role']; // Role User (1, 2, atau 3)

// Pass user_id dan role ke fungsi
$input = $tasklist->addTask($datatasklist, $user_id, $role_user);

if($input){
    header("Location: ../data-list.php?status=inputsuccess");
} else {
    header("Location: ../data-input.php?status=failed");
}
?>
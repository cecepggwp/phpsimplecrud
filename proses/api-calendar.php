<?php
session_start();
header('Content-Type: application/json');

// Pastikan User Login
if(!isset($_SESSION['id'])){
    echo json_encode([]);
    exit();
}

include '../config/class-mahasiswa.php';
$mahasiswa = new Mahasiswa();

$id_user = $_SESSION['id'];
$role    = $_SESSION['role'];

// Ambil Data JSON dari method yang baru kita buat
echo $mahasiswa->getCalendarEvents($id_user, $role);
?>
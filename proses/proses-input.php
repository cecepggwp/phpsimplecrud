<?php
session_start();
if(!isset($_SESSION["id"])){ header("Location: ../index.php"); exit(); }

include '../config/class-mahasiswa.php';
$tasklist = new Mahasiswa();

// Tangkap id_matkul (Bisa dari Dosen atau Admin)
$id_matkul_input = isset($_POST['id_matkul']) && $_POST['id_matkul'] != '' ? $_POST['id_matkul'] : NULL;

// Tangkap target_prodi (Khusus Admin)
$target_prodi_input = isset($_POST['target_prodi']) ? $_POST['target_prodi'] : NULL;

$datatasklist = [
    'name'      => $_POST['name'],
    'deskripsi' => $_POST['deskripsi'],
    'deadline'  => $_POST['deadline'],
    'category'  => $_POST['category'],
    'id_matkul' => $id_matkul_input,      // Tambahkan ini
    'target_prodi' => $target_prodi_input // Tambahkan ini
];

$user_id   = $_SESSION['id']; 
$role_user = $_SESSION['role']; 

$input = $tasklist->addTask($datatasklist, $user_id, $role_user);

if($input){
    header("Location: ../data-list.php?status=inputsuccess");
} else {
    header("Location: ../data-input.php?status=failed");
}
?>
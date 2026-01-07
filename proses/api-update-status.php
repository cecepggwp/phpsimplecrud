<?php
session_start();
header('Content-Type: application/json');

// Cek Login
if(!isset($_SESSION['id'])){
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include '../config/class-mahasiswa.php';

$mahasiswa = new Mahasiswa();
$user_id   = $_SESSION['id'];
$role      = $_SESSION['role'];

// Ambil data JSON yang dikirim oleh Javascript (fetch)
$input = json_decode(file_get_contents('php://input'), true);

if(isset($input['id_task']) && isset($input['status'])){
    // Panggil fungsi update di Class Mahasiswa
    $update = $mahasiswa->updateTaskStatus($input['id_task'], $input['status'], $user_id, $role);
    
    if($update){
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal update database']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
}
?>
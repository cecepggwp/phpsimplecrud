<?php
session_start();
// Aktifkan Error Reporting agar ketahuan jika ada salah koding
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION["id"]) || ($_SESSION["role"] == '3')){
    header("Location: ../index.php");
    exit();
}

include '../config/class-mahasiswa.php';
$mahasiswa = new Mahasiswa();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    
    $id_task = $_POST['id_task'];
    
    // Ambil array ID mahasiswa yang diceklis (Completed)
    $completed_ids = isset($_POST['completed_students']) ? $_POST['completed_students'] : [];

    // Ambil semua penerima tugas untuk diloop
    $allReceivers = $mahasiswa->getTaskReceivers($id_task);

    $countSuccess = 0;

    foreach($allReceivers as $mhs){
        $id_mhs = $mhs['id_mhs'];
        
        // Logika: Cek apakah ID ini ada di daftar yang dicentang admin?
        if(in_array($id_mhs, $completed_ids)){
            $status = 'Completed';
        } else {
            $status = 'Pending';
        }

        // Eksekusi Update
        $update = $mahasiswa->verifyTaskStatus($id_task, $id_mhs, $status);
        
        if($update){
            $countSuccess++;
        }
    }

    // Jika berhasil loop, kembalikan ke halaman
    header("Location: ../task-details.php?id=$id_task&msg=updated");
    exit();
} else {
    echo "Akses Ditolak. Method bukan POST.";
}
?>
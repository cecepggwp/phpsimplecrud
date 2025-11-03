<?php

// Memasukkan file class-mahasiswa.php untuk mengakses class Mahasiswa
include '../config/class-mahasiswa.php';
// Membuat objek dari class Mahasiswa
$tasklist = new Mahasiswa();
// Mengambil data mahasiswa dari form input menggunakan metode POST dan menyimpannya dalam array
$datatasklist = [
    'name' => $_POST['name'],
    'deskripsi' => $_POST['deskripsi'],
    'deadline' => $_POST['deadline'],
    'category' => $_POST['category'],
];
// Memanggil method inputMahasiswa untuk memasukkan data mahasiswa dengan parameter array $dataMahasiswa
$input = $tasklist->addTask($datatasklist);
// Mengecek apakah proses input berhasil atau tidak - true/false
if($input){
    // Jika berhasil, redirect ke halaman data-list.php dengan status inputsuccess
    header("Location: ../data-list.php?status=inputsuccess");
} else {
    // Jika gagal, redirect ke halaman data-input.php dengan status failed
    header("Location: ../data-input.php?status=failed");
}

?>
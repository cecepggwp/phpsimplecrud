<?php
// Sesuaikan path jika file db-config.php dan class-master.php tidak berada di direktori yang sama
include_once 'db-config.php'; 
include_once 'class-master.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Cek field yang diperlukan: username, email, password, retype_password
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['retype_password'])) {
        // Arahkan kembali ke register.php (satu level di atas folder config)
        header('Location: ../register.php?pesan=incomplete');
        exit();
    }
    
    // 2. Cek kesamaan password
    if ($_POST['password'] !== $_POST['retype_password']) {
        header('Location: ../register.php?pesan=password_mismatch');
        exit();
    }
    
    // 3. Siapkan data untuk class MasterData (hanya username, email, password)
    $data_user = [
        'username' => $_POST['username'], 
        'email'    => $_POST['email'],
        'password' => $_POST['password'] // Plain Text
    ];

    // 4. Buat objek MasterData dan panggil method inputUsers
    $master = new MasterData();
    $register_result = $master->inputUsers($data_user);

    if ($register_result === true) {
        // Registrasi berhasil, arahkan ke halaman login (index.php)
        header('Location: ../index.php?pesan=register_success');
        exit();
    } elseif ($register_result === "exists") {
        // Username atau email sudah ada
        header('Location: ../register.php?pesan=user_exists');
        exit();
    } else {
        // Registrasi gagal karena error database
        header('Location: ../register.php?pesan=register_failed');
        exit();
    }
} else {
    // Jika diakses tanpa method POST
    header('Location: ../register.php');
    exit();
}
?>
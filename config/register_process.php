<?php
include_once 'db-config.php'; 
include_once 'class-master.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Validasi field wajib
    if (empty($_POST['nama_lengkap']) || empty($_POST['username']) || empty($_POST['email']) || 
        empty($_POST['password']) || empty($_POST['retype_password']) || 
        empty($_POST['nmr_induk']) || empty($_POST['id_prodi'])) {
        header('Location: ../register.php?pesan=incomplete');
        exit();
    }
    
    if ($_POST['password'] !== $_POST['retype_password']) {
        header('Location: ../register.php?pesan=password_mismatch');
        exit();
    }
    
    // Tangkap data, termasuk array id_mk
    $id_mk_array = isset($_POST['id_mk']) ? $_POST['id_mk'] : [];

    $data_user = [
        'nama_lengkap' => $_POST['nama_lengkap'],
        'username'     => $_POST['username'], 
        'email'        => $_POST['email'],
        'password'     => $_POST['password'],
        'nmr_induk'    => $_POST['nmr_induk'],
        'id_prodi'     => $_POST['id_prodi'],
        'role'         => $_POST['role'], 
        'id_mk'        => $id_mk_array 
    ];

    $master = new MasterData();
    $register_result = $master->inputUsers($data_user);

    if ($register_result === true) {
        header('Location: ../index.php?pesan=register_success');
        exit();
    } elseif ($register_result === "exists") {
        header('Location: ../register.php?pesan=user_exists');
        exit();
    } else {
        header('Location: ../register.php?pesan=register_failed');
        exit();
    }
} else {
    header('Location: ../register.php');
    exit();
}
?>
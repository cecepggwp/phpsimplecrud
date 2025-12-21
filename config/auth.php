<?php
session_start();
// Pastikan file koneksi database disertakan
include ('db-config.php'); //

// Inisialisasi objek Database
$database = new Database();
// Akses objek koneksi mysqli yang sudah dibuat di db-config.php
$conn = $database->conn; 

// Ambil input dari pengguna
$username = $_POST['username']; //
$password = $_POST['password']; //

// 1. Kueri dengan Prepared Statements
// Kami mengambil baris pengguna berdasarkan username dan password.
// Gunakan placeholder (?) untuk data yang dimasukkan pengguna.
$sql = "SELECT * FROM tb_users WHERE username = ? AND password = ?"; 

// Inisialisasi statement
// Perlu diingat bahwa fungsi prepare() adalah method dari objek $conn (mysqli object)
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Menangani error jika kueri gagal dipersiapkan
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

// 2. Bind Parameter
// Mengikat nilai $username dan $password ke placeholder (s = string, s = string)
$stmt->bind_param("ss", $username, $password);

// 3. Eksekusi Statement
$stmt->execute();

// 4. Ambil Hasil
$result = $stmt->get_result();

// 5. Cek Hasil
if ($result->num_rows === 1) {
    $fetch = $result->fetch_assoc();
    $_SESSION["id"] = $fetch["id_user"];
    $_SESSION["user"] = $fetch["username"];
    $_SESSION["role"] = $fetch["role"];
    // Login berhasil
    header('Location:../home.php');
    exit();
} else {
    // Login gagal
    header('Location:../?pesan=gagal');
    exit();
}

// Tutup statement
$stmt->close();
?>
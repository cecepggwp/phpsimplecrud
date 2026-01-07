<?php
// Memasukkan file class-master.php untuk mengakses class MasterData
// Sesuaikan path jika letak folder 'proses' dan 'config' berbeda
include_once '../config/class-master.php';

// Membuat objek dari class MasterData
$master = new MasterData();

// Mengecek aksi yang dilakukan berdasarkan parameter GET 'aksi'
if (isset($_GET['aksi'])) {
    
    // AKSI: SET ROLE (Mengatur Role dan Detail User)
    if ($_GET['aksi'] == 'setrole') {
        
        // 1. Ambil data dasar dari POST
        $id_user = $_POST['id_user'];
        $role    = $_POST['role'];

        // 2. Siapkan array data untuk dikirim ke Method Class
        $data = [
            'id_user' => $id_user,
            'role'    => $role
        ];

        // 3. Percabangan berdasarkan Role yang dipilih Admin
        if ($role == '2') { 
            // Jika dipilih sebagai DOSEN
            $data['nip_nim']  = $_POST['nip'];
            $data['nama']     = $_POST['nama_dosen'];
            $data['id_prodi'] = $_POST['id_prodi_dosen'];
            // id_mk sudah dihapus

        } elseif ($role == '3') { 
            // Jika dipilih sebagai MAHASISWA
            $data['nip_nim']  = $_POST['nim'];
            $data['nama']     = $_POST['nama_mhs'];
            $data['id_prodi'] = $_POST['id_prodi_mhs'];
        }

        // 4. Panggil method updateRoleAndDetail di class-master.php
        $update = $master->updateRoleAndDetail($data);

        if ($update) {
            // Jika berhasil, arahkan ke daftar user dengan status success
            header("Location: ../master-user-list.php?status=success");
            exit();
        } else {
            // Jika gagal, kembali ke form setting dengan status failed
            header("Location: ../master-user-set-role.php?id=$id_user&status=failed");
            exit();
        }
    } 
}
?>
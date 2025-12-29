<?php

// Memasukkan file konfigurasi database
include_once 'db-config.php';

class MasterData extends Database {

    // Method untuk mendapatkan daftar program studi
    public function getCategories(){
        $query = "SELECT * FROM tb_categories";
        $result = $this->conn->query($query);
        $categories = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $categories[] = [
                    'id' => $row['id'],
                    'name' => $row['name']
                ];
            }
        }
        return $categories;
    }

    // Method untuk mendapatkan daftar provinsi
    /* public function getUsername(){
        $query = "SELECT id, username FROM users";
        $result = $this->conn->query($query);
        $username = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $username[] = [
                    'id' => $row['id'],
                    'name' => $row['username']
                ];
            }
        }
        return $username;
    } */


    // Method untuk input data program studi
    public function inputCategories($data){
        $name = $data['name'];
        $query = "INSERT INTO tb_categories (name) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("s", $name);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Method untuk mendapatkan data program studi berdasarkan kode
    public function getUpdateCategories($id){
        $query = "SELECT * FROM tb_categories WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $categories = null;
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $categories = [
                'id' => $row['id'],
                'name' => $row['name']
            ];
        }
        $stmt->close();
        return $categories;
    }

    // Method untuk mengedit data program studi
    public function updateCategories($data){
        $id = $data['id'];
        $name = $data['name'];
        $query = "UPDATE tb_categories SET name = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("si", $name, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Method untuk menghapus data program studi
    public function deleteCategories($id){
        $query = "DELETE FROM tb_categories WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Method untuk input data provinsi
/*     public function inputUsers($data){
        $username = $data['username'];
        $pass = $data['pass'];
        $query = "INSERT INTO users (username, pass) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("ss", $username, $pass);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    } */

    // Method untuk mendapatkan data provinsi berdasarkan id
/*     public function getUpdateUsers($id){
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = null;
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $users = [
                'id' => $row['id'],
                'username' => $row['username'],
                'pass' => $row['pass']
            ];
        }
        $stmt->close();
        return $users;
    } */

    // Method untuk mengedit data provinsi
/*     public function updateUsers($data){
        $id = $data['id'];
        $username = $data['username'];
        $pass = $data['pass'];
        $query = "UPDATE users SET username = ?, pass = ?  WHERE id_provinsi = ?";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("ssi", $username, $pass, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
 */
    // Method untuk menghapus data provinsi
/*     public function deleteUser($id){
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    } */

    public function getStatus(){
        return [
            ['name' => 'Pending', 'value' => 'Pending'],
            ['name' => 'Completed', 'value' => 'Completed']
        ];
    }

// Tambahkan kode ini di dalam class MasterData di class-master.php

// Method pendukung untuk cek duplikasi username atau email
public function isUserExists($username, $email){
    // Menggunakan id_user sebagai kolom primary key
    $query = "SELECT id_user FROM tb_users WHERE username = ? OR email = ?";
    $stmt = $this->conn->prepare($query);
    
    if(!$stmt){
        // Error saat prepare statement
        return false; 
    }
    
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    
    return $exists;
}

// Method untuk input data pengguna (Registrasi)
public function inputUsers($data){
    $nama_lengkap = $data['nama_lengkap'];
    $username     = $data['username'];
    $email        = $data['email'];
    $password     = $data['password']; 
    $nmr_induk    = $data['nmr_induk'];
    $role         = $data['role']; 
    $id_prodi     = $data['id_prodi'];
    // id_mk berupa ARRAY karena checkbox (bisa pilih banyak)
    $mk_array     = isset($data['id_mk']) ? $data['id_mk'] : [];

    // Cek duplikasi username/email
    $check = $this->conn->prepare("SELECT id_user FROM tb_users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    if($check->get_result()->num_rows > 0) return "exists";

    $this->conn->begin_transaction();

    try {
        // A. Insert ke tb_users
        $queryUser = "INSERT INTO tb_users (nama_lengkap, username, password, email, role, nmr_induk) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtUser = $this->conn->prepare($queryUser);
        $stmtUser->bind_param("ssssss", $nama_lengkap, $username, $password, $email, $role, $nmr_induk);
        $stmtUser->execute();
        $id_user = $this->conn->insert_id;

        // B. Cek Role untuk Insert Lanjutan
        if($role == '2'){ // DOSEN
            // 1. Insert Data Dosen (Tanpa Matkul di tabel tb_dosen)
            $queryDosen = "INSERT INTO tb_dosen (id_user, nip_dosen, nama_dosen, id_prodi) VALUES (?, ?, ?, ?)";
            $stmtDosen = $this->conn->prepare($queryDosen);
            $stmtDosen->bind_param("issi", $id_user, $nmr_induk, $nama_lengkap, $id_prodi);
            $stmtDosen->execute();
            $id_dosen = $this->conn->insert_id;

            // 2. Insert Banyak Mata Kuliah ke tb_dosen_matakuliah
            if(!empty($mk_array) && is_array($mk_array)){
                // Kita tidak perlu menyebut kolom 'id_dosen_matakuliah' karena itu AUTO_INCREMENT
                $stmtMK = $this->conn->prepare("INSERT INTO tb_dosen_matakuliah (id_dosen, id_mk) VALUES (?, ?)");
                foreach($mk_array as $mk_id){
                    $stmtMK->bind_param("ii", $id_dosen, $mk_id);
                    $stmtMK->execute();
                }
            }
        
        } else { // MAHASISWA
            $queryMhs = "INSERT INTO tb_mhs (id_user, nim_mhs, nm_mhs, id_prodi) VALUES (?, ?, ?, ?)";
            $stmtMhs = $this->conn->prepare($queryMhs);
            $stmtMhs->bind_param("issi", $id_user, $nmr_induk, $nama_lengkap, $id_prodi);
            $stmtMhs->execute();
        }

        $this->conn->commit(); 
        return true;
    } catch (Exception $e) {
        $this->conn->rollback();
        return false;
    }
}


// --- CEK KELENGKAPAN DATA USER (UNTUK ALERT DI HOME) ---
    // Return: 0 (Lengkap), 1 (Prodi Kosong), 2 (Matkul Kosong)
    public function checkUserDataStatus($id_user, $role){
        if($role == '3'){ // Mahasiswa
            $q = $this->conn->query("SELECT id_prodi FROM tb_mhs WHERE id_user = $id_user");
            $data = $q->fetch_assoc();
            if(empty($data['id_prodi'])) return 1; 
        } 
        elseif($role == '2'){ // Dosen
            // 1. Cek Prodi di tabel tb_dosen
            $q = $this->conn->query("SELECT id_dosen, id_prodi FROM tb_dosen WHERE id_user = $id_user");
            $data = $q->fetch_assoc();
            
            if(!$data || empty($data['id_prodi'])) return 1; // Prodi Kosong

            // 2. Cek apakah sudah punya Matkul di tabel tb_dosen_matakuliah
            $id_dosen = $data['id_dosen'];
            $q2 = $this->conn->query("SELECT count(*) as total FROM tb_dosen_matakuliah WHERE id_dosen = $id_dosen");
            $mkData = $q2->fetch_assoc();
            
            if($mkData['total'] == 0) return 2; // Matkul Kosong
        }
        return 0; // Aman
    }


    public function updateDosenData($id_user, $id_prodi, $mk_array){
        $this->conn->begin_transaction();
        try {
            // 1. Update Prodi di tabel tb_dosen
            $this->conn->query("UPDATE tb_dosen SET id_prodi = $id_prodi WHERE id_user = $id_user");
            
            // Ambil ID Dosen
            $q = $this->conn->query("SELECT id_dosen FROM tb_dosen WHERE id_user = $id_user");
            $dosen = $q->fetch_assoc();
            $id_dosen = $dosen['id_dosen'];

            // 2. Hapus semua matkul lama (Reset) di tb_dosen_matakuliah
            // Kita menghapus berdasarkan id_dosen, jadi perubahan nama primary key tidak berpengaruh di sini
            $this->conn->query("DELETE FROM tb_dosen_matakuliah WHERE id_dosen = $id_dosen");

            // 3. Masukkan matkul baru yang dipilih (Looping)
            if(!empty($mk_array) && is_array($mk_array)){
                $stmt = $this->conn->prepare("INSERT INTO tb_dosen_matakuliah (id_dosen, id_mk) VALUES (?, ?)");
                foreach($mk_array as $mk_id){
                    $stmt->bind_param("ii", $id_dosen, $mk_id);
                    $stmt->execute();
                }
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }



public function checkUserProdi($id_user, $role){
    if($role == '3'){
        // Cek Mahasiswa
        $query = "SELECT id_prodi FROM tb_mhs WHERE id_user = ?";
    } elseif($role == '2'){
        // Cek Dosen
        $query = "SELECT id_prodi FROM tb_dosen WHERE id_user = ?";
    } else {
        return true; // Admin dianggap aman
    }

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    // Jika id_prodi NULL atau 0, return false (artinya belum punya prodi)
    if(empty($result['id_prodi'])){
        return false;
    }
    return true;
}

// TAMBAHKAN METHOD BARU: Update Prodi User (Dari Home)
public function updateUserProdi($id_user, $role, $id_prodi){
    if($role == '3'){
        $query = "UPDATE tb_mhs SET id_prodi = ? WHERE id_user = ?";
    } elseif($role == '2'){
        $query = "UPDATE tb_dosen SET id_prodi = ? WHERE id_user = ?";
    } else {
        return false;
    }
    
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ii", $id_prodi, $id_user);
    return $stmt->execute();
}


// Method untuk mendapatkan semua daftar prodi
public function getProdi(){
    $query = "SELECT * FROM tb_prodi ORDER BY id_prodi DESC";
    $result = $this->conn->query($query);
    $prodi = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $prodi[] = [
                'id_prodi' => $row['id_prodi'],
                'nm_prodi' => $row['nm_prodi']
            ];
        }
    }
    return $prodi;
}

// Method untuk input data prodi
public function inputProdi($data){
    $nm_prodi = $data['nm_prodi'];
    $query = "INSERT INTO tb_prodi (nm_prodi) VALUES (?)";
    $stmt = $this->conn->prepare($query);
    if(!$stmt){
        return false;
    }
    $stmt->bind_param("s", $nm_prodi);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Method untuk mengambil 1 data prodi berdasarkan ID (untuk Edit)
public function getUpdateProdi($id){
    $query = "SELECT * FROM tb_prodi WHERE id_prodi = ?";
    $stmt = $this->conn->prepare($query);
    if(!$stmt){
        return false;
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $prodi = null;
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $prodi = [
            'id_prodi' => $row['id_prodi'],
            'nm_prodi' => $row['nm_prodi']
        ];
    }
    $stmt->close();
    return $prodi;
}

// Method untuk mengupdate data prodi
public function updateProdi($data){
    $id = $data['id_prodi'];
    $name = $data['nm_prodi'];
    $query = "UPDATE tb_prodi SET nm_prodi = ? WHERE id_prodi = ?";
    $stmt = $this->conn->prepare($query);
    if(!$stmt){
        return false;
    }
    $stmt->bind_param("si", $name, $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Method untuk menghapus data prodi
public function deleteProdi($id){
    $query = "DELETE FROM tb_prodi WHERE id_prodi = ?";
    $stmt = $this->conn->prepare($query);
    if(!$stmt){
        return false;
    }
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// --- MASTER MATA KULIAH ---

public function getMk(){
    // Join dengan tb_prodi agar kita bisa tahu mata kuliah ini milik prodi mana
    $query = "SELECT tb_matakuliah.*, tb_prodi.nm_prodi 
              FROM tb_matakuliah 
              JOIN tb_prodi ON tb_matakuliah.id_prodi = tb_prodi.id_prodi 
              ORDER BY tb_matakuliah.id_mk DESC";
    $result = $this->conn->query($query);
    $mk = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $mk[] = $row;
        }
    }
    return $mk;
}

public function inputMk($data){
    $nm_mk = $data['nm_mk'];
    $id_prodi = $data['id_prodi'];
    $query = "INSERT INTO tb_matakuliah (nm_mk, id_prodi) VALUES (?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("si", $nm_mk, $id_prodi);
    return $stmt->execute();
}

public function getUpdateMk($id){
    $query = "SELECT * FROM tb_matakuliah WHERE id_mk = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

public function updateMk($data){
    $id_mk = $data['id_mk'];
    $nm_mk = $data['nm_mk'];
    $id_prodi = $data['id_prodi'];
    $query = "UPDATE tb_matakuliah SET nm_mk = ?, id_prodi = ? WHERE id_mk = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("sii", $nm_mk, $id_prodi, $id_mk);
    return $stmt->execute();
}

public function deleteMk($id){
    $query = "DELETE FROM tb_matakuliah WHERE id_mk = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// --- MASTER USER & ROLE MANAGEMENT ---

public function getUsers($search = null) {
    $query = "SELECT * FROM tb_users";
    if ($search) {
        $query .= " WHERE username LIKE '%$search%' OR email LIKE '%$search%'";
    }
    $query .= " ORDER BY id_user DESC";
    $result = $this->conn->query($query);
    $users = [];
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}

public function getUserById($id) {
    $query = "SELECT * FROM tb_users WHERE id_user = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

public function updateRoleAndDetail($data) {
    $id_user = $data['id_user'];
    $role    = $data['role'];
    $nip_nim = $data['nip_nim'];
    $nama    = $data['nama'];
    $id_prodi= isset($data['id_prodi']) ? $data['id_prodi'] : NULL;
    $id_mk   = isset($data['id_mk']) ? $data['id_mk'] : NULL;

    $this->conn->begin_transaction();

    try {
        // A. Update Role, Nama, dan Nmr Induk di tabel utama tb_users
        $queryUser = "UPDATE tb_users SET role = ?, nama_lengkap = ?, nmr_induk = ? WHERE id_user = ?";
        $stmtUser = $this->conn->prepare($queryUser);
        $stmtUser->bind_param("sssi", $role, $nama, $nip_nim, $id_user);
        $stmtUser->execute();

        if ($role == '2') { // JIKA JADI DOSEN
            // Hapus dari tabel mahasiswa (jika ada)
            $this->conn->query("DELETE FROM tb_mhs WHERE id_user = $id_user");
            
            // Masukkan atau Update ke tabel dosen
            $queryDosen = "INSERT INTO tb_dosen (id_user, nip_dosen, nama_dosen, id_prodi, id_mk) 
                           VALUES (?, ?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE nip_dosen=?, nama_dosen=?, id_prodi=?, id_mk=?";
            $stmtDosen = $this->conn->prepare($queryDosen);
            $stmtDosen->bind_param("issiiisii", 
                $id_user, $nip_nim, $nama, $id_prodi, $id_mk,
                $nip_nim, $nama, $id_prodi, $id_mk
            );
            $stmtDosen->execute();

        } elseif ($role == '3') { // JIKA JADI MAHASISWA
            // Hapus dari tabel dosen (jika ada)
            $this->conn->query("DELETE FROM tb_dosen WHERE id_user = $id_user");

            // Masukkan atau Update ke tabel mahasiswa
            $queryMhs = "INSERT INTO tb_mhs (id_user, nim_mhs, nm_mhs, id_prodi) 
                         VALUES (?, ?, ?, ?) 
                         ON DUPLICATE KEY UPDATE nim_mhs=?, nm_mhs=?, id_prodi=?";
            $stmtMhs = $this->conn->prepare($queryMhs);
            $stmtMhs->bind_param("issiissi", 
                $id_user, $nip_nim, $nama, $id_prodi,
                $nip_nim, $nama, $id_prodi
            );
            $stmtMhs->execute();
        }

        $this->conn->commit();
        return true;
    } catch (Exception $e) {
        $this->conn->rollback();
        return false;
    }
}
}
?>
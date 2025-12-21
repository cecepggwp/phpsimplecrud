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
    // HANYA AMBIL username, email, password
    $username = $data['username'];
    $email    = $data['email'];
    $password = $data['password'];

    // Cek apakah username/email sudah ada
    if ($this->isUserExists($username, $email)) {
        return "exists"; // Mengembalikan string "exists" jika username/email sudah ada
    }

    // Menggunakan Prepared Statement untuk INSERT (Wajib Keamanan)
    // ASUMSI KOLOM: username, email, password
    $query = "INSERT INTO tb_users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    
    if(!$stmt){
        return false;
    }
    
    // Parameter: sss (Semua String)
    $stmt->bind_param("sss", $username, $email, $password);
    
    $result = $stmt->execute();
    $stmt->close();
    
    return $result; // Mengembalikan true/false
}

}
?>
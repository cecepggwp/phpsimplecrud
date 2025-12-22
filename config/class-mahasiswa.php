<?php 

// Memasukkan file konfigurasi database
include_once 'db-config.php';

class Mahasiswa extends Database {

    // Method untuk input data mahasiswa
    public function addTask($data){
        // Mengambil data dari parameter $data
        $name        = $data['name'];
        $description = $data['deskripsi'];
        $deadline    = $data['deadline'];
        $category_id = $data['category'];
        // Menyiapkan query SQL untuk insert data menggunakan prepared statement
        $query = "INSERT INTO tasks (name, description, deadline, category_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        // Mengecek apakah statement berhasil disiapkan
        if(!$stmt){
            return false;
        }
        // Memasukkan parameter ke statement
        $stmt->bind_param("sssi", $name, $description, $deadline, $category_id);
        $result = $stmt->execute();
        $stmt->close();
        // Mengembalikan hasil eksekusi query
        return $result;
    }

    // Method untuk mengambil semua data mahasiswa
    public function getAllTasks(){
        // Menyiapkan query SQL untuk mengambil data mahasiswa beserta prodi dan provinsi
        $query = "SELECT t.id, t.name, t.description, t.deadline, t.status, 
                         c.name AS category_name, t.category_id
                  FROM tasks t
                  LEFT JOIN tb_categories c ON t.category_id = c.id
                  ORDER BY t.status ASC, t.deadline ASC, t.id DESC";
        // Menyiapkan array kosong untuk menyimpan data mahasiswa
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return [];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = [];
        // Mengecek apakah ada data yang ditemukan
        if($result->num_rows > 0){
            // Mengambil setiap baris data dan memasukkannya ke dalam array
            while($row = $result->fetch_assoc()) {
                $tasks[] = [
                    'id'          => $row['id'],
                    'name'        => $row['name'],
                    'description' => $row['description'],
                    'deadline'    => $row['deadline'],
                    'status'      => $row['status'],
                    'category'    => $row['category_name'] ?? 'Uncategorized',
                    'category_id' => $row['category_id']
                ];
            }
        }
        $stmt->close();
        // Mengembalikan array data mahasiswa
        return $tasks;
    }

    // Method untuk mengambil data mahasiswa berdasarkan ID
    public function getTaskById($id){
        // Menyiapkan query SQL untuk mengambil data mahasiswa berdasarkan ID menggunakan prepared statement
        $query = "SELECT * FROM tasks WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = false;
        if($result->num_rows > 0){
            // Mengambil data mahasiswa  
            $row = $result->fetch_assoc();
            // Menyimpan data dalam array
            $data = [
                    'id'          => $row['id'],
                    'name'        => $row['name'],
                    'description' => $row['description'],
                    'deadline'    => $row['deadline'],
                    'status'      => $row['status'],
                    'category'    => $row['category_name'] ?? 'Uncategorized',
                    'category_id' => $row['category_id']
            ];
        }
        $stmt->close();
        // Mengembalikan data mahasiswa
        return $data;
    }

    // Method untuk mengedit data mahasiswa
    public function updateTask($data){
        // Mengambil data dari parameter $data
        $id          = $data['id'];
        $name        = $data['name'];
        $description = $data['description'];
        $deadline    = $data['deadline'];
        $status      = $data['status'];
        $category_id = $data['category'];      // ← UBAH: dari 'category_id' ke 'category'
        // $user_id  = $data['user_id'];       // ← HAPUS: tidak digunakan
        
        // Menyiapkan query SQL untuk update data menggunakan prepared statement
        $query = "UPDATE tasks SET name = ?, description = ?, deadline = ?, status = ?, category_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        // Memasukkan parameter ke statement
        $stmt->bind_param("ssssii", $name, $description, $deadline, $status, $category_id, $id);  // ← UBAH: dari "ssssii" ke "sssisi"
        $result = $stmt->execute();
        $stmt->close();
        // Mengembalikan hasil eksekusi query
        return $result;
    }

    // Method untuk menghapus data mahasiswa
    public function deleteTask($id){
        // Menyiapkan query SQL untuk delete data menggunakan prepared statement
        $query = "DELETE FROM tasks WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        // Mengembalikan hasil eksekusi query
        return $result;
    }

    // Method untuk mencari data mahasiswa berdasarkan kata kunci
    public function getTasksByFilter($user_id, $filter_type, $filter_value){
        if($filter_type == 'status'){
            $query = "SELECT t.id, t.name, t.description, t.deadline, t.status, 
                             c.name as category_name, t.category_id
                      FROM tasks t
                      LEFT JOIN categories c ON t.category_id = c.id
                      WHERE t.user_id = ? AND t.status = ?
                      ORDER BY t.deadline ASC";
            
            $stmt = $this->conn->prepare($query);
            if(!$stmt) return [];
            $stmt->bind_param("is", $user_id, $filter_value);
            
        } elseif($filter_type == 'category'){
            $query = "SELECT t.id, t.name, t.description, t.deadline, t.status, 
                             c.name as category_name, t.category_id
                      FROM tasks t
                      LEFT JOIN categories c ON t.category_id = c.id
                      WHERE t.user_id = ? AND t.category_id = ?
                      ORDER BY t.deadline ASC";
            
            $stmt = $this->conn->prepare($query);
            if(!$stmt) return [];
            $stmt->bind_param("ii", $user_id, $filter_value);
        } else {
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tasks = [];
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
                $tasks[] = [
                    'id'          => $row['id'],
                    'name'        => $row['name'],
                    'description' => $row['description'],
                    'deadline'    => $row['deadline'],
                    'status'      => $row['status'],
                    'category'    => $row['category_name'] ?? 'Uncategorized',
                    'category_id' => $row['category_id']
                ];
            }
        }
        $stmt->close();
        
        return $tasks;
    }

    public function getTaskStats(){
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed
                  FROM tasks";
        
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return ['total' => 0, 'pending' => 0, 'completed' => 0];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $stats = ['total' => 0, 'pending' => 0, 'completed' => 0];
        if($result->num_rows > 0){
            $stats = $result->fetch_assoc();
        }
        $stmt->close();
        
        return $stats;
    }

}

?>
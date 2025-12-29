<?php 
include_once 'db-config.php';

class Mahasiswa extends Database {

    /**
     * MENAMBAHKAN TUGAS BARU
     * - Jika Mahasiswa: Input biasa ke tabel tasks.
     * - Jika Dosen: Input ke tasks, lalu otomatis distribusikan ke semua mahasiswa 
     * di prodi yang sama lewat tabel tb_tugas_mhs.
     */
    public function addTask($data, $user_id, $role_user){
        $name        = $data['name'];
        $description = $data['deskripsi'];
        $deadline    = $data['deadline'];
        $category_id = $data['category'];
        
        // Cek apakah ada id_matkul (Hanya dikirim jika user adalah Dosen)
        $id_matkul   = isset($data['id_matkul']) && $data['id_matkul'] != '' ? $data['id_matkul'] : NULL;
        
        $task_type   = ($role_user == '2') ? 'Dosen' : 'Personal'; 

        // 1. Insert ke tabel MASTER (tasks)
        // Perhatikan: Kolom ID otomatis (AUTO_INCREMENT), jadi tidak perlu disebut.
        $query = "INSERT INTO tasks (name, description, deadline, category_id, created_by, task_type, id_matkul) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        if(!$stmt) return false;
        
        $stmt->bind_param("sssiisi", $name, $description, $deadline, $category_id, $user_id, $task_type, $id_matkul);
        $execute = $stmt->execute();
        
        // Ambil ID Task yang baru saja dibuat (id_task)
        $new_task_id = $this->conn->insert_id;
        $stmt->close();

        // 2. LOGIKA DISTRIBUSI TUGAS DOSEN
        // Jika Dosen membuat tugas untuk Matkul tertentu, bagikan ke semua mahasiswa jurusan itu.
        if($execute && $task_type == 'Dosen' && $id_matkul != NULL){
            
            // A. Cari ID Prodi dari Mata Kuliah ini
            $qMatkul = "SELECT id_prodi FROM tb_matakuliah WHERE id_mk = ?";
            $stmtMk = $this->conn->prepare($qMatkul);
            $stmtMk->bind_param("i", $id_matkul);
            $stmtMk->execute();
            $resMatkul = $stmtMk->get_result()->fetch_assoc();
            
            if($resMatkul){
                $id_prodi_target = $resMatkul['id_prodi'];

                // B. Cari Semua Mahasiswa di Prodi tersebut
                $qMhs = "SELECT id_mhs FROM tb_mhs WHERE id_prodi = ?";
                $stmtMhsList = $this->conn->prepare($qMhs);
                $stmtMhsList->bind_param("i", $id_prodi_target);
                $stmtMhsList->execute();
                $resMhs = $stmtMhsList->get_result();

                if($resMhs->num_rows > 0){
                    // C. Masukkan ke tabel tb_tugas_mhs satu per satu
                    $insertQuery = "INSERT INTO tb_tugas_mhs (id_task, id_mhs, status) VALUES (?, ?, 'Pending')";
                    $stmtInsert = $this->conn->prepare($insertQuery);

                    while($mhs = $resMhs->fetch_assoc()){
                        $id_mhs_target = $mhs['id_mhs'];
                        $stmtInsert->bind_param("ii", $new_task_id, $id_mhs_target);
                        $stmtInsert->execute();
                    }
                    $stmtInsert->close();
                }
            }
        }

        return $execute;
    }

    /**
     * MENGAMBIL DAFTAR TUGAS (List View)
     * - Mahasiswa: Gabungan Tugas Personal (tabel tasks) & Tugas Dosen (tabel tb_tugas_mhs)
     * - Dosen: Hanya tugas yang dibuat olehnya.
     */
    public function getAllTasks($user_id, $role_user){
        $tasks = [];

        if ($role_user == '3') { 
            // --- LOGIKA MAHASISWA ---
            
            // 1. Ambil ID Mahasiswa (id_mhs) dari tabel tb_mhs
            $qMhs = "SELECT id_mhs FROM tb_mhs WHERE id_user = ?";
            $stmtMhs = $this->conn->prepare($qMhs);
            $stmtMhs->bind_param("i", $user_id);
            $stmtMhs->execute();
            $resMhs = $stmtMhs->get_result();
            
            if($resMhs->num_rows > 0){
                $id_mhs_login = $resMhs->fetch_assoc()['id_mhs'];
            } else {
                return []; // Jika data mahasiswa tidak ditemukan
            }

            // 2. Query UNION (Gabung Tugas Pribadi + Tugas Dosen)
            // Perhatikan penggunaan 'id_task' di JOIN dan SELECT
            $query = "
                SELECT t.id_task, t.name, t.description, t.deadline, t.status AS status_tugas, 
                       c.name AS category_name, t.task_type, mk.nm_mk AS nama_matkul,
                       t.created_by
                FROM tasks t
                LEFT JOIN tb_categories c ON t.category_id = c.id
                LEFT JOIN tb_matakuliah mk ON t.id_matkul = mk.id_mk
                WHERE t.created_by = ? AND t.task_type = 'Personal'

                UNION

                SELECT t.id_task, t.name, t.description, t.deadline, tm.status AS status_tugas, 
                       c.name AS category_name, t.task_type, mk.nm_mk AS nama_matkul,
                       t.created_by
                FROM tb_tugas_mhs tm
                JOIN tasks t ON tm.id_task = t.id_task  
                LEFT JOIN tb_categories c ON t.category_id = c.id
                LEFT JOIN tb_matakuliah mk ON t.id_matkul = mk.id_mk
                WHERE tm.id_mhs = ? 
                
                ORDER BY status_tugas ASC, deadline ASC
            ";

            $stmt = $this->conn->prepare($query);
            if(!$stmt) die("Error SQL getAllTasks: " . $this->conn->error); 
            $stmt->bind_param("ii", $user_id, $id_mhs_login);

        } elseif ($role_user == '2') {
            // --- LOGIKA DOSEN ---
            $query = "SELECT t.id_task, t.name, t.description, t.deadline, t.status AS status_tugas, 
                             c.name AS category_name, t.task_type, mk.nm_mk AS nama_matkul,
                             t.created_by
                      FROM tasks t
                      LEFT JOIN tb_categories c ON t.category_id = c.id 
                      LEFT JOIN tb_matakuliah mk ON t.id_matkul = mk.id_mk
                      WHERE t.created_by = ? 
                      ORDER BY t.deadline ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            
        } else {
            // --- LOGIKA ADMIN ---
            $query = "SELECT t.id_task, t.name, t.description, t.deadline, t.status AS status_tugas, 
                             c.name AS category_name, t.task_type, mk.nm_mk AS nama_matkul,
                             t.created_by
                      FROM tasks t
                      LEFT JOIN tb_categories c ON t.category_id = c.id 
                      LEFT JOIN tb_matakuliah mk ON t.id_matkul = mk.id_mk
                      ORDER BY t.id_task DESC";
            $stmt = $this->conn->prepare($query);
        }

        if(isset($stmt) && $stmt !== false){
            $stmt->execute();
            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                // Mapping Data: 'id_task' database -> key 'id' array
                // Ini PENTING agar file data-list.php tidak error (karena dia memanggil ['id'])
                $tasks[] = [
                    'id'            => $row['id_task'], 
                    'name'          => $row['name'],
                    'description'   => $row['description'],
                    'deadline'      => $row['deadline'],
                    'status'        => $row['status_tugas'], 
                    'category_name' => $row['category_name'] ?? 'Uncategorized',
                    'created_by'    => $row['created_by'],
                    'task_type'     => $row['task_type'],
                    'nama_matkul'   => $row['nama_matkul'] ?? ''
                ];
            }
            $stmt->close();
        }
        
        return $tasks;
    }

    /**
     * MENGAMBIL 1 TUGAS (Untuk Edit)
     */
    public function getTaskById($id){
        // Ubah WHERE id = ? menjadi WHERE id_task = ?
        $query = "SELECT * FROM tasks WHERE id_task = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            return [
                'id'          => $row['id_task'], // Mapping ke 'id'
                'name'        => $row['name'],
                'description' => $row['description'],
                'deadline'    => $row['deadline'],
                'status'      => $row['status'],
                'category_id' => $row['category_id'],
                'created_by'  => $row['created_by'],
                'id_matkul'   => $row['id_matkul']
            ];
        }
        return false;
    }

    /**
     * UPDATE TUGAS
     */
    public function updateTask($data, $user_id, $role_user){
        $id          = $data['id']; // ID ini dikirim dari form hidden (tetap pakai variabel $id)
        $name        = $data['name'];
        $description = $data['description'];
        $deadline    = $data['deadline'];
        $status      = $data['status'];
        $category_id = $data['category'];
        
        // Cek Admin atau Pemilik
        if($role_user == '1'){
            // Admin bisa edit semua
            $query = "UPDATE tasks SET name=?, description=?, deadline=?, status=?, category_id=? WHERE id_task=?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssisi", $name, $description, $deadline, $status, $category_id, $id);
        } else {
            // User Biasa: Cek id_task AND created_by
            $query = "UPDATE tasks SET name=?, description=?, deadline=?, status=?, category_id=? WHERE id_task=? AND created_by=?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssisii", $name, $description, $deadline, $status, $category_id, $id, $user_id);
        }

        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * HAPUS TUGAS
     */
    public function deleteTask($id, $user_id, $role_user){
        if($role_user == '1'){
            // Admin Hapus Bebas
            $query = "DELETE FROM tasks WHERE id_task = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
        } else {
            // User Biasa Hapus Milik Sendiri
            $query = "DELETE FROM tasks WHERE id_task = ? AND created_by = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $id, $user_id);
        }

        $stmt->execute();
        // Cek apakah ada baris yang terhapus
        $success = ($stmt->affected_rows > 0);
        $stmt->close();
        return $success;
    }
}
?>
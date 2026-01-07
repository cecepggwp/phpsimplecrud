<?php 
include_once 'db-config.php';

class Mahasiswa extends Database {

    /**
     * MENAMBAHKAN TUGAS BARU
     */
    public function addTask($data, $user_id, $role_user){
        $name        = $data['name'];
        $description = $data['deskripsi'];
        $deadline    = $data['deadline'];
        $category_id = $data['category'];
        
        $target_prodi = isset($data['target_prodi']) ? $data['target_prodi'] : NULL;
        $id_matkul    = isset($data['id_matkul']) && $data['id_matkul'] != '' ? $data['id_matkul'] : NULL;

        $task_type = ($role_user == '3') ? 'Personal' : 'Dosen'; 

        $this->conn->begin_transaction();
        try {
            // 1. Insert ke tabel TASKS
            $stmt = $this->conn->prepare("INSERT INTO tasks (name, description, deadline, category_id, created_by, task_type, id_matkul) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssiisi", $name, $description, $deadline, $category_id, $user_id, $task_type, $id_matkul);
            $stmt->execute();
            $new_task_id = $this->conn->insert_id;

            // 2. LOGIKA DISTRIBUSI (Admin / Dosen)
            if($role_user == '1' || $role_user == '2'){
                $prodi_id_to_fetch = 0;

                if($role_user == '1' && $target_prodi != NULL){
                    $prodi_id_to_fetch = $target_prodi;
                } elseif ($role_user == '2') {
                    $qDosen = $this->conn->query("SELECT id_prodi FROM tb_dosen WHERE id_user = $user_id");
                    $dosenData = $qDosen->fetch_assoc();
                    $prodi_id_to_fetch = $dosenData['id_prodi'];
                }

                if($prodi_id_to_fetch > 0){
                    $qMhs = $this->conn->query("SELECT id_mhs FROM tb_mhs WHERE id_prodi = $prodi_id_to_fetch");
                    if($qMhs->num_rows > 0){
                        $stmtDist = $this->conn->prepare("INSERT INTO tb_tugas_mhs (id_task, id_mhs, status) VALUES (?, ?, 'Pending')");
                        while($mhs = $qMhs->fetch_assoc()){
                            $stmtDist->bind_param("ii", $new_task_id, $mhs['id_mhs']);
                            $stmtDist->execute();
                        }
                    }
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    /**
     * KHUSUS TAMPILAN DOSEN/ADMIN (DATA-LIST.PHP)
     * Mengambil daftar tugas yang dibuat user ini, LENGKAP dengan statistik pengerjaan mahasiswa
     */
    public function getTasksWithStats($user_id){
        $data = [];
        // Query ini menghitung jumlah mahasiswa yang sudah selesai (Completed) dan Total mahasiswa yang dapat tugas
        $query = "SELECT 
                    t.id_task, 
                    t.name, 
                    t.description, 
                    t.deadline, 
                    c.name as category_name,
                    (SELECT COUNT(*) FROM tb_tugas_mhs tm WHERE tm.id_task = t.id_task) as total_assignee,
                    (SELECT COUNT(*) FROM tb_tugas_mhs tm WHERE tm.id_task = t.id_task AND tm.status = 'Completed') as total_completed
                  FROM tasks t
                  LEFT JOIN tb_categories c ON t.category_id = c.id
                  WHERE t.created_by = ? 
                  ORDER BY t.deadline DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()){
            // Hitung Pending
            $row['total_pending'] = $row['total_assignee'] - $row['total_completed'];
            $data[] = $row;
        }
        return $data;
    }

    /**
     * AMBIL SEMUA TUGAS UNTUK MONITORING ADMIN (PAGE MONITORING)
     */
    public function getAllTasksForAdmin(){
        $data = [];
        $q1 = "SELECT t.id_task, t.name as judul_tugas, t.deadline, t.status, 'Personal' as tipe,
                m.nm_mhs as nama_mahasiswa, p.nm_prodi, '-' as mata_kuliah
               FROM tasks t
               JOIN tb_mhs m ON t.created_by = m.id_user
               JOIN tb_prodi p ON m.id_prodi = p.id_prodi
               WHERE t.task_type = 'Personal'";

        $q2 = "SELECT t.id_task, t.name as judul_tugas, t.deadline, tm.status, 'Dari Dosen/Admin' as tipe,
                m.nm_mhs as nama_mahasiswa, p.nm_prodi, mk.nm_mk as mata_kuliah
               FROM tb_tugas_mhs tm
               JOIN tasks t ON tm.id_task = t.id_task
               JOIN tb_mhs m ON tm.id_mhs = m.id_mhs
               JOIN tb_prodi p ON m.id_prodi = p.id_prodi
               LEFT JOIN tb_matakuliah mk ON t.id_matkul = mk.id_mk";

        $finalQuery = "$q1 UNION ALL $q2 ORDER BY deadline ASC";
        $result = $this->conn->query($finalQuery);
        if($result){
            while($row = $result->fetch_assoc()) $data[] = $row;
        }
        return $data;
    }

    // --- FUNGSI UPDATE STATUS (KANBAN MAHASISWA) ---
    public function updateTaskStatus($id_task, $new_status, $user_id, $role){
        if(!in_array($new_status, ['Pending', 'Completed'])) return false;

        if($role == '3'){ // MAHASISWA
            
            // 1. Cek Apakah Tugas Personal?
            $qCheck = "SELECT id_task FROM tasks WHERE id_task = ? AND created_by = ? AND task_type = 'Personal'";
            $stmt = $this->conn->prepare($qCheck);
            $stmt->bind_param("ii", $id_task, $user_id);
            $stmt->execute();
            
            if($stmt->get_result()->num_rows > 0){
                // JIKA PERSONAL: BOLEH UPDATE
                $sUp = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id_task = ?");
                $sUp->bind_param("si", $new_status, $id_task);
                return $sUp->execute();
            } else {
                // JIKA BUKAN PERSONAL (BERARTI TUGAS DOSEN):
                // TOLAK AKSES UPDATE! Mahasiswa tidak boleh ubah ini.
                return false; 
            }
        } 
        
        // Dosen/Admin pakai metode verifikasi lain (verifyTaskStatus), jadi ini return false
        return false;
    }

    // --- FUNGSI STANDAR LAINNYA ---
    // --- GET ALL TASKS (UPDATE: Include Mata Kuliah) ---
    public function getAllTasks($user_id, $role_user){
        $tasks = [];
        if($role_user == '3'){ // MAHASISWA
             $qMhs = $this->conn->prepare("SELECT id_mhs FROM tb_mhs WHERE id_user = ?");
             $qMhs->bind_param("i", $user_id); 
             $qMhs->execute();
             $mhsData = $qMhs->get_result()->fetch_assoc();
             $id_mhs = $mhsData['id_mhs'] ?? 0;

             // Perhatikan penambahan: mk.nm_mk as mata_kuliah dan LEFT JOIN tb_matakuliah
             $query = "SELECT t.id_task as id, t.name, t.description, t.deadline, t.status, t.created_by, t.task_type,
                       c.name as category_name, mk.nm_mk as mata_kuliah
                       FROM tasks t 
                       LEFT JOIN tb_categories c ON t.category_id = c.id
                       LEFT JOIN tb_matakuliah mk ON t.id_matkul = mk.id_mk
                       WHERE t.created_by = ? AND t.task_type = 'Personal'
                       
                       UNION
                       
                       SELECT t.id_task as id, t.name, t.description, t.deadline, tm.status, t.created_by, t.task_type,
                       c.name as category_name, mk.nm_mk as mata_kuliah
                       FROM tasks t 
                       JOIN tb_tugas_mhs tm ON t.id_task = tm.id_task 
                       LEFT JOIN tb_categories c ON t.category_id = c.id
                       LEFT JOIN tb_matakuliah mk ON t.id_matkul = mk.id_mk
                       WHERE tm.id_mhs = ?";
             
             $stmt = $this->conn->prepare($query);
             $stmt->bind_param("ii", $user_id, $id_mhs);
             $stmt->execute();
             $res = $stmt->get_result();
             while($row = $res->fetch_assoc()) $tasks[] = $row;

        } elseif($role_user == '2' || $role_user == '1'){ 
             // Dosen/Admin
             $query = "SELECT id_task as id, name, description, deadline, status, created_by, task_type FROM tasks WHERE created_by = ?";
             $stmt = $this->conn->prepare($query); 
             $stmt->bind_param("i", $user_id); 
             $stmt->execute();
             $res = $stmt->get_result(); 
             while($row = $res->fetch_assoc()) $tasks[] = $row;
        }
        return $tasks;
    }

    public function getTaskById($id){
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE id_task = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function updateTask($data, $user_id, $role_user){
        $id          = $data['id'];
        $name        = $data['name'];
        $desc        = $data['description'];
        $deadline    = $data['deadline'];
        $cat         = $data['category'];
        $status      = isset($data['status']) ? $data['status'] : 'Pending';

        if($role_user == '1' || $role_user == '2'){
             // ADMIN / DOSEN: HANYA UPDATE DATA UTAMA (Tanpa Status)
             $q = "UPDATE tasks SET name=?, description=?, deadline=?, category_id=? WHERE id_task=?";
             $s = $this->conn->prepare($q);
             // Binding: s(name), s(desc), s(deadline), i(cat), i(id_task)
             $s->bind_param("sssii", $name, $desc, $deadline, $cat, $id);
             return $s->execute();

        } else {
             // MAHASISWA: BISA UPDATE STATUS (JIKA TUGAS PERSONAL)
             $qCheck = "SELECT task_type FROM tasks WHERE id_task = ?";
             $sc = $this->conn->prepare($qCheck); $sc->bind_param("i", $id); $sc->execute();
             $type = $sc->get_result()->fetch_assoc()['task_type'];

             if($type == 'Personal'){
                $q = "UPDATE tasks SET name=?, description=?, deadline=?, status=?, category_id=? WHERE id_task=? AND created_by=?";
                $s = $this->conn->prepare($q);
                $s->bind_param("sssisii", $name, $desc, $deadline, $status, $cat, $id, $user_id);
                return $s->execute();
             } else {
                 return false; // Mahasiswa tidak boleh edit tugas dosen lewat form ini
             }
        }
    }
    
    public function deleteTask($id, $user_id, $role_user){
        if($role_user == '1'){
            $q = "DELETE FROM tasks WHERE id_task = ?";
            $s = $this->conn->prepare($q); $s->bind_param("i", $id);
        } else {
            $q = "DELETE FROM tasks WHERE id_task = ? AND created_by = ?";
            $s = $this->conn->prepare($q); $s->bind_param("ii", $id, $user_id);
        }
        return $s->execute();
    }
    
    // Calendar JSON
    public function getCalendarEvents($user_id, $role){
        $events = [];
        if($role == '1'){ 
            $query = "SELECT id_task, name, deadline, status FROM tasks";
            $result = $this->conn->query($query);
            while($row = $result->fetch_assoc()){
                $events[] = ['id'=>$row['id_task'], 'title'=>$row['name'], 'start'=>$row['deadline'], 'color'=>'#6c757d', 'url'=>'data-edit.php?id='.$row['id_task']];
            }
        } elseif($role == '2'){ 
            $query = "SELECT id_task, name, deadline, status FROM tasks WHERE created_by = ?";
            $stmt = $this->conn->prepare($query); $stmt->bind_param("i", $user_id); $stmt->execute(); $result = $stmt->get_result();
            while($row = $result->fetch_assoc()){
                $events[] = ['id'=>$row['id_task'], 'title'=>$row['name'], 'start'=>$row['deadline'], 'color'=>'#0d6efd', 'url'=>'data-edit.php?id='.$row['id_task']];
            }
        } elseif($role == '3'){ 
            $qMhs = $this->conn->prepare("SELECT id_mhs FROM tb_mhs WHERE id_user = ?"); $qMhs->bind_param("i", $user_id); $qMhs->execute();
            $mhs = $qMhs->get_result()->fetch_assoc(); $id_mhs = $mhs['id_mhs'] ?? 0;
            $query = "SELECT t.id_task, t.name, t.deadline, t.status AS status_asli FROM tasks t WHERE t.created_by = ? AND t.task_type = 'Personal' UNION SELECT t.id_task, t.name, t.deadline, tm.status AS status_asli FROM tasks t JOIN tb_tugas_mhs tm ON t.id_task = tm.id_task WHERE tm.id_mhs = ?";
            $stmt = $this->conn->prepare($query); $stmt->bind_param("ii", $user_id, $id_mhs); $stmt->execute(); $result = $stmt->get_result();
            while($row = $result->fetch_assoc()){
                $color = ($row['status_asli'] == 'Completed') ? '#198754' : '#dc3545';
                $events[] = ['id'=>$row['id_task'], 'title'=>$row['name']." (".$row['status_asli'].")", 'start'=>$row['deadline'], 'color'=>$color, 'url'=>'data-edit.php?id='.$row['id_task']];
            }
        }
        return json_encode($events);
    }

    // --- FITUR VERIFIKASI TUGAS (BARU) ---
    
    // 1. Ambil daftar mahasiswa penerima tugas spesifik
    public function getTaskReceivers($id_task){
        $receivers = [];
        $query = "SELECT tm.id_mhs, tm.status, tm.finish_date, 
                         m.nm_mhs, m.nim_mhs, p.nm_prodi
                  FROM tb_tugas_mhs tm
                  JOIN tb_mhs m ON tm.id_mhs = m.id_mhs
                  JOIN tb_prodi p ON m.id_prodi = p.id_prodi
                  WHERE tm.id_task = ?
                  ORDER BY m.nm_mhs ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id_task);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while($row = $result->fetch_assoc()){
            $receivers[] = $row;
        }
        return $receivers;
    }

    // 2. Update Status Mahasiswa (Dipakai saat Dosen mencentang checkbox)
    // Update Status Mahasiswa (Versi Perbaikan)
    public function verifyTaskStatus($id_task, $id_mhs, $status){
        // Jika completed, isi tanggal sekarang. Jika Pending, set NULL
        $finish_date = ($status == 'Completed') ? date('Y-m-d H:i:s') : NULL;
        
        $query = "UPDATE tb_tugas_mhs SET status = ?, finish_date = ? WHERE id_task = ? AND id_mhs = ?";
        $stmt = $this->conn->prepare($query);
        
        // s = string (status), s = string (date), i = int (id_task), i = int (id_mhs)
        $stmt->bind_param("ssii", $status, $finish_date, $id_task, $id_mhs);
        
        if($stmt->execute()){
            return true;
        } else {
            // Tampilkan error jika query gagal (untuk debugging)
            echo "Error DB: " . $stmt->error; 
            return false;
        }
    }
}
?>
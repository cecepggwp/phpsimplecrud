<?php
include_once 'db-config.php';

class MasterData extends Database {

    // --- FUNGSI DASHBOARD TERUPDATE ---
    public function getDashboardStats($id_user, $role){
        $stats = [
            'counts' => [],
            'charts' => [],
            'lists'  => [] // Penampung daftar tugas
        ];

        if($role == '1'){ // --- ADMIN ---
            // 1. Total User
            $q1 = $this->conn->query("SELECT count(*) as total FROM tb_users");
            $stats['counts']['total_user'] = $q1->fetch_assoc()['total'];
            // 2. Total Prodi
            $q2 = $this->conn->query("SELECT count(*) as total FROM tb_prodi");
            $stats['counts']['total_prodi'] = $q2->fetch_assoc()['total'];
            // 3. Total MK
            $q3 = $this->conn->query("SELECT count(*) as total FROM tb_matakuliah");
            $stats['counts']['total_mk'] = $q3->fetch_assoc()['total'];

            // 4. Chart Roles
            $qChart = $this->conn->query("SELECT role, count(*) as total FROM tb_users GROUP BY role");
            $roleData = ['Admin' => 0, 'Dosen' => 0, 'Mahasiswa' => 0];
            while($row = $qChart->fetch_assoc()){
                if($row['role'] == '1') $roleData['Admin'] = $row['total'];
                if($row['role'] == '2') $roleData['Dosen'] = $row['total'];
                if($row['role'] == '3') $roleData['Mahasiswa'] = $row['total'];
            }
            $stats['charts']['roles'] = $roleData;

        } elseif($role == '2'){ // --- DOSEN ---
            // 1. Total Tugas yang Dibuat Dosen Ini
            $stmt = $this->conn->prepare("SELECT count(*) as total FROM tasks WHERE created_by = ?");
            $stmt->bind_param("i", $id_user);
            $stmt->execute();
            $stats['counts']['my_tasks'] = $stmt->get_result()->fetch_assoc()['total'];

            // 2. Hitung Mahasiswa Selesai vs Belum (Dari semua tugas dosen ini)
            $queryStats = "SELECT 
                            SUM(CASE WHEN tm.status = 'Completed' THEN 1 ELSE 0 END) as mhs_selesai,
                            SUM(CASE WHEN tm.status = 'Pending' THEN 1 ELSE 0 END) as mhs_belum
                           FROM tb_tugas_mhs tm
                           JOIN tasks t ON tm.id_task = t.id_task
                           WHERE t.created_by = ?";
            $stmtC = $this->conn->prepare($queryStats);
            $stmtC->bind_param("i", $id_user);
            $stmtC->execute();
            $res = $stmtC->get_result()->fetch_assoc();
            
            $stats['counts']['mhs_selesai'] = $res['mhs_selesai'] ?? 0;
            $stats['counts']['mhs_belum']   = $res['mhs_belum'] ?? 0;

            // Data untuk Chart Pie
            $stats['charts']['progress'] = [
                'Completed' => $stats['counts']['mhs_selesai'], 
                'Pending'   => $stats['counts']['mhs_belum']
            ];

        } elseif($role == '3'){ // --- MAHASISWA ---
            // A. Ambil ID Mahasiswa
            $qMhs = $this->conn->prepare("SELECT id_mhs FROM tb_mhs WHERE id_user = ?");
            $qMhs->bind_param("i", $id_user);
            $qMhs->execute();
            $mhs = $qMhs->get_result()->fetch_assoc();
            $id_mhs = $mhs['id_mhs'] ?? 0;

            // B. Hitung Statistik (Personal + Dosen)
            // Query Union untuk menghitung total
            $queryCount = "
                SELECT status, COUNT(*) as total FROM (
                    SELECT status FROM tasks WHERE created_by = ? AND task_type = 'Personal'
                    UNION ALL
                    SELECT status FROM tb_tugas_mhs WHERE id_mhs = ?
                ) as gabungan GROUP BY status
            ";
            $stmtCount = $this->conn->prepare($queryCount);
            $stmtCount->bind_param("ii", $id_user, $id_mhs);
            $stmtCount->execute();
            $resCount = $stmtCount->get_result();
            
            $pending = 0; $completed = 0;
            while($row = $resCount->fetch_assoc()){
                if($row['status'] == 'Pending') $pending = $row['total'];
                if($row['status'] == 'Completed') $completed = $row['total'];
            }

            $stats['counts']['total_pending'] = $pending;
            $stats['counts']['total_completed'] = $completed;
            $stats['charts']['overall'] = ['Pending' => $pending, 'Completed' => $completed];

            // C. AMBIL LIST TUGAS PENDING (Deadline Terdekat)
            // Ini agar mahasiswa tahu \"Apa saja yang belum selesai\"
            $queryList = "
                SELECT name, deadline, 'Personal' as sumber, id_task FROM tasks 
                WHERE created_by = ? AND task_type = 'Personal' AND status = 'Pending'
                UNION ALL
                SELECT t.name, t.deadline, 'Dosen' as sumber, t.id_task 
                FROM tasks t 
                JOIN tb_tugas_mhs tm ON t.id_task = tm.id_task 
                WHERE tm.id_mhs = ? AND tm.status = 'Pending'
                ORDER BY deadline ASC LIMIT 5
            ";
            $stmtList = $this->conn->prepare($queryList);
            $stmtList->bind_param("ii", $id_user, $id_mhs);
            $stmtList->execute();
            $resList = $stmtList->get_result();
            
            while($row = $resList->fetch_assoc()){
                $stats['lists'][] = $row;
            }
        }

        return $stats;
    }

    // --- FUNGSI STANDAR LAINNYA (TETAP SAMA) ---
    public function getCategories(){
        $query = "SELECT * FROM tb_categories";
        $result = $this->conn->query($query);
        $categories = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) $categories[] = $row;
        }
        return $categories;
    }
    public function inputCategories($data){
        $stmt = $this->conn->prepare("INSERT INTO tb_categories (name) VALUES (?)");
        $stmt->bind_param("s", $data['name']);
        return $stmt->execute();
    }
    public function getUpdateCategories($id){
        $stmt = $this->conn->prepare("SELECT * FROM tb_categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function updateCategories($data){
        $stmt = $this->conn->prepare("UPDATE tb_categories SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $data['name'], $data['id']);
        return $stmt->execute();
    }
    public function deleteCategories($id){
        $stmt = $this->conn->prepare("DELETE FROM tb_categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    public function getMatkulByProdi($id_user){
        $qDosen = $this->conn->prepare("SELECT id_prodi FROM tb_dosen WHERE id_user = ?");
        $qDosen->bind_param("i", $id_user);
        $qDosen->execute();
        $resDosen = $qDosen->get_result()->fetch_assoc();
        if(!$resDosen) return []; 
        $id_prodi = $resDosen['id_prodi'];
        $query = "SELECT m.id_mk, m.nm_mk, p.nm_prodi FROM tb_matakuliah m JOIN tb_prodi p ON m.id_prodi = p.id_prodi WHERE m.id_prodi = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id_prodi);
        $stmt->execute();
        $result = $stmt->get_result();
        $matkul = [];
        while($row = $result->fetch_assoc()) $matkul[] = $row;
        return $matkul;
    }
    public function inputUsers($data){
        $nama_lengkap = $data['nama_lengkap'];
        $username     = $data['username'];
        $email        = $data['email'];
        $password     = $data['password']; 
        $nmr_induk    = $data['nmr_induk'];
        $role         = $data['role']; 
        $id_prodi     = $data['id_prodi'];
        $check = $this->conn->prepare("SELECT id_user FROM tb_users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        if($check->get_result()->num_rows > 0) return "exists";
        $this->conn->begin_transaction();
        try {
            $stmtUser = $this->conn->prepare("INSERT INTO tb_users (nama_lengkap, username, password, email, role, nmr_induk) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtUser->bind_param("ssssss", $nama_lengkap, $username, $password, $email, $role, $nmr_induk);
            $stmtUser->execute();
            $id_user = $this->conn->insert_id;
            if($role == '2'){ 
                $stmtDosen = $this->conn->prepare("INSERT INTO tb_dosen (id_user, nip_dosen, nama_dosen, id_prodi) VALUES (?, ?, ?, ?)");
                $stmtDosen->bind_param("issi", $id_user, $nmr_induk, $nama_lengkap, $id_prodi);
                $stmtDosen->execute();
            } else { 
                $stmtMhs = $this->conn->prepare("INSERT INTO tb_mhs (id_user, nim_mhs, nm_mhs, id_prodi) VALUES (?, ?, ?, ?)");
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
    public function checkUserDataStatus($id_user, $role){
        if($role == '3'){ 
            $q = $this->conn->query("SELECT id_prodi FROM tb_mhs WHERE id_user = $id_user");
            if(!$q) return 0;
            $data = $q->fetch_assoc();
            if(empty($data['id_prodi'])) return 1; 
        } 
        elseif($role == '2'){ 
            $q = $this->conn->query("SELECT id_prodi FROM tb_dosen WHERE id_user = $id_user");
            if(!$q) return 0;
            $data = $q->fetch_assoc();
            if(empty($data['id_prodi'])) return 1; 
        }
        return 0; 
    }
    public function updateDosenData($id_user, $id_prodi){
        $this->conn->begin_transaction();
        try {
            $this->conn->query("UPDATE tb_dosen SET id_prodi = $id_prodi WHERE id_user = $id_user");
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }
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
    public function getProdi(){
        $result = $this->conn->query("SELECT * FROM tb_prodi ORDER BY id_prodi DESC");
        $prodi = [];
        if($result) while($row = $result->fetch_assoc()) $prodi[] = $row;
        return $prodi;
    }
    public function inputProdi($data){
        $stmt = $this->conn->prepare("INSERT INTO tb_prodi (nm_prodi) VALUES (?)");
        $stmt->bind_param("s", $data['nm_prodi']);
        return $stmt->execute();
    }
    public function getUpdateProdi($id){
        $stmt = $this->conn->prepare("SELECT * FROM tb_prodi WHERE id_prodi = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function updateProdi($data){
        $stmt = $this->conn->prepare("UPDATE tb_prodi SET nm_prodi = ? WHERE id_prodi = ?");
        $stmt->bind_param("si", $data['nm_prodi'], $data['id_prodi']);
        return $stmt->execute();
    }
    public function deleteProdi($id){
        $stmt = $this->conn->prepare("DELETE FROM tb_prodi WHERE id_prodi = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    public function getMk(){
        $query = "SELECT tb_matakuliah.*, tb_prodi.nm_prodi FROM tb_matakuliah JOIN tb_prodi ON tb_matakuliah.id_prodi = tb_prodi.id_prodi ORDER BY tb_matakuliah.id_mk DESC";
        $result = $this->conn->query($query);
        $mk = [];
        if($result) while($row = $result->fetch_assoc()) $mk[] = $row;
        return $mk;
    }
    public function inputMk($data){
        $stmt = $this->conn->prepare("INSERT INTO tb_matakuliah (nm_mk, id_prodi) VALUES (?, ?)");
        $stmt->bind_param("si", $data['nm_mk'], $data['id_prodi']);
        return $stmt->execute();
    }
    public function getUpdateMk($id){
        $stmt = $this->conn->prepare("SELECT * FROM tb_matakuliah WHERE id_mk = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function updateMk($data){
        $stmt = $this->conn->prepare("UPDATE tb_matakuliah SET nm_mk = ?, id_prodi = ? WHERE id_mk = ?");
        $stmt->bind_param("sii", $data['nm_mk'], $data['id_prodi'], $data['id_mk']);
        return $stmt->execute();
    }
    public function deleteMk($id){
        $stmt = $this->conn->prepare("DELETE FROM tb_matakuliah WHERE id_mk = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    public function getUsers($search = null) {
        $query = "SELECT * FROM tb_users";
        if ($search) $query .= " WHERE username LIKE '%$search%' OR email LIKE '%$search%'";
        $query .= " ORDER BY id_user DESC";
        $result = $this->conn->query($query);
        $users = [];
        if($result) while($row = $result->fetch_assoc()) $users[] = $row;
        return $users;
    }
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM tb_users WHERE id_user = ?");
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
        $this->conn->begin_transaction();
        try {
            $stmtUser = $this->conn->prepare("UPDATE tb_users SET role = ?, nama_lengkap = ?, nmr_induk = ? WHERE id_user = ?");
            if(!$stmtUser) throw new Exception("Gagal Prepare User: " . $this->conn->error);
            $stmtUser->bind_param("sssi", $role, $nama, $nip_nim, $id_user);
            $stmtUser->execute();
            if ($role == '2') { 
                $this->conn->query("DELETE FROM tb_mhs WHERE id_user = $id_user");
                $this->conn->query("DELETE FROM tb_dosen WHERE id_user = $id_user");
                if(empty($id_prodi)) $id_prodi = NULL;
                $queryDosen = "INSERT INTO tb_dosen (id_user, nip_dosen, nama_dosen, id_prodi) VALUES (?, ?, ?, ?)";
                $stmtDosen = $this->conn->prepare($queryDosen);
                $stmtDosen->bind_param("issi", $id_user, $nip_nim, $nama, $id_prodi);
                $stmtDosen->execute();
            } elseif ($role == '3') { 
                $this->conn->query("DELETE FROM tb_dosen WHERE id_user = $id_user");
                $this->conn->query("DELETE FROM tb_mhs WHERE id_user = $id_user");
                if(empty($id_prodi)) $id_prodi = NULL;
                $stmtMhs = $this->conn->prepare("INSERT INTO tb_mhs (id_user, nim_mhs, nm_mhs, id_prodi) VALUES (?, ?, ?, ?)");
                $stmtMhs->bind_param("issi", $id_user, $nip_nim, $nama, $id_prodi);
                $stmtMhs->execute();
            }
            $this->conn->commit();
            return true;
        } catch (Throwable $e) { 
            $this->conn->rollback();
            echo "<div style='background:red; color:white; padding:10px;'>SYSTEM ERROR: " . $e->getMessage() . "</div>";
            exit(); 
        }
    }
    // --- FUNGSI STATUS TUGAS ---
    public function getStatus(){
        return [
            ['name' => 'Pending', 'value' => 'Pending'],
            ['name' => 'Completed', 'value' => 'Completed']
        ];
    }
}
?>
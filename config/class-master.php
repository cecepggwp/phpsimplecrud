<?php

// Memasukkan file konfigurasi database
include_once 'db-config.php';

class MasterData extends Database {

    // Method untuk mendapatkan daftar program studi
    public function getCategories(){
        $query = "SELECT * FROM categories";
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
    public function getUsername(){
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
    }


    // Method untuk input data program studi
    public function inputCategories($data){
        $id = $data['id'];
        $name = $data['name'];
        $query = "INSERT INTO categories (id, name,) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("ss", $id, $name);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Method untuk mendapatkan data program studi berdasarkan kode
    public function getUpdateCategories($id){
        $query = "SELECT * FROM categories WHERE id = ?";
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
        $query = "UPDATE categories SET name = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("is", $id, $name);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Method untuk menghapus data program studi
    public function deleteCategories($id){
        $query = "DELETE FROM categories WHERE id = ?";
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
    public function inputUsers($data){
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
    }

    // Method untuk mendapatkan data provinsi berdasarkan id
    public function getUpdateUsers($id){
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
    }

    // Method untuk mengedit data provinsi
    public function updateUsers($data){
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

    // Method untuk menghapus data provinsi
    public function deleteUser($id){
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        if(!$stmt){
            return false;
        }
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function getStatus(){
        return [
            ['name' => 'Pending', 'value' => 'Pending'],
            ['name' => 'Completed', 'value' => 'Completed']
        ];
    }
}

?>
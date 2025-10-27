<?php

class Database {

    // Konfigurasi database dapat dilihat di SQLYog atau HeidiSQL
    private $db_host = "localhost"; // database host
    private $db_user = "root"; // database username
    private $db_pass = ""; // database password
    private $db_name = "db_simplecrud"; // database name
    public $conn; // database connection

    // Konstruktor untuk inisialisasi koneksi database
    public function __construct(){
        // Memanggil method untuk membuat koneksi database
        $this->getConnection();
    }
    
    // Method untuk membuat koneksi database
    public function getConnection(){
        try{
            // Membuat koneksi database menggunakan mysqli
            $this->conn = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
            // Cek koneksi database
            if ($this->conn->connect_error) {
                // Melemparkan exception jika koneksi gagal
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch(Exception $e){
            // Menangani exception dan menampilkan pesan error
            die("Connection error: " . $e->getMessage());
        }
    }

}

?>
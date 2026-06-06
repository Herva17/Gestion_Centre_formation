<?php

class Database {
    private $host = 'localhost';
    private $db_name = 'gestion_centre_formation';
    private $user = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->user,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Erreur de connexion: ' . $e->getMessage();
        }

        return $this->conn;
    }

    public function getConnection() {
        return $this->conn ?? $this->connect();
    }
}

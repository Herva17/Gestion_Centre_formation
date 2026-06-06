<?php

class Salle {
    private $conn;
    private $table = 'salle';

    public $id_salle;
    public $nom;
    public $capacite;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_salle = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nom, $capacite) {
        $query = "INSERT INTO " . $this->table . " 
                  (nom, capacite) 
                  VALUES (:nom, :capacite)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':capacite', $capacite);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $nom, $capacite) {
        $query = "UPDATE " . $this->table . " 
                  SET nom = :nom, capacite = :capacite 
                  WHERE id_salle = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':capacite', $capacite);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_salle = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}

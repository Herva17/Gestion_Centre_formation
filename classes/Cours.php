<?php

class Cours {
    private $conn;
    private $table = 'cours';

    public $id_cours;
    public $nom;
    public $description;
    public $id_filiere;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT c.*, f.nom as filiere_nom FROM " . $this->table . " c
                  LEFT JOIN filiere f ON c.id_filiere = f.id_filiere
                  ORDER BY c.nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT c.*, f.nom as filiere_nom FROM " . $this->table . " c
                  LEFT JOIN filiere f ON c.id_filiere = f.id_filiere
                  WHERE c.id_cours = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nom, $description, $id_filiere) {
        $query = "INSERT INTO " . $this->table . " 
                  (nom, description, id_filiere) 
                  VALUES (:nom, :description, :id_filiere)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id_filiere', $id_filiere);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $nom, $description, $id_filiere) {
        $query = "UPDATE " . $this->table . " 
                  SET nom = :nom, description = :description, id_filiere = :id_filiere 
                  WHERE id_cours = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id_filiere', $id_filiere);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_cours = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getByFiliere($id_filiere) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_filiere = :id_filiere ORDER BY nom ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_filiere', $id_filiere);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}

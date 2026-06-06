<?php

class Apprenant {
    private $conn;
    private $table = 'apprenant';

    public $id_apprenant;
    public $nom;
    public $prenom;
    public $telephone;
    public $adresse;

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
        $query = "SELECT * FROM " . $this->table . " WHERE id_apprenant = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nom, $prenom, $telephone, $adresse) {
        $query = "INSERT INTO " . $this->table . " 
                  (nom, prenom, telephone, adresse) 
                  VALUES (:nom, :prenom, :telephone, :adresse)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':adresse', $adresse);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $nom, $prenom, $telephone, $adresse) {
        $query = "UPDATE " . $this->table . " 
                  SET nom = :nom, prenom = :prenom, telephone = :telephone, adresse = :adresse 
                  WHERE id_apprenant = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':adresse', $adresse);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_apprenant = :id";
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

    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE nom LIKE :keyword OR prenom LIKE :keyword OR telephone LIKE :keyword
                  ORDER BY nom ASC";
        $stmt = $this->conn->prepare($query);
        $keyword = "%$keyword%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

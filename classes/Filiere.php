<?php

class Filiere {
    private $conn;
    private $table = 'filiere';

    public $id_filiere;
    public $nom;
    public $duree;
    public $frais_mensuel;

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
        $query = "SELECT * FROM " . $this->table . " WHERE id_filiere = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nom, $duree, $frais_mensuel) {
        $query = "INSERT INTO " . $this->table . " 
                  (nom, duree, frais_mensuel) 
                  VALUES (:nom, :duree, :frais_mensuel)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':duree', $duree);
        $stmt->bindParam(':frais_mensuel', $frais_mensuel);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $nom, $duree, $frais_mensuel) {
        $query = "UPDATE " . $this->table . " 
                  SET nom = :nom, duree = :duree, frais_mensuel = :frais_mensuel 
                  WHERE id_filiere = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':duree', $duree);
        $stmt->bindParam(':frais_mensuel', $frais_mensuel);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_filiere = :id";
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

    public function getRevenueMensuel() {
        $query = "SELECT COALESCE(SUM(frais_mensuel), 0) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}

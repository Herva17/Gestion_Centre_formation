<?php

class Paiement {
    private $conn;
    private $table = 'paiement';

    public $id_paiement;
    public $montant;
    public $type;
    public $mois;
    public $id_inscription;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT p.*, i.id_apprenant, a.nom, a.prenom 
                  FROM " . $this->table . " p
                  LEFT JOIN inscription i ON p.id_inscription = i.id_inscription
                  LEFT JOIN apprenant a ON i.id_apprenant = a.id_apprenant
                  ORDER BY p.id_paiement DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT p.*, a.nom, a.prenom FROM " . $this->table . " p
                  LEFT JOIN inscription i ON p.id_inscription = i.id_inscription
                  LEFT JOIN apprenant a ON i.id_apprenant = a.id_apprenant
                  WHERE p.id_paiement = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($montant, $type, $mois, $id_inscription) {
        $query = "INSERT INTO " . $this->table . " 
                  (montant, type, mois, id_inscription) 
                  VALUES (:montant, :type, :mois, :id_inscription)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':mois', $mois);
        $stmt->bindParam(':id_inscription', $id_inscription);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getTotalMontant() {
        $query = "SELECT COALESCE(SUM(montant), 0) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getByInscription($id_inscription) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE id_inscription = :id_inscription 
                  ORDER BY id_paiement DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_inscription', $id_inscription);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

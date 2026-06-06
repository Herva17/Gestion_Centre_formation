<?php

class Inscription {
    private $conn;
    private $table = 'inscription';

    public $id_inscription;
    public $date_inscription;
    public $frais_inscription;
    public $id_apprenant;
    public $id_filiere;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT i.*, a.nom, a.prenom, f.nom as filiere_nom 
                  FROM " . $this->table . " i
                  LEFT JOIN apprenant a ON i.id_apprenant = a.id_apprenant
                  LEFT JOIN filiere f ON i.id_filiere = f.id_filiere
                  ORDER BY i.date_inscription DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT i.*, a.nom, a.prenom, f.nom as filiere_nom, f.frais_mensuel
                  FROM " . $this->table . " i
                  LEFT JOIN apprenant a ON i.id_apprenant = a.id_apprenant
                  LEFT JOIN filiere f ON i.id_filiere = f.id_filiere
                  WHERE i.id_inscription = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($id_apprenant, $id_filiere, $date_inscription, $frais_inscription) {
        $query = "INSERT INTO " . $this->table . " 
                  (id_apprenant, id_filiere, date_inscription, frais_inscription) 
                  VALUES (:id_apprenant, :id_filiere, :date_inscription, :frais_inscription)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id_apprenant', $id_apprenant);
        $stmt->bindParam(':id_filiere', $id_filiere);
        $stmt->bindParam(':date_inscription', $date_inscription);
        $stmt->bindParam(':frais_inscription', $frais_inscription);
        
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

    public function getTotalRevenue() {
        $query = "SELECT COALESCE(SUM(frais_inscription), 0) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getInscriptionsByMonth() {
        $query = "SELECT MONTH(date_inscription) as mois, COUNT(*) as total 
                  FROM " . $this->table . "
                  WHERE YEAR(date_inscription) = YEAR(NOW())
                  GROUP BY MONTH(date_inscription)
                  ORDER BY mois ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRevenueByMonth() {
        $query = "SELECT MONTH(date_inscription) as mois, SUM(frais_inscription) as total 
                  FROM " . $this->table . "
                  WHERE YEAR(date_inscription) = YEAR(NOW())
                  GROUP BY MONTH(date_inscription)
                  ORDER BY mois ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInscriptionsByFiliere() {
        $query = "SELECT f.nom, COUNT(i.id_inscription) as total
                  FROM filiere f
                  LEFT JOIN " . $this->table . " i ON f.id_filiere = i.id_filiere
                  GROUP BY f.id_filiere, f.nom";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php

class Horaire {
    private $conn;
    private $table = 'horaire';

    public $id_horaire;
    public $jour;
    public $heure_debut;
    public $heure_fin;
    public $id_salle;
    public $id_cours;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT h.*, s.nom as salle_nom, c.nom as cours_nom 
                  FROM " . $this->table . " h
                  LEFT JOIN salle s ON h.id_salle = s.id_salle
                  LEFT JOIN cours c ON h.id_cours = c.id_cours
                  ORDER BY h.jour ASC, h.heure_debut ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT h.*, s.nom as salle_nom, c.nom as cours_nom 
                  FROM " . $this->table . " h
                  LEFT JOIN salle s ON h.id_salle = s.id_salle
                  LEFT JOIN cours c ON h.id_cours = c.id_cours
                  WHERE h.id_horaire = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($jour, $heure_debut, $heure_fin, $id_salle, $id_cours) {
        $query = "INSERT INTO " . $this->table . " 
                  (jour, heure_debut, heure_fin, id_salle, id_cours) 
                  VALUES (:jour, :heure_debut, :heure_fin, :id_salle, :id_cours)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':jour', $jour);
        $stmt->bindParam(':heure_debut', $heure_debut);
        $stmt->bindParam(':heure_fin', $heure_fin);
        $stmt->bindParam(':id_salle', $id_salle);
        $stmt->bindParam(':id_cours', $id_cours);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $jour, $heure_debut, $heure_fin, $id_salle, $id_cours) {
        $query = "UPDATE " . $this->table . " 
                  SET jour = :jour, heure_debut = :heure_debut, heure_fin = :heure_fin, 
                      id_salle = :id_salle, id_cours = :id_cours
                  WHERE id_horaire = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':jour', $jour);
        $stmt->bindParam(':heure_debut', $heure_debut);
        $stmt->bindParam(':heure_fin', $heure_fin);
        $stmt->bindParam(':id_salle', $id_salle);
        $stmt->bindParam(':id_cours', $id_cours);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id_horaire = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getByCours($id_cours) {
        $query = "SELECT h.*, s.nom as salle_nom FROM " . $this->table . " h
                  LEFT JOIN salle s ON h.id_salle = s.id_salle
                  WHERE h.id_cours = :id_cours
                  ORDER BY h.jour ASC, h.heure_debut ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_cours', $id_cours);
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

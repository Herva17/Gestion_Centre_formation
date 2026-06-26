<?php

class Apprenant {
    private $conn;
    private $table = 'apprenant';

    // Propriétés avec validation
    private $id_apprenant;
    private $nom;
    private $prenom;
    private $telephone;
    private $adresse;
    private $date_creation;
    private $statut;

    public function __construct($db) {
        $this->conn = $db;
        // Définir le fuseau horaire
        date_default_timezone_set('Africa/Kinshasa');
    }

    // Getters et Setters
    public function getId() {
        return $this->id_apprenant;
    }

    public function getNom() {
        return $this->nom;
    }

    public function setNom($nom) {
        $this->nom = htmlspecialchars(strip_tags(trim($nom)));
        return $this;
    }

    public function getPrenom() {
        return $this->prenom;
    }

    public function setPrenom($prenom) {
        $this->prenom = htmlspecialchars(strip_tags(trim($prenom)));
        return $this;
    }

    public function getTelephone() {
        return $this->telephone;
    }

    public function setTelephone($telephone) {
        // Nettoyer le numéro de téléphone
        $telephone = preg_replace('/[^0-9+]/', '', trim($telephone));
        $this->telephone = $telephone;
        return $this;
    }

    public function getAdresse() {
        return $this->adresse;
    }

    public function setAdresse($adresse) {
        $this->adresse = htmlspecialchars(strip_tags(trim($adresse)));
        return $this;
    }

    // Méthode pour obtenir tous les apprenants avec pagination
    public function getAll($limit = null, $offset = null) {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY nom ASC";
            
            if ($limit !== null) {
                $query .= " LIMIT :limit";
                if ($offset !== null) {
                    $query .= " OFFSET :offset";
                }
            }
            
            $stmt = $this->conn->prepare($query);
            
            if ($limit !== null) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getAll: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id_apprenant = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getById: " . $e->getMessage());
            return false;
        }
    }

    // Méthode create améliorée avec validation
    public function create($nom, $prenom, $telephone, $adresse) {
        try {
            // Validation des données
            if (empty($nom) || empty($prenom)) {
                throw new Exception("Le nom et le prénom sont obligatoires");
            }

            // Nettoyer et valider les données
            $nom = htmlspecialchars(strip_tags(trim($nom)));
            $prenom = htmlspecialchars(strip_tags(trim($prenom)));
            $telephone = preg_replace('/[^0-9+]/', '', trim($telephone));
            $adresse = htmlspecialchars(strip_tags(trim($adresse)));

            // Vérifier si l'apprenant existe déjà
            if ($this->exists($nom, $prenom, $telephone)) {
                throw new Exception("Cet apprenant existe déjà dans le système");
            }

            $query = "INSERT INTO " . $this->table . " 
                      (nom, prenom, telephone, adresse, date_creation, statut) 
                      VALUES (:nom, :prenom, :telephone, :adresse, :date_creation, :statut)";
            
            $stmt = $this->conn->prepare($query);
            
            $date_creation = date('Y-m-d H:i:s');
            $statut = 'actif';
            
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':date_creation', $date_creation);
            $stmt->bindParam(':statut', $statut);
            
            if ($stmt->execute()) {
                $lastId = $this->conn->lastInsertId();
                // Vérifier que l'ID est valide
                if ($lastId === '0' || empty($lastId)) {
                    // Si l'ID est 0, récupérer le dernier ID manuellement
                    $lastId = $this->getLastInsertId();
                }
                return $lastId;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur create: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erreur validation: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour récupérer le dernier ID inséré
    private function getLastInsertId() {
        try {
            $query = "SELECT MAX(id_apprenant) as last_id FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['last_id'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Vérifier si l'apprenant existe déjà
    public function exists($nom, $prenom, $telephone) {
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                      WHERE nom = :nom AND prenom = :prenom AND telephone = :telephone";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update($id, $nom, $prenom, $telephone, $adresse) {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET nom = :nom, prenom = :prenom, telephone = :telephone, 
                          adresse = :adresse, date_modification = :date_modification
                      WHERE id_apprenant = :id";
            
            $stmt = $this->conn->prepare($query);
            
            $date_modification = date('Y-m-d H:i:s');
            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':adresse', $adresse);
            $stmt->bindParam(':date_modification', $date_modification);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur update: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            // Vérifier si l'apprenant a des inscriptions
            $query = "SELECT COUNT(*) as count FROM inscription WHERE id_apprenant = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                // Si des inscriptions existent, on peut soit empêcher la suppression, soit marquer comme inactif
                return $this->setInactive($id);
            }
            
            $query = "DELETE FROM " . $this->table . " WHERE id_apprenant = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur delete: " . $e->getMessage());
            return false;
        }
    }

    // Marquer l'apprenant comme inactif au lieu de le supprimer
    private function setInactive($id) {
        try {
            $query = "UPDATE " . $this->table . " SET statut = 'inactif' WHERE id_apprenant = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Erreur getCount: " . $e->getMessage());
            return 0;
        }
    }

    public function search($keyword) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                      WHERE nom LIKE :keyword 
                         OR prenom LIKE :keyword 
                         OR telephone LIKE :keyword
                         OR adresse LIKE :keyword
                      ORDER BY nom ASC";
            $stmt = $this->conn->prepare($query);
            $keyword = "%$keyword%";
            $stmt->bindParam(':keyword', $keyword);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur search: " . $e->getMessage());
            return [];
        }
    }

    // Obtenir les apprenants actifs
    public function getActive() {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE statut = 'actif' ORDER BY nom ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getActive: " . $e->getMessage());
            return [];
        }
    }

    // Obtenir les statistiques des apprenants
    public function getStats() {
        try {
            $stats = [
                'total' => 0,
                'actifs' => 0,
                'inactifs' => 0,
                'nouveaux_mois' => 0
            ];

            // Total
            $stats['total'] = $this->getCount();

            // Actifs et inactifs
            $query = "SELECT statut, COUNT(*) as count FROM " . $this->table . " GROUP BY statut";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $row) {
                if ($row['statut'] === 'actif') {
                    $stats['actifs'] = (int)$row['count'];
                } elseif ($row['statut'] === 'inactif') {
                    $stats['inactifs'] = (int)$row['count'];
                }
            }

            // Nouveaux du mois
            $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                      WHERE MONTH(date_creation) = MONTH(CURRENT_DATE()) 
                      AND YEAR(date_creation) = YEAR(CURRENT_DATE())";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['nouveaux_mois'] = (int)$result['count'];

            return $stats;
        } catch (PDOException $e) {
            error_log("Erreur getStats: " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour obtenir les apprenants paginés
    public function getPaginated($page = 1, $perPage = 20) {
        try {
            $offset = ($page - 1) * $perPage;
            return $this->getAll($perPage, $offset);
        } catch (PDOException $e) {
            error_log("Erreur getPaginated: " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour obtenir le nombre total de pages
    public function getTotalPages($perPage = 20) {
        try {
            $total = $this->getCount();
            return ceil($total / $perPage);
        } catch (PDOException $e) {
            error_log("Erreur getTotalPages: " . $e->getMessage());
            return 0;
        }
    }
}
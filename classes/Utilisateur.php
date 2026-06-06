<?php

class Utilisateur {
    private $conn;
    private $table = 'utilisateur';

    public $id_utilisateur;
    public $nom;
    public $prenom;
    public $sexe;
    public $telephone;
    public $email;
    public $nom_utilisateur;
    public $mot_de_passe;
    public $role;
    public $statut;
    public $date_creation;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Créer un utilisateur
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (nom, prenom, sexe, telephone, email, nom_utilisateur, mot_de_passe, role, statut) 
                  VALUES 
                  (:nom, :prenom, :sexe, :telephone, :email, :nom_utilisateur, :mot_de_passe, :role, :statut)";

        $stmt = $this->conn->prepare($query);

        // Hash le mot de passe
        $this->mot_de_passe = password_hash($this->mot_de_passe, PASSWORD_BCRYPT);

        // Bind les paramètres
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':sexe', $this->sexe);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':nom_utilisateur', $this->nom_utilisateur);
        $stmt->bindParam(':mot_de_passe', $this->mot_de_passe);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':statut', $this->statut);

        return $stmt->execute();
    }

    // Récupérer tous les utilisateurs
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_utilisateur = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par nom d'utilisateur
    public function getByUsername($nom_utilisateur) {
        $query = "SELECT * FROM " . $this->table . " WHERE nom_utilisateur = :nom_utilisateur";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom_utilisateur', $nom_utilisateur);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par email
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Authentifier un utilisateur
    public function authenticate($nom_utilisateur, $mot_de_passe) {
        $user = $this->getByUsername($nom_utilisateur);
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            if ($user['statut'] === 'Actif') {
                return $user;
            }
        }
        return false;
    }

    // Mettre à jour un utilisateur
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET nom = :nom, prenom = :prenom, sexe = :sexe, telephone = :telephone, 
                      email = :email, nom_utilisateur = :nom_utilisateur, role = :role, statut = :statut 
                  WHERE id_utilisateur = :id";

        $stmt = $this->conn->prepare($query);

        // Bind les paramètres
        $stmt->bindParam(':id', $this->id_utilisateur);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':sexe', $this->sexe);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':nom_utilisateur', $this->nom_utilisateur);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':statut', $this->statut);

        return $stmt->execute();
    }

    // Changer le mot de passe
    public function changePassword($id, $new_password) {
        $query = "UPDATE " . $this->table . " SET mot_de_passe = :mot_de_passe WHERE id_utilisateur = :id";
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':mot_de_passe', $hashed_password);
        return $stmt->execute();
    }

    // Supprimer un utilisateur
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id_utilisateur = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_utilisateur);
        return $stmt->execute();
    }

    // Compter les utilisateurs
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Compter les utilisateurs par rôle
    public function countByRole($role) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE role = :role";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Compter les utilisateurs actifs
    public function countActive() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE statut = 'Actif'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Récupérer les utilisateurs par rôle
    public function getByRole($role) {
        $query = "SELECT * FROM " . $this->table . " WHERE role = :role ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les utilisateurs actifs
    public function getActive() {
        $query = "SELECT * FROM " . $this->table . " WHERE statut = 'Actif' ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vérifier si un nom d'utilisateur existe
    public function userExists($nom_utilisateur) {
        $query = "SELECT id_utilisateur FROM " . $this->table . " WHERE nom_utilisateur = :nom_utilisateur";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom_utilisateur', $nom_utilisateur);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Vérifier si un email existe
    public function emailExists($email) {
        $query = "SELECT id_utilisateur FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>

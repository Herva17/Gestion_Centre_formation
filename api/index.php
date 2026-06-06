<?php
/**
 * API REST pour l'application GCF
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Apprenant.php';
require_once __DIR__ . '/../classes/Filiere.php';
require_once __DIR__ . '/../classes/Inscription.php';
require_once __DIR__ . '/../classes/Paiement.php';
require_once __DIR__ . '/../classes/Utilisateur.php';

$database = new Database();
$db = $database->connect();

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', $request_uri);
$resource = end($path_parts);

// Récupérer l'action si elle existe
$action = $_GET['action'] ?? null;
$utilisateur = new Utilisateur($db);

try {
    // Actions Utilisateurs
    if ($action === 'save_user') {
        $id = $_POST['id_utilisateur'] ?? null;
        $utilisateur->nom = $_POST['nom'] ?? '';
        $utilisateur->prenom = $_POST['prenom'] ?? '';
        $utilisateur->sexe = $_POST['sexe'] ?? '';
        $utilisateur->telephone = $_POST['telephone'] ?? '';
        $utilisateur->email = $_POST['email'] ?? '';
        $utilisateur->nom_utilisateur = $_POST['nom_utilisateur'] ?? '';
        $utilisateur->role = $_POST['role'] ?? '';
        $utilisateur->statut = $_POST['statut'] ?? 'Actif';

        if ($id) {
            // Mise à jour
            $utilisateur->id_utilisateur = $id;
            if (!empty($_POST['mot_de_passe'])) {
                $utilisateur->changePassword($id, $_POST['mot_de_passe']);
            }
            if ($utilisateur->update()) {
                echo json_encode(['success' => true, 'message' => 'Utilisateur mis à jour avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        } else {
            // Création
            if (empty($_POST['mot_de_passe'])) {
                echo json_encode(['success' => false, 'message' => 'Le mot de passe est requis']);
            } else {
                $utilisateur->mot_de_passe = $_POST['mot_de_passe'];
                if ($utilisateur->create()) {
                    echo json_encode(['success' => true, 'message' => 'Utilisateur créé avec succès']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la création']);
                }
            }
        }
        exit;
    }

    if ($action === 'get_user') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $user = $utilisateur->getById($id);
            if ($user) {
                echo json_encode(['success' => true, 'data' => $user]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
        }
        exit;
    }

    if ($action === 'delete_user') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $utilisateur->id_utilisateur = $id;
            if ($utilisateur->delete()) {
                echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID manquant']);
        }
        exit;
    }

    // Routes Standards
    // Route: GET /api/apprenants
    if ($resource === 'apprenants' && $request_method === 'GET') {
        $apprenant = new Apprenant($db);
        $apprenants = $apprenant->getAll();
        echo json_encode([
            'success' => true,
            'data' => $apprenants,
            'count' => count($apprenants)
        ]);
    }
    
    // Route: GET /api/filieres
    elseif ($resource === 'filieres' && $request_method === 'GET') {
        $filiere = new Filiere($db);
        $filieres = $filiere->getAll();
        echo json_encode([
            'success' => true,
            'data' => $filieres,
            'count' => count($filieres)
        ]);
    }
    
    // Route: GET /api/inscriptions
    elseif ($resource === 'inscriptions' && $request_method === 'GET') {
        $inscription = new Inscription($db);
        $inscriptions = $inscription->getAll();
        echo json_encode([
            'success' => true,
            'data' => $inscriptions,
            'count' => count($inscriptions)
        ]);
    }
    
    // Route: GET /api/paiements
    elseif ($resource === 'paiements' && $request_method === 'GET') {
        $paiement = new Paiement($db);
        $paiements = $paiement->getAll();
        echo json_encode([
            'success' => true,
            'data' => $paiements,
            'count' => count($paiements)
        ]);
    }
    
    // Route: GET /api/statistiques
    elseif ($resource === 'statistiques' && $request_method === 'GET') {
        $apprenant = new Apprenant($db);
        $filiere = new Filiere($db);
        $inscription = new Inscription($db);
        $paiement = new Paiement($db);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_apprenants' => $apprenant->getCount(),
                'total_filieres' => $filiere->getCount(),
                'total_inscriptions' => $inscription->getCount(),
                'total_paiements' => $paiement->getCount(),
                'total_revenue' => $paiement->getTotalMontant(),
                'revenue_inscriptions' => $inscription->getTotalRevenue()
            ]
        ]);
    }
    
    else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Route non trouvée'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

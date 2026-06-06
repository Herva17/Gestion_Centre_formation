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

$database = new Database();
$db = $database->connect();

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', $request_uri);
$resource = end($path_parts);

try {
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

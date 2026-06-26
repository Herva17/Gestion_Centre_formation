<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Cours.php';
require_once __DIR__ . '/../classes/Filiere.php';

$page_title = 'Gestion des Cours';

$database = new Database();
$db = $database->connect();
$cours = new Cours($db);
$filiere = new Filiere($db);

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        // Supprimer d'abord les horaires liés
        $query = "DELETE FROM horaire WHERE id_cours = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($cours->delete($id)) {
            $_SESSION['success'] = 'Cours supprimé avec succès';
            $_SESSION['success_type'] = 'success';
        }
    }
    header('Location: cours.php');
    exit;
}

// Handle edit
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    if ($id > 0) {
        $edit_data = $cours->getById($id);
        if ($edit_data) {
            $edit_mode = true;
        }
    }
}

// Handle POST (Create/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id_filiere = isset($_POST['id_filiere']) && $_POST['id_filiere'] !== '' ? (int)$_POST['id_filiere'] : null;
    $duree = trim($_POST['duree'] ?? '');
    $niveau = $_POST['niveau'] ?? 'Débutant';
    $type = $_POST['type'] ?? 'Théorique';
    $credit = (int)($_POST['credit'] ?? 0);
    
    $id_cours = isset($_POST['id_cours']) ? (int)$_POST['id_cours'] : 0;
    
    // Validation
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom du cours est obligatoire";
    if (empty($id_filiere)) $errors[] = "Veuillez sélectionner une filière";
    
    if (empty($errors)) {
        if ($id_cours > 0) {
            // UPDATE
            if ($cours->update($id_cours, $nom, $description, $id_filiere)) {
                $_SESSION['success'] = 'Cours mis à jour avec succès';
                $_SESSION['success_type'] = 'success';
            } else {
                $_SESSION['success'] = 'Erreur lors de la mise à jour';
                $_SESSION['success_type'] = 'error';
            }
        } else {
            // CREATE
            if ($cours->create($nom, $description, $id_filiere)) {
                $_SESSION['success'] = 'Cours ajouté avec succès';
                $_SESSION['success_type'] = 'success';
            } else {
                $_SESSION['success'] = 'Erreur lors de l\'ajout';
                $_SESSION['success_type'] = 'error';
            }
        }
    } else {
        $_SESSION['success'] = implode(', ', $errors);
        $_SESSION['success_type'] = 'error';
    }
    
    header('Location: cours.php');
    exit;
}

$touts_cours = $cours->getAll();
$filieres = $filiere->getAll();

// Données fictives pour la démonstration
if (empty($touts_cours) && !empty($filieres)) {
    $filiere_noms = [];
    foreach ($filieres as $f) {
        $filiere_noms[$f['id_filiere']] = $f['nom'];
    }
    
    $touts_cours = [
        ['id_cours' => 1, 'nom' => 'PHP & MySQL - Développement Backend', 'description' => 'Maîtrisez PHP 8 et MySQL pour créer des applications web dynamiques et sécurisées', 'id_filiere' => array_key_first($filiere_noms), 'filiere_nom' => reset($filiere_noms), 'duree' => '8 semaines', 'niveau' => 'Intermédiaire', 'type' => 'Pratique', 'credit' => 6, 'couleur' => '#667eea'],
        ['id_cours' => 2, 'nom' => 'JavaScript & React - Frontend Moderne', 'description' => 'Développez des interfaces utilisateur réactives avec JavaScript ES6 et React.js', 'id_filiere' => array_key_first($filiere_noms), 'filiere_nom' => reset($filiere_noms), 'duree' => '6 semaines', 'niveau' => 'Intermédiaire', 'type' => 'Pratique', 'credit' => 5, 'couleur' => '#f093fb'],
        ['id_cours' => 3, 'nom' => 'Python pour la Data Science', 'description' => 'Apprenez à analyser et visualiser des données avec Python, Pandas et Matplotlib', 'id_filiere' => array_key_first($filiere_noms), 'filiere_nom' => reset($filiere_noms), 'duree' => '10 semaines', 'niveau' => 'Avancé', 'type' => 'Mixte', 'credit' => 8, 'couleur' => '#4facfe'],
        ['id_cours' => 4, 'nom' => 'UI/UX Design - Figma & Adobe XD', 'description' => 'Concevez des interfaces utilisateur intuitives avec Figma et Adobe XD', 'id_filiere' => array_key_first($filiere_noms), 'filiere_nom' => reset($filiere_noms), 'duree' => '5 semaines', 'niveau' => 'Débutant', 'type' => 'Théorique', 'credit' => 4, 'couleur' => '#fa709a'],
        ['id_cours' => 5, 'nom' => 'Sécurité des Réseaux Informatiques', 'description' => 'Protégez les infrastructures réseau et maîtrisez les protocoles de sécurité', 'id_filiere' => array_key_first($filiere_noms), 'filiere_nom' => reset($filiere_noms), 'duree' => '7 semaines', 'niveau' => 'Avancé', 'type' => 'Théorique', 'credit' => 7, 'couleur' => '#48bb78'],
        ['id_cours' => 6, 'nom' => 'DevOps & CI/CD avec Docker', 'description' => 'Automatisez vos déploiements et maîtrisez les pipelines CI/CD avec Docker', 'id_filiere' => array_key_first($filiere_noms), 'filiere_nom' => reset($filiere_noms), 'duree' => '6 semaines', 'niveau' => 'Avancé', 'type' => 'Pratique', 'credit' => 6, 'couleur' => '#ed8936'],
    ];
}

include __DIR__ . '/../includes/header.php';
?>

<style>
    :root {
        --primary: #667eea;
        --primary-dark: #5a67d8;
        --secondary: #764ba2;
        --success: #48bb78;
        --warning: #ed8936;
        --danger: #fc8181;
        --info: #4facfe;
        --dark: #1a202c;
        --gray: #718096;
        --light-gray: #f7fafc;
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 20px rgba(0,0,0,0.12);
        --shadow-lg: 0 10px 40px rgba(0,0,0,0.15);
        --shadow-xl: 0 20px 60px rgba(0,0,0,0.2);
    }

    .page-wrapper {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px;
        position: relative;
    }

    .page-wrapper::before {
        content: '';
        position: fixed;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: 
            radial-gradient(circle at 70% 20%, rgba(102, 126, 234, 0.08) 0%, transparent 50%),
            radial-gradient(circle at 30% 80%, rgba(118, 75, 162, 0.08) 0%, transparent 50%);
        animation: rotateBg 30s linear infinite;
        pointer-events: none;
        z-index: 0;
    }

    @keyframes rotateBg {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .content-wrapper {
        position: relative;
        z-index: 1;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border-radius: 24px;
        padding: 30px 40px;
        margin-bottom: 30px;
        box-shadow: 0 20px 60px rgba(72, 187, 120, 0.3);
        position: relative;
        overflow: hidden;
        animation: slideDown 0.6s ease-out;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
        animation: pulseGlow 4s ease-in-out infinite;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulseGlow {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.2); opacity: 1; }
    }

    .page-header h1 {
        color: white;
        font-size: 28px;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .page-header .subtitle {
        color: rgba(255,255,255,0.9);
        font-size: 14px;
        margin-top: 5px;
        position: relative;
        z-index: 1;
    }

    .btn-add {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        color: white;
        padding: 12px 28px;
        border-radius: 50px;
        border: 2px solid rgba(255,255,255,0.3);
        transition: all 0.3s ease;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        cursor: pointer;
        animation: bounceIn 0.8s ease-out;
        position: relative;
        z-index: 1;
    }

    .btn-add:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        color: white;
        text-decoration: none;
    }

    @keyframes bounceIn {
        0% { opacity: 0; transform: scale(0.8); }
        60% { transform: scale(1.05); }
        100% { opacity: 1; transform: scale(1); }
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 22px 25px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.04);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .stat-card .stat-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 50px;
        opacity: 0.08;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon {
        opacity: 0.12;
        transform: translateY(-50%) scale(1.1) rotate(-5deg);
    }

    .stat-card .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark);
        line-height: 1.2;
    }

    .stat-card .stat-label {
        font-size: 13px;
        color: var(--gray);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

    .stat-card .stat-change {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        padding: 3px 12px;
        border-radius: 20px;
        margin-top: 10px;
        font-weight: 600;
    }

    .stat-change.up { background: rgba(72, 187, 120, 0.12); color: #48bb78; }
    .stat-change.down { background: rgba(252, 129, 129, 0.12); color: #fc8181; }
    .stat-change.neutral { background: rgba(237, 137, 54, 0.12); color: #ed8936; }

    .search-wrapper {
        position: relative;
        margin-bottom: 25px;
    }

    .search-wrapper input {
        width: 100%;
        padding: 16px 20px 16px 55px;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: white;
        box-shadow: var(--shadow-sm);
    }

    .search-wrapper input:focus {
        outline: none;
        border-color: var(--success);
        box-shadow: 0 0 0 4px rgba(72, 187, 120, 0.1), var(--shadow-md);
    }

    .search-wrapper .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 18px;
        transition: all 0.3s ease;
    }

    .search-wrapper:focus-within .search-icon {
        color: var(--success);
    }

    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        animation: slideInRight 0.5s ease;
        max-width: 400px;
    }

    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    .toast {
        padding: 16px 24px;
        border-radius: 16px;
        color: white;
        font-weight: 500;
        box-shadow: var(--shadow-lg);
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
        backdrop-filter: blur(10px);
    }

    .toast-success { background: linear-gradient(135deg, #48bb78, #38a169); }
    .toast-error { background: linear-gradient(135deg, #fc8181, #e53e3e); }

    .toast .toast-close {
        margin-left: auto;
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toast .toast-close:hover {
        background: rgba(255,255,255,0.3);
        transform: rotate(90deg);
    }

    .cours-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 25px;
    }

    .cours-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: all 0.4s ease;
        cursor: pointer;
        position: relative;
        animation: fadeInUp 0.5s ease-out;
        animation-fill-mode: both;
    }

    .cours-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }

    .cours-card .card-header {
        padding: 20px 25px;
        color: white;
        position: relative;
        overflow: hidden;
        min-height: 90px;
    }

    .cours-card .card-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transition: all 0.4s ease;
    }

    .cours-card:hover .card-header::after {
        transform: scale(1.3);
        opacity: 0.5;
    }

    .cours-card .card-header .card-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 50px;
        opacity: 0.15;
        transition: all 0.4s ease;
    }

    .cours-card:hover .card-header .card-icon {
        opacity: 0.25;
        transform: translateY(-50%) scale(1.1) rotate(-10deg);
    }

    .cours-card .card-header .cours-nom {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 10px rgba(0,0,0,0.15);
        line-height: 1.3;
    }

    .cours-card .card-header .cours-meta {
        font-size: 12px;
        opacity: 0.9;
        margin-top: 8px;
        position: relative;
        z-index: 1;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .cours-card .card-body {
        padding: 20px 25px;
    }

    .cours-card .card-body .description {
        color: var(--gray);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .cours-card .card-body .details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 15px;
    }

    .cours-card .card-body .detail-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--dark);
    }

    .cours-card .card-body .detail-item i {
        width: 18px;
        color: var(--success);
        font-size: 14px;
    }

    .cours-card .card-body .detail-item .label {
        color: var(--gray);
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: block;
    }

    .cours-card .card-body .detail-item .value {
        font-weight: 600;
    }

    .cours-card .card-footer {
        padding: 15px 25px;
        border-top: 1px solid #edf2f7;
        display: flex;
        gap: 10px;
        background: var(--light-gray);
    }

    .cours-card .card-footer .btn-action {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
    }

    .cours-card .card-footer .btn-edit {
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    .cours-card .card-footer .btn-edit:hover {
        background: #48bb78;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(72, 187, 120, 0.3);
    }

    .cours-card .card-footer .btn-delete {
        background: rgba(252, 129, 129, 0.1);
        color: #fc8181;
    }

    .cours-card .card-footer .btn-delete:hover {
        background: #fc8181;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(252, 129, 129, 0.3);
    }

    .cours-card .card-footer .btn-view {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .cours-card .card-footer .btn-view:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }

    .cours-card .badge-niveau {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        background: rgba(255,255,255,0.2);
        color: white;
        backdrop-filter: blur(5px);
    }

    .cours-card .badge-type {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 600;
        background: rgba(255,255,255,0.15);
        color: white;
        backdrop-filter: blur(5px);
    }

    .cours-card .badge-credit {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 600;
        background: rgba(255,255,255,0.25);
        color: white;
        backdrop-filter: blur(5px);
    }

    .filiere-tag {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #a0aec0;
        grid-column: 1 / -1;
    }

    .empty-state i {
        font-size: 60px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .empty-state h3 {
        color: #4a5568;
        font-size: 20px;
        margin-bottom: 10px;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999;
        animation: fadeIn 0.3s ease;
        padding: 20px;
    }

    .modal-overlay.active { display: flex; }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-content {
        background: white;
        border-radius: 24px;
        max-width: 550px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        animation: modalSlideIn 0.4s ease-out;
        box-shadow: var(--shadow-xl);
    }

    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(50px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-header-custom {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        padding: 25px 30px;
        border-radius: 24px 24px 0 0;
        color: white;
        position: relative;
    }

    .modal-header-custom h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
    }

    .modal-header-custom .modal-subtitle {
        font-size: 14px;
        opacity: 0.9;
        margin-top: 5px;
    }

    .modal-close {
        position: absolute;
        right: 16px;
        top: 16px;
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        font-size: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        background: rgba(255,255,255,0.3);
        transform: rotate(90deg);
    }

    .modal-body-custom {
        padding: 30px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 6px;
        font-size: 14px;
    }

    .form-group label .required {
        color: var(--danger);
        margin-left: 3px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #fafbfc;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--success);
        background: white;
        box-shadow: 0 0 0 4px rgba(72, 187, 120, 0.1);
    }

    .form-control::placeholder {
        color: #a0aec0;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .form-hint {
        font-size: 12px;
        color: #a0aec0;
        margin-top: 4px;
    }

    .modal-footer-custom {
        padding: 20px 30px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-submit {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(72, 187, 120, 0.3);
    }

    .btn-cancel {
        background: var(--light-gray);
        color: #4a5568;
        padding: 12px 30px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #edf2f7;
    }

    @media (max-width: 992px) {
        .page-header { padding: 25px; }
        .page-header h1 { font-size: 24px; }
        .cours-grid { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 20px;
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .page-header .flex-wrap { justify-content: center; }
        .page-header h1 { font-size: 20px; }

        .btn-add { width: 100%; justify-content: center; }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .stat-card .stat-number { font-size: 22px; }
        .stat-card { padding: 16px 18px; }

        .cours-grid { grid-template-columns: 1fr; }

        .cours-card .card-body .details {
            grid-template-columns: 1fr;
        }

        .cours-card .card-footer {
            flex-direction: column;
        }

        .cours-card .card-footer .btn-action {
            justify-content: center;
        }

        .modal-content { margin: 10px; }
        .modal-footer-custom { flex-direction: column; }
        .btn-submit, .btn-cancel { width: 100%; justify-content: center; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .page-header { padding: 16px; }
    }
</style>

<div class="page-wrapper">
    <div class="content-wrapper">

        <!-- Toast Notification -->
        <?php if (isset($_SESSION['success'])): ?>
        <div class="toast-container" id="toastContainer">
            <div class="toast toast-<?php echo $_SESSION['success_type'] ?? 'success'; ?>">
                <i class="fas fa-<?php echo ($_SESSION['success_type'] ?? 'success') === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <span><?php echo $_SESSION['success']; unset($_SESSION['success']); unset($_SESSION['success_type']); ?></span>
                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toastContainer');
                if (toast) { toast.style.opacity = '0'; toast.style.transform = 'translateX(100%)'; setTimeout(() => toast.style.display = 'none', 300); }
            }, 5000);
        </script>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h1><i class="fas fa-video me-3"></i>Gestion des Cours</h1>
                    <div class="subtitle">
                        <i class="fas fa-database me-2"></i>
                        <?php echo count($touts_cours); ?> cours disponibles
                        <span style="margin: 0 10px;">•</span>
                        <i class="fas fa-calendar-alt me-2"></i>
                        <?php echo date('d/m/Y H:i'); ?>
                    </div>
                </div>
                <button onclick="openModal()" class="btn-add">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouveau Cours</span>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <?php
        $total = count($touts_cours);
        $total_credits = array_sum(array_column($touts_cours, 'credit'));
        $types = array_count_values(array_column($touts_cours, 'type'));
        $type_principal = !empty($types) ? array_keys($types)[0] : 'N/A';
        ?>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-video stat-icon"></i>
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total Cours</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +18%</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-star stat-icon"></i>
                <div class="stat-number"><?php echo $total_credits; ?></div>
                <div class="stat-label">Crédits Totaux</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +12%</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-tag stat-icon"></i>
                <div class="stat-number"><?php echo $type_principal; ?></div>
                <div class="stat-label">Type Principal</div>
                <span class="stat-change neutral"><i class="fas fa-minus"></i> Stable</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock stat-icon"></i>
                <div class="stat-number"><?php echo count(array_unique(array_column($touts_cours, 'niveau'))); ?></div>
                <div class="stat-label">Niveaux Différents</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +5%</span>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Rechercher un cours par nom, description, filière ou niveau..." onkeyup="filterCards()">
        </div>

        <!-- Cours Grid -->
        <div class="cours-grid" id="coursGrid">
            <?php if (empty($touts_cours)): ?>
                <div class="empty-state">
                    <i class="fas fa-video-slash"></i>
                    <h3>Aucun cours</h3>
                    <p>Commencez par ajouter votre premier cours</p>
                </div>
            <?php else: ?>
                <?php foreach ($touts_cours as $index => $c): 
                    $couleur = $c['couleur'] ?? ['#667eea', '#f093fb', '#4facfe', '#fa709a', '#48bb78', '#ed8936'][$index % 6];
                ?>
                <div class="cours-card" style="animation-delay: <?php echo $index * 0.06; ?>s" data-search="<?php echo strtolower($c['nom'] . ' ' . ($c['description'] ?? '') . ' ' . ($c['filiere_nom'] ?? '') . ' ' . ($c['niveau'] ?? '')); ?>">
                    <div class="card-header" style="background: linear-gradient(135deg, <?php echo $couleur; ?>, <?php echo $couleur; ?>cc);">
                        <i class="fas fa-graduation-cap card-icon"></i>
                        <div class="cours-nom"><?php echo htmlspecialchars($c['nom']); ?></div>
                        <div class="cours-meta">
                            <span class="badge-niveau">
                                <i class="fas fa-signal"></i>
                                <?php echo htmlspecialchars($c['niveau'] ?? 'Débutant'); ?>
                            </span>
                            <span class="badge-type">
                                <i class="fas fa-<?php echo ($c['type'] ?? 'Théorique') === 'Pratique' ? 'code' : 'book'; ?>"></i>
                                <?php echo htmlspecialchars($c['type'] ?? 'Théorique'); ?>
                            </span>
                            <span class="badge-credit">
                                <i class="fas fa-star"></i>
                                <?php echo $c['credit'] ?? rand(3, 8); ?> crédits
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="description">
                            <?php echo htmlspecialchars($c['description'] ?? 'Description non disponible'); ?>
                        </p>
                        <div class="details">
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <span class="label">Durée</span>
                                    <span class="value"><?php echo htmlspecialchars($c['duree'] ?? 'Non spécifiée'); ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-book"></i>
                                <div>
                                    <span class="label">Filière</span>
                                    <span class="value"><?php echo htmlspecialchars($c['filiere_nom'] ?? '-'); ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-id-card"></i>
                                <div>
                                    <span class="label">Code</span>
                                    <span class="value">#<?php echo str_pad($c['id_cours'], 4, '0', STR_PAD_LEFT); ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <div>
                                    <span class="label">Étudiants</span>
                                    <span class="value"><?php echo rand(15, 45); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="?edit=<?php echo $c['id_cours']; ?>" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="?delete=<?php echo $c['id_cours']; ?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ? Cette action est irréversible et supprimera également les horaires associés.')">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                        <a href="#" class="btn-action btn-view" onclick="event.preventDefault(); alert('📚 Détails du cours #<?php echo $c['id_cours']; ?>\n\n📖 Nom: <?php echo htmlspecialchars($c['nom']); ?>\n📂 Filière: <?php echo htmlspecialchars($c['filiere_nom'] ?? '-'); ?>\n⏱️ Durée: <?php echo htmlspecialchars($c['duree'] ?? 'Non spécifiée'); ?>\n📊 Niveau: <?php echo htmlspecialchars($c['niveau'] ?? 'Débutant'); ?>\n🏷️ Type: <?php echo htmlspecialchars($c['type'] ?? 'Théorique'); ?>\n⭐ Crédits: <?php echo $c['credit'] ?? rand(3, 8); ?>\n📝 Description: <?php echo htmlspecialchars($c['description'] ?? 'Non disponible'); ?>')">
                            <i class="fas fa-eye"></i> Détails
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Modal -->
        <div id="addModal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header-custom">
                    <h3>
                        <i class="fas fa-<?php echo $edit_mode ? 'edit' : 'plus-circle'; ?> me-2"></i>
                        <?php echo $edit_mode ? 'Modifier le Cours' : 'Ajouter un Cours'; ?>
                    </h3>
                    <div class="modal-subtitle">
                        <?php echo $edit_mode ? 'Mettez à jour les informations du cours' : 'Remplissez les informations pour ajouter un nouveau cours'; ?>
                    </div>
                    <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                
                <form method="POST" onsubmit="return validateForm()">
                    <?php if ($edit_mode && $edit_data): ?>
                        <input type="hidden" name="id_cours" value="<?php echo $edit_data['id_cours']; ?>">
                    <?php endif; ?>

                    <div class="modal-body-custom">
                        <div class="form-group">
                            <label><i class="fas fa-book me-2" style="color: var(--success);"></i>Nom du Cours <span class="required">*</span></label>
                            <input type="text" name="nom" class="form-control" 
                                   placeholder="Ex: PHP & MySQL - Développement Backend"
                                   value="<?php echo $edit_mode && $edit_data ? htmlspecialchars($edit_data['nom']) : ''; ?>"
                                   required>
                            <div class="form-hint">Le nom doit être clair et descriptif</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-align-left me-2" style="color: var(--info);"></i>Description</label>
                            <textarea name="description" class="form-control" 
                                      placeholder="Décrivez le contenu et les objectifs du cours"><?php echo $edit_mode && $edit_data ? htmlspecialchars($edit_data['description'] ?? '') : ''; ?></textarea>
                            <div class="form-hint">Présentez les compétences acquises</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-book me-2" style="color: var(--secondary);"></i>Filière <span class="required">*</span></label>
                            <select name="id_filiere" class="form-control" required>
                                <option value="">-- Sélectionner une filière --</option>
                                <?php foreach ($filieres as $fil): ?>
                                <option value="<?php echo $fil['id_filiere']; ?>" <?php echo ($edit_mode && $edit_data && $edit_data['id_filiere'] == $fil['id_filiere']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($fil['nom']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Choisissez la filière associée</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-clock me-2" style="color: var(--warning);"></i>Durée</label>
                            <input type="text" name="duree" class="form-control" 
                                   placeholder="Ex: 8 semaines, 3 mois"
                                   value="<?php echo $edit_mode && $edit_data ? htmlspecialchars($edit_data['duree'] ?? '') : ''; ?>">
                            <div class="form-hint">Durée estimée du cours</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-signal me-2" style="color: var(--secondary);"></i>Niveau</label>
                            <select name="niveau" class="form-control">
                                <option value="Débutant" <?php echo ($edit_mode && $edit_data && ($edit_data['niveau'] ?? 'Débutant') === 'Débutant') ? 'selected' : ''; ?>>Débutant</option>
                                <option value="Intermédiaire" <?php echo ($edit_mode && $edit_data && ($edit_data['niveau'] ?? '') === 'Intermédiaire') ? 'selected' : ''; ?>>Intermédiaire</option>
                                <option value="Avancé" <?php echo ($edit_mode && $edit_data && ($edit_data['niveau'] ?? '') === 'Avancé') ? 'selected' : ''; ?>>Avancé</option>
                                <option value="Expert" <?php echo ($edit_mode && $edit_data && ($edit_data['niveau'] ?? '') === 'Expert') ? 'selected' : ''; ?>>Expert</option>
                            </select>
                            <div class="form-hint">Niveau de difficulté du cours</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-<?php echo ($edit_mode && $edit_data && ($edit_data['type'] ?? 'Théorique') === 'Pratique') ? 'code' : 'book'; ?> me-2" style="color: var(--primary);"></i>Type</label>
                            <select name="type" class="form-control">
                                <option value="Théorique" <?php echo ($edit_mode && $edit_data && ($edit_data['type'] ?? 'Théorique') === 'Théorique') ? 'selected' : ''; ?>>Théorique</option>
                                <option value="Pratique" <?php echo ($edit_mode && $edit_data && ($edit_data['type'] ?? '') === 'Pratique') ? 'selected' : ''; ?>>Pratique</option>
                                <option value="Mixte" <?php echo ($edit_mode && $edit_data && ($edit_data['type'] ?? '') === 'Mixte') ? 'selected' : ''; ?>>Mixte</option>
                            </select>
                            <div class="form-hint">Format d'enseignement du cours</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-star me-2" style="color: var(--warning);"></i>Crédits</label>
                            <input type="number" name="credit" class="form-control" 
                                   placeholder="Ex: 6"
                                   value="<?php echo $edit_mode && $edit_data ? ($edit_data['credit'] ?? '') : ''; ?>"
                                   min="1" max="12">
                            <div class="form-hint">Nombre de crédits ECTS (1-12)</div>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn-cancel" onclick="closeModal()">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-<?php echo $edit_mode ? 'save' : 'plus'; ?> me-2"></i>
                            <?php echo $edit_mode ? 'Mettre à jour' : 'Ajouter'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
    // Filter Cards
    function filterCards() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const cards = document.querySelectorAll('.cours-card');

        cards.forEach(card => {
            const searchData = card.dataset.search || '';
            const match = searchData.indexOf(filter) > -1;
            card.style.display = match ? '' : 'none';
        });
    }

    // Modal
    function openModal() {
        document.getElementById('addModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('addModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    document.getElementById('addModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('addModal').classList.contains('active')) {
            closeModal();
        }
    });

    // Validation
    function validateForm() {
        const nom = document.querySelector('input[name="nom"]');
        const filiere = document.querySelector('select[name="id_filiere"]');
        
        if (!nom.value.trim()) {
            alert('⚠️ Le nom du cours est obligatoire');
            nom.focus();
            return false;
        }
        
        if (!filiere.value) {
            alert('⚠️ Veuillez sélectionner une filière');
            filiere.focus();
            return false;
        }
        
        return true;
    }

    // Open modal if edit mode
    <?php if ($edit_mode): ?>
    document.addEventListener('DOMContentLoaded', function() {
        openModal();
    });
    <?php endif; ?>

    // Card hover animation
    document.querySelectorAll('.cours-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.01)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
</script>
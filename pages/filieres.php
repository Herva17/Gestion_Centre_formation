<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Filiere.php';

$page_title = 'Gestion des Filières';

$database = new Database();
$db = $database->connect();
$filiere = new Filiere($db);

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0 && $filiere->delete($id)) {
        $_SESSION['success'] = 'Filière supprimée avec succès';
        $_SESSION['success_type'] = 'success';
    }
    header('Location: filieres.php');
    exit;
}

// Handle edit - récupérer les données
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    if ($id > 0) {
        $edit_data = $filiere->getById($id);
        if ($edit_data) {
            $edit_mode = true;
        }
    }
}

// Handle POST (Create/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $duree = trim($_POST['duree'] ?? '');
    $frais_mensuel = floatval($_POST['frais_mensuel'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $niveau = $_POST['niveau'] ?? 'Débutant';
    $type = $_POST['type'] ?? 'Présentiel';
    
    $id_filiere = isset($_POST['id_filiere']) ? (int)$_POST['id_filiere'] : 0;
    
    // Validation
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom de la filière est obligatoire";
    if (empty($duree)) $errors[] = "La durée est obligatoire";
    if ($frais_mensuel <= 0) $errors[] = "Le frais mensuel doit être supérieur à 0";
    
    if (empty($errors)) {
        if ($id_filiere > 0) {
            // UPDATE
            if ($filiere->update($id_filiere, $nom, $duree, $frais_mensuel)) {
                $_SESSION['success'] = 'Filière mise à jour avec succès';
                $_SESSION['success_type'] = 'success';
            } else {
                $_SESSION['success'] = 'Erreur lors de la mise à jour';
                $_SESSION['success_type'] = 'error';
            }
        } else {
            // CREATE
            if ($filiere->create($nom, $duree, $frais_mensuel)) {
                $_SESSION['success'] = 'Filière ajoutée avec succès';
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
    
    header('Location: filieres.php');
    exit;
}

$filieres = $filiere->getAll();

// Données fictives pour la démonstration
if (empty($filieres)) {
    $filieres = [
        ['id_filiere' => 1, 'nom' => 'Développement Web Full Stack', 'duree' => '12 mois', 'frais_mensuel' => 150, 'description' => 'Devenez un expert en développement web avec PHP, Laravel, JavaScript et React', 'niveau' => 'Avancé', 'type' => 'Présentiel', 'couleur' => '#667eea', 'nb_apprenants' => 45, 'date_creation' => '2024-01-15'],
        ['id_filiere' => 2, 'nom' => 'Data Science & Intelligence Artificielle', 'duree' => '18 mois', 'frais_mensuel' => 200, 'description' => 'Maîtrisez les techniques de data science, machine learning et deep learning', 'niveau' => 'Expert', 'type' => 'Mixte', 'couleur' => '#f093fb', 'nb_apprenants' => 32, 'date_creation' => '2024-01-20'],
        ['id_filiere' => 3, 'nom' => 'Design UX/UI', 'duree' => '9 mois', 'frais_mensuel' => 120, 'description' => 'Apprenez à concevoir des interfaces utilisateur intuitives et esthétiques', 'niveau' => 'Intermédiaire', 'type' => 'Présentiel', 'couleur' => '#4facfe', 'nb_apprenants' => 28, 'date_creation' => '2024-02-01'],
        ['id_filiere' => 4, 'nom' => 'Marketing Digital', 'duree' => '6 mois', 'frais_mensuel' => 100, 'description' => 'Maîtrisez les stratégies de marketing digital, SEO, SEM et réseaux sociaux', 'niveau' => 'Débutant', 'type' => 'En ligne', 'couleur' => '#fa709a', 'nb_apprenants' => 38, 'date_creation' => '2024-02-15'],
        ['id_filiere' => 5, 'nom' => 'Cybersécurité', 'duree' => '15 mois', 'frais_mensuel' => 180, 'description' => 'Protégez les systèmes d\'information et maîtrisez les techniques de sécurité', 'niveau' => 'Avancé', 'type' => 'Présentiel', 'couleur' => '#48bb78', 'nb_apprenants' => 25, 'date_creation' => '2024-03-01'],
        ['id_filiere' => 6, 'nom' => 'DevOps & Cloud Computing', 'duree' => '12 mois', 'frais_mensuel' => 170, 'description' => 'Automatisez les déploiements et maîtrisez les infrastructures cloud', 'niveau' => 'Avancé', 'type' => 'Mixte', 'couleur' => '#ed8936', 'nb_apprenants' => 30, 'date_creation' => '2024-03-15'],
        ['id_filiere' => 7, 'nom' => 'Mobile App Development (iOS/Android)', 'duree' => '14 mois', 'frais_mensuel' => 160, 'description' => 'Créez des applications mobiles natives avec Swift et Kotlin', 'niveau' => 'Intermédiaire', 'type' => 'Présentiel', 'couleur' => '#fc8181', 'nb_apprenants' => 22, 'date_creation' => '2024-04-01'],
        ['id_filiere' => 8, 'nom' => 'Blockchain & Cryptomonnaies', 'duree' => '10 mois', 'frais_mensuel' => 190, 'description' => 'Comprenez et développez des applications décentralisées sur la blockchain', 'niveau' => 'Expert', 'type' => 'En ligne', 'couleur' => '#764ba2', 'nb_apprenants' => 18, 'date_creation' => '2024-04-15']
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 24px;
        padding: 30px 40px;
        margin-bottom: 30px;
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
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
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), var(--shadow-md);
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
        color: var(--primary);
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

    .filieres-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
    }

    .filiere-card {
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

    .filiere-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }

    .filiere-card .card-header {
        padding: 20px 25px;
        color: white;
        position: relative;
        overflow: hidden;
        min-height: 100px;
    }

    .filiere-card .card-header::after {
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

    .filiere-card:hover .card-header::after {
        transform: scale(1.3);
        opacity: 0.5;
    }

    .filiere-card .card-header .card-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 50px;
        opacity: 0.15;
        transition: all 0.4s ease;
    }

    .filiere-card:hover .card-header .card-icon {
        opacity: 0.25;
        transform: translateY(-50%) scale(1.1) rotate(-10deg);
    }

    .filiere-card .card-header .filiere-nom {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }

    .filiere-card .card-header .filiere-meta {
        font-size: 13px;
        opacity: 0.9;
        margin-top: 5px;
        position: relative;
        z-index: 1;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filiere-card .card-body {
        padding: 20px 25px;
    }

    .filiere-card .card-body .description {
        color: var(--gray);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .filiere-card .card-body .details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 18px;
    }

    .filiere-card .card-body .detail-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: var(--dark);
    }

    .filiere-card .card-body .detail-item i {
        width: 20px;
        color: var(--primary);
        font-size: 16px;
    }

    .filiere-card .card-body .detail-item .label {
        color: var(--gray);
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
    }

    .filiere-card .card-body .detail-item .value {
        font-weight: 600;
    }

    .filiere-card .card-footer {
        padding: 15px 25px;
        border-top: 1px solid #edf2f7;
        display: flex;
        gap: 10px;
        background: var(--light-gray);
    }

    .filiere-card .card-footer .btn-action {
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

    .filiere-card .card-footer .btn-edit {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .filiere-card .card-footer .btn-edit:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }

    .filiere-card .card-footer .btn-delete {
        background: rgba(252, 129, 129, 0.1);
        color: #fc8181;
    }

    .filiere-card .card-footer .btn-delete:hover {
        background: #fc8181;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(252, 129, 129, 0.3);
    }

    .filiere-card .card-footer .btn-view {
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    .filiere-card .card-footer .btn-view:hover {
        background: #48bb78;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(72, 187, 120, 0.3);
    }

    .filiere-card .badge-niveau {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        background: rgba(255,255,255,0.2);
        color: white;
        backdrop-filter: blur(5px);
    }

    .filiere-card .badge-type {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        background: rgba(255,255,255,0.15);
        color: white;
        backdrop-filter: blur(5px);
    }

    .filiere-card .frais-badge {
        background: rgba(255,255,255,0.2);
        padding: 6px 14px;
        border-radius: 50px;
        backdrop-filter: blur(5px);
        font-size: 14px;
        font-weight: 700;
        color: white;
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
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
        .filieres-grid { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); }
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

        .filieres-grid { grid-template-columns: 1fr; }

        .filiere-card .card-body .details {
            grid-template-columns: 1fr;
        }

        .filiere-card .card-footer {
            flex-direction: column;
        }

        .filiere-card .card-footer .btn-action {
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
                    <h1><i class="fas fa-graduation-cap me-3"></i>Gestion des Filières</h1>
                    <div class="subtitle">
                        <i class="fas fa-database me-2"></i>
                        <?php echo count($filieres); ?> filières disponibles
                        <span style="margin: 0 10px;">•</span>
                        <i class="fas fa-calendar-alt me-2"></i>
                        <?php echo date('d/m/Y H:i'); ?>
                    </div>
                </div>
                <button onclick="openModal()" class="btn-add">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouvelle Filière</span>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <?php
        $total = count($filieres);
        $total_apprenants = 0;
        $revenu_total = 0;
        $max_apprenants = 0;
        $filiere_populaire = null;
        
        foreach ($filieres as $fil) {
            $nb = $fil['nb_apprenants'] ?? 0;
            $total_apprenants += $nb;
            $revenu_total += ($fil['frais_mensuel'] ?? 0) * 12;
            
            if ($nb > $max_apprenants) {
                $max_apprenants = $nb;
                $filiere_populaire = $fil;
            }
        }
        ?>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-book stat-icon"></i>
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total Filières</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +15%</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-number"><?php echo $total_apprenants; ?></div>
                <div class="stat-label">Apprenants Total</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +8%</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign stat-icon"></i>
                <div class="stat-number">$<?php echo number_format($revenu_total, 0, ',', ' '); ?></div>
                <div class="stat-label">Revenu Annuel Estimé</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +12%</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-star stat-icon"></i>
                <div class="stat-number"><?php echo $max_apprenants; ?></div>
                <div class="stat-label">Plus Populaire</div>
                <span class="stat-change neutral"><i class="fas fa-minus"></i> <?php echo $filiere_populaire ? htmlspecialchars($filiere_populaire['nom']) : 'N/A'; ?></span>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Rechercher une filière par nom, description, niveau ou type..." onkeyup="filterCards()">
        </div>

        <!-- Filières Grid -->
        <div class="filieres-grid" id="filieresGrid">
            <?php if (empty($filieres)): ?>
                <div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h3>Aucune filière</h3>
                    <p>Commencez par ajouter votre première filière</p>
                </div>
            <?php else: ?>
                <?php foreach ($filieres as $index => $fil): 
                    $couleur = $fil['couleur'] ?? ['#667eea', '#f093fb', '#4facfe', '#fa709a', '#48bb78', '#ed8936', '#fc8181', '#764ba2'][$index % 8];
                ?>
                <div class="filiere-card" style="animation-delay: <?php echo $index * 0.06; ?>s" data-search="<?php echo strtolower($fil['nom'] . ' ' . ($fil['description'] ?? '') . ' ' . ($fil['niveau'] ?? '') . ' ' . ($fil['type'] ?? '')); ?>">
                    <div class="card-header" style="background: linear-gradient(135deg, <?php echo $couleur; ?>, <?php echo $couleur; ?>cc);">
                        <i class="fas fa-graduation-cap card-icon"></i>
                        <div class="filiere-nom"><?php echo htmlspecialchars($fil['nom']); ?></div>
                        <div class="filiere-meta">
                            <span class="badge-niveau">
                                <i class="fas fa-signal"></i>
                                <?php echo htmlspecialchars($fil['niveau'] ?? 'Débutant'); ?>
                            </span>
                            <span class="badge-type">
                                <i class="fas fa-<?php echo ($fil['type'] ?? 'Présentiel') === 'En ligne' ? 'wifi' : 'building'; ?>"></i>
                                <?php echo htmlspecialchars($fil['type'] ?? 'Présentiel'); ?>
                            </span>
                            <span class="frais-badge">
                                <i class="fas fa-dollar-sign me-1"></i>
                                <?php echo number_format($fil['frais_mensuel'] ?? 0, 0, ',', ' '); ?>/mois
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="description">
                            <?php echo htmlspecialchars($fil['description'] ?? 'Description non disponible'); ?>
                        </p>
                        <div class="details">
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <span class="label">Durée</span>
                                    <span class="value"><?php echo htmlspecialchars($fil['duree'] ?? 'Non spécifiée'); ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <div>
                                    <span class="label">Apprenants</span>
                                    <span class="value"><?php echo $fil['nb_apprenants'] ?? rand(15, 50); ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <span class="label">Créée le</span>
                                    <span class="value"><?php echo isset($fil['date_creation']) ? date('d/m/Y', strtotime($fil['date_creation'])) : 'N/A'; ?></span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-id-card"></i>
                                <div>
                                    <span class="label">Code</span>
                                    <span class="value">#<?php echo str_pad($fil['id_filiere'], 4, '0', STR_PAD_LEFT); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="?edit=<?php echo $fil['id_filiere']; ?>" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="?delete=<?php echo $fil['id_filiere']; ?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette filière ? Cette action est irréversible.')">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                        <a href="#" class="btn-action btn-view" onclick="event.preventDefault(); alert('📚 Détails de la filière #<?php echo $fil['id_filiere']; ?>\n\n📖 Nom: <?php echo htmlspecialchars($fil['nom']); ?>\n⏱️ Durée: <?php echo htmlspecialchars($fil['duree'] ?? 'Non spécifiée'); ?>\n💰 Frais mensuel: $<?php echo number_format($fil['frais_mensuel'] ?? 0, 0, ',', ' '); ?>\n📊 Niveau: <?php echo htmlspecialchars($fil['niveau'] ?? 'Débutant'); ?>\n🏷️ Type: <?php echo htmlspecialchars($fil['type'] ?? 'Présentiel'); ?>\n👥 Apprenants: <?php echo $fil['nb_apprenants'] ?? rand(15, 50); ?>\n📝 Description: <?php echo htmlspecialchars($fil['description'] ?? 'Non disponible'); ?>')">
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
                        <?php echo $edit_mode ? 'Modifier la Filière' : 'Ajouter une Filière'; ?>
                    </h3>
                    <div class="modal-subtitle">
                        <?php echo $edit_mode ? 'Mettez à jour les informations de la filière' : 'Remplissez les informations pour ajouter une nouvelle filière'; ?>
                    </div>
                    <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                
                <form method="POST" onsubmit="return validateForm()">
                    <?php if ($edit_mode && $edit_data): ?>
                        <input type="hidden" name="id_filiere" value="<?php echo $edit_data['id_filiere']; ?>">
                    <?php endif; ?>

                    <div class="modal-body-custom">
                        <div class="form-group">
                            <label><i class="fas fa-book me-2" style="color: var(--primary);"></i>Nom de la Filière <span class="required">*</span></label>
                            <input type="text" name="nom" class="form-control" 
                                   placeholder="Ex: Développement Web Full Stack"
                                   value="<?php echo $edit_mode && $edit_data ? htmlspecialchars($edit_data['nom']) : ''; ?>"
                                   required>
                            <div class="form-hint">Le nom doit être unique et descriptif</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-align-left me-2" style="color: var(--info);"></i>Description</label>
                            <textarea name="description" class="form-control" 
                                      placeholder="Décrivez la filière en quelques lignes"><?php echo $edit_mode && $edit_data ? htmlspecialchars($edit_data['description'] ?? '') : ''; ?></textarea>
                            <div class="form-hint">Présentez les objectifs et le contenu de la formation</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-clock me-2" style="color: var(--warning);"></i>Durée <span class="required">*</span></label>
                            <input type="text" name="duree" class="form-control" 
                                   placeholder="Ex: 12 mois, 6 mois, 2 ans"
                                   value="<?php echo $edit_mode && $edit_data ? htmlspecialchars($edit_data['duree'] ?? '') : ''; ?>"
                                   required>
                            <div class="form-hint">Indiquez la durée totale de la formation</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-dollar-sign me-2" style="color: var(--success);"></i>Frais Mensuel ($) <span class="required">*</span></label>
                            <input type="number" name="frais_mensuel" class="form-control" 
                                   placeholder="Ex: 150"
                                   value="<?php echo $edit_mode && $edit_data ? $edit_data['frais_mensuel'] : ''; ?>"
                                   required min="0" step="1">
                            <div class="form-hint">Entrez le montant en dollars ($)</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-signal me-2" style="color: var(--secondary);"></i>Niveau</label>
                            <select name="niveau" class="form-control">
                                <option value="Débutant" <?php echo ($edit_mode && $edit_data && ($edit_data['niveau'] ?? 'Débutant') === 'Débutant') ? 'selected' : ''; ?>>Débutant</option>
                                <option value="Intermédiaire" <?php echo ($edit_mode && $edit_data && ($edit_data['niveau'] ?? '') === 'Intermédiaire') ? 'selected' : ''; ?>>Intermédiaire</option>
                                <option value="Avancé" <?php echo ($edit_mode && $edit_data && ($edit_data['niveau'] ?? '') === 'Avancé') ? 'selected' : ''; ?>>Avancé</option>
                                <option value="Expert" <?php echo ($edit_mode && $edit_data && ($edit_data['niveau'] ?? '') === 'Expert') ? 'selected' : ''; ?>>Expert</option>
                            </select>
                            <div class="form-hint">Sélectionnez le niveau de difficulté</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-<?php echo ($edit_mode && $edit_data && ($edit_data['type'] ?? 'Présentiel') === 'En ligne') ? 'wifi' : 'building'; ?> me-2" style="color: var(--primary);"></i>Type</label>
                            <select name="type" class="form-control">
                                <option value="Présentiel" <?php echo ($edit_mode && $edit_data && ($edit_data['type'] ?? 'Présentiel') === 'Présentiel') ? 'selected' : ''; ?>>Présentiel</option>
                                <option value="En ligne" <?php echo ($edit_mode && $edit_data && ($edit_data['type'] ?? '') === 'En ligne') ? 'selected' : ''; ?>>En ligne</option>
                                <option value="Mixte" <?php echo ($edit_mode && $edit_data && ($edit_data['type'] ?? '') === 'Mixte') ? 'selected' : ''; ?>>Mixte</option>
                            </select>
                            <div class="form-hint">Le format de la formation</div>
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
        const cards = document.querySelectorAll('.filiere-card');

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
        const duree = document.querySelector('input[name="duree"]');
        const frais = document.querySelector('input[name="frais_mensuel"]');
        
        if (!nom.value.trim()) {
            alert('⚠️ Le nom de la filière est obligatoire');
            nom.focus();
            return false;
        }
        
        if (!duree.value.trim()) {
            alert('⚠️ La durée est obligatoire');
            duree.focus();
            return false;
        }
        
        if (!frais.value || parseFloat(frais.value) <= 0) {
            alert('⚠️ Veuillez entrer un montant valide pour les frais mensuels');
            frais.focus();
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
    document.querySelectorAll('.filiere-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.01)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
</script>
<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Apprenant.php';

$page_title = 'Gestion des Apprenants';

$database = new Database();
$db = $database->connect();
$apprenant = new Apprenant($db);

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($apprenant->delete($id)) {
        $_SESSION['success'] = 'Apprenant supprimé avec succès';
        $_SESSION['success_type'] = 'success';
    }
    header('Location: apprenants.php');
    exit;
}

// Handle create/update
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_data = $apprenant->getById($_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $adresse = $_POST['adresse'] ?? '';

    if (isset($_POST['id_apprenant']) && $_POST['id_apprenant']) {
        // Update
        if ($apprenant->update($_POST['id_apprenant'], $nom, $prenom, $telephone, $adresse)) {
            $_SESSION['success'] = 'Apprenant mis à jour avec succès';
            $_SESSION['success_type'] = 'success';
        } else {
            $_SESSION['success'] = 'Erreur lors de la mise à jour';
            $_SESSION['success_type'] = 'error';
        }
    } else {
        // Create
        if ($apprenant->create($nom, $prenom, $telephone, $adresse)) {
            $_SESSION['success'] = 'Apprenant ajouté avec succès';
            $_SESSION['success_type'] = 'success';
        } else {
            $_SESSION['success'] = 'Erreur lors de l\'ajout';
            $_SESSION['success_type'] = 'error';
        }
    }
    header('Location: apprenants.php');
    exit;
}

$apprenants = $apprenant->getAll();

include __DIR__ . '/../includes/header.php';
?>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
        --shadow-md: 0 4px 20px rgba(0,0,0,0.12);
        --shadow-lg: 0 10px 40px rgba(0,0,0,0.15);
        --shadow-xl: 0 20px 60px rgba(0,0,0,0.2);
    }

    /* Animation de fond */
    .page-wrapper {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .page-wrapper::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at 70% 20%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 30% 80%, rgba(118, 75, 162, 0.1) 0%, transparent 50%);
        animation: rotateBackground 60s linear infinite;
        pointer-events: none;
    }

    @keyframes rotateBackground {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .content-wrapper {
        position: relative;
        z-index: 1;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header avec animation */
    .page-header {
        background: var(--primary-gradient);
        border-radius: 20px;
        padding: 30px 40px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
        animation: slideDown 0.6s ease-out;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
        animation: pulseGlow 4s ease-in-out infinite;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        position: relative;
        z-index: 1;
    }

    .page-header .subtitle {
        color: rgba(255,255,255,0.9);
        font-size: 14px;
        margin-top: 5px;
        position: relative;
        z-index: 1;
    }

    .header-actions {
        display: flex;
        gap: 15px;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .btn-add {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        color: white;
        padding: 12px 25px;
        border-radius: 50px;
        border: 2px solid rgba(255,255,255,0.3);
        transition: all 0.3s ease;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        animation: bounceIn 0.8s ease-out;
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

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 20px 25px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    .stat-card .stat-icon {
        position: absolute;
        right: 15px;
        bottom: 15px;
        font-size: 45px;
        opacity: 0.1;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon {
        opacity: 0.15;
        transform: scale(1.1) rotate(-5deg);
    }

    .stat-card .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 5px;
    }

    .stat-card .stat-label {
        font-size: 13px;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .stat-change {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 20px;
        margin-top: 10px;
        font-weight: 600;
    }

    .stat-change.up {
        color: #48bb78;
        background: rgba(72, 187, 120, 0.1);
    }

    .stat-change.down {
        color: #fc8181;
        background: rgba(252, 129, 129, 0.1);
    }

    /* Search Bar améliorée */
    .search-wrapper {
        position: relative;
        margin-bottom: 25px;
    }

    .search-wrapper input {
        width: 100%;
        padding: 15px 20px 15px 50px;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: white;
        box-shadow: var(--shadow-sm);
    }

    .search-wrapper input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), var(--shadow-md);
    }

    .search-wrapper .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 18px;
        transition: all 0.3s ease;
    }

    .search-wrapper:focus-within .search-icon {
        color: #667eea;
    }

    /* Table améliorée */
    .table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
    }

    .table-container:hover {
        box-shadow: var(--shadow-lg);
    }

    .table-header {
        background: var(--primary-gradient);
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-header h3 {
        color: white;
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .table-header .badge-count {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        color: white;
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 500;
    }

    .table-responsive {
        overflow-x: auto;
        padding: 0 0 5px 0;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    thead {
        background: #f7fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    thead th {
        padding: 15px 20px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #4a5568;
        position: sticky;
        top: 0;
        background: #f7fafc;
        z-index: 10;
    }

    tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #edf2f7;
        animation: fadeInUp 0.5s ease-out;
        animation-fill-mode: both;
    }

    tbody tr:nth-child(even) {
        background: #fafbfc;
    }

    tbody tr:hover {
        background: #f0f4ff;
        transform: scale(1.002);
        box-shadow: 0 2px 10px rgba(102, 126, 234, 0.1);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    tbody td {
        padding: 15px 20px;
        color: #2d3748;
        font-size: 14px;
        vertical-align: middle;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 16px;
        margin-right: 10px;
    }

    .user-info {
        display: flex;
        align-items: center;
    }

    .user-name {
        font-weight: 600;
        color: #2d3748;
    }

    .user-detail {
        font-size: 12px;
        color: #718096;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-badge.active {
        background: rgba(72, 187, 120, 0.15);
        color: #48bb78;
    }

    .status-badge.inactive {
        background: rgba(252, 129, 129, 0.15);
        color: #fc8181;
    }

    .status-badge.pending {
        background: rgba(237, 137, 54, 0.15);
        color: #ed8936;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
    }

    .action-btn.edit {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .action-btn.edit:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .action-btn.delete {
        background: rgba(252, 129, 129, 0.1);
        color: #fc8181;
    }

    .action-btn.delete:hover {
        background: #fc8181;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(252, 129, 129, 0.3);
    }

    .action-btn.view {
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    .action-btn.view:hover {
        background: #48bb78;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(72, 187, 120, 0.3);
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #a0aec0;
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

    /* Modal amélioré */
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
    }

    .modal-overlay.active {
        display: flex;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-content {
        background: white;
        border-radius: 25px;
        max-width: 550px;
        width: 95%;
        max-height: 90vh;
        overflow-y: auto;
        animation: modalSlideIn 0.4s ease-out;
        box-shadow: var(--shadow-xl);
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(50px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-header-custom {
        background: var(--primary-gradient);
        padding: 25px 30px;
        border-radius: 25px 25px 0 0;
        color: white;
        position: relative;
    }

    .modal-header-custom h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
    }

    .modal-header-custom .modal-subtitle {
        font-size: 14px;
        opacity: 0.9;
        margin-top: 5px;
    }

    .modal-close {
        position: absolute;
        right: 20px;
        top: 20px;
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
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
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-group label .required {
        color: #fc8181;
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
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .form-control::placeholder {
        color: #a0aec0;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .form-hint {
        font-size: 12px;
        color: #a0aec0;
        margin-top: 5px;
    }

    .modal-footer-custom {
        padding: 20px 30px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-submit {
        background: var(--primary-gradient);
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
        background: #f7fafc;
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

    /* Notification toast */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        animation: slideInRight 0.5s ease;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast {
        padding: 15px 25px;
        border-radius: 15px;
        color: white;
        font-weight: 500;
        box-shadow: var(--shadow-lg);
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
        min-width: 300px;
        animation: slideInRight 0.5s ease;
    }

    .toast-success {
        background: linear-gradient(135deg, #48bb78, #38a169);
    }

    .toast-error {
        background: linear-gradient(135deg, #fc8181, #e53e3e);
    }

    .toast i {
        font-size: 20px;
    }

    .toast .toast-close {
        margin-left: auto;
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.3s;
    }

    .toast .toast-close:hover {
        opacity: 1;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 20px;
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .page-header h1 {
            font-size: 22px;
        }

        .header-actions {
            width: 100%;
            justify-content: center;
        }

        .btn-add {
            width: 100%;
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .stat-card .stat-number {
            font-size: 22px;
        }

        .table-header {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }

        .action-buttons {
            flex-direction: column;
            gap: 5px;
        }

        .modal-content {
            margin: 10px;
            max-height: 95vh;
        }

        .modal-footer-custom {
            flex-direction: column;
        }

        .btn-submit, .btn-cancel {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .user-info {
            flex-direction: column;
            align-items: flex-start;
        }

        .user-avatar {
            margin-bottom: 5px;
        }
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
                if (toast) toast.style.display = 'none';
            }, 5000);
        </script>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <div>
                    <h1>
                        <i class="fas fa-users me-3"></i>
                        Gestion des Apprenants
                    </h1>
                    <div class="subtitle">
                        <i class="fas fa-database me-2"></i>
                        <?php echo count($apprenants); ?> apprenants enregistrés
                        <span style="margin: 0 10px;">•</span>
                        <i class="fas fa-calendar-alt me-2"></i>
                        <?php echo date('d/m/Y H:i'); ?>
                    </div>
                </div>
                <div class="header-actions">
                    <button onclick="openModal()" class="btn-add">
                        <i class="fas fa-plus-circle"></i>
                        <span>Nouvel Apprenant</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <?php
        $total = count($apprenants);
        $actifs = count(array_filter($apprenants, function($a) { return ($a['statut'] ?? 'actif') === 'actif'; }));
        $inactifs = $total - $actifs;
        ?>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total Apprenants</div>
                <span class="stat-change up">
                    <i class="fas fa-arrow-up"></i> 12%
                </span>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-check stat-icon"></i>
                <div class="stat-number"><?php echo $actifs; ?></div>
                <div class="stat-label">Apprenants Actifs</div>
                <span class="stat-change up">
                    <i class="fas fa-arrow-up"></i> 8%
                </span>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-times stat-icon"></i>
                <div class="stat-number"><?php echo $inactifs; ?></div>
                <div class="stat-label">Apprenants Inactifs</div>
                <span class="stat-change down">
                    <i class="fas fa-arrow-down"></i> 3%
                </span>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-plus stat-icon"></i>
                <div class="stat-number"><?php echo rand(5, 20); ?></div>
                <div class="stat-label">Nouveaux ce mois</div>
                <span class="stat-change up">
                    <i class="fas fa-arrow-up"></i> 25%
                </span>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Rechercher par nom, prénom, téléphone ou adresse..." 
                   onkeyup="filterTable()">
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list me-2"></i>
                    Liste des Apprenants
                </h3>
                <span class="badge-count">
                    <i class="fas fa-user me-1"></i>
                    <?php echo $total; ?> enregistrements
                </span>
            </div>
            <div class="table-responsive">
                <table id="apprenantTable">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Apprenant</th>
                            <th style="width: 20%;">Téléphone</th>
                            <th style="width: 25%;">Adresse</th>
                            <th style="width: 15%;">Statut</th>
                            <th style="width: 10%; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (empty($apprenants)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-users-slash"></i>
                                        <h3>Aucun apprenant</h3>
                                        <p>Commencez par ajouter votre premier apprenant</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($apprenants as $index => $app): ?>
                            <tr style="animation-delay: <?php echo $index * 0.05; ?>s">
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($app['prenom'] ?? 'A', 0, 1) . substr($app['nom'] ?? '', 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="user-name">
                                                <?php echo htmlspecialchars($app['prenom'] . ' ' . $app['nom']); ?>
                                            </div>
                                            <div class="user-detail">
                                                ID: #<?php echo str_pad($app['id_apprenant'], 4, '0', STR_PAD_LEFT); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-phone me-2" style="color: #667eea;"></i>
                                    <?php echo htmlspecialchars($app['telephone'] ?? 'Non renseigné'); ?>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt me-2" style="color: #f093fb;"></i>
                                    <?php echo htmlspecialchars($app['adresse'] ?? 'Non renseignée'); ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo ($app['statut'] ?? 'actif') === 'actif' ? 'active' : 'inactive'; ?>">
                                        <i class="fas fa-<?php echo ($app['statut'] ?? 'actif') === 'actif' ? 'circle' : 'times-circle'; ?> me-1" style="font-size: 8px;"></i>
                                        <?php echo ucfirst($app['statut'] ?? 'actif'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?edit=<?php echo $app['id_apprenant']; ?>" class="action-btn edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $app['id_apprenant']; ?>" class="action-btn delete" title="Supprimer" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet apprenant ? Cette action est irréversible.')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="#" class="action-btn view" title="Voir détails" onclick="alert('Détails de l\'apprenant #<?php echo $app['id_apprenant']; ?>')">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div id="addModal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header-custom">
                    <h3>
                        <i class="fas fa-<?php echo $edit_mode ? 'edit' : 'user-plus'; ?> me-2"></i>
                        <?php echo $edit_mode ? 'Modifier l\'Apprenant' : 'Ajouter un Apprenant'; ?>
                    </h3>
                    <div class="modal-subtitle">
                        <?php echo $edit_mode ? 'Mettez à jour les informations de l\'apprenant' : 'Remplissez les informations pour ajouter un nouvel apprenant'; ?>
                    </div>
                    <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                
                <form method="POST" onsubmit="return validateForm()">
                    <?php if ($edit_mode): ?>
                        <input type="hidden" name="id_apprenant" value="<?php echo $edit_data['id_apprenant']; ?>">
                    <?php endif; ?>

                    <div class="modal-body-custom">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user me-2" style="color: #667eea;"></i>
                                Nom <span class="required">*</span>
                            </label>
                            <input type="text" name="nom" class="form-control" 
                                   placeholder="Entrez le nom de famille"
                                   value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nom']) : ''; ?>"
                                   required>
                            <div class="form-hint">Ex: DUPONT</div>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-user me-2" style="color: #667eea;"></i>
                                Prénom <span class="required">*</span>
                            </label>
                            <input type="text" name="prenom" class="form-control" 
                                   placeholder="Entrez le prénom"
                                   value="<?php echo $edit_mode ? htmlspecialchars($edit_data['prenom']) : ''; ?>"
                                   required>
                            <div class="form-hint">Ex: Jean</div>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-phone me-2" style="color: #48bb78;"></i>
                                Téléphone
                            </label>
                            <input type="tel" name="telephone" class="form-control" 
                                   placeholder="Entrez le numéro de téléphone"
                                   value="<?php echo $edit_mode ? htmlspecialchars($edit_data['telephone'] ?? '') : ''; ?>">
                            <div class="form-hint">Ex: +225 07 08 09 10 11</div>
                        </div>

                        <div class="form-group">
                            <label>
                                <i class="fas fa-map-marker-alt me-2" style="color: #f093fb;"></i>
                                Adresse
                            </label>
                            <textarea name="adresse" class="form-control" 
                                      placeholder="Entrez l'adresse complète"><?php echo $edit_mode ? htmlspecialchars($edit_data['adresse'] ?? '') : ''; ?></textarea>
                            <div class="form-hint">Ex: 123 Rue de la République, Abidjan</div>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn-cancel" onclick="closeModal()">
                            <i class="fas fa-times me-2"></i>
                            Annuler
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
    // Filter Table
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('apprenantTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            if (row.querySelector('.empty-state')) continue;
            
            const cells = row.getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell) {
                    const text = cell.textContent.toLowerCase();
                    if (text.indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    }

    // Modal functions
    function openModal() {
        document.getElementById('addModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('addModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close modal on overlay click
    document.getElementById('addModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('addModal').classList.contains('active')) {
            closeModal();
        }
    });

    // Form validation
    function validateForm() {
        const nom = document.querySelector('input[name="nom"]');
        const prenom = document.querySelector('input[name="prenom"]');
        
        if (!nom.value.trim() || !prenom.value.trim()) {
            alert('Le nom et le prénom sont obligatoires');
            nom.focus();
            return false;
        }
        
        return true;
    }

    // Auto-hide toast after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('toastContainer');
        if (toast) {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 300);
            }, 5000);
        }
    });

    // Row hover animation
    document.querySelectorAll('#apprenantTable tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.005)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Smooth scroll to top when opening modal
    const originalOpenModal = openModal;
    openModal = function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        originalOpenModal();
    };

    // Open modal if edit mode
    <?php if ($edit_mode): ?>
    document.addEventListener('DOMContentLoaded', function() {
        openModal();
    });
    <?php endif; ?>
</script>
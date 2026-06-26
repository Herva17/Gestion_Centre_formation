<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Inscription.php';
require_once __DIR__ . '/../classes/Apprenant.php';
require_once __DIR__ . '/../classes/Filiere.php';

$page_title = 'Gestion des Inscriptions';

$database = new Database();
$db = $database->connect();
$inscription = new Inscription($db);
$apprenant = new Apprenant($db);
$filiere = new Filiere($db);

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_apprenant = (int)$_POST['id_apprenant'] ?? 0;
    $id_filiere = (int)$_POST['id_filiere'] ?? 0;
    $date_inscription = $_POST['date_inscription'] ?? date('Y-m-d');
    $frais_inscription = floatval($_POST['frais_inscription'] ?? 0);
    $statut_paiement = $_POST['statut_paiement'] ?? 'en_attente';
    
    // Validation
    $errors = [];
    if ($id_apprenant <= 0) $errors[] = "Veuillez sélectionner un apprenant";
    if ($id_filiere <= 0) $errors[] = "Veuillez sélectionner une filière";
    if ($frais_inscription <= 0) $errors[] = "Le frais d'inscription doit être supérieur à 0";
    
    if (empty($errors)) {
        if ($inscription->create($id_apprenant, $id_filiere, $date_inscription, $frais_inscription)) {
            $_SESSION['success'] = 'Inscription ajoutée avec succès';
            $_SESSION['success_type'] = 'success';
        } else {
            $_SESSION['success'] = 'Erreur lors de l\'ajout de l\'inscription';
            $_SESSION['success_type'] = 'error';
        }
    } else {
        $_SESSION['success'] = implode(', ', $errors);
        $_SESSION['success_type'] = 'error';
    }
    header('Location: inscriptions.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        // Delete from paiement table first (foreign key constraint)
        $query = "DELETE FROM paiement WHERE id_inscription = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Then delete inscription
        $query = "DELETE FROM inscription WHERE id_inscription = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Inscription supprimée avec succès';
            $_SESSION['success_type'] = 'success';
        }
    }
    header('Location: inscriptions.php');
    exit;
}

$inscriptions = $inscription->getAll();
$apprenants = $apprenant->getAll();
$filieres = $filiere->getAll();

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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .table-header h3 {
        color: white;
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-header .badge-count {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        color: white;
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 500;
    }

    .table-responsive {
        overflow-x: auto;
        padding: 0;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    thead {
        background: var(--light-gray);
        border-bottom: 2px solid #e2e8f0;
    }

    thead th {
        padding: 14px 20px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #4a5568;
        position: sticky;
        top: 0;
        background: var(--light-gray);
        z-index: 10;
        white-space: nowrap;
    }

    tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #edf2f7;
        animation: fadeInUp 0.5s ease-out;
        animation-fill-mode: both;
    }

    tbody tr:nth-child(even) { background: #fafbfc; }
    tbody tr:hover { background: #f0f4ff; transform: scale(1.002); box-shadow: 0 2px 10px rgba(102, 126, 234, 0.08); }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    tbody td {
        padding: 14px 20px;
        color: var(--dark);
        font-size: 14px;
        vertical-align: middle;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 15px;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .user-info {
        display: flex;
        align-items: center;
    }

    .user-name {
        font-weight: 600;
        color: var(--dark);
    }
    .action-btn.receipt {
    background: rgba(237, 137, 54, 0.1);
    color: #ed8936;
}

.action-btn.receipt:hover {
    background: #ed8936;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(237, 137, 54, 0.3);
}

    .user-detail {
        font-size: 12px;
        color: var(--gray);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .status-badge {
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .status-badge.paye {
        background: rgba(72, 187, 120, 0.15);
        color: #48bb78;
    }

    .status-badge.en_attente {
        background: rgba(237, 137, 54, 0.15);
        color: #ed8936;
    }

    .status-badge.partiel {
        background: rgba(102, 126, 234, 0.15);
        color: #667eea;
    }

    .status-badge .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-badge.paye .dot { background: #48bb78; }
    .status-badge.en_attente .dot { background: #ed8936; }
    .status-badge.partiel .dot { background: #667eea; }

    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 13px;
        text-decoration: none;
    }

    .action-btn.view {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .action-btn.view:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .action-btn.delete {
        background: rgba(252, 129, 129, 0.1);
        color: #fc8181;
    }

    .action-btn.delete:hover {
        background: #fc8181;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(252, 129, 129, 0.3);
    }

    .action-btn.payment {
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    .action-btn.payment:hover {
        background: #48bb78;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
    }

    .frais-amount {
        font-weight: 700;
        font-size: 15px;
    }

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

    .empty-state p {
        color: #a0aec0;
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

        .table-header {
            flex-direction: column;
            text-align: center;
        }

        .action-buttons { gap: 4px; }
        .action-btn { width: 30px; height: 30px; font-size: 12px; }

        .modal-content { margin: 10px; }
        .modal-footer-custom { flex-direction: column; }
        .btn-submit, .btn-cancel { width: 100%; justify-content: center; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .page-header { padding: 16px; }
        .table-header { padding: 16px; }
        .modal-body-custom { padding: 20px; }
        .modal-header-custom { padding: 20px; }
    }

    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #5a67d8;
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
                    <h1><i class="fas fa-clipboard-list me-3"></i>Gestion des Inscriptions</h1>
                    <div class="subtitle">
                        <i class="fas fa-database me-2"></i>
                        <?php echo count($inscriptions); ?> inscriptions enregistrées
                        <span style="margin: 0 10px;">•</span>
                        <i class="fas fa-calendar-alt me-2"></i>
                        <?php echo date('d/m/Y H:i'); ?>
                    </div>
                </div>
                <button onclick="openModal()" class="btn-add">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouvelle Inscription</span>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <?php
        $total = count($inscriptions);
        $total_frais = array_sum(array_column($inscriptions, 'frais_inscription'));
        $statuts = array_count_values(array_column($inscriptions, 'statut_paiement'));
        $paye = $statuts['payé'] ?? 0;
        $en_attente = $statuts['en_attente'] ?? 0;
        $partiel = $statuts['partiel'] ?? 0;
        ?>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-clipboard-list stat-icon"></i>
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total Inscriptions</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +22%</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign stat-icon"></i>
                <div class="stat-number">$<?php echo number_format($total_frais, 0, ',', ' '); ?></div>
                <div class="stat-label">Total Frais</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +18%</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle stat-icon"></i>
                <div class="stat-number"><?php echo $paye; ?></div>
                <div class="stat-label">Payé</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +12%</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock stat-icon"></i>
                <div class="stat-number"><?php echo $en_attente; ?></div>
                <div class="stat-label">En Attente</div>
                <span class="stat-change down"><i class="fas fa-arrow-down"></i> -5%</span>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Rechercher par nom d'apprenant, filière, statut..." onkeyup="filterTable()">
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-header">
                <h3><i class="fas fa-list me-2"></i>Liste des Inscriptions</h3>
                <span class="badge-count">
                    <i class="fas fa-user me-1"></i>
                    <?php echo $total; ?> inscriptions
                </span>
            </div>
            <div class="table-responsive">
                <table id="inscriptionTable">
                    <thead>
                        <tr>
                            <th style="min-width: 180px;">Apprenant</th>
                            <th style="min-width: 160px;">Filière</th>
                            <th style="min-width: 130px;">Date</th>
                            <th style="min-width: 110px;">Frais</th>
                            <th style="min-width: 120px;">Statut</th>
                            <th style="min-width: 130px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (empty($inscriptions)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-clipboard-list"></i>
                                        <h3>Aucune inscription</h3>
                                        <p>Commencez par ajouter une nouvelle inscription</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inscriptions as $index => $insc): ?>
                            <tr style="animation-delay: <?php echo $index * 0.04; ?>s">
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($insc['prenom'] ?? 'A', 0, 1) . substr($insc['nom'] ?? '', 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="user-name"><?php echo htmlspecialchars($insc['prenom'] . ' ' . $insc['nom']); ?></div>
                                            <div class="user-detail">
                                                <i class="fas fa-id-card"></i>
                                                ID: #<?php echo str_pad($insc['id_apprenant'], 4, '0', STR_PAD_LEFT); ?>
                                                <?php if (isset($insc['telephone'])): ?>
                                                    <span style="margin: 0 4px;">•</span>
                                                    <i class="fas fa-phone"></i>
                                                    <?php echo htmlspecialchars($insc['telephone']); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-graduation-cap" style="color: var(--secondary);"></i>
                                        <span style="font-weight: 500;"><?php echo htmlspecialchars($insc['filiere_nom'] ?? '-'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px; color: var(--gray); font-size: 13px;">
                                        <i class="fas fa-calendar-alt" style="color: var(--primary);"></i>
                                        <?php echo date('d/m/Y', strtotime($insc['date_inscription'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="frais-amount" style="color: var(--success);">
                                        $<?php echo number_format($insc['frais_inscription'] ?? 0, 0, ',', ' '); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo ($insc['statut_paiement'] ?? 'en_attente'); ?>">
                                        <span class="dot"></span>
                                        <?php 
                                            $labels = [
                                                'payé' => 'Payé',
                                                'en_attente' => 'En Attente',
                                                'partiel' => 'Partiel'
                                            ];
                                            echo $labels[$insc['statut_paiement'] ?? 'en_attente'] ?? 'En Attente';
                                        ?>
                                    </span>
                                </td>
                                <td>
    <div class="action-buttons">
        <a href="inscription-detail.php?id=<?php echo $insc['id_inscription']; ?>" class="action-btn view" title="Voir détails">
            <i class="fas fa-eye"></i>
        </a>
        <a href="#" class="action-btn payment" title="Gérer paiement" onclick="event.preventDefault(); alert('💳 Gestion du paiement\n\n📋 Inscription #<?php echo $insc['id_inscription']; ?>\n👤 <?php echo htmlspecialchars($insc['prenom'] . ' ' . $insc['nom']); ?>\n📚 <?php echo htmlspecialchars($insc['filiere_nom'] ?? '-'); ?>\n💰 Montant: $<?php echo number_format($insc['frais_inscription'] ?? 0, 0, ',', ' '); ?>\n📊 Statut: <?php echo ucfirst($insc['statut_paiement'] ?? 'en_attente'); ?>')">
            <i class="fas fa-credit-card"></i>
        </a>
        <a href="?delete=<?php echo $insc['id_inscription']; ?>" class="action-btn delete" title="Supprimer" 
           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription ? Cette action est irréversible et supprimera également les paiements associés.')">
            <i class="fas fa-trash"></i>
        </a>
        <a href="reçu.php?id=<?php echo $insc['id_inscription']; ?>" class="action-btn receipt" title="Voir le reçu">
            <i class="fas fa-receipt"></i>
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
                        <i class="fas fa-plus-circle me-2"></i>
                        Nouvelle Inscription
                    </h3>
                    <div class="modal-subtitle">
                        Remplissez les informations pour inscrire un apprenant
                    </div>
                    <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                
                <form method="POST" onsubmit="return validateForm()">
                    <div class="modal-body-custom">
                        <div class="form-group">
                            <label><i class="fas fa-user me-2" style="color: var(--primary);"></i>Apprenant <span class="required">*</span></label>
                            <select name="id_apprenant" class="form-control" required>
                                <option value="">-- Sélectionner un apprenant --</option>
                                <?php foreach ($apprenants as $app): ?>
                                <option value="<?php echo $app['id_apprenant']; ?>">
                                    <?php echo htmlspecialchars($app['prenom'] . ' ' . $app['nom']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Sélectionnez l'apprenant à inscrire</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-graduation-cap me-2" style="color: var(--secondary);"></i>Filière <span class="required">*</span></label>
                            <select name="id_filiere" class="form-control" required>
                                <option value="">-- Sélectionner une filière --</option>
                                <?php foreach ($filieres as $fil): ?>
                                <option value="<?php echo $fil['id_filiere']; ?>">
                                    <?php echo htmlspecialchars($fil['nom']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Choisissez la filière pour cette inscription</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt me-2" style="color: var(--info);"></i>Date d'Inscription</label>
                            <input type="date" name="date_inscription" class="form-control"
                                   value="<?php echo date('Y-m-d'); ?>">
                            <div class="form-hint">Date de l'inscription</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-dollar-sign me-2" style="color: var(--success);"></i>Frais d'Inscription ($) <span class="required">*</span></label>
                            <input type="number" name="frais_inscription" class="form-control" 
                                   placeholder="Ex: 150"
                                   required min="0" step="1">
                            <div class="form-hint">Entrez le montant en dollars ($)</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-circle me-2" style="color: var(--warning);"></i>Statut de Paiement</label>
                            <select name="statut_paiement" class="form-control">
                                <option value="en_attente">En Attente</option>
                                <option value="payé">Payé</option>
                                <option value="partiel">Partiel</option>
                            </select>
                            <div class="form-hint">Définit le statut initial du paiement</div>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn-cancel" onclick="closeModal()">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-plus me-2"></i>Ajouter l'inscription
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
        const table = document.getElementById('inscriptionTable');
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
        const apprenant = document.querySelector('select[name="id_apprenant"]');
        const filiere = document.querySelector('select[name="id_filiere"]');
        const frais = document.querySelector('input[name="frais_inscription"]');
        
        if (!apprenant.value) {
            alert('⚠️ Veuillez sélectionner un apprenant');
            apprenant.focus();
            return false;
        }
        
        if (!filiere.value) {
            alert('⚠️ Veuillez sélectionner une filière');
            filiere.focus();
            return false;
        }
        
        if (!frais.value || parseFloat(frais.value) <= 0) {
            alert('⚠️ Veuillez entrer un montant valide pour les frais d\'inscription');
            frais.focus();
            return false;
        }
        
        return true;
    }

    // Row hover animation
    document.querySelectorAll('#inscriptionTable tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.005)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Toast auto-hide
    document.addEventListener('DOMContentLoaded', function() {
        const toast = document.getElementById('toastContainer');
        if (toast) {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.style.display = 'none', 300);
            }, 5000);
        }
    });
</script>
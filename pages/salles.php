<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Salle.php';

$page_title = 'Gestion des Salles';
$page_icon = 'door-open';

$database = new Database();
$db = $database->connect();
$salle = new Salle($db);

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $query = "DELETE FROM horaire WHERE id_salle = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($salle->delete($id)) {
            $_SESSION['success'] = 'Salle supprimée avec succès';
            $_SESSION['success_type'] = 'success';
        }
    }
    header('Location: salles.php');
    exit;
}

// Handle create/update
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    if ($id > 0) {
        $edit_data = $salle->getById($id);
        if ($edit_data) {
            $edit_mode = true;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $capacite = (int)($_POST['capacite'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $equipement = trim($_POST['equipement'] ?? '');

    // Validation
    $errors = [];
    if (empty($nom)) $errors[] = "Le nom de la salle est obligatoire";
    if ($capacite <= 0) $errors[] = "La capacité doit être supérieure à 0";

    if (isset($_POST['id_salle']) && $_POST['id_salle']) {
        // Update
        if (empty($errors)) {
            if ($salle->update($_POST['id_salle'], $nom, $capacite)) {
                $_SESSION['success'] = 'Salle mise à jour avec succès';
                $_SESSION['success_type'] = 'success';
            } else {
                $_SESSION['success'] = 'Erreur lors de la mise à jour';
                $_SESSION['success_type'] = 'error';
            }
        } else {
            $_SESSION['success'] = implode(', ', $errors);
            $_SESSION['success_type'] = 'error';
        }
    } else {
        // Create
        if (empty($errors)) {
            if ($salle->create($nom, $capacite)) {
                $_SESSION['success'] = 'Salle ajoutée avec succès';
                $_SESSION['success_type'] = 'success';
            } else {
                $_SESSION['success'] = 'Erreur lors de l\'ajout';
                $_SESSION['success_type'] = 'error';
            }
        } else {
            $_SESSION['success'] = implode(', ', $errors);
            $_SESSION['success_type'] = 'error';
        }
    }
    header('Location: salles.php');
    exit;
}

$salles = $salle->getAll();

include __DIR__ . '/../includes/header.php';
?>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.04);
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    .stat-card .stat-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 40px;
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
        color: #1a202c;
        line-height: 1.2;
    }

    .stat-card .stat-label {
        font-size: 13px;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

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
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .search-wrapper input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), 0 4px 20px rgba(0,0,0,0.12);
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
        color: #667eea;
    }

    .salles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 25px;
    }

    .salle-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.4s ease;
        position: relative;
        animation: fadeInUp 0.5s ease-out;
        animation-fill-mode: both;
        border: 1px solid rgba(0,0,0,0.04);
    }

    .salle-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 50px rgba(0,0,0,0.15);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .salle-card .card-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        position: relative;
        overflow: hidden;
        min-height: 80px;
    }

    .salle-card .card-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
        transition: all 0.4s ease;
    }

    .salle-card:hover .card-header::after {
        transform: scale(1.3);
        opacity: 0.5;
    }

    .salle-card .card-header .card-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 50px;
        opacity: 0.15;
        transition: all 0.4s ease;
    }

    .salle-card:hover .card-header .card-icon {
        opacity: 0.25;
        transform: translateY(-50%) scale(1.1) rotate(-10deg);
    }

    .salle-card .card-header .salle-nom {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }

    .salle-card .card-header .salle-info {
        font-size: 13px;
        opacity: 0.9;
        margin-top: 4px;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .salle-card .card-header .salle-info .badge-info {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        background: rgba(255,255,255,0.2);
        color: white;
        backdrop-filter: blur(5px);
    }

    .salle-card .card-body {
        padding: 20px 25px;
    }

    .salle-card .card-body .description {
        color: #718096;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 15px;
        min-height: 40px;
    }

    .salle-card .card-body .details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 15px;
    }

    .salle-card .card-body .detail-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #2d3748;
        padding: 8px 12px;
        background: #f7fafc;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .salle-card .card-body .detail-item:hover {
        background: #edf2f7;
    }

    .salle-card .card-body .detail-item i {
        width: 20px;
        color: #667eea;
        font-size: 14px;
    }

    .salle-card .card-body .detail-item .label {
        color: #718096;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        display: block;
    }

    .salle-card .card-body .detail-item .value {
        font-weight: 600;
    }

    .salle-card .card-body .equipement {
        padding: 10px 14px;
        background: #f7fafc;
        border-radius: 10px;
        margin-top: 10px;
        font-size: 13px;
        color: #4a5568;
    }

    .salle-card .card-body .equipement i {
        color: #667eea;
        margin-right: 8px;
    }

    .salle-card .card-footer {
        padding: 15px 25px;
        border-top: 1px solid #edf2f7;
        display: flex;
        gap: 10px;
        background: #fafbfc;
    }

    .salle-card .card-footer .btn-action {
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

    .salle-card .card-footer .btn-action:hover {
        transform: translateY(-2px);
    }

    .salle-card .card-footer .btn-edit {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .salle-card .card-footer .btn-edit:hover {
        background: #667eea;
        color: white;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }

    .salle-card .card-footer .btn-delete {
        background: rgba(252, 129, 129, 0.1);
        color: #fc8181;
    }

    .salle-card .card-footer .btn-delete:hover {
        background: #fc8181;
        color: white;
        box-shadow: 0 5px 20px rgba(252, 129, 129, 0.3);
    }

    .salle-card .card-footer .btn-view {
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    .salle-card .card-footer .btn-view:hover {
        background: #48bb78;
        color: white;
        box-shadow: 0 5px 20px rgba(72, 187, 120, 0.3);
    }

    .btn-add-top {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 14px 30px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .btn-add-top:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        color: white;
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
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
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
        display: flex;
        align-items: center;
        gap: 10px;
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
        color: #1a202c;
        margin-bottom: 6px;
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
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
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

    @media (max-width: 768px) {
        .salles-grid {
            grid-template-columns: 1fr;
        }

        .salle-card .card-body .details {
            grid-template-columns: 1fr;
        }

        .salle-card .card-footer {
            flex-direction: column;
        }

        .salle-card .card-footer .btn-action {
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .modal-content {
            margin: 10px;
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

        .salle-card .card-header {
            padding: 15px 20px;
        }

        .salle-card .card-body {
            padding: 15px 20px;
        }

        .salle-card .card-footer {
            padding: 12px 20px;
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
                if (toast) { toast.style.opacity = '0'; toast.style.transform = 'translateX(100%)'; setTimeout(() => toast.style.display = 'none', 300); }
            }, 5000);
        </script>
        <?php endif; ?>

        <!-- Stats -->
        <?php
        $total = count($salles);
        $total_capacite = array_sum(array_column($salles, 'capacite'));
        ?>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-door-open stat-icon"></i>
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total Salles</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-number"><?php echo $total_capacite; ?></div>
                <div class="stat-label">Capacité Totale</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle stat-icon"></i>
                <div class="stat-number"><?php echo $total > 0 ? round(($total_capacite / $total), 0) : 0; ?></div>
                <div class="stat-label">Moyenne/Place</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-tag stat-icon"></i>
                <div class="stat-number"><?php echo $total > 0 ? 'Actif' : '0'; ?></div>
                <div class="stat-label">Statut</div>
            </div>
        </div>

        <!-- Header avec bouton Ajouter -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <h2 style="font-size: 22px; font-weight: 700; color: #1a202c; margin: 0;">
                <i class="fas fa-door-open me-2" style="color: #667eea;"></i>
                Liste des Salles
                <span style="font-size: 14px; font-weight: 400; color: #718096; margin-left: 10px;">
                    (<?php echo $total; ?> salles)
                </span>
            </h2>
            <button onclick="openModal()" class="btn-add-top">
                <i class="fas fa-plus-circle"></i>
                Nouvelle Salle
            </button>
        </div>

        <!-- Search -->
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Rechercher une salle par nom..." onkeyup="filterCards()">
        </div>

        <!-- Salles Grid -->
        <div class="salles-grid" id="sallesGrid">
            <?php if (empty($salles)): ?>
                <div class="empty-state">
                    <i class="fas fa-door-open"></i>
                    <h3>Aucune salle</h3>
                    <p>Commencez par ajouter votre première salle</p>
                </div>
            <?php else: ?>
                <?php foreach ($salles as $index => $s): 
                    $couleurs = ['#667eea', '#f093fb', '#4facfe', '#fa709a', '#48bb78', '#ed8936'];
                    $couleur = $couleurs[$index % count($couleurs)];
                ?>
                <div class="salle-card" style="animation-delay: <?php echo $index * 0.06; ?>s" data-search="<?php echo strtolower($s['nom'] ?? ''); ?>">
                    <div class="card-header" style="background: linear-gradient(135deg, <?php echo $couleur; ?>, <?php echo $couleur; ?>cc);">
                        <i class="fas fa-door-open card-icon"></i>
                        <div class="salle-nom"><?php echo htmlspecialchars($s['nom'] ?? 'Salle sans nom'); ?></div>
                        <div class="salle-info">
                            <span class="badge-info">
                                <i class="fas fa-users me-1"></i>
                                <?php echo $s['capacite'] ?? 0; ?> places
                            </span>
                            <span class="badge-info">
                                <i class="fas fa-hashtag me-1"></i>
                                #<?php echo str_pad($s['id_salle'] ?? 0, 4, '0', STR_PAD_LEFT); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="description">
                            <?php echo htmlspecialchars($s['description'] ?? 'Description non disponible'); ?>
                        </p>
                        <div class="details">
                            <div class="detail-item">
                                <i class="fas fa-users"></i>
                                <div>
                                    <span class="label">Capacité</span>
                                    <span class="value"><?php echo $s['capacite'] ?? 0; ?> places</span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-id-card"></i>
                                <div>
                                    <span class="label">Code</span>
                                    <span class="value">#<?php echo str_pad($s['id_salle'] ?? 0, 4, '0', STR_PAD_LEFT); ?></span>
                                </div>
                            </div>
                            <?php if (isset($s['date_creation']) && !empty($s['date_creation'])): ?>
                            <div class="detail-item" style="grid-column: span 2;">
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <span class="label">Créée le</span>
                                    <span class="value"><?php echo date('d/m/Y', strtotime($s['date_creation'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if (isset($s['equipement']) && !empty($s['equipement'])): ?>
                        <div class="equipement">
                            <i class="fas fa-tools"></i>
                            <strong>Équipement :</strong> <?php echo htmlspecialchars($s['equipement']); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="?edit=<?php echo $s['id_salle']; ?>" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="#" class="btn-action btn-view" onclick="event.preventDefault(); alert('📋 Détails de la salle #<?php echo $s['id_salle']; ?>\n\n🏷️ Nom: <?php echo htmlspecialchars($s['nom'] ?? 'Sans nom'); ?>\n👥 Capacité: <?php echo $s['capacite'] ?? 0; ?> places\n📝 Description: <?php echo htmlspecialchars($s['description'] ?? 'Non disponible'); ?>\n🔧 Équipement: <?php echo htmlspecialchars($s['equipement'] ?? 'Aucun'); ?>')">
                            <i class="fas fa-eye"></i> Détails
                        </a>
                        <a href="?delete=<?php echo $s['id_salle']; ?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette salle ? Cette action est irréversible.')">
                            <i class="fas fa-trash"></i> Supprimer
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
                        <?php echo $edit_mode ? 'Modifier la Salle' : 'Ajouter une Salle'; ?>
                    </h3>
                    <div class="modal-subtitle">
                        <?php echo $edit_mode ? 'Mettez à jour les informations de la salle' : 'Remplissez les informations pour ajouter une nouvelle salle'; ?>
                    </div>
                    <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                
                <form method="POST" onsubmit="return validateForm()">
                    <?php if ($edit_mode && $edit_data): ?>
                        <input type="hidden" name="id_salle" value="<?php echo $edit_data['id_salle']; ?>">
                    <?php endif; ?>

                    <div class="modal-body-custom">
                        <div class="form-group">
                            <label><i class="fas fa-door-open me-2" style="color: #667eea;"></i>Nom de la Salle <span class="required">*</span></label>
                            <input type="text" name="nom" class="form-control" 
                                   placeholder="Ex: Salle A1, Amphithéâtre, Laboratoire..."
                                   value="<?php echo $edit_mode && $edit_data ? htmlspecialchars($edit_data['nom'] ?? '') : ''; ?>"
                                   required>
                            <div class="form-hint">Le nom doit être unique et descriptif</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-users me-2" style="color: #48bb78;"></i>Capacité (places) <span class="required">*</span></label>
                            <input type="number" name="capacite" class="form-control" 
                                   placeholder="Ex: 30"
                                   value="<?php echo $edit_mode && $edit_data ? ($edit_data['capacite'] ?? '') : ''; ?>"
                                   required min="1" step="1">
                            <div class="form-hint">Nombre maximum de personnes dans la salle</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-align-left me-2" style="color: #4facfe;"></i>Description</label>
                            <textarea name="description" class="form-control" 
                                      placeholder="Décrivez la salle (emplacement, équipements particuliers...)"><?php echo $edit_mode && $edit_data ? htmlspecialchars($edit_data['description'] ?? '') : ''; ?></textarea>
                            <div class="form-hint">Informations complémentaires sur la salle</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-tools me-2" style="color: #ed8936;"></i>Équipement</label>
                            <input type="text" name="equipement" class="form-control" 
                                   placeholder="Ex: Vidéoprojecteur, Tableau interactif..."
                                   value="<?php echo $edit_mode && $edit_data ? htmlspecialchars($edit_data['equipement'] ?? '') : ''; ?>">
                            <div class="form-hint">Équipements disponibles dans la salle</div>
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
        const cards = document.querySelectorAll('.salle-card');

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
        const capacite = document.querySelector('input[name="capacite"]');
        
        if (!nom.value.trim()) {
            alert('⚠️ Le nom de la salle est obligatoire');
            nom.focus();
            return false;
        }
        
        if (!capacite.value || parseInt(capacite.value) <= 0) {
            alert('⚠️ Veuillez entrer une capacité valide');
            capacite.focus();
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
    document.querySelectorAll('.salle-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.01)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
</script>
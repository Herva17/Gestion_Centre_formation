<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Horaire.php';
require_once __DIR__ . '/../classes/Cours.php';
require_once __DIR__ . '/../classes/Salle.php';

$page_title = 'Gestion des Horaires';
$page_icon = 'calendar';

$database = new Database();
$db = $database->connect();
$horaire = new Horaire($db);
$cours = new Cours($db);
$salle = new Salle($db);

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0 && $horaire->delete($id)) {
        $_SESSION['success'] = 'Horaire supprimé avec succès';
        $_SESSION['success_type'] = 'success';
    }
    header('Location: horaires.php');
    exit;
}

// Handle create/update
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    if ($id > 0) {
        $edit_data = $horaire->getById($id);
        if ($edit_data) {
            $edit_mode = true;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jour = $_POST['jour'] ?? '';
    $heure_debut = $_POST['heure_debut'] ?? '';
    $heure_fin = $_POST['heure_fin'] ?? '';
    $id_salle = (int)$_POST['id_salle'] ?? 0;
    $id_cours = (int)$_POST['id_cours'] ?? 0;

    // Validation
    $errors = [];
    if (empty($jour)) $errors[] = "Le jour est obligatoire";
    if (empty($heure_debut)) $errors[] = "L'heure de début est obligatoire";
    if (empty($heure_fin)) $errors[] = "L'heure de fin est obligatoire";
    if ($id_salle <= 0) $errors[] = "Veuillez sélectionner une salle";
    if ($id_cours <= 0) $errors[] = "Veuillez sélectionner un cours";

    if (isset($_POST['id_horaire']) && $_POST['id_horaire']) {
        // Update
        if (empty($errors)) {
            if ($horaire->update($_POST['id_horaire'], $jour, $heure_debut, $heure_fin, $id_salle, $id_cours)) {
                $_SESSION['success'] = 'Horaire mis à jour avec succès';
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
            if ($horaire->create($jour, $heure_debut, $heure_fin, $id_salle, $id_cours)) {
                $_SESSION['success'] = 'Horaire ajouté avec succès';
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
    header('Location: horaires.php');
    exit;
}

$horaires = $horaire->getAll();
$touts_cours = $cours->getAll();
$salles = $salle->getAll();
$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

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
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .search-wrapper input:focus {
        outline: none;
        border-color: #4facfe;
        box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.1), 0 4px 20px rgba(0,0,0,0.12);
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
        color: #4facfe;
    }

    .table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .table-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
        background: #f7fafc;
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
        background: #f7fafc;
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
    tbody tr:hover { background: #f0f7ff; transform: scale(1.002); }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    tbody td {
        padding: 14px 20px;
        color: #2d3748;
        font-size: 14px;
        vertical-align: middle;
    }

    .day-badge {
        display: inline-block;
        padding: 5px 16px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 600;
    }

    .day-badge.lundi { background: rgba(79, 172, 254, 0.15); color: #4facfe; }
    .day-badge.mardi { background: rgba(72, 187, 120, 0.15); color: #48bb78; }
    .day-badge.mercredi { background: rgba(237, 137, 54, 0.15); color: #ed8936; }
    .day-badge.jeudi { background: rgba(102, 126, 234, 0.15); color: #667eea; }
    .day-badge.vendredi { background: rgba(118, 75, 162, 0.15); color: #764ba2; }
    .day-badge.samedi { background: rgba(252, 129, 129, 0.15); color: #fc8181; }
    .day-badge.dimanche { background: rgba(237, 137, 54, 0.15); color: #ed8936; }

    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 600;
        background: rgba(79, 172, 254, 0.1);
        color: #4facfe;
    }

    .time-badge i {
        font-size: 12px;
    }

    .cours-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .salle-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    .btn-action {
        padding: 8px 14px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .btn-edit {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .btn-edit:hover {
        background: #667eea;
        color: white;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-delete {
        background: rgba(252, 129, 129, 0.1);
        color: #fc8181;
    }

    .btn-delete:hover {
        background: #fc8181;
        color: white;
        box-shadow: 0 5px 15px rgba(252, 129, 129, 0.3);
    }

    .btn-view {
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    .btn-view:hover {
        background: #48bb78;
        color: white;
        box-shadow: 0 5px 15px rgba(72, 187, 120, 0.3);
    }

    .btn-add-top {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
        box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3);
        color: white;
    }

    .btn-group-actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
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
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }

    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(50px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-header-custom {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
        border-color: #4facfe;
        background: white;
        box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.1);
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
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
        box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3);
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
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .btn-group-actions {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }

        .table-header {
            flex-direction: column;
            text-align: center;
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

        .table-responsive {
            font-size: 13px;
        }

        thead th, tbody td {
            padding: 10px 12px;
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
        $total = count($horaires);
        $jours_stats = array_count_values(array_column($horaires, 'jour'));
        $jour_plus_occupe = !empty($jours_stats) ? array_keys($jours_stats, max($jours_stats))[0] : 'Aucun';
        $total_cours_unique = count(array_unique(array_column($horaires, 'id_cours')));
        ?>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-calendar-alt stat-icon"></i>
                <div class="stat-number"><?php echo $total; ?></div>
                <div class="stat-label">Total Horaires</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> Actif</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock stat-icon"></i>
                <div class="stat-number"><?php echo $total > 0 ? round($total / 7, 1) : 0; ?></div>
                <div class="stat-label">Moyenne/Jour</div>
                <span class="stat-change neutral"><i class="fas fa-minus"></i> Stable</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-book stat-icon"></i>
                <div class="stat-number"><?php echo $total_cours_unique; ?></div>
                <div class="stat-label">Cours Uniques</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> Programmé</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-star stat-icon"></i>
                <div class="stat-number"><?php echo $jour_plus_occupe; ?></div>
                <div class="stat-label">Jour le + Occupé</div>
                <span class="stat-change neutral"><i class="fas fa-minus"></i> <?php echo isset($jours_stats[$jour_plus_occupe]) ? $jours_stats[$jour_plus_occupe] . ' cours' : 'N/A'; ?></span>
            </div>
        </div>

        <!-- Header avec bouton Ajouter -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <h2 style="font-size: 22px; font-weight: 700; color: #1a202c; margin: 0;">
                <i class="fas fa-calendar me-2" style="color: #4facfe;"></i>
                Liste des Horaires
                <span style="font-size: 14px; font-weight: 400; color: #718096; margin-left: 10px;">
                    (<?php echo $total; ?> horaires)
                </span>
            </h2>
            <button onclick="openModal()" class="btn-add-top">
                <i class="fas fa-plus-circle"></i>
                Nouvel Horaire
            </button>
        </div>

        <!-- Search -->
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Rechercher par jour, cours, salle..." onkeyup="filterTable()">
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list me-2"></i>
                    Emploi du Temps
                </h3>
                <span class="badge-count">
                    <i class="fas fa-clock me-1"></i>
                    <?php echo $total; ?> horaires
                </span>
            </div>
            <div class="table-responsive">
                <table id="horaireTable">
                    <thead>
                        <tr>
                            <th style="min-width: 120px;">Jour</th>
                            <th style="min-width: 180px;">Cours</th>
                            <th style="min-width: 130px;">Salle</th>
                            <th style="min-width: 100px;">Heure Début</th>
                            <th style="min-width: 100px;">Heure Fin</th>
                            <th style="min-width: 160px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (empty($horaires)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times"></i>
                                        <h3>Aucun horaire</h3>
                                        <p>Commencez par ajouter un nouvel horaire</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($horaires as $index => $h): 
                                $jour_class = strtolower($h['jour'] ?? 'lundi');
                                $jour_icons = [
                                    'lundi' => 'fa-calendar-alt',
                                    'mardi' => 'fa-calendar-alt',
                                    'mercredi' => 'fa-calendar-alt',
                                    'jeudi' => 'fa-calendar-alt',
                                    'vendredi' => 'fa-calendar-alt',
                                    'samedi' => 'fa-calendar-check',
                                    'dimanche' => 'fa-calendar-day'
                                ];
                            ?>
                            <tr style="animation-delay: <?php echo $index * 0.04; ?>s">
                                <td>
                                    <span class="day-badge <?php echo $jour_class; ?>">
                                        <i class="fas <?php echo $jour_icons[$jour_class] ?? 'fa-calendar-alt'; ?> me-1"></i>
                                        <?php echo htmlspecialchars($h['jour']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="cours-badge">
                                        <i class="fas fa-book me-1"></i>
                                        <?php echo htmlspecialchars($h['cours_nom'] ?? '-'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="salle-badge">
                                        <i class="fas fa-door-open me-1"></i>
                                        <?php echo htmlspecialchars($h['salle_nom'] ?? '-'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="time-badge">
                                        <i class="fas fa-play"></i>
                                        <?php echo htmlspecialchars($h['heure_debut']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="time-badge">
                                        <i class="fas fa-stop"></i>
                                        <?php echo htmlspecialchars($h['heure_fin']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group-actions">
                                        <a href="?edit=<?php echo $h['id_horaire']; ?>" class="btn-action btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn-action btn-view" title="Voir détails" onclick="event.preventDefault(); alert('📋 Détails de l\'horaire #<?php echo $h['id_horaire']; ?>\n\n📅 Jour: <?php echo htmlspecialchars($h['jour']); ?>\n📚 Cours: <?php echo htmlspecialchars($h['cours_nom'] ?? '-'); ?>\n🚪 Salle: <?php echo htmlspecialchars($h['salle_nom'] ?? '-'); ?>\n⏰ Début: <?php echo htmlspecialchars($h['heure_debut']); ?>\n⏰ Fin: <?php echo htmlspecialchars($h['heure_fin']); ?>')">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?delete=<?php echo $h['id_horaire']; ?>" class="btn-action btn-delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet horaire ? Cette action est irréversible.')">
                                            <i class="fas fa-trash"></i>
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
                        <i class="fas fa-<?php echo $edit_mode ? 'edit' : 'plus-circle'; ?> me-2"></i>
                        <?php echo $edit_mode ? 'Modifier l\'Horaire' : 'Ajouter un Horaire'; ?>
                    </h3>
                    <div class="modal-subtitle">
                        <?php echo $edit_mode ? 'Mettez à jour les informations de l\'horaire' : 'Remplissez les informations pour ajouter un nouvel horaire'; ?>
                    </div>
                    <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                
                <form method="POST" onsubmit="return validateForm()">
                    <?php if ($edit_mode && $edit_data): ?>
                        <input type="hidden" name="id_horaire" value="<?php echo $edit_data['id_horaire']; ?>">
                    <?php endif; ?>

                    <div class="modal-body-custom">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-day me-2" style="color: #4facfe;"></i>Jour <span class="required">*</span></label>
                            <select name="jour" class="form-control" required>
                                <option value="">-- Sélectionner un jour --</option>
                                <?php foreach ($jours as $j): ?>
                                <option value="<?php echo $j; ?>" <?php echo ($edit_mode && $edit_data && $edit_data['jour'] == $j) ? 'selected' : ''; ?>>
                                    <?php echo $j; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Sélectionnez le jour de la semaine</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-book me-2" style="color: #667eea;"></i>Cours <span class="required">*</span></label>
                            <select name="id_cours" class="form-control" required>
                                <option value="">-- Sélectionner un cours --</option>
                                <?php foreach ($touts_cours as $c): ?>
                                <option value="<?php echo $c['id_cours']; ?>" <?php echo ($edit_mode && $edit_data && $edit_data['id_cours'] == $c['id_cours']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['nom']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Sélectionnez le cours à planifier</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-door-open me-2" style="color: #48bb78;"></i>Salle <span class="required">*</span></label>
                            <select name="id_salle" class="form-control" required>
                                <option value="">-- Sélectionner une salle --</option>
                                <?php foreach ($salles as $s): ?>
                                <option value="<?php echo $s['id_salle']; ?>" <?php echo ($edit_mode && $edit_data && $edit_data['id_salle'] == $s['id_salle']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['nom']); ?> (<?php echo $s['capacite']; ?> places)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Sélectionnez la salle pour le cours</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-clock me-2" style="color: #ed8936;"></i>Heure Début <span class="required">*</span></label>
                            <input type="time" name="heure_debut" class="form-control" 
                                   value="<?php echo $edit_mode && $edit_data ? $edit_data['heure_debut'] : ''; ?>"
                                   required>
                            <div class="form-hint">Heure de début du cours</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-clock me-2" style="color: #fc8181;"></i>Heure Fin <span class="required">*</span></label>
                            <input type="time" name="heure_fin" class="form-control" 
                                   value="<?php echo $edit_mode && $edit_data ? $edit_data['heure_fin'] : ''; ?>"
                                   required>
                            <div class="form-hint">Heure de fin du cours</div>
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
    // Filter Table
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('horaireTable');
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
        const jour = document.querySelector('select[name="jour"]');
        const cours = document.querySelector('select[name="id_cours"]');
        const salle = document.querySelector('select[name="id_salle"]');
        const heure_debut = document.querySelector('input[name="heure_debut"]');
        const heure_fin = document.querySelector('input[name="heure_fin"]');
        
        if (!jour.value) {
            alert('⚠️ Veuillez sélectionner un jour');
            jour.focus();
            return false;
        }
        
        if (!cours.value) {
            alert('⚠️ Veuillez sélectionner un cours');
            cours.focus();
            return false;
        }
        
        if (!salle.value) {
            alert('⚠️ Veuillez sélectionner une salle');
            salle.focus();
            return false;
        }
        
        if (!heure_debut.value) {
            alert('⚠️ Veuillez entrer une heure de début');
            heure_debut.focus();
            return false;
        }
        
        if (!heure_fin.value) {
            alert('⚠️ Veuillez entrer une heure de fin');
            heure_fin.focus();
            return false;
        }
        
        // Vérifier que l'heure de début est avant l'heure de fin
        if (heure_debut.value >= heure_fin.value) {
            alert('⚠️ L\'heure de début doit être antérieure à l\'heure de fin');
            heure_debut.focus();
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

    // Row hover animation
    document.querySelectorAll('#horaireTable tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.005)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
</script>
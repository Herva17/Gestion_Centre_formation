<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Paiement.php';
require_once __DIR__ . '/../classes/Inscription.php';

$page_title = 'Gestion des Paiements';
$page_icon = 'credit-card';

$database = new Database();
$db = $database->connect();
$paiement = new Paiement($db);
$inscription = new Inscription($db);

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = floatval($_POST['montant'] ?? 0);
    $type = $_POST['type'] ?? '';
    $mois = $_POST['mois'] ?? '';
    $id_inscription = intval($_POST['id_inscription'] ?? 0);
    $date_paiement = $_POST['date_paiement'] ?? date('Y-m-d');

    if ($id_inscription > 0 && $montant > 0) {
        if ($paiement->create($montant, $type, $mois, $id_inscription)) {
            $_SESSION['success'] = 'Paiement enregistré avec succès';
            $_SESSION['success_type'] = 'success';
        } else {
            $_SESSION['success'] = 'Erreur lors de l\'enregistrement du paiement';
            $_SESSION['success_type'] = 'error';
        }
    } else {
        $_SESSION['success'] = 'Veuillez sélectionner une inscription et un montant valide';
        $_SESSION['success_type'] = 'error';
    }
    header('Location: paiements.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id > 0) {
        $query = "DELETE FROM paiement WHERE id_paiement = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Paiement supprimé avec succès';
            $_SESSION['success_type'] = 'success';
        }
    }
    header('Location: paiements.php');
    exit;
}

$paiements = $paiement->getAll();
$inscriptions = $inscription->getAll();

include __DIR__ . '/../includes/header.php';
?>

<style>
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.04);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    .stat-card .stat-icon {
        font-size: 35px;
        opacity: 0.1;
    }

    .stat-card .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #1a202c;
    }

    .stat-card .stat-label {
        font-size: 13px;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-action {
        padding: 8px 16px;
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

    .btn-receipt {
        background: rgba(237, 137, 54, 0.1);
        color: #ed8936;
    }

    .btn-receipt:hover {
        background: #ed8936;
        color: white;
        box-shadow: 0 5px 15px rgba(237, 137, 54, 0.3);
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

    .payment-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }

    .payment-badge.especes { background: rgba(72, 187, 120, 0.15); color: #48bb78; }
    .payment-badge.cheque { background: rgba(102, 126, 234, 0.15); color: #667eea; }
    .payment-badge.virement { background: rgba(237, 137, 54, 0.15); color: #ed8936; }
    .payment-badge.carte { background: rgba(118, 75, 162, 0.15); color: #764ba2; }

    .btn-group-actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .table-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
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
    tbody tr:hover { background: #f0f4ff; transform: scale(1.002); }

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

    .user-avatar-sm {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 14px;
        margin-right: 10px;
        flex-shrink: 0;
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

    .frais-amount {
        font-weight: 700;
        font-size: 15px;
        color: #48bb78;
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
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
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
        border-color: #ed8936;
        background: white;
        box-shadow: 0 0 0 4px rgba(237, 137, 54, 0.1);
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
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
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
        box-shadow: 0 10px 30px rgba(237, 137, 54, 0.3);
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
        border-color: #ed8936;
        box-shadow: 0 0 0 4px rgba(237, 137, 54, 0.1), 0 4px 20px rgba(0,0,0,0.12);
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
        color: #ed8936;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    /* Boutons d'action en haut */
    .top-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .btn-top-action {
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-top-action:hover {
        transform: translateY(-2px);
    }

    .btn-top-action.btn-report {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-top-action.btn-report:hover {
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-top-action.btn-add {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }

    .btn-top-action.btn-add:hover {
        box-shadow: 0 5px 20px rgba(72, 187, 120, 0.3);
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

        .top-actions {
            flex-direction: column;
        }

        .btn-top-action {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
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
        $total_paiements = array_sum(array_column($paiements, 'montant'));
        $nb_paiements = count($paiements);
        $types = array_count_values(array_column($paiements, 'type'));
        $type_principal = !empty($types) ? array_keys($types)[0] : 'Aucun';
        $moyenne = $nb_paiements > 0 ? $total_paiements / $nb_paiements : 0;
        ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="stat-number">$<?php echo number_format($total_paiements, 0, ',', ' '); ?></div>
                <div class="stat-label">Total Paiements</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-receipt"></i></div>
                <div class="stat-number"><?php echo $nb_paiements; ?></div>
                <div class="stat-label">Nombre de Paiements</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-tag"></i></div>
                <div class="stat-number"><?php echo $type_principal; ?></div>
                <div class="stat-label">Type Principal</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calculator"></i></div>
                <div class="stat-number">$<?php echo number_format($moyenne, 0, ',', ' '); ?></div>
                <div class="stat-label">Moyenne par Paiement</div>
            </div>
        </div>

        <!-- Actions Top -->
        <div class="top-actions">
            <a href="rapport-periodique.php" class="btn-top-action btn-report">
                <i class="fas fa-chart-bar"></i>
                Rapport Périodique
            </a>
            <button onclick="openModal()" class="btn-top-action btn-add">
                <i class="fas fa-plus-circle"></i>
                Enregistrer un Paiement
            </button>
        </div>

        <!-- Search -->
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Rechercher par apprenant, type, mois..." onkeyup="filterTable()">
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>
                    <i class="fas fa-credit-card me-2"></i>
                    Liste des Paiements
                </h3>
                <span class="badge-count">
                    <i class="fas fa-dollar-sign me-1"></i>
                    <?php echo $nb_paiements; ?> paiements
                </span>
            </div>
            <div class="table-responsive">
                <table id="paiementTable">
                    <thead>
                        <tr>
                            <th style="min-width: 180px;">Apprenant</th>
                            <th style="min-width: 120px;">Montant</th>
                            <th style="min-width: 130px;">Type</th>
                            <th style="min-width: 120px;">Mois</th>
                            <th style="min-width: 130px;">Date</th>
                            <th style="min-width: 140px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (empty($paiements)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px;">
                                    <div style="color: #a0aec0;">
                                        <i class="fas fa-credit-card" style="font-size: 48px; display: block; margin-bottom: 15px; opacity: 0.3;"></i>
                                        <h3 style="color: #4a5568; font-size: 18px; margin-bottom: 8px;">Aucun paiement</h3>
                                        <p>Commencez par enregistrer un premier paiement</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($paiements as $index => $pay): ?>
                            <tr style="animation-delay: <?php echo $index * 0.04; ?>s">
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar-sm">
                                            <?php echo strtoupper(substr($pay['prenom'] ?? 'A', 0, 1) . substr($pay['nom'] ?? '', 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="user-name"><?php echo htmlspecialchars($pay['prenom'] . ' ' . $pay['nom']); ?></div>
                                            <div class="user-detail">
                                                <i class="fas fa-id-card"></i>
                                                #<?php echo str_pad($pay['id_inscription'], 4, '0', STR_PAD_LEFT); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="frais-amount">$<?php echo number_format($pay['montant'], 0, ',', ' '); ?></span>
                                </td>
                                <td>
                                    <span class="payment-badge <?php echo strtolower($pay['type'] ?? 'especes'); ?>">
                                        <i class="fas fa-<?php echo $pay['type'] === 'Espèces' ? 'money-bill-wave' : ($pay['type'] === 'Chèque' ? 'file-invoice' : ($pay['type'] === 'Virement' ? 'exchange-alt' : 'credit-card')); ?> me-1"></i>
                                        <?php echo htmlspecialchars($pay['type'] ?? 'Espèces'); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($pay['mois'] ?? '-'); ?></td>
                                <td style="font-size: 13px; color: #718096;">
                                    <i class="fas fa-calendar-alt me-2" style="color: #ed8936;"></i>
                                    <?php echo isset($pay['date_paiement']) ? date('d/m/Y', strtotime($pay['date_paiement'])) : '-'; ?>
                                </td>
                                <td>
                                    <div class="btn-group-actions">
                                        <a href="recu-paiement.php?id=<?php echo $pay['id_paiement']; ?>" class="btn-action btn-receipt" title="Reçu de paiement">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                        <a href="?delete=<?php echo $pay['id_paiement']; ?>" class="btn-action btn-delete" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?')">
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
                        <i class="fas fa-plus-circle me-2"></i>
                        Enregistrer un Paiement
                    </h3>
                    <div class="modal-subtitle">
                        Remplissez les informations pour enregistrer un paiement
                    </div>
                    <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                
                <form method="POST" onsubmit="return validateForm()">
                    <div class="modal-body-custom">
                        <div class="form-group">
                            <label><i class="fas fa-user me-2" style="color: #ed8936;"></i>Inscription <span class="required">*</span></label>
                            <select name="id_inscription" class="form-control" required>
                                <option value="">-- Sélectionner une inscription --</option>
                                <?php foreach ($inscriptions as $insc): ?>
                                <option value="<?php echo $insc['id_inscription']; ?>">
                                    <?php echo htmlspecialchars($insc['prenom'] . ' ' . $insc['nom'] . ' - ' . $insc['filiere_nom']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Sélectionnez l'inscription concernée</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-dollar-sign me-2" style="color: #48bb78;"></i>Montant ($) <span class="required">*</span></label>
                            <input type="number" name="montant" class="form-control" 
                                   placeholder="Ex: 150"
                                   required min="0" step="1">
                            <div class="form-hint">Entrez le montant en dollars ($)</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-tag me-2" style="color: #667eea;"></i>Type de Paiement</label>
                            <select name="type" class="form-control">
                                <option value="">-- Sélectionner --</option>
                                <option value="Espèces">💵 Espèces</option>
                                <option value="Chèque">📄 Chèque</option>
                                <option value="Virement">🏦 Virement</option>
                                <option value="Carte">💳 Carte Bancaire</option>
                            </select>
                            <div class="form-hint">Moyen de paiement utilisé</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-calendar-alt me-2" style="color: #ed8936;"></i>Mois</label>
                            <select name="mois" class="form-control">
                                <option value="">-- Sélectionner --</option>
                                <option value="Janvier">Janvier</option>
                                <option value="Février">Février</option>
                                <option value="Mars">Mars</option>
                                <option value="Avril">Avril</option>
                                <option value="Mai">Mai</option>
                                <option value="Juin">Juin</option>
                                <option value="Juillet">Juillet</option>
                                <option value="Août">Août</option>
                                <option value="Septembre">Septembre</option>
                                <option value="Octobre">Octobre</option>
                                <option value="Novembre">Novembre</option>
                                <option value="Décembre">Décembre</option>
                            </select>
                            <div class="form-hint">Mois concerné par le paiement</div>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-calendar-day me-2" style="color: #4facfe;"></i>Date du paiement</label>
                            <input type="date" name="date_paiement" class="form-control"
                                   value="<?php echo date('Y-m-d'); ?>">
                            <div class="form-hint">Date à laquelle le paiement a été effectué</div>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn-cancel" onclick="closeModal()">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save me-2"></i>Enregistrer
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
        const table = document.getElementById('paiementTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            if (row.querySelector('td[colspan]')) continue;
            
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
        const inscription = document.querySelector('select[name="id_inscription"]');
        const montant = document.querySelector('input[name="montant"]');
        
        if (!inscription.value) {
            alert('⚠️ Veuillez sélectionner une inscription');
            inscription.focus();
            return false;
        }
        
        if (!montant.value || parseFloat(montant.value) <= 0) {
            alert('⚠️ Veuillez entrer un montant valide');
            montant.focus();
            return false;
        }
        
        return true;
    }

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
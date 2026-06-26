<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Utilisateur.php';

$page_title = 'Gestion des Utilisateurs';
$page_icon = 'user-cog';

$db = new Database();
$conn = $db->getConnection();
$utilisateur = new Utilisateur($conn);

// Récupérer tous les utilisateurs
$users = $utilisateur->getAll();
$total_users = $utilisateur->countAll();
$active_users = $utilisateur->countActive();

// Récupérer le nombre d'utilisateurs par rôle
$admins = $utilisateur->countByRole('Admin');
$formateurs = $utilisateur->countByRole('Formateur');
$gestionnaires = $utilisateur->countByRole('Gestionnaire');

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

    .table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .table-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        color: #2d3748;
    }

    .user-detail {
        font-size: 12px;
        color: #718096;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .role-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }

    .role-badge.admin {
        background: rgba(252, 129, 129, 0.15);
        color: #fc8181;
    }

    .role-badge.formateur {
        background: rgba(118, 75, 162, 0.15);
        color: #764ba2;
    }

    .role-badge.gestionnaire {
        background: rgba(237, 137, 54, 0.15);
        color: #ed8936;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.actif {
        background: rgba(72, 187, 120, 0.15);
        color: #48bb78;
    }

    .status-badge.inactif {
        background: rgba(252, 129, 129, 0.15);
        color: #fc8181;
    }

    .btn-group-actions {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    .btn-action {
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

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .btn-action.edit {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .btn-action.edit:hover {
        background: #667eea;
        color: white;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-action.delete {
        background: rgba(252, 129, 129, 0.1);
        color: #fc8181;
    }

    .btn-action.delete:hover {
        background: #fc8181;
        color: white;
        box-shadow: 0 5px 15px rgba(252, 129, 129, 0.3);
    }

    .btn-action.view {
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    .btn-action.view:hover {
        background: #48bb78;
        color: white;
        box-shadow: 0 5px 15px rgba(72, 187, 120, 0.3);
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
        max-width: 600px;
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

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group.full-width {
        grid-column: span 2;
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

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%234a5568' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
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
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: span 1;
        }

        .btn-group-actions {
            flex-direction: column;
            align-items: center;
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
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Utilisateurs</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> Actif</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle stat-icon"></i>
                <div class="stat-number"><?php echo $active_users; ?></div>
                <div class="stat-label">Utilisateurs Actifs</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> Connectés</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-crown stat-icon"></i>
                <div class="stat-number"><?php echo $admins; ?></div>
                <div class="stat-label">Administrateurs</div>
                <span class="stat-change neutral"><i class="fas fa-minus"></i> Super</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-chalkboard-user stat-icon"></i>
                <div class="stat-number"><?php echo $formateurs; ?></div>
                <div class="stat-label">Formateurs</div>
                <span class="stat-change neutral"><i class="fas fa-minus"></i> Pédagogie</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-clipboard-list stat-icon"></i>
                <div class="stat-number"><?php echo $gestionnaires; ?></div>
                <div class="stat-label">Gestionnaires</div>
                <span class="stat-change neutral"><i class="fas fa-minus"></i> Organisation</span>
            </div>
        </div>

        <!-- Header avec bouton Ajouter -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <h2 style="font-size: 22px; font-weight: 700; color: #1a202c; margin: 0;">
                <i class="fas fa-user-cog me-2" style="color: #667eea;"></i>
                Liste des Utilisateurs
                <span style="font-size: 14px; font-weight: 400; color: #718096; margin-left: 10px;">
                    (<?php echo $total_users; ?> utilisateurs)
                </span>
            </h2>
            <button onclick="openAddModal()" class="btn-add-top">
                <i class="fas fa-plus-circle"></i>
                Ajouter un Utilisateur
            </button>
        </div>

        <!-- Search -->
        <div class="search-wrapper">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Rechercher par nom, email, rôle..." onkeyup="filterTable()">
        </div>

        <!-- Table -->
        <div class="table-container">
            <div class="table-header">
                <h3>
                    <i class="fas fa-list me-2"></i>
                    Utilisateurs
                </h3>
                <span class="badge-count">
                    <i class="fas fa-users me-1"></i>
                    <?php echo $total_users; ?> utilisateurs
                </span>
            </div>
            <div class="table-responsive">
                <table id="userTable">
                    <thead>
                        <tr>
                            <th style="min-width: 180px;">Utilisateur</th>
                            <th style="min-width: 180px;">Email</th>
                            <th style="min-width: 130px;">Nom d'utilisateur</th>
                            <th style="min-width: 120px;">Rôle</th>
                            <th style="min-width: 100px;">Statut</th>
                            <th style="min-width: 120px;">Date Création</th>
                            <th style="min-width: 110px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fas fa-users-slash"></i>
                                        <h3>Aucun utilisateur</h3>
                                        <p>Commencez par ajouter votre premier utilisateur</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $index => $user): ?>
                            <tr style="animation-delay: <?php echo $index * 0.04; ?>s">
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user['prenom'] ?? 'U', 0, 1) . substr($user['nom'] ?? '', 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="user-name"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></div>
                                            <div class="user-detail">
                                                <i class="fas fa-id-card"></i>
                                                #<?php echo str_pad($user['id_utilisateur'], 4, '0', STR_PAD_LEFT); ?>
                                                <?php if (!empty($user['sexe'])): ?>
                                                    <span style="margin: 0 4px;">•</span>
                                                    <i class="fas fa-<?php echo $user['sexe'] === 'Masculin' ? 'mars' : 'venus'; ?>"></i>
                                                    <?php echo htmlspecialchars($user['sexe']); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px; color: #718096; font-size: 13px;">
                                        <i class="fas fa-envelope" style="color: #667eea;"></i>
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px; font-family: monospace; font-size: 13px; color: #2d3748;">
                                        <i class="fas fa-user" style="color: #764ba2;"></i>
                                        <?php echo htmlspecialchars($user['nom_utilisateur']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    $role_class = match($user['role']) {
                                        'Admin' => 'admin',
                                        'Formateur' => 'formateur',
                                        'Gestionnaire' => 'gestionnaire',
                                        default => 'admin'
                                    };
                                    $role_icon = match($user['role']) {
                                        'Admin' => 'fa-crown',
                                        'Formateur' => 'fa-chalkboard-user',
                                        'Gestionnaire' => 'fa-clipboard-list',
                                        default => 'fa-user'
                                    };
                                    ?>
                                    <span class="role-badge <?php echo $role_class; ?>">
                                        <i class="fas <?php echo $role_icon; ?> me-1"></i>
                                        <?php echo htmlspecialchars($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($user['statut'] ?? 'actif'); ?>">
                                        <i class="fas fa-<?php echo ($user['statut'] ?? 'Actif') === 'Actif' ? 'circle' : 'times-circle'; ?> me-1" style="font-size: 8px;"></i>
                                        <?php echo htmlspecialchars($user['statut'] ?? 'Actif'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #718096;">
                                        <i class="fas fa-calendar-alt" style="color: #4facfe;"></i>
                                        <?php echo date('d/m/Y', strtotime($user['date_creation'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group-actions">
                                        <button onclick="openEditModal(<?php echo $user['id_utilisateur']; ?>)" class="btn-action edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="openViewModal(<?php echo $user['id_utilisateur']; ?>)" class="btn-action view" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="deleteUser(<?php echo $user['id_utilisateur']; ?>)" class="btn-action delete" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Ajouter/Modifier -->
        <div id="userModal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header-custom">
                    <h3>
                        <i class="fas fa-<?php echo isset($_GET['edit']) ? 'edit' : 'user-plus'; ?> me-2"></i>
                        <span id="modalTitle">Ajouter un Utilisateur</span>
                    </h3>
                    <div class="modal-subtitle" id="modalSubtitle">Remplissez les informations pour ajouter un nouvel utilisateur</div>
                    <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
                </div>
                
                <form id="userForm" method="POST" action="../api/index.php?action=save_user" onsubmit="return validateForm()">
                    <input type="hidden" id="userId" name="id_utilisateur" value="">

                    <div class="modal-body-custom">
                        <div class="form-grid">
                            <div class="form-group">
                                <label><i class="fas fa-user me-2" style="color: #667eea;"></i>Nom <span class="required">*</span></label>
                                <input type="text" id="nom" name="nom" class="form-control" 
                                       placeholder="Ex: KAZIGE" required>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-user me-2" style="color: #667eea;"></i>Prénom <span class="required">*</span></label>
                                <input type="text" id="prenom" name="prenom" class="form-control" 
                                       placeholder="Ex: Stéphane" required>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-<?php echo isset($edit_data) && $edit_data['sexe'] === 'Féminin' ? 'venus' : 'mars'; ?> me-2" style="color: #4facfe;"></i>Sexe</label>
                                <select id="sexe" name="sexe" class="form-control">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Masculin">Masculin</option>
                                    <option value="Féminin">Féminin</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-phone me-2" style="color: #48bb78;"></i>Téléphone</label>
                                <input type="tel" id="telephone" name="telephone" class="form-control" 
                                       placeholder="+225 07 08 09 10 11">
                            </div>

                            <div class="form-group full-width">
                                <label><i class="fas fa-envelope me-2" style="color: #ed8936;"></i>Email <span class="required">*</span></label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       placeholder="utilisateur@email.com" required>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-user-tag me-2" style="color: #764ba2;"></i>Nom d'utilisateur <span class="required">*</span></label>
                                <input type="text" id="nom_utilisateur" name="nom_utilisateur" class="form-control" 
                                       placeholder="skazige" required>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-tag me-2" style="color: #fc8181;"></i>Rôle <span class="required">*</span></label>
                                <select id="role" name="role" class="form-control" required>
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Admin">Administrateur</option>
                                    <option value="Formateur">Formateur</option>
                                    <option value="Gestionnaire">Gestionnaire</option>
                                </select>
                            </div>

                            <div class="form-group full-width">
                                <label><i class="fas fa-lock me-2" style="color: #ed8936;"></i>Mot de passe <span id="passwordRequired" class="required">*</span></label>
                                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" 
                                       placeholder="Entrez un mot de passe sécurisé">
                                <div class="form-hint" id="passwordHint">Minimum 8 caractères, incluez majuscules, minuscules et chiffres</div>
                            </div>

                            <div class="form-group full-width">
                                <label><i class="fas fa-circle me-2" style="color: #48bb78;"></i>Statut</label>
                                <select id="statut" name="statut" class="form-control">
                                    <option value="Actif">Actif</option>
                                    <option value="Inactif">Inactif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer-custom">
                        <button type="button" class="btn-cancel" onclick="closeModal()">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save me-2"></i>
                            <span id="submitBtnText">Ajouter</span>
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
        const table = document.getElementById('userTable');
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
    function openAddModal() {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('modalTitle').textContent = 'Ajouter un Utilisateur';
        document.getElementById('modalSubtitle').textContent = 'Remplissez les informations pour ajouter un nouvel utilisateur';
        document.getElementById('submitBtnText').textContent = 'Ajouter';
        document.getElementById('mot_de_passe').required = true;
        document.getElementById('passwordRequired').textContent = '*';
        document.getElementById('passwordHint').textContent = 'Minimum 8 caractères, incluez majuscules, minuscules et chiffres';
        document.getElementById('userModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function openEditModal(userId) {
        fetch(`../api/index.php?action=get_user&id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.data;
                    document.getElementById('userId').value = user.id_utilisateur;
                    document.getElementById('nom').value = user.nom || '';
                    document.getElementById('prenom').value = user.prenom || '';
                    document.getElementById('sexe').value = user.sexe || '';
                    document.getElementById('telephone').value = user.telephone || '';
                    document.getElementById('email').value = user.email || '';
                    document.getElementById('nom_utilisateur').value = user.nom_utilisateur || '';
                    document.getElementById('role').value = user.role || '';
                    document.getElementById('statut').value = user.statut || 'Actif';
                    document.getElementById('mot_de_passe').value = '';
                    document.getElementById('mot_de_passe').required = false;
                    document.getElementById('passwordRequired').textContent = '';
                    document.getElementById('passwordHint').textContent = 'Laissez vide pour conserver le mot de passe actuel';
                    document.getElementById('modalTitle').textContent = 'Modifier l\'Utilisateur';
                    document.getElementById('modalSubtitle').textContent = 'Mettez à jour les informations de l\'utilisateur';
                    document.getElementById('submitBtnText').textContent = 'Mettre à jour';
                    document.getElementById('userModal').classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            })
            .catch(error => {
                alert('Erreur lors du chargement des données');
                console.error('Erreur:', error);
            });
    }

    function openViewModal(userId) {
        fetch(`../api/index.php?action=get_user&id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.data;
                    alert('📋 Détails de l\'utilisateur #' + user.id_utilisateur + 
                          '\n\n👤 Nom: ' + user.prenom + ' ' + user.nom +
                          '\n📧 Email: ' + user.email +
                          '\n🔑 Nom d\'utilisateur: ' + user.nom_utilisateur +
                          '\n🎯 Rôle: ' + user.role +
                          '\n📊 Statut: ' + user.statut +
                          (user.telephone ? '\n📞 Téléphone: ' + user.telephone : '') +
                          (user.sexe ? '\n⚧ Sexe: ' + user.sexe : ''));
                }
            })
            .catch(error => {
                alert('Erreur lors du chargement des données');
                console.error('Erreur:', error);
            });
    }

    function closeModal() {
        document.getElementById('userModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Click outside modal
    document.getElementById('userModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('userModal').classList.contains('active')) {
            closeModal();
        }
    });

    // Delete User
    function deleteUser(userId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
            fetch(`../api/index.php?action=delete_user&id=${userId}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Utilisateur supprimé avec succès');
                        location.reload();
                    } else {
                        alert('❌ Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('❌ Erreur lors de la suppression');
                    console.error('Erreur:', error);
                });
        }
    }

    // Validation
    function validateForm() {
        const password = document.getElementById('mot_de_passe').value;
        const userId = document.getElementById('userId').value;
        
        if (!userId && !password) {
            alert('⚠️ Le mot de passe est requis pour un nouvel utilisateur');
            return false;
        }

        if (password && password.length < 8) {
            alert('⚠️ Le mot de passe doit contenir au moins 8 caractères');
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

    // Row hover animation
    document.querySelectorAll('#userTable tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.005)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
</script>
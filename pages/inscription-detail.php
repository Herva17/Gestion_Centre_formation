<?php
// Move to pages folder for relative path resolution

session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Inscription.php';

$page_title = 'Détail Inscription';

$database = new Database();
$db = $database->connect();
$inscription = new Inscription($db);

if (!isset($_GET['id'])) {
    header('Location: inscriptions.php');
    exit;
}

$detail = $inscription->getById($_GET['id']);

if (!$detail) {
    $_SESSION['error'] = 'Inscription non trouvée';
    header('Location: inscriptions.php');
    exit;
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
        max-width: 900px;
        margin: 0 auto;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
        padding: 15px 20px;
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        animation: slideDown 0.5s ease-out;
    }

    .breadcrumb a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .breadcrumb a:hover {
        color: var(--primary-dark);
        transform: translateX(-3px);
    }

    .breadcrumb .separator {
        color: var(--gray);
    }

    .breadcrumb .current {
        color: var(--dark);
        font-weight: 600;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
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

    .card {
        background: white;
        border-radius: 20px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out;
        animation-fill-mode: both;
    }

    .card:hover {
        box-shadow: var(--shadow-lg);
    }

    .card-header-custom {
        padding: 20px 25px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .card-header-custom::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }

    .card-header-custom h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header-custom .subtitle {
        font-size: 13px;
        opacity: 0.85;
        margin-top: 4px;
        position: relative;
        z-index: 1;
    }

    .card-body-custom {
        padding: 25px;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .card:nth-child(1) { animation-delay: 0.1s; }
    .card:nth-child(2) { animation-delay: 0.2s; }

    .user-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 32px;
        font-weight: 700;
        margin-right: 20px;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .user-header {
        display: flex;
        align-items: center;
        padding: 20px 25px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .user-header .user-name {
        font-size: 26px;
        font-weight: 700;
        color: var(--dark);
    }

    .user-header .user-id {
        font-size: 14px;
        color: var(--gray);
        margin-top: 4px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .info-item {
        padding: 15px 18px;
        background: var(--light-gray);
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: #edf2f7;
        transform: translateY(-2px);
    }

    .info-item .label {
        font-size: 12px;
        color: var(--gray);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .info-item .value {
        font-size: 18px;
        font-weight: 700;
        color: var(--dark);
        margin-top: 5px;
    }

    .info-item .value.text-green { color: var(--success); }
    .info-item .value.text-blue { color: var(--primary); }
    .info-item .value.text-purple { color: var(--secondary); }
    .info-item .value.text-orange { color: var(--warning); }

    .status-badge {
        display: inline-block;
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
    }

    .status-badge.active {
        background: rgba(72, 187, 120, 0.15);
        color: #48bb78;
    }

    .status-badge.inactive {
        background: rgba(252, 129, 129, 0.15);
        color: #fc8181;
    }

    .btn-group {
        display: flex;
        gap: 12px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .btn-action-card {
        padding: 12px 25px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-action-card:hover {
        transform: translateY(-2px);
    }

    .btn-back {
        background: #e2e8f0;
        color: #4a5568;
    }

    .btn-back:hover {
        background: #cbd5e0;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .btn-edit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-edit:hover {
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-receipt {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
    }

    .btn-receipt:hover {
        box-shadow: 0 5px 20px rgba(237, 137, 54, 0.3);
    }

    .btn-delete {
        background: linear-gradient(135deg, #fc8181 0%, #e53e3e 100%);
        color: white;
    }

    .btn-delete:hover {
        box-shadow: 0 5px 20px rgba(252, 129, 129, 0.3);
    }

    @media (max-width: 768px) {
        .user-header {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .user-avatar-large {
            margin-right: 0;
            margin-bottom: 15px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .breadcrumb {
            flex-wrap: wrap;
            padding: 12px 16px;
        }

        .btn-group {
            flex-direction: column;
        }

        .btn-action-card {
            width: 100%;
            justify-content: center;
        }

        .card-body-custom {
            padding: 20px;
        }
    }

    @media (max-width: 480px) {
        .page-wrapper { padding: 10px; }
        .card-body-custom { padding: 15px; }
        .card-header-custom { padding: 15px 20px; }
        .user-header { padding: 15px; }
        .user-header .user-name { font-size: 20px; }
        .info-item .value { font-size: 16px; }
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

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="inscriptions.php">
                <i class="fas fa-arrow-left"></i>
                Retour aux inscriptions
            </a>
            <span class="separator">/</span>
            <span class="current">
                <i class="fas fa-file-invoice me-2"></i>
                Détail de l'inscription #<?php echo str_pad($_GET['id'], 4, '0', STR_PAD_LEFT); ?>
            </span>
        </div>

        <!-- User Header -->
        <div class="card" style="margin-bottom: 25px;">
            <div class="user-header">
                <div class="user-avatar-large">
                    <?php echo strtoupper(substr($detail['prenom'] ?? 'A', 0, 1) . substr($detail['nom'] ?? '', 0, 1)); ?>
                </div>
                <div style="flex: 1;">
                    <div class="user-name">
                        <?php echo htmlspecialchars($detail['prenom'] . ' ' . $detail['nom']); ?>
                    </div>
                    <div class="user-id">
                        <i class="fas fa-id-card me-2"></i>
                        Apprenant #<?php echo str_pad($detail['id_apprenant'], 4, '0', STR_PAD_LEFT); ?>
                        <span style="margin: 0 8px;">•</span>
                        <i class="fas fa-phone me-2"></i>
                        <?php echo htmlspecialchars($detail['telephone'] ?? 'Non renseigné'); ?>
                        <span style="margin: 0 8px;">•</span>
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo htmlspecialchars($detail['email'] ?? 'Non renseigné'); ?>
                    </div>
                </div>
                <div>
                    <span class="status-badge <?php echo ($detail['statut'] ?? 'actif') === 'actif' ? 'active' : 'inactive'; ?>">
                        <i class="fas fa-<?php echo ($detail['statut'] ?? 'actif') === 'actif' ? 'check-circle' : 'times-circle'; ?> me-1"></i>
                        <?php echo ucfirst($detail['statut'] ?? 'Actif'); ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Inscription Details -->
        <div class="card">
            <div class="card-header-custom" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h3><i class="fas fa-clipboard-list"></i> Détails de l'Inscription</h3>
                <div class="subtitle">Informations complètes de l'inscription</div>
            </div>
            <div class="card-body-custom">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label"><i class="fas fa-graduation-cap"></i> Filière</div>
                        <div class="value text-purple"><?php echo htmlspecialchars($detail['filiere_nom']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label"><i class="fas fa-calendar-alt"></i> Date d'Inscription</div>
                        <div class="value"><?php echo date('d/m/Y', strtotime($detail['date_inscription'])); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label"><i class="fas fa-dollar-sign"></i> Frais d'Inscription</div>
                        <div class="value text-green">$<?php echo number_format($detail['frais_inscription'], 0, ',', ' '); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label"><i class="fas fa-dollar-sign"></i> Frais Mensuel</div>
                        <div class="value text-blue">$<?php echo number_format($detail['frais_mensuel'], 0, ',', ' '); ?></div>
                    </div>
                    <?php if (isset($detail['date_creation'])): ?>
                    <div class="info-item">
                        <div class="label"><i class="fas fa-clock"></i> Créée le</div>
                        <div class="value"><?php echo date('d/m/Y à H:i', strtotime($detail['date_creation'])); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($detail['date_modification'])): ?>
                    <div class="info-item">
                        <div class="label"><i class="fas fa-edit"></i> Dernière modification</div>
                        <div class="value"><?php echo date('d/m/Y à H:i', strtotime($detail['date_modification'])); ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Boutons d'action -->
                <div class="btn-group">
                    <a href="inscriptions.php" class="btn-action-card btn-back">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <a href="?edit=<?php echo $_GET['id']; ?>" class="btn-action-card btn-edit">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="recu.php?id=<?php echo $_GET['id']; ?>" class="btn-action-card btn-receipt">
                        <i class="fas fa-receipt"></i> Voir le reçu
                    </a>
                    <a href="?delete=<?php echo $_GET['id']; ?>" class="btn-action-card btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription ? Cette action est irréversible.')">
                        <i class="fas fa-trash"></i> Supprimer
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
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
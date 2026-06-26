<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Inscription.php';
require_once __DIR__ . '/../classes/Apprenant.php';
require_once __DIR__ . '/../classes/Filiere.php';

$page_title = "Reçu d'Inscription";

$database = new Database();
$db = $database->connect();
$inscription = new Inscription($db);
$apprenant = new Apprenant($db);
$filiere = new Filiere($db);

if (!isset($_GET['id'])) {
    header('Location: inscriptions.php');
    exit;
}

$id_inscription = (int)$_GET['id'];
$detail = $inscription->getById($id_inscription);

if (!$detail) {
    $_SESSION['error'] = 'Inscription non trouvée';
    header('Location: inscriptions.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<style>
    /* Style pour l'impression */
    @media print {
        body * {
            visibility: hidden;
        }
        #receipt-content, #receipt-content * {
            visibility: visible;
        }
        #receipt-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
            background: white;
        }
        .no-print {
            display: none !important;
        }
        .receipt-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
        .receipt-header {
            border-bottom: 2px solid #333 !important;
        }
        .receipt-footer {
            border-top: 2px solid #333 !important;
        }
        .receipt-watermark {
            opacity: 0.05 !important;
        }
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
        max-width: 800px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .receipt-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        overflow: hidden;
        animation: slideDown 0.6s ease-out;
        position: relative;
    }

    .receipt-card .receipt-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 120px;
        font-weight: 900;
        color: #667eea;
        opacity: 0.05;
        pointer-events: none;
        white-space: nowrap;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .receipt-header {
        background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
        padding: 30px 40px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 4px solid #667eea;
        position: relative;
        z-index: 1;
    }

    .receipt-header .logo-section {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .receipt-header .logo-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: white;
    }

    .receipt-header .company-name {
        font-size: 22px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .receipt-header .company-sub {
        font-size: 12px;
        opacity: 0.7;
    }

    .receipt-header .receipt-title {
        text-align: right;
    }

    .receipt-header .receipt-title h2 {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .receipt-header .receipt-number {
        font-size: 13px;
        opacity: 0.8;
    }

    .receipt-body {
        padding: 30px 40px;
        position: relative;
        z-index: 1;
    }

    .receipt-title-section {
        text-align: center;
        margin-bottom: 30px;
    }

    .receipt-title-section h3 {
        font-size: 20px;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .receipt-title-section .subtitle {
        font-size: 13px;
        color: #a0aec0;
        margin-top: 5px;
    }

    .receipt-divider {
        border: none;
        border-top: 2px dashed #e2e8f0;
        margin: 20px 0;
    }

    .receipt-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .receipt-info-item {
        padding: 12px 16px;
        background: #f7fafc;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .receipt-info-item:hover {
        background: #edf2f7;
    }

    .receipt-info-item .label {
        font-size: 11px;
        color: #a0aec0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .receipt-info-item .value {
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
        margin-top: 4px;
    }

    .receipt-info-item .value.text-green { color: #48bb78; }
    .receipt-info-item .value.text-blue { color: #667eea; }
    .receipt-info-item .value.text-purple { color: #764ba2; }

    .receipt-amount-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 20px 30px;
        margin: 20px 0;
        text-align: center;
        color: white;
    }

    .receipt-amount-box .amount-label {
        font-size: 14px;
        opacity: 0.9;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .receipt-amount-box .amount-value {
        font-size: 36px;
        font-weight: 700;
        margin-top: 5px;
    }

    .receipt-amount-box .amount-value .currency {
        font-size: 24px;
        font-weight: 600;
        opacity: 0.8;
    }

    .receipt-footer {
        padding: 20px 40px;
        background: #f7fafc;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        position: relative;
        z-index: 1;
    }

    .receipt-footer .signature {
        text-align: center;
        flex: 1;
    }

    .receipt-footer .signature .line {
        width: 180px;
        border-top: 1px solid #a0aec0;
        margin: 8px auto;
    }

    .receipt-footer .signature .label {
        font-size: 11px;
        color: #a0aec0;
        font-weight: 500;
    }

    .receipt-footer .footer-info {
        text-align: center;
        font-size: 11px;
        color: #a0aec0;
    }

    .receipt-footer .footer-info i {
        margin-right: 4px;
    }

    .btn-group {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .btn-print {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
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
    }

    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(72, 187, 120, 0.3);
    }

    .btn-back {
        background: #e2e8f0;
        color: #4a5568;
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

    .btn-back:hover {
        background: #cbd5e0;
        transform: translateY(-2px);
    }

    .btn-pdf {
        background: linear-gradient(135deg, #fc8181 0%, #e53e3e 100%);
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
    }

    .btn-pdf:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(252, 129, 129, 0.3);
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
        .receipt-header {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .receipt-header .receipt-title {
            text-align: center;
            margin-top: 15px;
        }

        .receipt-body {
            padding: 20px;
        }

        .receipt-info-grid {
            grid-template-columns: 1fr;
        }

        .receipt-footer {
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .receipt-footer .signature .line {
            width: 150px;
        }

        .btn-group {
            flex-direction: column;
        }

        .btn-group .btn-print,
        .btn-group .btn-back,
        .btn-group .btn-pdf {
            width: 100%;
            justify-content: center;
        }

        .receipt-amount-box .amount-value {
            font-size: 28px;
        }

        .receipt-card .receipt-watermark {
            font-size: 60px;
        }
    }

    @media print {
        .btn-group, .no-print, .toast-container {
            display: none !important;
        }
        
        .receipt-card {
            border-radius: 0 !important;
            box-shadow: none !important;
        }
        
        .receipt-header {
            background: #1a202c !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .receipt-header .logo-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .receipt-header .receipt-title h2 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
        }
        
        .receipt-amount-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .receipt-footer {
            background: #f7fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .receipt-info-item {
            background: #f7fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
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

        <div id="receipt-content">
            <div class="receipt-card">
                <!-- Watermark -->
                <div class="receipt-watermark">RECU</div>

                <!-- Header -->
                <div class="receipt-header">
                    <div class="logo-section">
                        <div class="logo-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <div class="company-name">GESTION FORMATION</div>
                            <div class="company-sub">Centre de Formation Professionnelle</div>
                            <div class="company-sub" style="margin-top: 2px;">
                                <i class="fas fa-map-marker-alt me-1"></i> Nord-kivu, Goma
                            </div>
                        </div>
                    </div>
                    <div class="receipt-title">
                        <h2>REÇU D'INSCRIPTION</h2>
                        <div class="receipt-number">
                            <i class="fas fa-hashtag"></i>
                            N° <?php echo str_pad($id_inscription, 6, '0', STR_PAD_LEFT); ?>
                        </div>
                        <div class="receipt-number" style="margin-top: 3px;">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo date('d/m/Y'); ?>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="receipt-body">
                    <!-- Titre -->
                    <div class="receipt-title-section">
                        <h3>Attestation d'Inscription</h3>
                        <div class="subtitle">Ce document certifie l'inscription de l'apprenant</div>
                    </div>

                    <hr class="receipt-divider">

                    <!-- Informations -->
                    <div class="receipt-info-grid">
                        <div class="receipt-info-item">
                            <div class="label"><i class="fas fa-user"></i> Apprenant</div>
                            <div class="value"><?php echo htmlspecialchars($detail['prenom'] . ' ' . $detail['nom']); ?></div>
                        </div>
                        <div class="receipt-info-item">
                            <div class="label"><i class="fas fa-id-card"></i> ID Apprenant</div>
                            <div class="value">#<?php echo str_pad($detail['id_apprenant'], 4, '0', STR_PAD_LEFT); ?></div>
                        </div>
                        <div class="receipt-info-item">
                            <div class="label"><i class="fas fa-graduation-cap"></i> Filière</div>
                            <div class="value text-purple"><?php echo htmlspecialchars($detail['filiere_nom']); ?></div>
                        </div>
                        <div class="receipt-info-item">
                            <div class="label"><i class="fas fa-calendar-alt"></i> Date d'inscription</div>
                            <div class="value text-blue"><?php echo date('d/m/Y', strtotime($detail['date_inscription'])); ?></div>
                        </div>
                        <?php if (isset($detail['telephone']) && !empty($detail['telephone'])): ?>
                        <div class="receipt-info-item">
                            <div class="label"><i class="fas fa-phone"></i> Téléphone</div>
                            <div class="value"><?php echo htmlspecialchars($detail['telephone']); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($detail['email']) && !empty($detail['email'])): ?>
                        <div class="receipt-info-item">
                            <div class="label"><i class="fas fa-envelope"></i> Email</div>
                            <div class="value"><?php echo htmlspecialchars($detail['email']); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($detail['adresse']) && !empty($detail['adresse'])): ?>
                        <div class="receipt-info-item" style="grid-column: span 2;">
                            <div class="label"><i class="fas fa-map-marker-alt"></i> Adresse</div>
                            <div class="value"><?php echo htmlspecialchars($detail['adresse']); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Montant -->
                    <div class="receipt-amount-box">
                        <div class="amount-label">
                            <i class="fas fa-dollar-sign me-2"></i>
                            Frais d'inscription
                        </div>
                        <div class="amount-value">
                            <span class="currency">$</span>
                            <?php echo number_format($detail['frais_inscription'], 0, ',', ' '); ?>
                        </div>
                        <div style="font-size: 13px; opacity: 0.8; margin-top: 5px;">
                            Montant total de l'inscription
                        </div>
                    </div>

                    <!-- Mention -->
                    <div style="text-align: center; padding: 10px 0; font-size: 13px; color: #718096;">
                        <i class="fas fa-check-circle" style="color: #48bb78;"></i>
                        Inscription validée le <?php echo date('d/m/Y à H:i', strtotime($detail['date_inscription'])); ?>
                    </div>
                </div>

                <!-- Footer -->
                <div class="receipt-footer">
                    <div class="signature">
                        <div class="line"></div>
                        <div class="label">Signature de l'apprenant</div>
                    </div>
                    <div class="footer-info">
                        <div>
                            <i class="fas fa-print"></i>
                            Imprimé le <?php echo date('d/m/Y à H:i'); ?>
                        </div>
                        <div style="margin-top: 3px;">
                            <i class="fas fa-qrcode"></i>
                            Document officiel
                        </div>
                    </div>
                    <div class="signature">
                        <div class="line"></div>
                        <div class="label">Signature du responsable</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="btn-group no-print">
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i>
                Imprimer le reçu
            </button>
            <button onclick="generatePDF()" class="btn-pdf">
                <i class="fas fa-file-pdf"></i>
                Télécharger PDF
            </button>
            <a href="inscription-detail.php?id=<?php echo $id_inscription; ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function generatePDF() {
        const element = document.getElementById('receipt-content');
        const opt = {
            margin:        [10, 10, 10, 10],
            filename:     'recu-inscription-<?php echo date('Ymd') . '-' . $id_inscription; ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, letterRendering: true, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        
        const btn = document.querySelector('.btn-pdf');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération en cours...';
        btn.disabled = true;
        
        html2pdf().set(opt).from(element).save().then(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }).catch(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('Erreur lors de la génération du PDF. Veuillez réessayer.');
        });
    }

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
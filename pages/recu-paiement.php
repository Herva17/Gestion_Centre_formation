<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Paiement.php';
require_once __DIR__ . '/../classes/Inscription.php';
require_once __DIR__ . '/../classes/Apprenant.php';
require_once __DIR__ . '/../classes/Filiere.php';

$page_title = 'Reçu de Paiement';
$page_icon = 'receipt';

$database = new Database();
$db = $database->connect();
$paiement = new Paiement($db);
$inscription = new Inscription($db);
$apprenant = new Apprenant($db);
$filiere = new Filiere($db);

if (!isset($_GET['id'])) {
    header('Location: paiements.php');
    exit;
}

$id_paiement = (int)$_GET['id'];
$pay = $paiement->getById($id_paiement);

if (!$pay) {
    $_SESSION['error'] = 'Paiement non trouvé';
    header('Location: paiements.php');
    exit;
}

// Récupérer les infos complètes
$detail_inscription = $inscription->getById($pay['id_inscription']);
$apprenant_info = $apprenant->getById($detail_inscription['id_apprenant']);
$filiere_info = $filiere->getById($detail_inscription['id_filiere']);

include __DIR__ . '/../includes/header.php';
?>

<style>
    @media print {
        body * { visibility: hidden; }
        #receipt-content, #receipt-content * { visibility: visible; }
        #receipt-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
            background: white;
        }
        .no-print { display: none !important; }
        .receipt-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }

    .receipt-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        overflow: hidden;
        max-width: 800px;
        margin: 0 auto;
        animation: slideDown 0.6s ease-out;
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
        border-bottom: 4px solid #ed8936;
    }

    .receipt-header .logo-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
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
    }

    .receipt-header .receipt-title h2 {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .receipt-body {
        padding: 30px 40px;
    }

    .receipt-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .receipt-info-item .label {
        font-size: 12px;
        color: #a0aec0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .receipt-info-item .value {
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
        margin-top: 4px;
    }

    .receipt-amount-box {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        border-radius: 12px;
        padding: 25px 30px;
        text-align: center;
        color: white;
        margin: 20px 0;
    }

    .receipt-amount-box .amount-value {
        font-size: 42px;
        font-weight: 700;
    }

    .receipt-amount-box .amount-label {
        font-size: 14px;
        opacity: 0.9;
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
    }

    .receipt-footer .signature .line {
        width: 180px;
        border-top: 1px solid #a0aec0;
        margin: 8px auto;
    }

    .receipt-footer .signature .label {
        font-size: 11px;
        color: #a0aec0;
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

    @media (max-width: 768px) {
        .receipt-header {
            flex-direction: column;
            text-align: center;
            padding: 20px;
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
        }
        .btn-group {
            flex-direction: column;
        }
        .btn-group .btn-print,
        .btn-group .btn-back {
            width: 100%;
            justify-content: center;
        }
        .receipt-amount-box .amount-value {
            font-size: 32px;
        }
    }
</style>

<div class="content-wrapper" style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <div id="receipt-content">
        <div class="receipt-card">
            <!-- Header -->
            <div class="receipt-header">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div>
                        <div class="company-name">UMOJA MAENDELEO</div>
                        <div style="font-size: 12px; opacity: 0.7;">Centre de Formation</div>
                    </div>
                </div>
                <div class="receipt-title">
                    <h2>REÇU DE PAIEMENT</h2>
                    <div style="font-size: 13px; opacity: 0.8;">
                        <i class="fas fa-hashtag"></i> N° <?php echo str_pad($id_paiement, 6, '0', STR_PAD_LEFT); ?>
                    </div>
                    <div style="font-size: 13px; opacity: 0.8;">
                        <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($pay['date_paiement'] ?? 'now')); ?>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="receipt-body">
                <div class="receipt-info-grid">
                    <div>
                        <div class="receipt-info-item">
                            <div class="label">Apprenant</div>
                            <div class="value"><?php echo htmlspecialchars($apprenant_info['prenom'] . ' ' . $apprenant_info['nom']); ?></div>
                        </div>
                        <div class="receipt-info-item" style="margin-top: 10px;">
                            <div class="label">ID Apprenant</div>
                            <div class="value">#<?php echo str_pad($apprenant_info['id_apprenant'], 4, '0', STR_PAD_LEFT); ?></div>
                        </div>
                    </div>
                    <div>
                        <div class="receipt-info-item">
                            <div class="label">Filière</div>
                            <div class="value"><?php echo htmlspecialchars($filiere_info['nom']); ?></div>
                        </div>
                        <div class="receipt-info-item" style="margin-top: 10px;">
                            <div class="label">Mois</div>
                            <div class="value"><?php echo htmlspecialchars($pay['mois'] ?? 'N/A'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Montant -->
                <div class="receipt-amount-box">
                    <div class="amount-label">
                        <i class="fas fa-dollar-sign me-2"></i>
                        Montant payé
                    </div>
                    <div class="amount-value">$<?php echo number_format($pay['montant'], 0, ',', ' '); ?></div>
                    <div style="font-size: 13px; opacity: 0.8; margin-top: 5px;">
                        Type: <?php echo htmlspecialchars($pay['type'] ?? 'Espèces'); ?>
                    </div>
                </div>

                <div style="text-align: center; padding: 10px 0; font-size: 13px; color: #718096;">
                    <i class="fas fa-check-circle" style="color: #48bb78;"></i>
                    Paiement validé le <?php echo date('d/m/Y à H:i', strtotime($pay['date_paiement'] ?? 'now')); ?>
                </div>
            </div>

            <!-- Footer -->
            <div class="receipt-footer">
                <div class="signature">
                    <div class="line"></div>
                    <div class="label">Signature de l'apprenant</div>
                </div>
                <div style="text-align: center; font-size: 12px; color: #a0aec0;">
                    <i class="fas fa-print me-2"></i>
                    Imprimé le <?php echo date('d/m/Y à H:i'); ?>
                </div>
                <div class="signature">
                    <div class="line"></div>
                    <div class="label">Signature du responsable</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons -->
    <div class="btn-group no-print">
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Imprimer
        </button>
        <a href="paiements.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
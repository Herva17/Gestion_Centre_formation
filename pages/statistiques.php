<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Apprenant.php';
require_once __DIR__ . '/../classes/Filiere.php';
require_once __DIR__ . '/../classes/Inscription.php';
require_once __DIR__ . '/../classes/Paiement.php';
require_once __DIR__ . '/../classes/Cours.php';

$page_title = 'Statistiques Détaillées';
$page_icon = 'chart-pie';

$database = new Database();
$db = $database->connect();

$apprenant = new Apprenant($db);
$filiere = new Filiere($db);
$inscription = new Inscription($db);
$paiement = new Paiement($db);
$cours = new Cours($db);

// Get all data
$all_apprenants = $apprenant->getAll();
$all_filieres = $filiere->getAll();
$all_inscriptions = $inscription->getAll();
$all_paiements = $paiement->getAll();
$all_cours = $cours->getAll();

// Calculate statistics
$stats = [
    'total_apprenants' => count($all_apprenants),
    'total_filieres' => count($all_filieres),
    'total_inscriptions' => count($all_inscriptions),
    'total_paiements' => count($all_paiements),
    'total_cours' => count($all_cours),
    'total_revenue' => array_sum(array_column($all_paiements, 'montant')),
    'avg_frais_inscription' => array_sum(array_column($all_inscriptions, 'frais_inscription')) / max(count($all_inscriptions), 1),
];

// Trouver la filière la plus populaire
$filiere_counts = array_count_values(array_column($all_inscriptions, 'id_filiere'));
arsort($filiere_counts);
$top_filiere_id = key($filiere_counts);
$top_filiere_nom = '';
foreach ($all_filieres as $f) {
    if ($f['id_filiere'] == $top_filiere_id) {
        $top_filiere_nom = $f['nom'];
        break;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 22px 25px;
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

    .stat-card-blue .stat-number { color: #4facfe; }
    .stat-card-green .stat-number { color: #48bb78; }
    .stat-card-purple .stat-number { color: #764ba2; }
    .stat-card-orange .stat-number { color: #ed8936; }
    .stat-card-red .stat-number { color: #fc8181; }
    .stat-card-pink .stat-number { color: #fa709a; }

    .chart-container {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .chart-container:hover {
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        transform: translateY(-3px);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f7fafc;
    }

    .chart-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1a202c;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-header .badge-count {
        background: #f7fafc;
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 12px;
        color: #718096;
        font-weight: 600;
    }

    .chart-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    .list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #f7fafc;
        border-radius: 10px;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .list-item:hover {
        background: #edf2f7;
        transform: translateX(5px);
    }

    .list-item .item-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .list-item .item-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 14px;
    }

    .list-item .item-name {
        font-weight: 600;
        color: #2d3748;
    }

    .list-item .item-badge {
        padding: 3px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
    }

    .list-item .item-badge.active {
        background: rgba(72, 187, 120, 0.15);
        color: #48bb78;
    }

    .list-item .item-badge.count {
        background: rgba(102, 126, 234, 0.15);
        color: #667eea;
    }

    .list-item .item-count {
        font-weight: 700;
        color: #667eea;
        font-size: 16px;
    }

    .payment-methods {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
    }

    .payment-method-card {
        padding: 18px 20px;
        border-radius: 12px;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .payment-method-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .payment-method-card .method-icon {
        font-size: 30px;
        margin-bottom: 8px;
    }

    .payment-method-card .method-name {
        font-size: 14px;
        font-weight: 600;
        color: #2d3748;
    }

    .payment-method-card .method-count {
        font-size: 24px;
        font-weight: 700;
        margin-top: 5px;
    }

    .payment-method-card.method-blue { background: #ebf5ff; border-color: #4facfe; }
    .payment-method-card.method-blue .method-count { color: #4facfe; }

    .payment-method-card.method-green { background: #f0fff4; border-color: #48bb78; }
    .payment-method-card.method-green .method-count { color: #48bb78; }

    .payment-method-card.method-purple { background: #f5f0ff; border-color: #764ba2; }
    .payment-method-card.method-purple .method-count { color: #764ba2; }

    .payment-method-card.method-orange { background: #fffaf0; border-color: #ed8936; }
    .payment-method-card.method-orange .method-count { color: #ed8936; }

    .export-section {
        background: white;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .export-section .export-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .export-section .export-header .icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .btn-export {
        padding: 12px 25px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(72, 187, 120, 0.3);
    }

    .btn-export-excel {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }

    .btn-export-excel:hover {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
    }

    .btn-export-pdf {
        background: linear-gradient(135deg, #fc8181 0%, #e53e3e 100%);
        color: white;
    }

    .btn-export-pdf:hover {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
    }

    .btn-export-print {
        background: linear-gradient(135deg, #4facfe 0%, #3182ce 100%);
        color: white;
    }

    .btn-export-print:hover {
        background: linear-gradient(135deg, #3182ce 0%, #2b6cb0 100%);
    }

    .export-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 15px;
    }

    @media (max-width: 992px) {
        .chart-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .payment-methods {
            grid-template-columns: repeat(2, 1fr);
        }

        .export-buttons {
            flex-direction: column;
        }

        .btn-export {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .payment-methods {
            grid-template-columns: 1fr;
        }

        .chart-header {
            flex-direction: column;
            text-align: center;
            gap: 10px;
        }

        .list-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }
</style>

<div class="page-wrapper">
    <div class="content-wrapper">

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card stat-card-blue">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-number"><?php echo number_format($stats['total_apprenants']); ?></div>
                <div class="stat-label">Total Apprenants</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +12%</span>
            </div>

            <div class="stat-card stat-card-green">
                <i class="fas fa-book stat-icon"></i>
                <div class="stat-number"><?php echo number_format($stats['total_filieres']); ?></div>
                <div class="stat-label">Total Filières</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +8%</span>
            </div>

            <div class="stat-card stat-card-purple">
                <i class="fas fa-clipboard-list stat-icon"></i>
                <div class="stat-number"><?php echo number_format($stats['total_inscriptions']); ?></div>
                <div class="stat-label">Total Inscriptions</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +15%</span>
            </div>

            <div class="stat-card stat-card-orange">
                <i class="fas fa-credit-card stat-icon"></i>
                <div class="stat-number"><?php echo number_format($stats['total_paiements']); ?></div>
                <div class="stat-label">Total Paiements</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +22%</span>
            </div>

            <div class="stat-card stat-card-red">
                <i class="fas fa-chalkboard stat-icon"></i>
                <div class="stat-number"><?php echo number_format($stats['total_cours']); ?></div>
                <div class="stat-label">Total Cours</div>
                <span class="stat-change neutral"><i class="fas fa-minus"></i> Stable</span>
            </div>

            <div class="stat-card stat-card-pink">
                <i class="fas fa-dollar-sign stat-icon"></i>
                <div class="stat-number">$<?php echo number_format($stats['total_revenue'], 0, ',', ' '); ?></div>
                <div class="stat-label">Revenu Total</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +18%</span>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid" style="margin-bottom: 30px;">
            <div class="stat-card">
                <i class="fas fa-chart-line stat-icon"></i>
                <div class="stat-number"><?php echo $stats['total_apprenants'] > 0 ? round(($stats['total_inscriptions'] / $stats['total_apprenants']) * 100) : 0; ?>%</div>
                <div class="stat-label">Taux d'Inscription</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +5%</span>
            </div>

            <div class="stat-card">
                <i class="fas fa-calculator stat-icon"></i>
                <div class="stat-number">$<?php echo number_format($stats['avg_frais_inscription'], 0, ',', ' '); ?></div>
                <div class="stat-label">Frais Moyen par Inscription</div>
                <span class="stat-change neutral"><i class="fas fa-minus"></i> Stable</span>
            </div>

            <div class="stat-card">
                <i class="fas fa-star stat-icon"></i>
                <div class="stat-number" style="font-size: 22px; line-height: 1.3;"><?php echo htmlspecialchars($top_filiere_nom ?: 'N/A'); ?></div>
                <div class="stat-label">Filière la Plus Populaire</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> <?php echo isset($filiere_counts[$top_filiere_id]) ? $filiere_counts[$top_filiere_id] . ' inscriptions' : '0'; ?></span>
            </div>

            <div class="stat-card">
                <i class="fas fa-user-graduate stat-icon"></i>
                <div class="stat-number"><?php echo $stats['total_apprenants'] > 0 ? round(($stats['total_paiements'] / $stats['total_apprenants']) * 100) : 0; ?>%</div>
                <div class="stat-label">Taux de Paiement</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> +3%</span>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="chart-grid">
            <!-- Derniers Apprenants -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3><i class="fas fa-user-plus" style="color: #4facfe;"></i> Derniers Apprenants</h3>
                    <span class="badge-count">5 derniers</span>
                </div>
                <div class="space-y-3">
                    <?php $recent = array_slice($all_apprenants, -5); 
                    $colors = ['#4facfe', '#48bb78', '#764ba2', '#ed8936', '#fc8181'];
                    foreach ($recent as $index => $app): ?>
                    <div class="list-item" style="border-left-color: <?php echo $colors[$index % count($colors)]; ?>;">
                        <div class="item-info">
                            <div class="item-avatar" style="background: <?php echo $colors[$index % count($colors)]; ?>;">
                                <?php echo strtoupper(substr($app['prenom'] ?? 'A', 0, 1) . substr($app['nom'] ?? '', 0, 1)); ?>
                            </div>
                            <div>
                                <div class="item-name"><?php echo htmlspecialchars($app['prenom'] . ' ' . $app['nom']); ?></div>
                                <div style="font-size: 12px; color: #718096;">
                                    <i class="fas fa-id-card"></i> #<?php echo str_pad($app['id_apprenant'], 4, '0', STR_PAD_LEFT); ?>
                                </div>
                            </div>
                        </div>
                        <span class="item-badge active"><i class="fas fa-circle" style="font-size: 8px; margin-right: 4px;"></i>Actif</span>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recent)): ?>
                    <div style="text-align: center; padding: 20px; color: #a0aec0;">
                        <i class="fas fa-inbox" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                        Aucun apprenant
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top Cours -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-bar" style="color: #ed8936;"></i> Top Cours</h3>
                    <span class="badge-count">5 plus populaires</span>
                </div>
                <div class="space-y-3">
                    <?php 
                    $course_counts = array_count_values(array_column($all_inscriptions, 'id_filiere'));
                    arsort($course_counts);
                    $top = array_slice($course_counts, 0, 5, true);
                    $colors = ['#4facfe', '#48bb78', '#764ba2', '#ed8936', '#fc8181'];
                    $index = 0;
                    foreach ($top as $course_id => $count): 
                        $filiere_nom = '';
                        foreach ($all_filieres as $f) {
                            if ($f['id_filiere'] == $course_id) {
                                $filiere_nom = $f['nom'];
                                break;
                            }
                        }
                    ?>
                    <div class="list-item" style="border-left-color: <?php echo $colors[$index % count($colors)]; ?>;">
                        <div class="item-info">
                            <div style="width: 35px; height: 35px; border-radius: 50%; background: <?php echo $colors[$index % count($colors)]; ?>20; display: flex; align-items: center; justify-content: center; color: <?php echo $colors[$index % count($colors)]; ?>; font-weight: 700; font-size: 14px;">
                                <?php echo $index + 1; ?>
                            </div>
                            <div>
                                <div class="item-name"><?php echo htmlspecialchars(substr($filiere_nom, 0, 25)) . (strlen($filiere_nom) > 25 ? '...' : ''); ?></div>
                                <div style="font-size: 12px; color: #718096;">
                                    <i class="fas fa-users"></i> <?php echo $count; ?> inscriptions
                                </div>
                            </div>
                        </div>
                        <span class="item-count"><?php echo $count; ?></span>
                    </div>
                    <?php $index++; endforeach; ?>
                    <?php if (empty($top)): ?>
                    <div style="text-align: center; padding: 20px; color: #a0aec0;">
                        <i class="fas fa-inbox" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                        Aucune inscription
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="chart-container" style="margin-bottom: 30px;">
            <div class="chart-header">
                <h3><i class="fas fa-credit-card" style="color: #48bb78;"></i> Distribution des Méthodes de Paiement</h3>
                <span class="badge-count"><?php echo count($all_paiements); ?> paiements</span>
            </div>
            <div class="payment-methods">
                <?php 
                $types = array_count_values(array_filter(array_column($all_paiements, 'type')));
                $method_config = [
                    'Espèces' => ['icon' => 'fa-money-bill-wave', 'color' => 'blue', 'class' => 'method-blue'],
                    'Chèque' => ['icon' => 'fa-file-invoice', 'color' => 'green', 'class' => 'method-green'],
                    'Virement' => ['icon' => 'fa-exchange-alt', 'color' => 'purple', 'class' => 'method-purple'],
                    'Carte' => ['icon' => 'fa-credit-card', 'color' => 'orange', 'class' => 'method-orange'],
                ];
                foreach ($types as $type => $count): 
                    $config = $method_config[$type] ?? ['icon' => 'fa-circle', 'color' => 'gray', 'class' => 'method-blue'];
                ?>
                <div class="payment-method-card <?php echo $config['class']; ?>">
                    <div class="method-icon">
                        <i class="fas <?php echo $config['icon']; ?>" style="color: <?php echo $config['color'] === 'blue' ? '#4facfe' : ($config['color'] === 'green' ? '#48bb78' : ($config['color'] === 'purple' ? '#764ba2' : '#ed8936')); ?>;"></i>
                    </div>
                    <div class="method-name"><?php echo htmlspecialchars($type); ?></div>
                    <div class="method-count"><?php echo $count; ?></div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($types)): ?>
                <div style="text-align: center; padding: 30px; color: #a0aec0; grid-column: 1 / -1;">
                    <i class="fas fa-credit-card" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                    Aucun paiement enregistré
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Export Section -->
        <div class="export-section">
            <div class="export-header">
                <div class="icon">
                    <i class="fas fa-file-export"></i>
                </div>
                <div>
                    <h3 style="font-size: 18px; font-weight: 700; color: #1a202c; margin: 0;">Exportation des Données</h3>
                    <p style="color: #718096; margin: 5px 0 0 0; font-size: 14px;">
                        Téléchargez les données au format Excel, PDF ou imprimez-les pour une meilleure analyse
                    </p>
                </div>
            </div>
            <div class="export-buttons">
                <button class="btn-export btn-export-excel" onclick="alert('Export Excel - Apprenants')">
                    <i class="fas fa-file-excel"></i> Exporter Apprenants
                </button>
                <button class="btn-export btn-export-excel" onclick="alert('Export Excel - Paiements')">
                    <i class="fas fa-file-excel"></i> Exporter Paiements
                </button>
                <button class="btn-export btn-export-excel" onclick="alert('Export Excel - Inscriptions')">
                    <i class="fas fa-file-excel"></i> Exporter Inscriptions
                </button>
                <button class="btn-export btn-export-pdf" onclick="alert('Export PDF - Rapport complet')">
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </button>
                <button class="btn-export btn-export-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
    // Animation au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.stat-card, .chart-container, .export-section');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = `all 0.5s ease ${index * 0.05}s`;
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
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
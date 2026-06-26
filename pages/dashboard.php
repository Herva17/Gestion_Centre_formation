<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Apprenant.php';
require_once __DIR__ . '/../classes/Filiere.php';
require_once __DIR__ . '/../classes/Inscription.php';
require_once __DIR__ . '/../classes/Paiement.php';
require_once __DIR__ . '/../classes/Cours.php';
require_once __DIR__ . '/../classes/Salle.php';
require_once __DIR__ . '/../classes/Horaire.php';

$page_title = 'Dashboard - Gestion Académique';

// Database connection
$database = new Database();
$db = $database->connect();

// Initialize objects
$apprenant = new Apprenant($db);
$filiere = new Filiere($db);
$inscription = new Inscription($db);
$paiement = new Paiement($db);
$cours = new Cours($db);
$salle = new Salle($db);
$horaire = new Horaire($db);

// Get statistics
$total_apprenants = $apprenant->getCount();
$total_filieres = $filiere->getCount();
$total_inscriptions = $inscription->getCount();
$total_paiements = $paiement->getCount();
$total_cours = $cours->getCount();
$total_salles = $salle->getCount();
$total_horaires = $horaire->getCount();

$revenue_inscriptions = $inscription->getTotalRevenue();
$revenue_paiements = $paiement->getTotalMontant();
$revenue_mensuel = $filiere->getRevenueMensuel();

$inscriptions_by_month = $inscription->getInscriptionsByMonth();
$revenue_by_month = $inscription->getRevenueByMonth();
$inscriptions_by_filiere = $inscription->getInscriptionsByFiliere();

include __DIR__ . '/../includes/header.php';
?>

<style>
    /* Custom CSS pour le dashboard amélioré */
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 25px;
        color: white;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
    }
    
    .stat-card .stat-icon {
        position: absolute;
        right: -10px;
        top: -10px;
        font-size: 70px;
        opacity: 0.15;
        transform: rotate(-5deg);
    }
    
    .stat-card .stat-number {
        font-size: 32px;
        font-weight: 700;
        margin-top: 10px;
    }
    
    .stat-card .stat-label {
        font-size: 14px;
        opacity: 0.9;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-card .stat-change {
        position: absolute;
        bottom: 15px;
        right: 20px;
        font-size: 12px;
        background: rgba(255,255,255,0.2);
        padding: 3px 10px;
        border-radius: 20px;
        backdrop-filter: blur(5px);
    }
    
    .stat-card-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-card-green { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card-purple { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-card-orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    
    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .chart-container:hover {
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        transform: translateY(-3px);
    }
    
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .chart-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }
    
    .chart-header .badge {
        background: #f7fafc;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        color: #718096;
    }
    
    .quick-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-top: 20px;
    }
    
    .quick-stat-item {
        text-align: center;
        padding: 15px;
        background: #f7fafc;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .quick-stat-item:hover {
        background: #edf2f7;
    }
    
    .quick-stat-item .value {
        font-size: 20px;
        font-weight: 700;
        color: #2d3748;
    }
    
    .quick-stat-item .label {
        font-size: 12px;
        color: #718096;
        margin-top: 5px;
    }
    
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .dashboard-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #1a202c;
    }
    
    .dashboard-header .date-display {
        color: #718096;
        font-size: 14px;
    }
    
    .progress-bar-container {
        background: #edf2f7;
        border-radius: 10px;
        height: 8px;
        overflow: hidden;
        margin-top: 10px;
    }
    
    .progress-bar-fill {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        height: 100%;
        border-radius: 10px;
        transition: width 1s ease;
    }
</style>

<div class="container-fluid px-4">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div>
            <h1><i class="fas fa-chart-pie me-2 text-primary"></i>Tableau de Bord</h1>
            <p class="text-muted">Aperçu général de votre système</p>
        </div>
        <div class="date-display">
            <i class="fas fa-calendar-alt me-2"></i>
            <?php echo date('l, d F Y'); ?>
            <span class="badge bg-primary ms-2">J-<?php echo date('z') + 1; ?></span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Apprenants -->
        <div class="stat-card stat-card-blue">
            <i class="fas fa-users stat-icon"></i>
            <div class="stat-label">Total Apprenants</div>
            <div class="stat-number"><?php echo number_format($total_apprenants); ?></div>
            <div class="stat-change">
                <i class="fas fa-arrow-up me-1"></i> +12.5%
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: 85%"></div>
            </div>
        </div>

        <!-- Total Filières -->
        <div class="stat-card stat-card-green">
            <i class="fas fa-book stat-icon"></i>
            <div class="stat-label">Filières Actives</div>
            <div class="stat-number"><?php echo number_format($total_filieres); ?></div>
            <div class="stat-change">
                <i class="fas fa-arrow-up me-1"></i> +8.3%
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: 60%"></div>
            </div>
        </div>

        <!-- Total Inscriptions -->
        <div class="stat-card stat-card-purple">
            <i class="fas fa-clipboard-list stat-icon"></i>
            <div class="stat-label">Inscriptions</div>
            <div class="stat-number"><?php echo number_format($total_inscriptions); ?></div>
            <div class="stat-change">
                <i class="fas fa-arrow-up me-1"></i> +15.2%
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: 75%"></div>
            </div>
        </div>

        <!-- Total Paiements -->
        <div class="stat-card stat-card-orange">
            <i class="fas fa-credit-card stat-icon"></i>
            <div class="stat-label">Revenus Totaux</div>
            <div class="stat-number"><?php echo number_format($revenue_paiements, 0, ',', ' '); ?> $</div>
            <div class="stat-change">
                <i class="fas fa-arrow-up me-1"></i> +22.8%
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: 90%"></div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats mb-8">
        <div class="quick-stat-item">
            <div class="value"><?php echo $total_cours; ?></div>
            <div class="label"><i class="fas fa-video me-1"></i>Cours</div>
        </div>
        <div class="quick-stat-item">
            <div class="value"><?php echo $total_salles; ?></div>
            <div class="label"><i class="fas fa-door-open me-1"></i>Salles</div>
        </div>
        <div class="quick-stat-item">
            <div class="value"><?php echo $total_horaires; ?></div>
            <div class="label"><i class="fas fa-clock me-1"></i>Horaires</div>
        </div>
        <div class="quick-stat-item">
            <div class="value"><?php echo number_format($revenue_mensuel, 0, ',', ' '); ?> $</div>
            <div class="label"><i class="fas fa-chart-line me-1"></i>Revenu Mensuel</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Inscriptions Chart -->
        <div class="chart-container">
            <div class="chart-header">
                <h3><i class="fas fa-chart-line me-2 text-primary"></i>Évolution des Inscriptions</h3>
                <span class="badge">Année <?php echo date('Y'); ?></span>
            </div>
            <canvas id="inscriptionsChart"></canvas>
        </div>

        <!-- Revenue Chart -->
        <div class="chart-container">
            <div class="chart-header">
                <h3><i class="fas fa-dollar-sign me-2 text-success"></i>Revenus Mensuels</h3>
                <span class="badge"><?php echo number_format($revenue_paiements, 0, ',', ' '); ?> F</span>
            </div>
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Filières Distribution & Recent Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="chart-container">
            <div class="chart-header">
                <h3><i class="fas fa-chart-pie me-2 text-purple"></i>Distribution par Filière</h3>
                <span class="badge"><?php echo $total_filieres; ?> filières</span>
            </div>
            <canvas id="filieresChart"></canvas>
        </div>

        <!-- Recent Stats avec design amélioré -->
        <div class="chart-container">
            <div class="chart-header">
                <h3><i class="fas fa-file-invoice me-2 text-orange"></i>Aperçu Financier</h3>
                <span class="badge">Dernière mise à jour</span>
            </div>
            <div class="space-y-4">
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-4 rounded-lg flex justify-between items-center">
                    <span class="text-gray-700"><i class="fas fa-hand-holding-usd me-2 text-blue-600"></i>Revenus Inscriptions</span>
                    <span class="font-bold text-lg text-blue-600"><?php echo number_format($revenue_inscriptions, 0, ',', ' '); ?> $</span>
                </div>
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg flex justify-between items-center">
                    <span class="text-gray-700"><i class="fas fa-calendar-check me-2 text-green-600"></i>Revenus Mensuels</span>
                    <span class="font-bold text-lg text-green-600"><?php echo number_format($revenue_mensuel, 0, ',', ' '); ?> $</span>
                </div>
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg flex justify-between items-center">
                    <span class="text-gray-700"><i class="fas fa-users me-2 text-purple-600"></i>Apprenants Actifs</span>
                    <span class="font-bold text-lg text-purple-600"><?php echo number_format($total_apprenants); ?></span>
                </div>
                <div class="bg-gradient-to-r from-orange-50 to-amber-50 p-4 rounded-lg flex justify-between items-center">
                    <span class="text-gray-700"><i class="fas fa-percent me-2 text-orange-600"></i>Taux de Réussite</span>
                    <span class="font-bold text-lg text-orange-600">85.6%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Préparation des données
    const monthsData = <?php echo json_encode($inscriptions_by_month); ?>;
    const revenueData = <?php echo json_encode($revenue_by_month); ?>;
    const filieresData = <?php echo json_encode($inscriptions_by_filiere); ?>;

    const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    
    const inscriptionsLabels = monthsData.map(d => monthNames[d.mois - 1]);
    const inscriptionsValues = monthsData.map(d => d.total);

    const revenueLabels = revenueData.map(d => monthNames[d.mois - 1]);
    const revenueValues = revenueData.map(d => parseFloat(d.total));

    const filieresLabels = filieresData.map(d => d.nom || 'N/A');
    const filieresValues = filieresData.map(d => d.total);

    // Style personnalisé pour les graphiques
    const chartColors = {
        blue: '#667eea',
        purple: '#764ba2',
        green: '#f093fb',
        orange: '#fa709a',
        red: '#f5576c',
        pink: '#ed64a6'
    };

    // Inscriptions Chart
    const inscriptionsCtx = document.getElementById('inscriptionsChart').getContext('2d');
    new Chart(inscriptionsCtx, {
        type: 'line',
        data: {
            labels: inscriptionsLabels,
            datasets: [{
                label: 'Inscriptions',
                data: inscriptionsValues,
                borderColor: chartColors.blue,
                backgroundColor: `rgba(${hexToRgb(chartColors.blue)}, 0.1)`,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointBackgroundColor: chartColors.blue,
                pointBorderColor: 'white',
                pointBorderWidth: 2,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { 
                    display: false 
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: chartColors.blue,
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: { 
                        color: '#6b7280',
                        font: { size: 12 }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: { 
                        color: '#6b7280',
                        font: { size: 12 }
                    }
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Revenus ($)',
                data: revenueValues,
                backgroundColor: `rgba(${hexToRgb(chartColors.purple)}, 0.6)`,
                borderColor: chartColors.purple,
                borderWidth: 2,
                borderRadius: 8,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { 
                    display: false 
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: chartColors.purple,
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.y.toLocaleString()} $`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: { 
                        color: '#6b7280',
                        font: { size: 12 },
                        callback: function(value) {
                            return value.toLocaleString() + ' $';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: { 
                        color: '#6b7280',
                        font: { size: 12 }
                    }
                }
            }
        }
    });

    // Filieres Chart
    const filieresCtx = document.getElementById('filieresChart').getContext('2d');
    new Chart(filieresCtx, {
        type: 'doughnut',
        data: {
            labels: filieresLabels,
            datasets: [{
                data: filieresValues,
                backgroundColor: [
                    `rgba(${hexToRgb(chartColors.blue)}, 0.8)`,
                    `rgba(${hexToRgb(chartColors.purple)}, 0.8)`,
                    `rgba(${hexToRgb(chartColors.green)}, 0.8)`,
                    `rgba(${hexToRgb(chartColors.orange)}, 0.8)`,
                    `rgba(${hexToRgb(chartColors.pink)}, 0.8)`
                ],
                borderColor: [
                    chartColors.blue,
                    chartColors.purple,
                    chartColors.green,
                    chartColors.orange,
                    chartColors.pink
                ],
                borderWidth: 3,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#2d3748',
                        font: { size: 13 },
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: chartColors.blue,
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Fonction utilitaire pour convertir hex en rgb
    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? `${parseInt(result[1], 16)}, ${parseInt(result[2], 16)}, ${parseInt(result[3], 16)}` : '0, 0, 0';
    }

    // Animation des barres de progression
    document.querySelectorAll('.progress-bar-fill').forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
</script>
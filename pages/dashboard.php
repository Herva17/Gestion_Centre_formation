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

$page_title = 'Dashboard';

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

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Apprenants -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 hover:shadow-lg transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Apprenants</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total_apprenants; ?></p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-users text-blue-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Filières -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500 hover:shadow-lg transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Filières</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total_filieres; ?></p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-book text-green-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Inscriptions -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500 hover:shadow-lg transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Inscriptions</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $total_inscriptions; ?></p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-purple-500 text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Total Paiements -->
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-orange-500 hover:shadow-lg transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Paiements</p>
                                <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo number_format($revenue_paiements, 0, ',', ' '); ?> XOF</p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                <i class="fas fa-credit-card text-orange-500 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Inscriptions Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Inscriptions par Mois</h3>
                        <canvas id="inscriptionsChart"></canvas>
                    </div>

                    <!-- Revenue Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Revenus par Mois</h3>
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Filières Distribution -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Distribution par Filière</h3>
                        <canvas id="filieresChart"></canvas>
                    </div>

                    <!-- Recent Stats -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Résumé</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b">
                                <span class="text-gray-600">Revenus Inscriptions</span>
                                <span class="font-bold text-lg text-blue-600"><?php echo number_format($revenue_inscriptions, 0, ',', ' '); ?> XOF</span>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b">
                                <span class="text-gray-600">Revenus Mensuels</span>
                                <span class="font-bold text-lg text-green-600"><?php echo number_format($revenue_mensuel, 0, ',', ' '); ?> XOF</span>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b">
                                <span class="text-gray-600">Total Cours</span>
                                <span class="font-bold text-lg text-purple-600"><?php echo $total_cours; ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Salles</span>
                                <span class="font-bold text-lg text-orange-600"><?php echo $total_salles; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
    // Prepare data for charts
    const monthsData = <?php echo json_encode($inscriptions_by_month); ?>;
    const revenueData = <?php echo json_encode($revenue_by_month); ?>;
    const filieresData = <?php echo json_encode($inscriptions_by_filiere); ?>;

    // Months names
    const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    
    // Prepare inscriptions chart data
    const inscriptionsLabels = monthsData.map(d => monthNames[d.mois - 1]);
    const inscriptionsValues = monthsData.map(d => d.total);

    // Prepare revenue chart data
    const revenueLabels = revenueData.map(d => monthNames[d.mois - 1]);
    const revenueValues = revenueData.map(d => parseFloat(d.total));

    // Prepare filieres chart data
    const filieresLabels = filieresData.map(d => d.nom || 'N/A');
    const filieresValues = filieresData.map(d => d.total);

    // Inscriptions Chart
    const inscriptionsCtx = document.getElementById('inscriptionsChart').getContext('2d');
    new Chart(inscriptionsCtx, {
        type: 'line',
        data: {
            labels: inscriptionsLabels,
            datasets: [{
                label: 'Inscriptions',
                data: inscriptionsValues,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#667eea'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#6b7280' }
                },
                x: {
                    ticks: { color: '#6b7280' }
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
                label: 'Revenus (XOF)',
                data: revenueValues,
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: '#667eea',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#6b7280' }
                },
                x: {
                    ticks: { color: '#6b7280' }
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
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(118, 75, 162, 0.8)',
                    'rgba(237, 100, 166, 0.8)',
                    'rgba(255, 154, 158, 0.8)',
                    'rgba(250, 195, 126, 0.8)'
                ],
                borderColor: ['#667eea', '#764ba2', '#ed64a6', '#ff9a9e', '#fac37e'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#6b7280' }
                }
            }
        }
    });
</script>

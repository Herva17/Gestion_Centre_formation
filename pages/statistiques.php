<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Apprenant.php';
require_once __DIR__ . '/../classes/Filiere.php';
require_once __DIR__ . '/../classes/Inscription.php';
require_once __DIR__ . '/../classes/Paiement.php';
require_once __DIR__ . '/../classes/Cours.php';

$page_title = 'Statistiques Détaillées';

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

include __DIR__ . '/../includes/header.php';
?>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                        <p class="text-gray-600 text-sm font-medium">Taux d'Inscription</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">
                            <?php echo $stats['total_apprenants'] > 0 ? round(($stats['total_inscriptions'] / $stats['total_apprenants']) * 100) : 0; ?>%
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                        <p class="text-gray-600 text-sm font-medium">Revenu Moyen par Inscription</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">
                            <?php echo number_format($stats['avg_frais_inscription'], 0, ',', ' '); ?> XOF
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                        <p class="text-gray-600 text-sm font-medium">Filière Populaire</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">
                            <?php 
                            $counts = array_count_values(array_column($all_inscriptions, 'id_filiere'));
                            arsort($counts);
                            $top_filiere_id = key($counts);
                            foreach ($all_filieres as $f) {
                                if ($f['id_filiere'] == $top_filiere_id) {
                                    echo htmlspecialchars(substr($f['nom'], 0, 20));
                                    break;
                                }
                            }
                            ?>
                        </p>
                    </div>
                </div>

                <!-- Detailed Tables -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Top Apprenants -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Derniers Apprenants</h3>
                        <div class="space-y-3">
                            <?php $recent = array_slice($all_apprenants, -5); foreach ($recent as $app): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-gray-800 font-medium"><?php echo htmlspecialchars($app['prenom'] . ' ' . $app['nom']); ?></span>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Actif</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Top Courses -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Top Cours</h3>
                        <div class="space-y-3">
                            <?php 
                            $course_counts = array_count_values(array_column($all_inscriptions, 'id_filiere'));
                            arsort($course_counts);
                            $top = array_slice($course_counts, 0, 5, true);
                            foreach ($top as $course_id => $count): 
                            ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-gray-800 font-medium">
                                    <?php 
                                    foreach ($all_filieres as $f) {
                                        if ($f['id_filiere'] == $course_id) {
                                            echo htmlspecialchars(substr($f['nom'], 0, 20));
                                            break;
                                        }
                                    }
                                    ?>
                                </span>
                                <span class="font-bold text-green-600"><?php echo $count; ?> inscriptions</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Distribution des Méthodes de Paiement</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <?php 
                        $types = array_count_values(array_filter(array_column($all_paiements, 'type')));
                        $colors = ['Espèces' => 'blue', 'Chèque' => 'green', 'Virement' => 'purple', 'Carte' => 'orange'];
                        foreach ($types as $type => $count): 
                        ?>
                        <div class="p-4 bg-<?php echo $colors[$type] ?? 'gray'; ?>-50 rounded-lg">
                            <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($type); ?></p>
                            <p class="text-2xl font-bold text-<?php echo $colors[$type] ?? 'gray'; ?>-600"><?php echo $count; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Export Options -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Exportation</h3>
                    <p class="text-gray-600 mb-4">Téléchargez les données au format Excel pour une meilleure analyse</p>
                    <div class="flex flex-wrap gap-3">
                        <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-semibold flex items-center">
                            <i class="fas fa-file-excel mr-2"></i>Exporter Apprenants
                        </button>
                        <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-semibold flex items-center">
                            <i class="fas fa-file-excel mr-2"></i>Exporter Paiements
                        </button>
                        <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 font-semibold flex items-center">
                            <i class="fas fa-file-excel mr-2"></i>Exporter Inscriptions
                        </button>
                    </div>
                </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php
// Move to pages folder for relative path resolution
chdir(__DIR__ . '/pages');

session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Inscription.php';
require_once __DIR__ . '/../classes/Paiement.php';

$page_title = 'Détail Inscription';

$database = new Database();
$db = $database->connect();
$inscription = new Inscription($db);
$paiement = new Paiement($db);

if (!isset($_GET['id'])) {
    header('Location: inscriptions.php');
    exit;
}

$detail = $inscription->getById($_GET['id']);
$paiements = $paiement->getByInscription($_GET['id']);

if (!$detail) {
    $_SESSION['error'] = 'Inscription non trouvée';
    header('Location: inscriptions.php');
    exit;
}

// Handle new payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = $_POST['montant'] ?? 0;
    $type = $_POST['type'] ?? '';
    $mois = $_POST['mois'] ?? '';
    
    if ($paiement->create($montant, $type, $mois, $_GET['id'])) {
        $_SESSION['success'] = 'Paiement enregistré avec succès';
        header('Location: ?id=' . $_GET['id']);
        exit;
    }
}

chdir(__DIR__);

include __DIR__ . '/../includes/header.php';
?>

                <!-- Breadcrumb -->
                <div class="mb-6">
                    <a href="inscriptions.php" class="text-purple-600 hover:text-purple-800"><i class="fas fa-arrow-left mr-2"></i>Retour</a>
                </div>

                <!-- Success Alert -->
                <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                    <div class="flex justify-between items-center">
                        <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
                        <button data-dismiss="alert" class="text-green-700 font-bold">&times;</button>
                    </div>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Info -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Informations de l'Apprenant</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600 text-sm">Nom</p>
                                    <p class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($detail['nom']); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Prénom</p>
                                    <p class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($detail['prenom']); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Détails de l'Inscription</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600 text-sm">Filière</p>
                                    <p class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($detail['filiere_nom']); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Date d'Inscription</p>
                                    <p class="text-lg font-bold text-gray-800"><?php echo date('d/m/Y', strtotime($detail['date_inscription'])); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Frais d'Inscription</p>
                                    <p class="text-lg font-bold text-green-600"><?php echo number_format($detail['frais_inscription'], 0, ',', ' '); ?> XOF</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Frais Mensuel</p>
                                    <p class="text-lg font-bold text-green-600"><?php echo number_format($detail['frais_mensuel'], 0, ',', ' '); ?> XOF</p>
                                </div>
                            </div>
                        </div>

                        <!-- Paiements List -->
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Historique des Paiements</h3>
                            <?php if (count($paiements) > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-100 border-b">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Montant</th>
                                            <th class="px-4 py-2 text-left">Type</th>
                                            <th class="px-4 py-2 text-left">Mois</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($paiements as $p): ?>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3 text-green-600 font-bold"><?php echo number_format($p['montant'], 0, ',', ' '); ?> XOF</td>
                                            <td class="px-4 py-3 text-gray-800"><?php echo htmlspecialchars($p['type']); ?></td>
                                            <td class="px-4 py-3 text-gray-800"><?php echo htmlspecialchars($p['mois']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <p class="text-gray-600 text-center py-8">Aucun paiement enregistré</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div>
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Résumé</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center pb-3 border-b">
                                    <span class="text-gray-600">Total Paiements</span>
                                    <span class="font-bold text-green-600">
                                        <?php 
                                        $total_paiements = array_sum(array_column($paiements, 'montant'));
                                        echo number_format($total_paiements, 0, ',', ' ');
                                        ?> XOF
                                    </span>
                                </div>
                                <div class="flex justify-between items-center pb-3 border-b">
                                    <span class="text-gray-600">Frais Inscription</span>
                                    <span class="font-bold text-blue-600"><?php echo number_format($detail['frais_inscription'], 0, ',', ' '); ?> XOF</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Reste à Payer</span>
                                    <span class="font-bold text-orange-600">
                                        <?php 
                                        $reste = $detail['frais_inscription'] - $total_paiements;
                                        echo $reste > 0 ? number_format($reste, 0, ',', ' ') . ' XOF' : 'Payé';
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button onclick="openModal('paymentModal')" class="w-full btn-primary text-white py-3 rounded-lg font-bold flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>Ajouter Paiement
                        </button>
                    </div>
                </div>

                <!-- Payment Modal -->
                <div id="paymentModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                        <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white px-6 py-4 rounded-t-lg">
                            <h3 class="text-xl font-bold">Ajouter un Paiement</h3>
                        </div>
                        
                        <form method="POST" class="p-6">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Montant (XOF) *</label>
                                <input type="number" name="montant" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Type de Paiement</label>
                                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Espèces">Espèces</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Virement">Virement</option>
                                    <option value="Carte">Carte Bancaire</option>
                                </select>
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 font-semibold mb-2">Mois</label>
                                <input type="text" name="mois" placeholder="ex: Janvier" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-semibold">
                                    Enregistrer
                                </button>
                                <button type="button" onclick="closeModal('paymentModal')" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 font-semibold">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

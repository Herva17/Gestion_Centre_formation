<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Paiement.php';
require_once __DIR__ . '/../classes/Inscription.php';

$page_title = 'Gestion des Paiements';

$database = new Database();
$db = $database->connect();
$paiement = new Paiement($db);
$inscription = new Inscription($db);

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = $_POST['montant'] ?? 0;
    $type = $_POST['type'] ?? '';
    $mois = $_POST['mois'] ?? '';
    $id_inscription = $_POST['id_inscription'] ?? '';

    if ($paiement->create($montant, $type, $mois, $id_inscription)) {
        $_SESSION['success'] = 'Paiement enregistré avec succès';
        header('Location: paiements.php');
        exit;
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $query = "DELETE FROM paiement WHERE id_paiement = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['delete']);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Paiement supprimé avec succès';
    }
    header('Location: paiements.php');
    exit;
}

$paiements = $paiement->getAll();
$inscriptions = $inscription->getAll();

include __DIR__ . '/../includes/header.php';
?>

                <!-- Success Alert -->
                <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                    <div class="flex justify-between items-center">
                        <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
                        <button data-dismiss="alert" class="text-green-700 font-bold">&times;</button>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Header with Add Button -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Paiements</h2>
                    <button onclick="openModal('addModal')" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold flex items-center">
                        <i class="fas fa-plus mr-2"></i>Enregistrer Paiement
                    </button>
                </div>

                <!-- Paiements Table -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full" id="paiementTable">
                            <thead class="bg-gradient-to-r from-orange-600 to-orange-800 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left">Apprenant</th>
                                    <th class="px-6 py-3 text-left">Montant</th>
                                    <th class="px-6 py-3 text-left">Type</th>
                                    <th class="px-6 py-3 text-left">Mois</th>
                                    <th class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paiements as $pay): ?>
                                <tr class="table-row border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($pay['prenom'] . ' ' . $pay['nom']); ?></td>
                                    <td class="px-6 py-4 text-green-600 font-bold"><?php echo number_format($pay['montant'], 0, ',', ' '); ?> XOF</td>
                                    <td class="px-6 py-4 text-gray-800">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold"><?php echo htmlspecialchars($pay['type'] ?? '-'); ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($pay['mois'] ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="?delete=<?php echo $pay['id_paiement']; ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal -->
                <div id="addModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                        <div class="bg-gradient-to-r from-orange-600 to-orange-800 text-white px-6 py-4 rounded-t-lg">
                            <h3 class="text-xl font-bold">Enregistrer un Paiement</h3>
                        </div>
                        
                        <form method="POST" class="p-6">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Inscription *</label>
                                <select name="id_inscription" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($inscriptions as $insc): ?>
                                    <option value="<?php echo $insc['id_inscription']; ?>">
                                        <?php echo htmlspecialchars($insc['prenom'] . ' ' . $insc['nom'] . ' - ' . $insc['filiere_nom']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Montant (XOF) *</label>
                                <input type="number" name="montant" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Type de Paiement</label>
                                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="Espèces">Espèces</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Virement">Virement</option>
                                    <option value="Carte">Carte Bancaire</option>
                                </select>
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 font-semibold mb-2">Mois</label>
                                <input type="text" name="mois" placeholder="ex: Janvier" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-semibold">
                                    Enregistrer
                                </button>
                                <button type="button" onclick="closeModal('addModal')" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 font-semibold">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
    searchTable('searchInput', 'paiementTable');
</script>

<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Inscription.php';
require_once __DIR__ . '/../classes/Apprenant.php';
require_once __DIR__ . '/../classes/Filiere.php';

$page_title = 'Gestion des Inscriptions';

$database = new Database();
$db = $database->connect();
$inscription = new Inscription($db);
$apprenant = new Apprenant($db);
$filiere = new Filiere($db);

// Handle create
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_apprenant = $_POST['id_apprenant'] ?? '';
    $id_filiere = $_POST['id_filiere'] ?? '';
    $date_inscription = $_POST['date_inscription'] ?? date('Y-m-d');
    $frais_inscription = $_POST['frais_inscription'] ?? 0;

    if ($inscription->create($id_apprenant, $id_filiere, $date_inscription, $frais_inscription)) {
        $_SESSION['success'] = 'Inscription ajoutée avec succès';
        header('Location: inscriptions.php');
        exit;
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    // Delete from paiement table first (foreign key constraint)
    $query = "DELETE FROM paiement WHERE id_inscription = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['delete']);
    $stmt->execute();

    // Then delete inscription
    $query = "DELETE FROM inscription WHERE id_inscription = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['delete']);
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Inscription supprimée avec succès';
    }
    header('Location: inscriptions.php');
    exit;
}

$inscriptions = $inscription->getAll();
$apprenants = $apprenant->getAll();
$filieres = $filiere->getAll();

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
                    <h2 class="text-3xl font-bold text-gray-800">Inscriptions</h2>
                    <button onclick="openModal('addModal')" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold flex items-center">
                        <i class="fas fa-plus mr-2"></i>Nouvelle Inscription
                    </button>
                </div>

                <!-- Inscriptions Table -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full" id="inscriptionTable">
                            <thead class="bg-gradient-to-r from-purple-600 to-purple-800 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left">Apprenant</th>
                                    <th class="px-6 py-3 text-left">Filière</th>
                                    <th class="px-6 py-3 text-left">Date d'Inscription</th>
                                    <th class="px-6 py-3 text-left">Frais</th>
                                    <th class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inscriptions as $insc): ?>
                                <tr class="table-row border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($insc['prenom'] . ' ' . $insc['nom']); ?></td>
                                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($insc['filiere_nom'] ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-gray-800"><?php echo date('d/m/Y', strtotime($insc['date_inscription'])); ?></td>
                                    <td class="px-6 py-4 text-green-600 font-bold"><?php echo number_format($insc['frais_inscription'], 0, ',', ' '); ?> XOF</td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="inscription-detail.php?id=<?php echo $insc['id_inscription']; ?>" class="text-blue-600 hover:text-blue-800 mr-3" title="Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?delete=<?php echo $insc['id_inscription']; ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr?')">
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
                        <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white px-6 py-4 rounded-t-lg">
                            <h3 class="text-xl font-bold">Nouvelle Inscription</h3>
                        </div>
                        
                        <form method="POST" class="p-6">
                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Apprenant *</label>
                                <select name="id_apprenant" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($apprenants as $app): ?>
                                    <option value="<?php echo $app['id_apprenant']; ?>"><?php echo htmlspecialchars($app['prenom'] . ' ' . $app['nom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Filière *</label>
                                <select name="id_filiere" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($filieres as $fil): ?>
                                    <option value="<?php echo $fil['id_filiere']; ?>"><?php echo htmlspecialchars($fil['nom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Date d'Inscription</label>
                                <input type="date" name="date_inscription" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 font-semibold mb-2">Frais d'Inscription (XOF)</label>
                                <input type="number" name="frais_inscription" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-semibold">
                                    Ajouter
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
    searchTable('searchInput', 'inscriptionTable');
</script>

<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Filiere.php';

$page_title = 'Gestion des Filières';

$database = new Database();
$db = $database->connect();
$filiere = new Filiere($db);

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($filiere->delete($id)) {
        $_SESSION['success'] = 'Filière supprimée avec succès';
    }
    header('Location: filieres.php');
    exit;
}

// Handle create/update
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_data = $filiere->getById($_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $duree = $_POST['duree'] ?? '';
    $frais_mensuel = $_POST['frais_mensuel'] ?? 0;

    if (isset($_POST['id_filiere']) && $_POST['id_filiere']) {
        // Update
        if ($filiere->update($_POST['id_filiere'], $nom, $duree, $frais_mensuel)) {
            $_SESSION['success'] = 'Filière mise à jour avec succès';
        }
    } else {
        // Create
        if ($filiere->create($nom, $duree, $frais_mensuel)) {
            $_SESSION['success'] = 'Filière ajoutée avec succès';
        }
    }
    header('Location: filieres.php');
    exit;
}

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
                    <h2 class="text-3xl font-bold text-gray-800">Filières</h2>
                    <button onclick="openModal('addModal')" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold flex items-center">
                        <i class="fas fa-plus mr-2"></i>Ajouter Filière
                    </button>
                </div>

                <!-- Filières Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($filieres as $fil): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-purple-600 hover:shadow-lg transition">
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($fil['nom']); ?></h3>
                        <div class="mb-4">
                            <p class="text-gray-600 text-sm"><strong>Durée:</strong> <?php echo htmlspecialchars($fil['duree'] ?? '-'); ?></p>
                            <p class="text-gray-600 text-sm mt-1"><strong>Frais Mensuel:</strong> <span class="text-green-600 font-bold"><?php echo number_format($fil['frais_mensuel'] ?? 0, 0, ',', ' '); ?> XOF</span></p>
                        </div>
                        <div class="flex gap-2">
                            <a href="?edit=<?php echo $fil['id_filiere']; ?>" class="flex-1 text-center bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 font-semibold">
                                <i class="fas fa-edit mr-2"></i>Modifier
                            </a>
                            <a href="?delete=<?php echo $fil['id_filiere']; ?>" class="flex-1 text-center bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 font-semibold" onclick="return confirm('Êtes-vous sûr?')">
                                <i class="fas fa-trash mr-2"></i>Supprimer
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Modal -->
                <div id="addModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                        <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white px-6 py-4 rounded-t-lg">
                            <h3 class="text-xl font-bold">
                                <?php echo $edit_mode ? 'Modifier Filière' : 'Ajouter Filière'; ?>
                            </h3>
                        </div>
                        
                        <form method="POST" class="p-6">
                            <?php if ($edit_mode): ?>
                            <input type="hidden" name="id_filiere" value="<?php echo $edit_data['id_filiere']; ?>">
                            <?php endif; ?>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Nom de la Filière *</label>
                                <input type="text" name="nom" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nom']) : ''; ?>">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Durée (ex: 6 mois)</label>
                                <input type="text" name="duree" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       value="<?php echo $edit_mode ? htmlspecialchars($edit_data['duree'] ?? '') : ''; ?>">
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 font-semibold mb-2">Frais Mensuel (XOF)</label>
                                <input type="number" name="frais_mensuel" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       value="<?php echo $edit_mode ? $edit_data['frais_mensuel'] : ''; ?>">
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-semibold">
                                    <?php echo $edit_mode ? 'Mettre à jour' : 'Ajouter'; ?>
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
    <?php if ($edit_mode) echo "openModal('addModal');"; ?>
</script>

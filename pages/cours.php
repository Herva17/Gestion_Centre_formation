<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Cours.php';
require_once __DIR__ . '/../classes/Filiere.php';

$page_title = 'Gestion des Cours';

$database = new Database();
$db = $database->connect();
$cours = new Cours($db);
$filiere = new Filiere($db);

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM horaire WHERE id_cours = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($cours->delete($id)) {
        $_SESSION['success'] = 'Cours supprimé avec succès';
    }
    header('Location: cours.php');
    exit;
}

// Handle create/update
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_data = $cours->getById($_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $id_filiere = $_POST['id_filiere'] ?? '';

    if (isset($_POST['id_cours']) && $_POST['id_cours']) {
        if ($cours->update($_POST['id_cours'], $nom, $description, $id_filiere)) {
            $_SESSION['success'] = 'Cours mis à jour avec succès';
        }
    } else {
        if ($cours->create($nom, $description, $id_filiere)) {
            $_SESSION['success'] = 'Cours ajouté avec succès';
        }
    }
    header('Location: cours.php');
    exit;
}

$touts_cours = $cours->getAll();
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
                    <h2 class="text-3xl font-bold text-gray-800">Cours</h2>
                    <button onclick="openModal('addModal')" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold flex items-center">
                        <i class="fas fa-plus mr-2"></i>Ajouter Cours
                    </button>
                </div>

                <!-- Cours Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($touts_cours as $c): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-600 hover:shadow-lg transition">
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($c['nom']); ?></h3>
                        <p class="text-sm text-gray-600 mb-3"><strong>Filière:</strong> <?php echo htmlspecialchars($c['filiere_nom'] ?? '-'); ?></p>
                        <p class="text-gray-700 text-sm mb-4 h-20 overflow-y-auto"><?php echo htmlspecialchars($c['description'] ?? '-'); ?></p>
                        <div class="flex gap-2">
                            <a href="?edit=<?php echo $c['id_cours']; ?>" class="flex-1 text-center bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 font-semibold">
                                <i class="fas fa-edit mr-2"></i>Modifier
                            </a>
                            <a href="?delete=<?php echo $c['id_cours']; ?>" class="flex-1 text-center bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 font-semibold" onclick="return confirm('Êtes-vous sûr?')">
                                <i class="fas fa-trash mr-2"></i>Supprimer
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Modal -->
                <div id="addModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                        <div class="bg-gradient-to-r from-green-600 to-green-800 text-white px-6 py-4 rounded-t-lg">
                            <h3 class="text-xl font-bold">
                                <?php echo $edit_mode ? 'Modifier Cours' : 'Ajouter Cours'; ?>
                            </h3>
                        </div>
                        
                        <form method="POST" class="p-6">
                            <?php if ($edit_mode): ?>
                            <input type="hidden" name="id_cours" value="<?php echo $edit_data['id_cours']; ?>">
                            <?php endif; ?>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Nom du Cours *</label>
                                <input type="text" name="nom" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                                       value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nom']) : ''; ?>">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Filière</label>
                                <select name="id_filiere" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($filieres as $fil): ?>
                                    <option value="<?php echo $fil['id_filiere']; ?>" <?php echo ($edit_mode && $edit_data['id_filiere'] == $fil['id_filiere']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($fil['nom']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 font-semibold mb-2">Description</label>
                                <textarea name="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 h-24"><?php echo $edit_mode ? htmlspecialchars($edit_data['description'] ?? '') : ''; ?></textarea>
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

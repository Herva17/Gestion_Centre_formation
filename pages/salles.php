<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Salle.php';

$page_title = 'Gestion des Salles';

$database = new Database();
$db = $database->connect();
$salle = new Salle($db);

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM horaire WHERE id_salle = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if ($salle->delete($id)) {
        $_SESSION['success'] = 'Salle supprimée avec succès';
    }
    header('Location: salles.php');
    exit;
}

// Handle create/update
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_data = $salle->getById($_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $capacite = $_POST['capacite'] ?? 0;

    if (isset($_POST['id_salle']) && $_POST['id_salle']) {
        if ($salle->update($_POST['id_salle'], $nom, $capacite)) {
            $_SESSION['success'] = 'Salle mise à jour avec succès';
        }
    } else {
        if ($salle->create($nom, $capacite)) {
            $_SESSION['success'] = 'Salle ajoutée avec succès';
        }
    }
    header('Location: salles.php');
    exit;
}

$salles = $salle->getAll();

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
                    <h2 class="text-3xl font-bold text-gray-800">Salles</h2>
                    <button onclick="openModal('addModal')" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold flex items-center">
                        <i class="fas fa-plus mr-2"></i>Ajouter Salle
                    </button>
                </div>

                <!-- Salles Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($salles as $s): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-indigo-600 hover:shadow-lg transition">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($s['nom']); ?></h3>
                                <p class="text-gray-600 text-sm">Salle</p>
                            </div>
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-door-open text-indigo-600 text-xl"></i>
                            </div>
                        </div>
                        <div class="mb-4 p-3 bg-indigo-50 rounded-lg">
                            <p class="text-gray-700"><strong>Capacité:</strong> <span class="text-lg font-bold text-indigo-600"><?php echo $s['capacite']; ?></span> places</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="?edit=<?php echo $s['id_salle']; ?>" class="flex-1 text-center bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 font-semibold">
                                <i class="fas fa-edit mr-2"></i>Modifier
                            </a>
                            <a href="?delete=<?php echo $s['id_salle']; ?>" class="flex-1 text-center bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 font-semibold" onclick="return confirm('Êtes-vous sûr?')">
                                <i class="fas fa-trash mr-2"></i>Supprimer
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Modal -->
                <div id="addModal" class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white px-6 py-4 rounded-t-lg">
                            <h3 class="text-xl font-bold">
                                <?php echo $edit_mode ? 'Modifier Salle' : 'Ajouter Salle'; ?>
                            </h3>
                        </div>
                        
                        <form method="POST" class="p-6">
                            <?php if ($edit_mode): ?>
                            <input type="hidden" name="id_salle" value="<?php echo $edit_data['id_salle']; ?>">
                            <?php endif; ?>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Nom de la Salle *</label>
                                <input type="text" name="nom" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                       value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nom']) : ''; ?>">
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 font-semibold mb-2">Capacité (Nombre de places) *</label>
                                <input type="number" name="capacite" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                       value="<?php echo $edit_mode ? $edit_data['capacite'] : ''; ?>">
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

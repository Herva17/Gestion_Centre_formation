<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Apprenant.php';

$page_title = 'Gestion des Apprenants';

$database = new Database();
$db = $database->connect();
$apprenant = new Apprenant($db);

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($apprenant->delete($id)) {
        $_SESSION['success'] = 'Apprenant supprimé avec succès';
    }
    header('Location: apprenants.php');
    exit;
}

// Handle create/update
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_data = $apprenant->getById($_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $adresse = $_POST['adresse'] ?? '';

    if (isset($_POST['id_apprenant']) && $_POST['id_apprenant']) {
        // Update
        if ($apprenant->update($_POST['id_apprenant'], $nom, $prenom, $telephone, $adresse)) {
            $_SESSION['success'] = 'Apprenant mis à jour avec succès';
        }
    } else {
        // Create
        if ($apprenant->create($nom, $prenom, $telephone, $adresse)) {
            $_SESSION['success'] = 'Apprenant ajouté avec succès';
        }
    }
    header('Location: apprenants.php');
    exit;
}

$apprenants = $apprenant->getAll();

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
                    <h2 class="text-3xl font-bold text-gray-800">Apprenants</h2>
                    <button onclick="openModal('addModal')" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold flex items-center">
                        <i class="fas fa-plus mr-2"></i>Ajouter Apprenant
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <input type="text" id="searchInput" placeholder="Rechercher un apprenant..." 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <!-- Apprenants Table -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full" id="apprenantTable">
                            <thead class="bg-gradient-to-r from-purple-600 to-purple-800 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left">Nom</th>
                                    <th class="px-6 py-3 text-left">Prénom</th>
                                    <th class="px-6 py-3 text-left">Téléphone</th>
                                    <th class="px-6 py-3 text-left">Adresse</th>
                                    <th class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($apprenants as $app): ?>
                                <tr class="table-row border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($app['nom']); ?></td>
                                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($app['prenom']); ?></td>
                                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($app['telephone'] ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($app['adresse'] ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="?edit=<?php echo $app['id_apprenant']; ?>" class="text-blue-600 hover:text-blue-800 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $app['id_apprenant']; ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr?')">
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
                            <h3 class="text-xl font-bold">
                                <?php echo $edit_mode ? 'Modifier Apprenant' : 'Ajouter Apprenant'; ?>
                            </h3>
                        </div>
                        
                        <form method="POST" class="p-6">
                            <?php if ($edit_mode): ?>
                            <input type="hidden" name="id_apprenant" value="<?php echo $edit_data['id_apprenant']; ?>">
                            <?php endif; ?>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Nom *</label>
                                <input type="text" name="nom" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       value="<?php echo $edit_mode ? htmlspecialchars($edit_data['nom']) : ''; ?>">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Prénom *</label>
                                <input type="text" name="prenom" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       value="<?php echo $edit_mode ? htmlspecialchars($edit_data['prenom']) : ''; ?>">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Téléphone</label>
                                <input type="tel" name="telephone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                                       value="<?php echo $edit_mode ? htmlspecialchars($edit_data['telephone'] ?? '') : ''; ?>">
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 font-semibold mb-2">Adresse</label>
                                <textarea name="adresse" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 h-24"><?php echo $edit_mode ? htmlspecialchars($edit_data['adresse'] ?? '') : ''; ?></textarea>
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
    searchTable('searchInput', 'apprenantTable');
    <?php if ($edit_mode) echo "openModal('addModal');"; ?>
</script>

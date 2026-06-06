<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Horaire.php';
require_once __DIR__ . '/../classes/Cours.php';
require_once __DIR__ . '/../classes/Salle.php';

$page_title = 'Gestion des Horaires';

$database = new Database();
$db = $database->connect();
$horaire = new Horaire($db);
$cours = new Cours($db);
$salle = new Salle($db);

// Handle delete
if (isset($_GET['delete'])) {
    if ($horaire->delete($_GET['delete'])) {
        $_SESSION['success'] = 'Horaire supprimé avec succès';
    }
    header('Location: horaires.php');
    exit;
}

// Handle create/update
$edit_mode = false;
$edit_data = null;

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_data = $horaire->getById($_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jour = $_POST['jour'] ?? '';
    $heure_debut = $_POST['heure_debut'] ?? '';
    $heure_fin = $_POST['heure_fin'] ?? '';
    $id_salle = $_POST['id_salle'] ?? '';
    $id_cours = $_POST['id_cours'] ?? '';

    if (isset($_POST['id_horaire']) && $_POST['id_horaire']) {
        if ($horaire->update($_POST['id_horaire'], $jour, $heure_debut, $heure_fin, $id_salle, $id_cours)) {
            $_SESSION['success'] = 'Horaire mis à jour avec succès';
        }
    } else {
        if ($horaire->create($jour, $heure_debut, $heure_fin, $id_salle, $id_cours)) {
            $_SESSION['success'] = 'Horaire ajouté avec succès';
        }
    }
    header('Location: horaires.php');
    exit;
}

$horaires = $horaire->getAll();
$touts_cours = $cours->getAll();
$salles = $salle->getAll();
$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

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
                    <h2 class="text-3xl font-bold text-gray-800">Horaires</h2>
                    <button onclick="openModal('addModal')" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold flex items-center">
                        <i class="fas fa-plus mr-2"></i>Ajouter Horaire
                    </button>
                </div>

                <!-- Horaires Table -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full" id="horaireTable">
                            <thead class="bg-gradient-to-r from-blue-600 to-blue-800 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left">Jour</th>
                                    <th class="px-6 py-3 text-left">Cours</th>
                                    <th class="px-6 py-3 text-left">Salle</th>
                                    <th class="px-6 py-3 text-left">Heure Début</th>
                                    <th class="px-6 py-3 text-left">Heure Fin</th>
                                    <th class="px-6 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($horaires as $h): ?>
                                <tr class="table-row border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-800 font-semibold"><?php echo htmlspecialchars($h['jour']); ?></td>
                                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($h['cours_nom'] ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($h['salle_nom'] ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-blue-600 font-semibold"><?php echo htmlspecialchars($h['heure_debut']); ?></td>
                                    <td class="px-6 py-4 text-blue-600 font-semibold"><?php echo htmlspecialchars($h['heure_fin']); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="?edit=<?php echo $h['id_horaire']; ?>" class="text-blue-600 hover:text-blue-800 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $h['id_horaire']; ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr?')">
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
                        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-6 py-4 rounded-t-lg">
                            <h3 class="text-xl font-bold">
                                <?php echo $edit_mode ? 'Modifier Horaire' : 'Ajouter Horaire'; ?>
                            </h3>
                        </div>
                        
                        <form method="POST" class="p-6">
                            <?php if ($edit_mode): ?>
                            <input type="hidden" name="id_horaire" value="<?php echo $edit_data['id_horaire']; ?>">
                            <?php endif; ?>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Jour *</label>
                                <select name="jour" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($jours as $j): ?>
                                    <option value="<?php echo $j; ?>" <?php echo ($edit_mode && $edit_data['jour'] == $j) ? 'selected' : ''; ?>>
                                        <?php echo $j; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Cours *</label>
                                <select name="id_cours" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($touts_cours as $c): ?>
                                    <option value="<?php echo $c['id_cours']; ?>" <?php echo ($edit_mode && $edit_data['id_cours'] == $c['id_cours']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c['nom']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Salle *</label>
                                <select name="id_salle" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($salles as $s): ?>
                                    <option value="<?php echo $s['id_salle']; ?>" <?php echo ($edit_mode && $edit_data['id_salle'] == $s['id_salle']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($s['nom']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 font-semibold mb-2">Heure Début *</label>
                                <input type="time" name="heure_debut" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="<?php echo $edit_mode ? $edit_data['heure_debut'] : ''; ?>">
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 font-semibold mb-2">Heure Fin *</label>
                                <input type="time" name="heure_fin" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="<?php echo $edit_mode ? $edit_data['heure_fin'] : ''; ?>">
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
    searchTable('searchInput', 'horaireTable');
    <?php if ($edit_mode) echo "openModal('addModal');"; ?>
</script>

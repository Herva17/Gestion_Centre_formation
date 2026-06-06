<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Utilisateur.php';

$page_title = 'Gestion des Utilisateurs';

$db = new Database();
$conn = $db->getConnection();
$utilisateur = new Utilisateur($conn);

// Récupérer tous les utilisateurs
$users = $utilisateur->getAll();
$total_users = $utilisateur->countAll();
$active_users = $utilisateur->countActive();

// Récupérer le nombre d'utilisateurs par rôle
$admins = $utilisateur->countByRole('Admin');
$formateurs = $utilisateur->countByRole('Formateur');
$gestionnaires = $utilisateur->countByRole('Gestionnaire');

?>

<?php include __DIR__ . '/../includes/header.php'; ?>

  <main class="flex-1 p-6">
    <div class="flex flex-col gap-6">
      <!-- En-tête -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Gestion des Utilisateurs</h1>
          <p class="text-gray-600 mt-2">Gérez les comptes utilisateurs, rôles et permissions</p>
        </div>
        <button onclick="openAddModal()" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition transform hover:scale-105 flex items-center gap-2">
          <i class="fas fa-plus"></i> Ajouter un utilisateur
        </button>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Total Utilisateurs -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md p-6 border-l-4 border-blue-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Total Utilisateurs</p>
              <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo $total_users; ?></p>
            </div>
            <i class="fas fa-users text-blue-300 text-4xl opacity-50"></i>
          </div>
        </div>

        <!-- Utilisateurs Actifs -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md p-6 border-l-4 border-green-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Utilisateurs Actifs</p>
              <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $active_users; ?></p>
            </div>
            <i class="fas fa-check-circle text-green-300 text-4xl opacity-50"></i>
          </div>
        </div>

        <!-- Admins -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow-md p-6 border-l-4 border-red-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Administrateurs</p>
              <p class="text-3xl font-bold text-red-600 mt-2"><?php echo $admins; ?></p>
            </div>
            <i class="fas fa-crown text-red-300 text-4xl opacity-50"></i>
          </div>
        </div>

        <!-- Formateurs -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-md p-6 border-l-4 border-purple-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Formateurs</p>
              <p class="text-3xl font-bold text-purple-600 mt-2"><?php echo $formateurs; ?></p>
            </div>
            <i class="fas fa-chalkboard-user text-purple-300 text-4xl opacity-50"></i>
          </div>
        </div>

        <!-- Gestionnaires -->
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 text-sm font-medium">Gestionnaires</p>
              <p class="text-3xl font-bold text-yellow-600 mt-2"><?php echo $gestionnaires; ?></p>
            </div>
            <i class="fas fa-clipboard-list text-yellow-300 text-4xl opacity-50"></i>
          </div>
        </div>
      </div>

      <!-- Tableau des utilisateurs -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-800">Liste des Utilisateurs</h2>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-100 border-b border-gray-200">
                <th class="text-left px-6 py-4 font-bold text-gray-700">Nom Complet</th>
                <th class="text-left px-6 py-4 font-bold text-gray-700">Email</th>
                <th class="text-left px-6 py-4 font-bold text-gray-700">Nom Utilisateur</th>
                <th class="text-left px-6 py-4 font-bold text-gray-700">Rôle</th>
                <th class="text-left px-6 py-4 font-bold text-gray-700">Téléphone</th>
                <th class="text-left px-6 py-4 font-bold text-gray-700">Statut</th>
                <th class="text-left px-6 py-4 font-bold text-gray-700">Date Création</th>
                <th class="text-center px-6 py-4 font-bold text-gray-700">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                  <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold">
                          <?php echo strtoupper(substr($user['prenom'], 0, 1)) . strtoupper(substr($user['nom'], 0, 1)); ?>
                        </div>
                        <div>
                          <p class="font-bold text-gray-800"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></p>
                          <?php if (!empty($user['sexe'])): ?>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['sexe']); ?></p>
                          <?php endif; ?>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <p class="text-gray-700"><?php echo htmlspecialchars($user['email']); ?></p>
                    </td>
                    <td class="px-6 py-4">
                      <p class="text-gray-700 font-mono"><?php echo htmlspecialchars($user['nom_utilisateur']); ?></p>
                    </td>
                    <td class="px-6 py-4">
                      <?php 
                      $role_badge = match($user['role']) {
                        'Admin' => 'bg-red-100 text-red-700 border-red-300',
                        'Formateur' => 'bg-purple-100 text-purple-700 border-purple-300',
                        'Gestionnaire' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                        default => 'bg-gray-100 text-gray-700 border-gray-300'
                      };
                      ?>
                      <span class="px-3 py-1 rounded-full text-sm font-bold border <?php echo $role_badge; ?>">
                        <?php echo htmlspecialchars($user['role']); ?>
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <p class="text-gray-700"><?php echo !empty($user['telephone']) ? htmlspecialchars($user['telephone']) : '-'; ?></p>
                    </td>
                    <td class="px-6 py-4">
                      <?php 
                      $status_badge = $user['statut'] === 'Actif' 
                        ? 'bg-green-100 text-green-700 border-green-300' 
                        : 'bg-red-100 text-red-700 border-red-300';
                      ?>
                      <span class="px-3 py-1 rounded-full text-sm font-bold border <?php echo $status_badge; ?>">
                        <?php echo htmlspecialchars($user['statut']); ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 text-gray-700">
                      <?php echo date('d/m/Y H:i', strtotime($user['date_creation'])); ?>
                    </td>
                    <td class="px-6 py-4 text-center">
                      <div class="flex justify-center gap-2">
                        <button onclick="openEditModal(<?php echo $user['id_utilisateur']; ?>)" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-3 rounded text-sm transition">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteUser(<?php echo $user['id_utilisateur']; ?>)" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 rounded text-sm transition">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="px-6 py-4 text-center text-gray-600">
                    <i class="fas fa-inbox text-4xl opacity-30 mb-2"></i>
                    <p class="mt-2">Aucun utilisateur trouvé</p>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal Ajouter/Éditer Utilisateur -->
  <div id="userModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
      <div class="flex justify-between items-center mb-6">
        <h2 id="modalTitle" class="text-2xl font-bold text-gray-800">Ajouter un Utilisateur</h2>
        <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <form id="userForm" method="POST" action="../api/index.php?action=save_user" class="space-y-4">
        <input type="hidden" id="userId" name="id_utilisateur" value="">

        <div class="grid grid-cols-2 gap-4">
          <!-- Nom -->
          <div>
            <label class="block text-gray-700 font-bold mb-2">Nom <span class="text-red-500">*</span></label>
            <input type="text" id="nom" name="nom" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="Kazige">
          </div>

          <!-- Prénom -->
          <div>
            <label class="block text-gray-700 font-bold mb-2">Prénom <span class="text-red-500">*</span></label>
            <input type="text" id="prenom" name="prenom" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="Stéphane">
          </div>

          <!-- Sexe -->
          <div>
            <label class="block text-gray-700 font-bold mb-2">Sexe</label>
            <select id="sexe" name="sexe" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
              <option value="">Sélectionner</option>
              <option value="Masculin">Masculin</option>
              <option value="Féminin">Féminin</option>
            </select>
          </div>

          <!-- Téléphone -->
          <div>
            <label class="block text-gray-700 font-bold mb-2">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="+221 77 123 45 67">
          </div>

          <!-- Email -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-bold mb-2">Email <span class="text-red-500">*</span></label>
            <input type="email" id="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="utilisateur@example.com">
          </div>

          <!-- Nom Utilisateur -->
          <div>
            <label class="block text-gray-700 font-bold mb-2">Nom Utilisateur <span class="text-red-500">*</span></label>
            <input type="text" id="nom_utilisateur" name="nom_utilisateur" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="skazige">
          </div>

          <!-- Rôle -->
          <div>
            <label class="block text-gray-700 font-bold mb-2">Rôle <span class="text-red-500">*</span></label>
            <select id="role" name="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
              <option value="">Sélectionner un rôle</option>
              <option value="Admin">Administrateur</option>
              <option value="Formateur">Formateur</option>
              <option value="Gestionnaire">Gestionnaire</option>
            </select>
          </div>

          <!-- Mot de passe -->
          <div id="passwordDiv" class="col-span-2">
            <label class="block text-gray-700 font-bold mb-2">Mot de passe <span id="passwordRequired" class="text-red-500">*</span></label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="Entrez un mot de passe sécurisé">
            <p id="passwordHint" class="text-sm text-gray-600 mt-1">Minimum 8 caractères, incluez majuscules, minuscules et chiffres</p>
          </div>

          <!-- Statut -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-bold mb-2">Statut</label>
            <select id="statut" name="statut" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
              <option value="Actif">Actif</option>
              <option value="Inactif">Inactif</option>
            </select>
          </div>
        </div>

        <!-- Boutons -->
        <div class="flex justify-end gap-3 mt-8">
          <button type="button" onclick="closeModal()" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded-lg transition">
            Annuler
          </button>
          <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg transition">
            <i class="fas fa-save"></i> Enregistrer
          </button>
        </div>
      </form>
    </div>
  </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
  function openAddModal() {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('modalTitle').textContent = 'Ajouter un Utilisateur';
    document.getElementById('mot_de_passe').required = true;
    document.getElementById('passwordRequired').textContent = '*';
    document.getElementById('userModal').classList.remove('hidden');
  }

  function openEditModal(userId) {
    fetch(`../api/index.php?action=get_user&id=${userId}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const user = data.data;
          document.getElementById('userId').value = user.id_utilisateur;
          document.getElementById('nom').value = user.nom;
          document.getElementById('prenom').value = user.prenom;
          document.getElementById('sexe').value = user.sexe || '';
          document.getElementById('telephone').value = user.telephone || '';
          document.getElementById('email').value = user.email;
          document.getElementById('nom_utilisateur').value = user.nom_utilisateur;
          document.getElementById('role').value = user.role;
          document.getElementById('statut').value = user.statut;
          document.getElementById('mot_de_passe').value = '';
          document.getElementById('mot_de_passe').required = false;
          document.getElementById('passwordRequired').textContent = '';
          document.getElementById('passwordHint').textContent = 'Laissez vide pour conserver le mot de passe actuel';
          document.getElementById('modalTitle').textContent = 'Éditer Utilisateur';
          document.getElementById('userModal').classList.remove('hidden');
        }
      })
      .catch(error => console.error('Erreur:', error));
  }

  function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
  }

  function deleteUser(userId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
      fetch(`../api/index.php?action=delete_user&id=${userId}`, { method: 'DELETE' })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Utilisateur supprimé avec succès');
            location.reload();
          } else {
            alert('Erreur: ' + data.message);
          }
        })
        .catch(error => {
          alert('Erreur lors de la suppression');
          console.error('Erreur:', error);
        });
    }
  }

  // Validation du formulaire
  document.getElementById('userForm').addEventListener('submit', function(e) {
    const password = document.getElementById('mot_de_passe').value;
    const userId = document.getElementById('userId').value;
    
    if (!userId && !password) {
      alert('Le mot de passe est requis pour un nouvel utilisateur');
      e.preventDefault();
      return;
    }

    if (password && password.length < 8) {
      alert('Le mot de passe doit contenir au moins 8 caractères');
      e.preventDefault();
      return;
    }
  });

  // Fermer le modal en cliquant en dehors
  document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) {
      closeModal();
    }
  });
</script>

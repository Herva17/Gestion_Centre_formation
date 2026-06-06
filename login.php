<?php
session_start();

// Si l'utilisateur est déjà connecté, redirection vers le dashboard
// if (isset($_SESSION['user_id'])) {
//     header('Location: pages/dashboard.php');
//     exit();
// }

require_once 'config/Database.php';
require_once 'classes/Utilisateur.php';

$error = '';
$success = '';

// Traiter la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = $_POST['nom_utilisateur'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($nom_utilisateur) || empty($mot_de_passe)) {
        $error = 'Veuillez entrer le nom d\'utilisateur et le mot de passe.';
    } else {
        $db = new Database();
        $conn = $db->getConnection();
        $utilisateur = new Utilisateur($conn);

        $user = $utilisateur->authenticate($nom_utilisateur, $mot_de_passe);

        if ($user) {
            // Créer la session
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_username'] = $user['nom_utilisateur'];

            // Redirection
            header('Location: pages/dashboard.php');
            exit();
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion Centre de Formation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <!-- Header Animation -->
        <div class="text-center mb-12 floating-animation">
            <div class="inline-block bg-white rounded-full p-4 mb-4 shadow-lg">
                <i class="fas fa-graduation-cap text-5xl text-purple-600"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">GCF</h1>
            <p class="text-white text-opacity-80">Gestion Centre de Formation</p>
        </div>

        <!-- Login Card -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8 mb-6">
            <!-- Title -->
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Connexion</h2>
            <p class="text-gray-600 mb-6">Accédez à votre compte</p>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
                    <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if (!empty($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-start gap-3">
                    <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="" class="space-y-5">
                <!-- Username Field -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        <i class="fas fa-user mr-2 text-purple-600"></i>Nom d'utilisateur
                    </label>
                    <input type="text" name="nom_utilisateur" required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg form-input focus:border-purple-600 focus:outline-none transition"
                           placeholder="Entrez votre nom d'utilisateur">
                </div>

                <!-- Password Field -->
                <div>
                    <label class="block text-gray-700 font-bold mb-2">
                        <i class="fas fa-lock mr-2 text-purple-600"></i>Mot de passe
                    </label>
                    <input type="password" name="mot_de_passe" required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg form-input focus:border-purple-600 focus:outline-none transition"
                           placeholder="Entrez votre mot de passe">
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center">
                        <input type="checkbox" class="w-4 h-4 rounded">
                        <span class="ml-2 text-gray-600">Se souvenir de moi</span>
                    </label>
                    <a href="#" class="text-purple-600 hover:text-purple-700 font-semibold">Mot de passe oublié ?</a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 btn-login text-white font-bold rounded-lg transition flex items-center justify-center gap-2 mt-8">
                    <i class="fas fa-sign-in-alt"></i>
                    Se connecter
                </button>
            </form>

            <!-- Demo Info -->
            <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs text-gray-600 mb-2"><i class="fas fa-info-circle mr-2 text-blue-600"></i><strong>Utilisateurs de démo :</strong></p>
                <div class="text-xs text-gray-700 space-y-1 ml-5">
                    <p><strong>Admin :</strong> admin / password123</p>
                    <p><strong>Gestionnaire :</strong> gestionnaire / password123</p>
                    <p><strong>Formateur :</strong> formateur / password123</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-white text-opacity-80 text-sm">
            <p>&copy; 2026 Gestion Centre de Formation. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>

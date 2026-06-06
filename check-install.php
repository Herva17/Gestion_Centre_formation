<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Installation - GCF</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <div class="text-center mb-8">
                <i class="fas fa-graduation-cap text-5xl text-purple-600 mb-4"></i>
                <h1 class="text-3xl font-bold text-gray-800">Vérification Installation</h1>
                <p class="text-gray-600 mt-2">Gestion Centre de Formation v1.0.0</p>
            </div>

            <?php
            $checks = [];
            
            // PHP Version
            $checks['PHP 8+'] = version_compare(PHP_VERSION, '8.0.0') >= 0;
            
            // Extensions
            $checks['Extension PDO'] = extension_loaded('pdo');
            $checks['Extension MySQL'] = extension_loaded('pdo_mysql');
            $checks['Extension JSON'] = extension_loaded('json');
            
            // Fichiers
            $checks['config/Database.php'] = file_exists(__DIR__ . '/config/Database.php');
            $checks['classes/Apprenant.php'] = file_exists(__DIR__ . '/classes/Apprenant.php');
            $checks['pages/dashboard.php'] = file_exists(__DIR__ . '/pages/dashboard.php');
            $checks['includes/header.php'] = file_exists(__DIR__ . '/includes/header.php');
            
            // Permissions
            $checks['Dossier classes (lecture)'] = is_readable(__DIR__ . '/classes');
            $checks['Dossier pages (lecture)'] = is_readable(__DIR__ . '/pages');
            
            // Test de connexion BD
            try {
                require_once __DIR__ . '/config/Database.php';
                $db = new Database();
                $conn = $db->connect();
                $checks['Connexion Base de Données'] = $conn !== null;
            } catch (Exception $e) {
                $checks['Connexion Base de Données'] = false;
            }
            
            // Affichage des résultats
            $all_passed = true;
            ?>

            <div class="space-y-3 mb-8">
                <?php foreach ($checks as $check => $status): ?>
                <div class="flex items-center p-4 rounded-lg <?php echo $status ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'; ?>">
                    <i class="fas fa-<?php echo $status ? 'check-circle' : 'times-circle'; ?> mr-3 text-lg <?php echo $status ? 'text-green-600' : 'text-red-600'; ?>"></i>
                    <span class="text-gray-800 flex-1 font-medium"><?php echo htmlspecialchars($check); ?></span>
                    <span class="<?php echo $status ? 'text-green-600 font-bold' : 'text-red-600 font-bold'; ?>">
                        <?php echo $status ? 'OK' : 'ERREUR'; ?>
                    </span>
                </div>
                <?php if (!$status) $all_passed = false; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($all_passed): ?>
            <div class="p-6 bg-green-100 border-l-4 border-green-600 rounded-lg mb-6">
                <h3 class="text-lg font-bold text-green-800 mb-2">
                    <i class="fas fa-check-circle mr-2"></i>Installation Complète!
                </h3>
                <p class="text-green-700">Tous les prérequis sont satisfaits. Vous pouvez commencer à utiliser l'application.</p>
            </div>

            <div class="flex gap-4">
                <a href="index.php" class="flex-1 text-center bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 font-bold transition">
                    <i class="fas fa-home mr-2"></i>Accueil
                </a>
                <a href="pages/dashboard.php" class="flex-1 text-center bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-bold transition">
                    <i class="fas fa-chart-line mr-2"></i>Tableau de Bord
                </a>
            </div>

            <?php else: ?>
            <div class="p-6 bg-red-100 border-l-4 border-red-600 rounded-lg mb-6">
                <h3 class="text-lg font-bold text-red-800 mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Problèmes Détectés
                </h3>
                <p class="text-red-700 mb-4">Veuillez corriger les erreurs ci-dessus avant de continuer.</p>
                
                <div class="bg-white p-4 rounded mt-4 text-sm text-gray-700">
                    <h4 class="font-bold mb-2">Conseils de dépannage:</h4>
                    <ul class="list-disc list-inside space-y-2">
                        <li>Vérifiez que vous avez PHP 8+</li>
                        <li>Activez l'extension PDO MySQL dans php.ini</li>
                        <li>Vérifiez les permissions des dossiers</li>
                        <li>Assurez-vous que MySQL est en cours d'exécution</li>
                        <li>Vérifiez les identifiants de la base de données</li>
                    </ul>
                </div>
            </div>

            <a href="check-install.php" class="block text-center bg-gray-600 text-white py-3 rounded-lg hover:bg-gray-700 font-bold transition">
                <i class="fas fa-redo mr-2"></i>Réessayer
            </a>

            <?php endif; ?>

            <div class="mt-8 pt-8 border-t border-gray-200 text-center text-gray-600 text-sm">
                <p>Gestion Centre de Formation - v1.0.0</p>
                <p class="mt-2">
                    <a href="GUIDE_UTILISATION.md" class="text-blue-600 hover:text-blue-800">Guide d'utilisation</a> • 
                    <a href="README.md" class="text-blue-600 hover:text-blue-800">Documentation</a> • 
                    <a href="pages/api-docs.php" class="text-blue-600 hover:text-blue-800">API Docs</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

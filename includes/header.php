<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Centre de Formation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .sidebar {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }
        .card-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .table-row:hover {
            background-color: rgba(102, 126, 234, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="sidebar w-64 text-white shadow-lg">
            <div class="p-6 border-b border-blue-700">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-graduation-cap mr-3"></i>GCF
                </h1>
                <p class="text-blue-200 text-sm">Gestion Centre Formation</p>
            </div>
            
            <nav class="mt-6">
                <a href="dashboard.php" class="block px-6 py-3 hover:bg-blue-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-chart-line mr-3"></i>Dashboard
                </a>
                <a href="apprenants.php" class="block px-6 py-3 hover:bg-blue-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'apprenants.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-users mr-3"></i>Apprenants
                </a>
                <a href="filieres.php" class="block px-6 py-3 hover:bg-blue-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'filieres.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-book mr-3"></i>Filières
                </a>
                <a href="cours.php" class="block px-6 py-3 hover:bg-blue-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'cours.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-chalkboard mr-3"></i>Cours
                </a>
                <a href="inscriptions.php" class="block px-6 py-3 hover:bg-blue-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'inscriptions.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-clipboard-list mr-3"></i>Inscriptions
                </a>
                <a href="paiements.php" class="block px-6 py-3 hover:bg-blue-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'paiements.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-credit-card mr-3"></i>Paiements
                </a>
                <a href="horaires.php" class="block px-6 py-3 hover:bg-blue-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'horaires.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-calendar mr-3"></i>Horaires
                </a>
                <a href="salles.php" class="block px-6 py-3 hover:bg-blue-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'salles.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-door-open mr-3"></i>Salles
                </a>
                <hr class="my-3 border-blue-600">
                <a href="statistiques.php" class="block px-6 py-3 hover:bg-blue-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'statistiques.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-chart-pie mr-3"></i>Statistiques
                </a>
                <a href="api-docs.php" class="block px-6 py-3 hover:bg-blue-700 transition <?php echo basename($_SERVER['PHP_SELF']) == 'api-docs.php' ? 'bg-blue-700' : ''; ?>">
                    <i class="fas fa-code mr-3"></i>API Docs
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <div class="bg-white border-b border-gray-200 shadow-sm">
                <div class="flex items-center justify-between px-8 py-4">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-home text-purple-600 mr-2"></i><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?>
                    </h2>
                    <div class="flex items-center space-x-4">
                        <button class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-bell text-lg"></i>
                        </button>
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-auto bg-gray-50 p-8">

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
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white bg-opacity-10 backdrop-blur-md text-white py-4 px-8">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <i class="fas fa-graduation-cap text-3xl"></i>
                <h1 class="text-2xl font-bold">GCF</h1>
            </div>
            <a href="pages/dashboard.php" class="bg-white text-purple-600 px-6 py-2 rounded-full font-bold hover:bg-gray-100 transition">
                Tableau de Bord
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-8 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-white">
                <h2 class="text-5xl font-bold mb-6 leading-tight">
                    Gestion Complète de Votre <span class="gradient-text">Centre de Formation</span>
                </h2>
                <p class="text-xl text-gray-100 mb-8 leading-relaxed">
                    Une plateforme moderne et intuitive pour gérer efficacement vos apprenants, filières, inscriptions et paiements.
                </p>
                <div class="flex space-x-4">
                    <a href="pages/dashboard.php" class="bg-white text-purple-600 px-8 py-4 rounded-lg font-bold hover:bg-gray-100 transition flex items-center">
                        <i class="fas fa-arrow-right mr-2"></i>Commencer
                    </a>
                    <a href="#features" class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold hover:bg-white hover:bg-opacity-10 transition flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>En Savoir Plus
                    </a>
                </div>
            </div>

            <!-- Right Illustration -->
            <div class="text-center">
                <div class="floating">
                    <div class="w-64 h-64 mx-auto relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-purple-600 rounded-3xl opacity-20 blur-3xl"></div>
                        <div class="relative w-full h-full bg-white bg-opacity-10 rounded-3xl flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-8xl opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="max-w-7xl mx-auto px-8 py-20">
        <div class="text-center mb-16">
            <h3 class="text-4xl font-bold text-white mb-4">Fonctionnalités Principales</h3>
            <p class="text-xl text-gray-100">Tout ce dont vous avez besoin pour gérer votre centre de formation</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Feature Card 1 -->
            <div class="glass-effect rounded-2xl p-8 hover:shadow-2xl transition transform hover:scale-105">
                <div class="w-14 h-14 bg-blue-500 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Apprenants</h4>
                <p class="text-gray-600">Gérez tous les apprenants avec leurs coordonnées et informations</p>
            </div>

            <!-- Feature Card 2 -->
            <div class="glass-effect rounded-2xl p-8 hover:shadow-2xl transition transform hover:scale-105">
                <div class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-book text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Filières</h4>
                <p class="text-gray-600">Créez et organisez vos filières avec durée et tarifs</p>
            </div>

            <!-- Feature Card 3 -->
            <div class="glass-effect rounded-2xl p-8 hover:shadow-2xl transition transform hover:scale-105">
                <div class="w-14 h-14 bg-purple-500 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-clipboard-list text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Inscriptions</h4>
                <p class="text-gray-600">Enregistrez les inscriptions et suivez les frais</p>
            </div>

            <!-- Feature Card 4 -->
            <div class="glass-effect rounded-2xl p-8 hover:shadow-2xl transition transform hover:scale-105">
                <div class="w-14 h-14 bg-orange-500 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-credit-card text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Paiements</h4>
                <p class="text-gray-600">Tracez les paiements et générez des rapports</p>
            </div>

            <!-- Feature Card 5 -->
            <div class="glass-effect rounded-2xl p-8 hover:shadow-2xl transition transform hover:scale-105">
                <div class="w-14 h-14 bg-pink-500 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-chalkboard text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Cours</h4>
                <p class="text-gray-600">Organisez vos cours et contenus pédagogiques</p>
            </div>

            <!-- Feature Card 6 -->
            <div class="glass-effect rounded-2xl p-8 hover:shadow-2xl transition transform hover:scale-105">
                <div class="w-14 h-14 bg-red-500 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-door-open text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Salles</h4>
                <p class="text-gray-600">Gérez vos salles et leur capacité</p>
            </div>

            <!-- Feature Card 7 -->
            <div class="glass-effect rounded-2xl p-8 hover:shadow-2xl transition transform hover:scale-105">
                <div class="w-14 h-14 bg-indigo-500 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-calendar text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Horaires</h4>
                <p class="text-gray-600">Planifiez les horaires des cours</p>
            </div>

            <!-- Feature Card 8 -->
            <div class="glass-effect rounded-2xl p-8 hover:shadow-2xl transition transform hover:scale-105">
                <div class="w-14 h-14 bg-cyan-500 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-800 mb-3">Dashboard</h4>
                <p class="text-gray-600">Statistiques et graphiques en temps réel</p>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="max-w-7xl mx-auto px-8 py-20">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="glass-effect rounded-2xl p-8 text-center">
                <div class="text-4xl font-bold gradient-text mb-2">100%</div>
                <p class="text-gray-800 font-semibold">Fonctionnel</p>
            </div>
            <div class="glass-effect rounded-2xl p-8 text-center">
                <div class="text-4xl font-bold gradient-text mb-2">8</div>
                <p class="text-gray-800 font-semibold">Modules</p>
            </div>
            <div class="glass-effect rounded-2xl p-8 text-center">
                <div class="text-4xl font-bold gradient-text mb-2">∞</div>
                <p class="text-gray-800 font-semibold">Scalable</p>
            </div>
            <div class="glass-effect rounded-2xl p-8 text-center">
                <div class="text-4xl font-bold gradient-text mb-2">24/7</div>
                <p class="text-gray-800 font-semibold">Disponible</p>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="max-w-7xl mx-auto px-8 py-20">
        <div class="glass-effect rounded-3xl p-12 text-center">
            <h3 class="text-4xl font-bold text-gray-800 mb-6">Prêt à Commencer ?</h3>
            <p class="text-xl text-gray-600 mb-8">Accédez à votre tableau de bord et commencez à gérer votre centre de formation dès maintenant</p>
            <a href="pages/dashboard.php" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-10 py-4 rounded-lg font-bold hover:shadow-lg transition inline-flex items-center">
                <i class="fas fa-rocket mr-2"></i>Lancer l'Application
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black bg-opacity-50 text-white py-8 mt-20">
        <div class="max-w-7xl mx-auto px-8 text-center">
            <p class="text-gray-400">© 2024 Gestion Centre de Formation. Tous droits réservés.</p>
            <p class="text-gray-500 mt-2">Créé avec <i class="fas fa-heart text-red-500"></i> pour optimiser votre gestion</p>
        </div>
    </footer>
</body>
</html>

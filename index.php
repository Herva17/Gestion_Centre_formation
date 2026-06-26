<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMOJA MAENDELEO - Gestion de Formation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .glass-effect:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }

        .feature-icon.blue { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .feature-icon.green { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .feature-icon.purple { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); }
        .feature-icon.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .feature-icon.pink { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .feature-icon.cyan { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .feature-icon.red { background: linear-gradient(135deg, #f5576c 0%, #ff6b6b 100%); }
        .feature-icon.indigo { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.2);
        }

        .nav-link.btn-outline {
            border: 2px solid rgba(255,255,255,0.5);
        }

        .nav-link.btn-outline:hover {
            background: white;
            color: #667eea;
            border-color: white;
        }

        @media (max-width: 768px) {
            .floating {
                animation: none;
            }
            
            .hero-title {
                font-size: 32px !important;
            }
            
            .features-grid {
                grid-template-columns: 1fr 1fr !important;
            }
        }

        @media (max-width: 480px) {
            .features-grid {
                grid-template-columns: 1fr !important;
            }
            
            .hero-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .hero-buttons a {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white bg-opacity-10 backdrop-blur-md text-white py-4 px-6 md:px-12">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">UMOJA MAENDELEO</h1>
                    <p class="text-xs opacity-75">Gestion de Formation</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="login.php" class="nav-link btn-outline">
                    <i class="fas fa-sign-in-alt mr-2"></i>Connexion
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-6 md:px-12 py-16 md:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div class="text-white">
                <span class="inline-block bg-white bg-opacity-20 px-4 py-2 rounded-full text-sm font-semibold mb-6">
                    <i class="fas fa-rocket mr-2"></i>Solution Complète
                </span>
                <h2 class="hero-title text-4xl md:text-5xl font-bold mb-6 leading-tight">
                    Gérez Votre Centre de <span class="gradient-text">Formation</span> en Toute Simplicité
                </h2>
                <p class="text-lg md:text-xl text-gray-100 mb-8 leading-relaxed opacity-90">
                    Une plateforme moderne et intuitive pour gérer vos apprenants, filières, inscriptions et paiements.
                </p>
                <div class="hero-buttons flex flex-wrap gap-4">
                    <a href="login.php" class="btn-primary text-white px-8 py-4 rounded-xl font-bold flex items-center gap-2">
                        <i class="fas fa-arrow-right"></i>
                        Accéder au Dashboard
                    </a>
                    <a href="#features" class="border-2 border-white text-white px-8 py-4 rounded-xl font-bold hover:bg-white hover:bg-opacity-10 transition flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        Découvrir
                    </a>
                </div>
            </div>

            <!-- Right Illustration -->
            <div class="flex justify-center">
                <div class="floating">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-purple-600 rounded-3xl opacity-20 blur-3xl"></div>
                        <div class="relative w-72 h-72 md:w-80 md:h-80 bg-white bg-opacity-10 rounded-3xl flex items-center justify-center backdrop-blur-sm border border-white border-opacity-20">
                            <div class="text-center">
                                <i class="fas fa-chart-line text-white text-7xl md:text-8xl opacity-50"></i>
                                <p class="text-white text-sm mt-4 opacity-75">Statistiques en temps réel</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="max-w-7xl mx-auto px-6 md:px-12 pb-16 md:pb-20">
        <div class="glass-effect rounded-3xl p-8 md:p-12 text-center">
            <h3 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Prêt à Commencer ?</h3>
            <p class="text-lg text-gray-600 mb-6 max-w-2xl mx-auto">
                Accédez à votre tableau de bord et optimisez la gestion de votre centre de formation
            </p>
            <a href="login.php" class="btn-primary text-white px-10 py-4 rounded-xl font-bold inline-flex items-center gap-3">
                <i class="fas fa-rocket"></i>
                Lancer l'Application
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black bg-opacity-40 text-white py-6">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm opacity-75">© 2024 UMOJA MAENDELEO - Gestion de Formation</p>
            <p class="text-xs opacity-50 mt-1">
                <i class="fas fa-heart text-red-400"></i> 
                Conçu pour simplifier votre gestion
            </p>
        </div>
    </footer>
</body>
</html>
<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMOJA MAENDELEO - <?php echo isset($page_title) ? $page_title : 'Gestion Centre de Formation'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
            width: 250px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .sidebar .brand {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar .brand h1 {
            font-size: 22px;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar .brand .subtitle {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            margin-top: 3px;
        }
        
        .sidebar nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 25px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-size: 14px;
        }
        
        .sidebar nav a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #667eea;
        }
        
        .sidebar nav a.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #667eea;
        }
        
        .sidebar nav a i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        .sidebar nav .nav-divider {
            border-top: 1px solid rgba(255,255,255,0.1);
            margin: 10px 25px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Top Bar */
        .top-bar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .top-bar .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a202c;
        }
        
        .top-bar .page-title i {
            color: #667eea;
            margin-right: 10px;
        }
        
        .top-bar .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .top-bar .user-menu .notification-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: #f7fafc;
            color: #4a5568;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .top-bar .user-menu .notification-btn:hover {
            background: #edf2f7;
        }
        
        .top-bar .user-menu .notification-btn .badge {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 18px;
            height: 18px;
            background: #fc8181;
            color: white;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .top-bar .user-menu .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .top-bar .user-menu .user-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .top-bar .user-menu .user-info {
            text-align: right;
        }
        
        .top-bar .user-menu .user-info .name {
            font-size: 14px;
            font-weight: 600;
            color: #1a202c;
        }
        
        .top-bar .user-menu .user-info .role {
            font-size: 12px;
            color: #718096;
        }

        /* Bouton Connexion */
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        /* Dropdown */
        .dropdown {
            position: relative;
        }
        
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 50px;
            width: 220px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            display: none;
            overflow: hidden;
            z-index: 1000;
        }
        
        .dropdown-menu.show {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .dropdown-menu .dropdown-header {
            padding: 15px 20px;
            border-bottom: 1px solid #edf2f7;
        }
        
        .dropdown-menu .dropdown-header .name {
            font-weight: 600;
            color: #1a202c;
        }
        
        .dropdown-menu .dropdown-header .email {
            font-size: 12px;
            color: #718096;
        }
        
        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .dropdown-menu a:hover {
            background: #f7fafc;
        }
        
        .dropdown-menu a i {
            width: 20px;
            text-align: center;
        }
        
        .dropdown-menu .dropdown-divider {
            border-top: 1px solid #edf2f7;
            margin: 5px 20px;
        }
        
        .dropdown-menu .logout {
            color: #fc8181;
            font-weight: 600;
        }
        
        .dropdown-menu .logout:hover {
            background: #fff5f5;
        }
        
        /* Content Area */
        .content-area {
            padding: 25px 30px;
            flex: 1;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar .brand h1 {
                font-size: 0;
            }
            
            .sidebar .brand h1 i {
                font-size: 22px;
            }
            
            .sidebar .brand .subtitle {
                display: none;
            }
            
            .sidebar nav a span {
                display: none;
            }
            
            .sidebar nav a {
                justify-content: center;
                padding: 12px 15px;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .top-bar .user-info {
                display: none;
            }
            
            .content-area {
                padding: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .top-bar {
                padding: 10px 15px;
            }
            
            .top-bar .page-title {
                font-size: 16px;
            }
            
            .content-area {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <h1>
                <i class="fas fa-graduation-cap"></i>
                <span>UMOJA MAENDELEO</span>
            </h1>
            <div class="subtitle">Gestion Centre de Formation</div>
        </div>
        
        <nav>
            <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="apprenants.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'apprenants.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Apprenants</span>
            </a>
            <a href="filieres.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'filieres.php' ? 'active' : ''; ?>">
                <i class="fas fa-book"></i>
                <span>Filières</span>
            </a>
            <a href="cours.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'cours.php' ? 'active' : ''; ?>">
                <i class="fas fa-chalkboard"></i>
                <span>Cours</span>
            </a>
            <a href="inscriptions.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'inscriptions.php' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-list"></i>
                <span>Inscriptions</span>
            </a>
            <a href="paiements.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'paiements.php' ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i>
                <span>Paiements</span>
            </a>
            <a href="horaires.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'horaires.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar"></i>
                <span>Horaires</span>
            </a>
            <a href="salles.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'salles.php' ? 'active' : ''; ?>">
                <i class="fas fa-door-open"></i>
                <span>Salles</span>
            </a>

            <a href="apprenants_par_filiere.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'apprenants_par_filiere.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Apprenants par Filière</span>
            </a>
            
            <div class="nav-divider"></div>
            
            <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-cog"></i>
                <span>Utilisateurs</span>
            </a>
            <a href="statistiques.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'statistiques.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i>
                <span>Statistiques</span>
            </a>
            <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i>
                <span>Paramètres</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="page-title">
                <i class="fas fa-<?php echo $page_icon ?? 'home'; ?>"></i>
                <?php echo isset($page_title) ? $page_title : 'Gestion Formation'; ?>
            </div>
            <div class="user-menu">
                <?php 
                // Vérifier si l'utilisateur est connecté
                $is_logged_in = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
                ?>
                <?php if ($is_logged_in): ?>
                    <!-- Utilisateur connecté -->
                    <!-- Bouton Notifications -->
                    <button class="notification-btn" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </button>
                    
                    <!-- Menu Utilisateur avec Déconnexion -->
                    <div class="dropdown">
                        <div class="user-avatar" onclick="toggleDropdown()">
                            <?php 
                                $user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'U';
                                $initial = strtoupper(substr($user_name, 0, 1));
                                echo $initial;
                            ?>
                        </div>
                        <div class="dropdown-menu" id="dropdownMenu">
                            <div class="dropdown-header">
                                <div class="name"><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Utilisateur'; ?></div>
                                <div class="email"><?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : 'user@email.com'; ?></div>
                            </div>
                            
                            <!-- Lien vers le profil -->
                            <a href="profile.php">
                                <i class="fas fa-user-circle"></i>
                                Mon profil
                            </a>
                            
                            <!-- Lien vers les paramètres -->
                            <a href="settings.php">
                                <i class="fas fa-cog"></i>
                                Paramètres
                            </a>
                            
                            <!-- Séparateur -->
                            <div class="dropdown-divider"></div>
                            
                            <!-- Bouton Déconnexion -->
                            <a href="logout.php" class="logout" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                                <i class="fas fa-sign-out-alt"></i>
                                Déconnexion
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Utilisateur non connecté -->
                    <a href="../login.php" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        Connexion
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
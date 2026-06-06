<?php
/**
 * Configuration globale de l'application
 */

// Informations de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gestion_centre_formation');

// Informations de l'application
define('APP_NAME', 'Gestion Centre de Formation');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Application complète de gestion pour centres de formation');

// Paramètres de session
define('SESSION_TIMEOUT', 3600); // 1 heure

// Devises
define('CURRENCY', 'XOF');
define('CURRENCY_SYMBOL', 'XOF');

// Format de date
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i:s');

// Messages
define('SUCCESS_MESSAGE', 'Opération réussie');
define('ERROR_MESSAGE', 'Une erreur s\'est produite');

// Chemins
define('BASE_PATH', dirname(__DIR__));
define('PAGES_PATH', BASE_PATH . '/pages');
define('CLASSES_PATH', BASE_PATH . '/classes');
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');

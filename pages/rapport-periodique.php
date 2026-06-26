<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Paiement.php';

$page_title = 'Rapport Périodique des Paiements';
$page_icon = 'chart-bar';

$database = new Database();
$db = $database->connect();

// Récupérer les colonnes de la table paiement
$columns_query = "SHOW COLUMNS FROM paiement";
$columns_stmt = $db->prepare($columns_query);
$columns_stmt->execute();
$columns = $columns_stmt->fetchAll(PDO::FETCH_COLUMN);

// Déterminer la colonne de date disponible
$date_column = 'date_creation'; // par défaut

// Vérifier les colonnes de date possibles
$possible_date_columns = ['date_creation', 'date_paiement', 'created_at', 'date_inscription', 'date'];
foreach ($possible_date_columns as $col) {
    if (in_array($col, $columns)) {
        $date_column = $col;
        break;
    }
}

// Récupérer les filtres
$mois_filter = $_GET['mois'] ?? '';
$type_filter = $_GET['type'] ?? '';
$date_debut = $_GET['date_debut'] ?? date('Y-m-01');
$date_fin = $_GET['date_fin'] ?? date('Y-m-t');

// Construction de la requête avec les bonnes jointures
$query = "SELECT 
            p.*,
            a.prenom,
            a.nom,
            f.nom as filiere_nom
          FROM paiement p
          JOIN inscription i ON p.id_inscription = i.id_inscription
          JOIN apprenant a ON i.id_apprenant = a.id_apprenant
          JOIN filiere f ON i.id_filiere = f.id_filiere
          WHERE 1=1";

$params = [];

if (!empty($mois_filter)) {
    $query .= " AND p.mois = :mois";
    $params[':mois'] = $mois_filter;
}

if (!empty($type_filter)) {
    $query .= " AND p.type = :type";
    $params[':type'] = $type_filter;
}

// Utiliser la colonne de date trouvée
if (!empty($date_debut) && !empty($date_fin)) {
    $query .= " AND DATE(p." . $date_column . ") BETWEEN :date_debut AND :date_fin";
    $params[':date_debut'] = $date_debut;
    $params[':date_fin'] = $date_fin;
}

$query .= " ORDER BY p." . $date_column . " DESC";

try {
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En cas d'erreur, afficher un message et les colonnes disponibles
    $paiements = [];
    echo '<div style="background: #fff5f5; border: 2px solid #fc8181; border-radius: 16px; padding: 20px; margin: 20px;">';
    echo '<h3 style="color: #e53e3e;">⚠️ Erreur de colonne</h3>';
    echo '<p style="color: #4a5568;">La colonne <strong>' . $date_column . '</strong> n\'existe pas dans la table paiement.</p>';
    echo '<p style="color: #4a5568;">Colonnes disponibles : <strong>' . implode(', ', $columns) . '</strong></p>';
    echo '<p style="color: #4a5568;">Veuillez corriger la variable $date_column dans le code.</p>';
    echo '</div>';
}

// Statistiques
$total_montant = array_sum(array_column($paiements, 'montant'));
$nb_paiements = count($paiements);
$types = array_count_values(array_column($paiements, 'type'));

include __DIR__ . '/../includes/header.php';
?>

<style>
    .report-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 30px 40px;
        color: white;
        margin-bottom: 30px;
    }

    .filter-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }

    .filter-card .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }

    .filter-card .filter-group label {
        font-size: 12px;
        font-weight: 600;
        color: #4a5568;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 5px;
    }

    .filter-card .filter-group input,
    .filter-card .filter-group select {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .filter-card .filter-group input:focus,
    .filter-card .filter-group select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-filter {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 30px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-reset {
        background: #e2e8f0;
        color: #4a5568;
        padding: 10px 30px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-reset:hover {
        background: #cbd5e0;
    }

    .stat-report {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .stat-report-item {
        background: white;
        border-radius: 12px;
        padding: 15px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        text-align: center;
    }

    .stat-report-item .number {
        font-size: 28px;
        font-weight: 700;
        color: #2d3748;
    }

    .stat-report-item .number.text-green { color: #48bb78; }
    .stat-report-item .number.text-blue { color: #667eea; }
    .stat-report-item .number.text-orange { color: #ed8936; }
    .stat-report-item .number.text-purple { color: #764ba2; }

    .stat-report-item .label {
        font-size: 12px;
        color: #a0aec0;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

    .btn-print-report {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-print-report:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(72, 187, 120, 0.3);
    }

    .payment-badge {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }

    .payment-badge.especes { background: rgba(72, 187, 120, 0.15); color: #48bb78; }
    .payment-badge.cheque { background: rgba(102, 126, 234, 0.15); color: #667eea; }
    .payment-badge.virement { background: rgba(237, 137, 54, 0.15); color: #ed8936; }
    .payment-badge.carte { background: rgba(118, 75, 162, 0.15); color: #764ba2; }

    .table-container {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .table-responsive {
        overflow-x: auto;
        padding: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f7fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    thead th {
        padding: 14px 20px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #4a5568;
        white-space: nowrap;
    }

    tbody tr {
        border-bottom: 1px solid #edf2f7;
        transition: all 0.3s ease;
    }

    tbody tr:hover {
        background: #f7fafc;
    }

    tbody td {
        padding: 12px 20px;
        color: #2d3748;
    }

    tfoot {
        background: #f7fafc;
        border-top: 2px solid #e2e8f0;
    }

    tfoot td {
        padding: 14px 20px;
        font-weight: 700;
    }

    @media print {
        .no-print { display: none !important; }
        .report-header { background: #1a202c !important; -webkit-print-color-adjust: exact !important; }
        .stat-report-item { background: #f7fafc !important; -webkit-print-color-adjust: exact !important; }
    }

    @media (max-width: 768px) {
        .report-header { padding: 20px; }
        .filter-card .filter-grid { grid-template-columns: 1fr; }
        .stat-report { grid-template-columns: 1fr 1fr; }
        thead th, tbody td { padding: 10px 15px; font-size: 13px; }
    }
</style>

<div class="content-wrapper">
    <!-- Header -->
    <div class="report-header">
        <h1 style="margin: 0; font-size: 24px; font-weight: 700;">
            <i class="fas fa-chart-bar me-3"></i>
            Rapport Périodique des Paiements
        </h1>
        <p style="opacity: 0.8; margin-top: 5px;">
            Période du <?php echo date('d/m/Y', strtotime($date_debut)); ?> au <?php echo date('d/m/Y', strtotime($date_fin)); ?>
        </p>
    </div>

    <!-- Filtres -->
    <div class="filter-card no-print">
        <form method="GET" class="filter-grid">
            <div class="filter-group">
                <label><i class="fas fa-calendar-alt me-1"></i>Date de début</label>
                <input type="date" name="date_debut" value="<?php echo $date_debut; ?>">
            </div>
            <div class="filter-group">
                <label><i class="fas fa-calendar-alt me-1"></i>Date de fin</label>
                <input type="date" name="date_fin" value="<?php echo $date_fin; ?>">
            </div>
            <div class="filter-group">
                <label><i class="fas fa-tag me-1"></i>Mois</label>
                <select name="mois">
                    <option value="">Tous les mois</option>
                    <?php 
                    $mois_list = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                  'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                    foreach ($mois_list as $m): 
                    ?>
                    <option value="<?php echo $m; ?>" <?php echo $mois_filter == $m ? 'selected' : ''; ?>>
                        <?php echo $m; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label><i class="fas fa-credit-card me-1"></i>Type</label>
                <select name="type">
                    <option value="">Tous les types</option>
                    <option value="Espèces" <?php echo $type_filter == 'Espèces' ? 'selected' : ''; ?>>Espèces</option>
                    <option value="Chèque" <?php echo $type_filter == 'Chèque' ? 'selected' : ''; ?>>Chèque</option>
                    <option value="Virement" <?php echo $type_filter == 'Virement' ? 'selected' : ''; ?>>Virement</option>
                    <option value="Carte" <?php echo $type_filter == 'Carte' ? 'selected' : ''; ?>>Carte Bancaire</option>
                </select>
            </div>
            <div class="filter-group" style="display: flex; gap: 10px;">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i> Filtrer
                </button>
                <a href="rapport-periodique.php" class="btn-reset">
                    <i class="fas fa-undo"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Statistiques -->
    <div class="stat-report">
        <div class="stat-report-item">
            <div class="number text-green">$<?php echo number_format($total_montant, 0, ',', ' '); ?></div>
            <div class="label">Total des paiements</div>
        </div>
        <div class="stat-report-item">
            <div class="number text-blue"><?php echo $nb_paiements; ?></div>
            <div class="label">Nombre de paiements</div>
        </div>
        <div class="stat-report-item">
            <div class="number text-orange"><?php echo $nb_paiements > 0 ? number_format($total_montant / $nb_paiements, 0, ',', ' ') : '0'; ?></div>
            <div class="label">Moyenne par paiement</div>
        </div>
        <div class="stat-report-item">
            <div class="number text-purple"><?php echo !empty($types) ? count($types) : '0'; ?></div>
            <div class="label">Types utilisés</div>
        </div>
    </div>

    <!-- Bouton d'impression -->
    <div style="margin-bottom: 20px; text-align: right;" class="no-print">
        <button onclick="window.print()" class="btn-print-report">
            <i class="fas fa-print"></i> Imprimer le rapport
        </button>
    </div>

    <!-- Tableau -->
    <div class="table-container">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Apprenant</th>
                        <th>Filière</th>
                        <th>Montant</th>
                        <th>Type</th>
                        <th>Mois</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($paiements)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #a0aec0;">
                                <i class="fas fa-info-circle" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                                Aucun paiement trouvé pour cette période
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($paiements as $pay): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($pay['prenom'] . ' ' . $pay['nom']); ?></strong></td>
                            <td><?php echo htmlspecialchars($pay['filiere_nom']); ?></td>
                            <td style="font-weight: 700; color: #48bb78;">$<?php echo number_format($pay['montant'], 0, ',', ' '); ?></td>
                            <td>
                                <span class="payment-badge <?php echo strtolower($pay['type'] ?? 'especes'); ?>">
                                    <?php echo htmlspecialchars($pay['type'] ?? 'Espèces'); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($pay['mois'] ?? '-'); ?></td>
                            <td style="font-size: 13px; color: #718096;">
                                <?php 
                                // Utiliser la colonne de date trouvée
                                if (isset($pay[$date_column]) && !empty($pay[$date_column])) {
                                    echo date('d/m/Y', strtotime($pay[$date_column]));
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: right; font-size: 16px;">TOTAL</td>
                        <td style="font-weight: 700; color: #48bb78; font-size: 18px;">$<?php echo number_format($total_montant, 0, ',', ' '); ?></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
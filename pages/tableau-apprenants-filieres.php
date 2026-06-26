<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Apprenant.php';
require_once __DIR__ . '/../classes/Filiere.php';
require_once __DIR__ . '/../classes/Inscription.php';

$page_title = 'Tableau des Apprenants par Filière';
$page_icon = 'table';

$database = new Database();
$db = $database->connect();

$apprenant = new Apprenant($db);
$filiere = new Filiere($db);
$inscription = new Inscription($db);

// Récupérer toutes les filières
$filieres = $filiere->getAll();

// Récupérer toutes les inscriptions avec les détails
$inscriptions = $inscription->getAll();

// Organiser les apprenants par filière
$apprenants_par_filiere = [];
$total_apprenants = 0;

foreach ($filieres as $fil) {
    $apprenants_filiere = [];
    foreach ($inscriptions as $insc) {
        if ($insc['id_filiere'] == $fil['id_filiere']) {
            $app = $apprenant->getById($insc['id_apprenant']);
            if ($app) {
                $apprenants_filiere[] = [
                    'id_apprenant' => $app['id_apprenant'],
                    'nom' => $app['nom'],
                    'prenom' => $app['prenom'],
                    'telephone' => $app['telephone'] ?? '',
                    'adresse' => $app['adresse'] ?? '',
                    'date_inscription' => $insc['date_inscription'],
                    'frais_inscription' => $insc['frais_inscription']
                ];
            }
        }
    }
    
    usort($apprenants_filiere, function($a, $b) {
        return strcmp($a['nom'], $b['nom']);
    });
    
    $apprenants_par_filiere[$fil['id_filiere']] = [
        'filiere' => $fil,
        'apprenants' => $apprenants_filiere,
        'total' => count($apprenants_filiere)
    ];
    
    $total_apprenants += count($apprenants_filiere);
}

include __DIR__ . '/../includes/header.php';
?>

<style>
    .top-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 25px;
    }

    .btn-top-action {
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-top-action:hover {
        transform: translateY(-2px);
    }

    .btn-top-action.btn-back {
        background: #e2e8f0;
        color: #4a5568;
    }

    .btn-top-action.btn-back:hover {
        background: #cbd5e0;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .btn-top-action.btn-print {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }

    .btn-top-action.btn-print:hover {
        box-shadow: 0 5px 20px rgba(72, 187, 120, 0.3);
    }

    .btn-top-action.btn-excel {
        background: linear-gradient(135deg, #217346 0%, #1e7e34 100%);
        color: white;
    }

    .btn-top-action.btn-excel:hover {
        box-shadow: 0 5px 20px rgba(33, 115, 70, 0.3);
    }

    .table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    .table-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .table-header h3 {
        color: white;
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .table-header .badge-count {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        color: white;
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 500;
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
        position: sticky;
        top: 0;
        z-index: 10;
    }

    thead th {
        padding: 14px 18px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #4a5568;
        white-space: nowrap;
    }

    tbody tr {
        border-bottom: 1px solid #edf2f7;
        transition: all 0.3s ease;
    }

    tbody tr:hover {
        background: #f0f4ff;
    }

    tbody td {
        padding: 12px 18px;
        color: #2d3748;
        font-size: 14px;
        vertical-align: middle;
    }

    .filiere-label {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        color: white;
    }

    .user-avatar-sm {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 13px;
        margin-right: 10px;
        flex-shrink: 0;
    }

    .user-info {
        display: flex;
        align-items: center;
    }

    .user-name {
        font-weight: 600;
        color: #2d3748;
    }

    .user-detail {
        font-size: 12px;
        color: #718096;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .frais-amount {
        font-weight: 600;
        color: #48bb78;
    }

    .btn-view {
        padding: 5px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .btn-view:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #a0aec0;
    }

    .empty-state i {
        font-size: 60px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .empty-state h3 {
        color: #4a5568;
        font-size: 20px;
        margin-bottom: 10px;
    }

    .search-wrapper {
        position: relative;
        margin-bottom: 25px;
    }

    .search-wrapper input {
        width: 100%;
        padding: 16px 20px 16px 55px;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .search-wrapper input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), 0 4px 20px rgba(0,0,0,0.12);
    }

    .search-wrapper .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 18px;
        transition: all 0.3s ease;
    }

    .search-wrapper:focus-within .search-icon {
        color: #667eea;
    }

    @media print {
        .no-print { display: none !important; }
        .table-container { box-shadow: none !important; border: 1px solid #ddd !important; }
        .table-header { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .filiere-label { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .user-avatar-sm { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .btn-view { display: none !important; }
        thead { position: static !important; }
    }

    @media (max-width: 768px) {
        .table-header {
            flex-direction: column;
            text-align: center;
        }

        .top-actions {
            flex-direction: column;
        }

        .btn-top-action {
            width: 100%;
            justify-content: center;
        }

        .user-info {
            flex-direction: column;
            align-items: flex-start;
        }

        .user-avatar-sm {
            margin-bottom: 5px;
        }

        thead th, tbody td {
            padding: 8px 10px;
            font-size: 13px;
        }
    }
</style>

<div class="page-wrapper">
    <div class="content-wrapper">

        <!-- Actions -->
        <div class="top-actions no-print">
            <a href="apprenants_par_filiere.php" class="btn-top-action btn-back">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <button onclick="window.print()" class="btn-top-action btn-print">
                <i class="fas fa-print"></i> Imprimer
            </button>
            <button onclick="exportExcel()" class="btn-top-action btn-excel">
                <i class="fas fa-file-excel"></i> Exporter Excel
            </button>
        </div>

        <!-- Search -->
        <div class="search-wrapper no-print">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Rechercher par nom, prénom, filière..." onkeyup="filterTable()">
        </div>

        <!-- Tableau -->
        <div class="table-container">
            <div class="table-header">
                <h3>
                    <i class="fas fa-table me-2"></i>
                    Tableau des Apprenants par Filière
                </h3>
                <span class="badge-count">
                    <i class="fas fa-users me-1"></i>
                    <?php echo $total_apprenants; ?> apprenants
                </span>
            </div>
            <div class="table-responsive">
                <table id="apprenantsTable">
                    <thead>
                        <tr>
                            <th style="min-width: 180px;">Apprenant</th>
                            <th style="min-width: 160px;">Filière</th>
                            <th style="min-width: 130px;">Téléphone</th>
                            <th style="min-width: 160px;">Adresse</th>
                            <th style="min-width: 130px;">Date d'inscription</th>
                            <th style="min-width: 120px;">Frais</th>
                            <th style="min-width: 100px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $has_data = false;
                        foreach ($apprenants_par_filiere as $data): 
                            $fil = $data['filiere'];
                            $couleurs = ['#667eea', '#f093fb', '#4facfe', '#fa709a', '#48bb78', '#ed8936', '#fc8181', '#764ba2'];
                            $couleur = $couleurs[array_rand($couleurs)];
                            
                            foreach ($data['apprenants'] as $app): 
                                $has_data = true;
                        ?>
                        <tr data-search="<?php echo strtolower($app['nom'] . ' ' . $app['prenom'] . ' ' . $fil['nom']); ?>">
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar-sm">
                                        <?php echo strtoupper(substr($app['prenom'] ?? 'A', 0, 1) . substr($app['nom'] ?? '', 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="user-name"><?php echo htmlspecialchars($app['prenom'] . ' ' . $app['nom']); ?></div>
                                        <div class="user-detail">
                                            <i class="fas fa-id-card"></i>
                                            #<?php echo str_pad($app['id_apprenant'], 4, '0', STR_PAD_LEFT); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="filiere-label" style="background: <?php echo $couleur; ?>;">
                                    <?php echo htmlspecialchars(substr($fil['nom'], 0, 25)) . (strlen($fil['nom']) > 25 ? '...' : ''); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($app['telephone'])): ?>
                                    <i class="fas fa-phone" style="color: #4facfe; margin-right: 6px;"></i>
                                    <?php echo htmlspecialchars($app['telephone']); ?>
                                <?php else: ?>
                                    <span style="color: #a0aec0;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($app['adresse'])): ?>
                                    <i class="fas fa-map-marker-alt" style="color: #ed8936; margin-right: 6px;"></i>
                                    <?php echo htmlspecialchars($app['adresse']); ?>
                                <?php else: ?>
                                    <span style="color: #a0aec0;">-</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 13px; color: #718096;">
                                <i class="fas fa-calendar-alt" style="color: #667eea; margin-right: 6px;"></i>
                                <?php echo date('d/m/Y', strtotime($app['date_inscription'])); ?>
                            </td>
                            <td>
                                <span class="frais-amount">
                                    $<?php echo number_format($app['frais_inscription'], 0, ',', ' '); ?>
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="inscription-detail.php?id=<?php echo $app['id_apprenant']; ?>" class="btn-view" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            endforeach; 
                        endforeach; 
                        ?>
                        <?php if (!$has_data): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <h3>Aucun apprenant</h3>
                                    <p>Aucun apprenant inscrit dans les filières</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
    // Filter Table
    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('apprenantsTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            if (row.querySelector('.empty-state')) continue;
            
            const cells = row.getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell) {
                    const text = cell.textContent.toLowerCase();
                    if (text.indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    }

    // Export Excel
    function exportExcel() {
        const table = document.getElementById('apprenantsTable');
        const rows = table.querySelectorAll('tr');
        let csv = [];
        
        // Header
        let header = [];
        table.querySelectorAll('thead th').forEach(th => {
            header.push(th.textContent.trim());
        });
        csv.push(header.join(';'));
        
        // Data
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            let rowData = [];
            row.querySelectorAll('td').forEach(td => {
                let text = td.textContent.trim();
                // Nettoyer les espaces et les caractères spéciaux
                text = text.replace(/;/g, ',').replace(/\s+/g, ' ');
                rowData.push(text);
            });
            if (rowData.length > 0) {
                csv.push(rowData.join(';'));
            }
        });
        
        // Télécharger
        const blob = new Blob(['\uFEFF' + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'apprenants_par_filiere.csv';
        link.click();
    }

    // Row hover animation
    document.querySelectorAll('#apprenantsTable tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.005)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
</script>
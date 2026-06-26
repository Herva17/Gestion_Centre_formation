<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Apprenant.php';
require_once __DIR__ . '/../classes/Filiere.php';
require_once __DIR__ . '/../classes/Inscription.php';

$page_title = 'Apprenants par Filière';
$page_icon = 'users';

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
            // Récupérer les infos de l'apprenant
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
    
    // Trier les apprenants par nom
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
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.04);
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    .stat-card .stat-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 40px;
        opacity: 0.08;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon {
        opacity: 0.12;
        transform: translateY(-50%) scale(1.1) rotate(-5deg);
    }

    .stat-card .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #1a202c;
        line-height: 1.2;
    }

    .stat-card .stat-label {
        font-size: 13px;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

    .stat-card .stat-change {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        padding: 3px 12px;
        border-radius: 20px;
        margin-top: 10px;
        font-weight: 600;
    }

    .stat-change.up { background: rgba(72, 187, 120, 0.12); color: #48bb78; }
    .stat-change.down { background: rgba(252, 129, 129, 0.12); color: #fc8181; }
    .stat-change.neutral { background: rgba(237, 137, 54, 0.12); color: #ed8936; }

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

    .btn-top-action.btn-print {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }

    .btn-top-action.btn-print:hover {
        box-shadow: 0 5px 20px rgba(72, 187, 120, 0.3);
    }

    .btn-top-action.btn-table {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-top-action.btn-table:hover {
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-top-action.btn-pdf {
        background: linear-gradient(135deg, #fc8181 0%, #e53e3e 100%);
        color: white;
    }

    .btn-top-action.btn-pdf:hover {
        box-shadow: 0 5px 20px rgba(252, 129, 129, 0.3);
    }

    .filiere-section {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 25px;
        transition: all 0.3s ease;
        animation: fadeInUp 0.5s ease-out;
        animation-fill-mode: both;
    }

    .filiere-section:hover {
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .filiere-header {
        padding: 20px 25px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filiere-header:hover {
        filter: brightness(1.05);
    }

    .filiere-header .filiere-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .filiere-header .filiere-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .filiere-header .filiere-nom {
        font-size: 20px;
        font-weight: 700;
    }

    .filiere-header .filiere-detail {
        font-size: 13px;
        opacity: 0.85;
    }

    .filiere-header .filiere-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filiere-header .badge-count {
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 600;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(5px);
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .filiere-header .toggle-icon {
        font-size: 20px;
        transition: transform 0.3s ease;
        background: rgba(255,255,255,0.15);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .filiere-header .toggle-icon.rotated {
        transform: rotate(180deg);
    }

    .filiere-body {
        padding: 0;
        overflow: hidden;
        transition: all 0.3s ease;
        max-height: 0;
        opacity: 0;
    }

    .filiere-body.open {
        padding: 20px 25px;
        max-height: 2000px;
        opacity: 1;
    }

    .filiere-body .table-responsive {
        overflow-x: auto;
    }

    .filiere-body table {
        width: 100%;
        border-collapse: collapse;
    }

    .filiere-body table thead {
        background: #f7fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .filiere-body table thead th {
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #4a5568;
    }

    .filiere-body table tbody tr {
        border-bottom: 1px solid #edf2f7;
        transition: all 0.3s ease;
    }

    .filiere-body table tbody tr:hover {
        background: #f0f4ff;
    }

    .filiere-body table tbody td {
        padding: 12px 16px;
        color: #2d3748;
        font-size: 14px;
        vertical-align: middle;
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

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #a0aec0;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.3;
    }

    .empty-state h4 {
        color: #4a5568;
        font-size: 18px;
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 14px;
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

    .frais-amount {
        font-weight: 600;
        color: #48bb78;
    }

    .no-apprenants {
        text-align: center;
        padding: 20px;
        color: #a0aec0;
        font-style: italic;
    }

    .no-apprenants i {
        margin-right: 8px;
    }

    /* Styles pour l'impression */
    @media print {
        .no-print { display: none !important; }
        .filiere-body { max-height: none !important; opacity: 1 !important; padding: 20px 25px !important; }
        .filiere-section { break-inside: avoid; page-break-inside: avoid; box-shadow: none !important; border: 1px solid #ddd !important; }
        .filiere-header { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .filiere-header .badge-count { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .filiere-header .filiere-icon { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .filiere-header .toggle-icon { display: none !important; }
        .stats-grid { break-inside: avoid; page-break-inside: avoid; }
        .user-avatar-sm { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        .btn-view { display: none !important; }
        .stat-card { break-inside: avoid; page-break-inside: avoid; }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .filiere-header {
            flex-direction: column;
            text-align: center;
        }

        .filiere-header .filiere-info {
            flex-direction: column;
        }

        .filiere-header .filiere-badges {
            justify-content: center;
        }

        .filiere-body.open {
            padding: 15px;
        }

        .filiere-body table thead th,
        .filiere-body table tbody td {
            padding: 8px 10px;
            font-size: 13px;
        }

        .user-info {
            flex-direction: column;
            align-items: flex-start;
        }

        .user-avatar-sm {
            margin-bottom: 5px;
        }

        .top-actions {
            flex-direction: column;
        }

        .btn-top-action {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .filiere-header .filiere-nom {
            font-size: 17px;
        }
    }
</style>

<div class="page-wrapper">
    <div class="content-wrapper">

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-number"><?php echo $total_apprenants; ?></div>
                <div class="stat-label">Total Apprenants</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> Actif</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-book stat-icon"></i>
                <div class="stat-number"><?php echo count($filieres); ?></div>
                <div class="stat-label">Total Filières</div>
                <span class="stat-change neutral"><i class="fas fa-minus"></i> Disponibles</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-users-between-lines stat-icon"></i>
                <div class="stat-number"><?php echo count($filieres) > 0 ? round($total_apprenants / count($filieres), 1) : 0; ?></div>
                <div class="stat-label">Moyenne par Filière</div>
                <span class="stat-change neutral"><i class="fas fa-minus"></i> Stable</span>
            </div>
            <div class="stat-card">
                <i class="fas fa-star stat-icon"></i>
                <div class="stat-number" style="font-size: 20px; line-height: 1.3;">
                    <?php 
                    $max = 0;
                    $max_filiere = '';
                    foreach ($apprenants_par_filiere as $data) {
                        if ($data['total'] > $max) {
                            $max = $data['total'];
                            $max_filiere = $data['filiere']['nom'];
                        }
                    }
                    echo htmlspecialchars($max_filiere ?: 'N/A');
                    ?>
                </div>
                <div class="stat-label">Filière la plus peuplée</div>
                <span class="stat-change up"><i class="fas fa-arrow-up"></i> <?php echo $max; ?> apprenants</span>
            </div>
        </div>

        <!-- Actions -->
        <div class="top-actions no-print">
            <button onclick="window.print()" class="btn-top-action btn-print">
                <i class="fas fa-print"></i> Imprimer la liste
            </button>
            <a href="tableau-apprenants-filieres.php" class="btn-top-action btn-table">
                <i class="fas fa-table"></i> Voir le tableau complet
            </a>
            <button onclick="generatePDF()" class="btn-top-action btn-pdf">
                <i class="fas fa-file-pdf"></i> Télécharger PDF
            </button>
        </div>

        <!-- Search -->
        <div class="search-wrapper no-print">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Rechercher une filière ou un apprenant..." onkeyup="filterSections()">
        </div>

        <!-- Filières Sections -->
        <?php if (empty($filieres)): ?>
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                <i class="fas fa-book-open" style="font-size: 60px; color: #a0aec0; opacity: 0.3; display: block; margin-bottom: 20px;"></i>
                <h3 style="color: #4a5568; font-size: 20px; margin-bottom: 10px;">Aucune filière</h3>
                <p style="color: #a0aec0;">Commencez par créer des filières pour voir les apprenants</p>
            </div>
        <?php else: ?>
            <?php foreach ($apprenants_par_filiere as $index => $data): 
                $fil = $data['filiere'];
                $apprenants = $data['apprenants'];
                $total = $data['total'];
                $couleurs = ['#667eea', '#f093fb', '#4facfe', '#fa709a', '#48bb78', '#ed8936', '#fc8181', '#764ba2'];
                $couleur = $couleurs[$index % count($couleurs)];
                $delay = $index * 0.1;
            ?>
            <div class="filiere-section" style="animation-delay: <?php echo $delay; ?>s" data-search="<?php echo strtolower($fil['nom']); ?>">
                <!-- Header -->
                <div class="filiere-header" style="background: linear-gradient(135deg, <?php echo $couleur; ?>, <?php echo $couleur; ?>cc);" onclick="toggleSection(this)">
                    <div class="filiere-info">
                        <div class="filiere-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div>
                            <div class="filiere-nom"><?php echo htmlspecialchars($fil['nom']); ?></div>
                            <div class="filiere-detail">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo htmlspecialchars($fil['duree'] ?? 'Durée non spécifiée'); ?>
                                <span style="margin: 0 8px;">•</span>
                                <i class="fas fa-dollar-sign me-1"></i>
                                $<?php echo number_format($fil['frais_mensuel'] ?? 0, 0, ',', ' '); ?>/mois
                            </div>
                        </div>
                    </div>
                    <div class="filiere-badges">
                        <span class="badge-count">
                            <i class="fas fa-users"></i>
                            <?php echo $total; ?> apprenants
                        </span>
                        <span class="badge-count" style="background: rgba(255,255,255,0.15);">
                            <i class="fas fa-id-card"></i>
                            #<?php echo str_pad($fil['id_filiere'], 4, '0', STR_PAD_LEFT); ?>
                        </span>
                        <div class="toggle-icon">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="filiere-body">
                    <div class="table-responsive">
                        <?php if (empty($apprenants)): ?>
                            <div class="no-apprenants">
                                <i class="fas fa-info-circle"></i>
                                Aucun apprenant inscrit dans cette filière
                            </div>
                        <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th style="min-width: 180px;">Apprenant</th>
                                    <th style="min-width: 140px;">Téléphone</th>
                                    <th style="min-width: 160px;">Adresse</th>
                                    <th style="min-width: 130px;">Date d'inscription</th>
                                    <th style="min-width: 120px;">Frais</th>
                                    <th style="min-width: 100px; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($apprenants as $app): ?>
                                <tr data-search="<?php echo strtolower($app['nom'] . ' ' . $app['prenom']); ?>">
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
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // Toggle section
    function toggleSection(header) {
        const section = header.closest('.filiere-section');
        const body = section.querySelector('.filiere-body');
        const icon = header.querySelector('.toggle-icon i');
        
        body.classList.toggle('open');
        icon.classList.toggle('fa-chevron-down');
        icon.classList.toggle('fa-chevron-up');
        header.querySelector('.toggle-icon').classList.toggle('rotated');
    }

    // Filter sections
    function filterSections() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const sections = document.querySelectorAll('.filiere-section');

        sections.forEach(section => {
            const searchData = section.dataset.search || '';
            const rows = section.querySelectorAll('tbody tr');
            
            let sectionMatch = searchData.indexOf(filter) > -1;
            let rowMatch = false;
            
            rows.forEach(row => {
                const rowData = row.dataset.search || '';
                const match = rowData.indexOf(filter) > -1;
                row.style.display = match ? '' : 'none';
                if (match) rowMatch = true;
            });
            
            if (sectionMatch || rowMatch) {
                section.style.display = '';
                if (!sectionMatch && rowMatch) {
                    const body = section.querySelector('.filiere-body');
                    const icon = section.querySelector('.toggle-icon i');
                    body.classList.add('open');
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                    section.querySelector('.toggle-icon').classList.add('rotated');
                }
            } else {
                section.style.display = 'none';
            }
        });
    }

    // Generate PDF
    function generatePDF() {
        const element = document.querySelector('.content-wrapper');
        const btn = document.querySelector('.btn-pdf');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération en cours...';
        btn.disabled = true;
        
        // Ouvrir toutes les sections pour le PDF
        document.querySelectorAll('.filiere-body').forEach(body => {
            body.classList.add('open');
        });
        document.querySelectorAll('.toggle-icon').forEach(icon => {
            icon.classList.add('rotated');
        });
        document.querySelectorAll('.toggle-icon i').forEach(icon => {
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        });
        
        const opt = {
            margin:        [10, 10, 10, 10],
            filename:     'apprenants-par-filiere.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, letterRendering: true, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        
        html2pdf().set(opt).from(element).save().then(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }).catch(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('Erreur lors de la génération du PDF. Veuillez réessayer.');
        });
    }

    // Ouvrir la première section par défaut
    document.addEventListener('DOMContentLoaded', function() {
        const firstSection = document.querySelector('.filiere-section');
        if (firstSection) {
            const body = firstSection.querySelector('.filiere-body');
            const icon = firstSection.querySelector('.toggle-icon i');
            body.classList.add('open');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
            firstSection.querySelector('.toggle-icon').classList.add('rotated');
        }
    });

    // Animation des sections au scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.filiere-section').forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'all 0.5s ease';
        observer.observe(section);
    });
</script>
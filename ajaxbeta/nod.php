<?php
$jsonFile = '/opt/rolink/conf/rolink.json';

// Activer l’affichage des erreurs (pour debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = "";
$champs_a_cacher = ['pl_in', 'pl_out', 'rx_frq', 'tx_frq', 'isx', 'tip', 'LinkedTo'];

$champs_avec_cadre = [
    'Sysop', 'Name', 'LAT', 'LONG', 'nodeLocation', 'Location', 'Locator',
    'RXFREQ', 'TXFREQ', 'Website', 'Mode', 'Type',
    'LinkedTo', 'CTCSS', 'DefaultTG'
];

$aides_champs = [
    'Sysop' => 'Veuillez mettre le nom du propriétaire.',
    'Name' => 'Nom du Node ou équipement (ex : Jean).',
    'LAT' => 'Latitude en format décimal (ex : 47.218371). Pour trouver vos coordonnées GPS, consultez <a href="https://www.coordonnees-gps.fr/" target="_blank" rel="noopener noreferrer">coordonnees-gps.fr</a>.',
    'LONG' => 'Longitude en format décimal (ex : -1.553621). Pour trouver vos coordonnées GPS, consultez <a href="https://www.coordonnees-gps.fr/" target="_blank" rel="noopener noreferrer">coordonnees-gps.fr</a>.',
    'nodeLocation' => 'Ville d\'origine du Node avec département entre parenthèses (ex : Lens (62).',
    'Locator' => 'Locator radioamateur (ex : FN31pr). Pour trouver votre locator, consultez <a href="https://www.f4hxn.fr/qra-locator/" target="_blank" rel="noopener noreferrer">le site F4HXN QRA Locator</a>.',
    'RXFREQ' => 'Fréquence réception en MHz.',
    'TXFREQ' => 'Fréquence transmission en MHz.',
    'Website' => 'URL du site web associé.',
    'Mode' => 'Mode opératoire (ex : FM, DMR).',
    'Type' => 'Choisissez un nombre de 1 à 20 qui déterminera la couleur du point dans la carte.',
    'LinkedTo' => 'Liste des liaisons connectées.',
    'CTCSS' => 'Code CTCSS utilisé. Choisissez dans la liste.',
    'DefaultTG' => 'Talk Group par défaut.',
    'Location' => 'Veuillez indiquer votre région (exemple : Bretagne, Île-de-France, Provence-Alpes-Côte d\'Azur).'
];

$regions = [
    'Auvergne-Rhône-Alpes',
    'Bourgogne-Franche-Comté',
    'Bretagne',
    'Centre-Val de Loire',
    'Corse',
    'Grand Est',
    'Hauts-de-France',
    'Île-de-France',
    'Normandie',
    'Nouvelle-Aquitaine',
    'Occitanie',
    'Pays de la Loire',
    'Provence-Alpes-Côte d’Azur',
    'Guadeloupe',
    'Martinique',
    'Guyane',
    'La Réunion',
    'Mayotte',
    'Saint-Pierre-et-Miquelon',
    'Saint-Barthélemy',
    'Saint-Martin'
];

function backupFile($filePath) {
    $dir = dirname($filePath);
    $backup = $dir . '/rolink_backup_' . date('dmY_His') . '.json';
    return copy($filePath, $backup) ? $backup : false;
}

function remountFilesystemRW($mountPoint = '/') {
    exec("sudo mount -o remount,rw " . escapeshellarg($mountPoint) . " 2>&1", $output, $returnVar);
    return ($returnVar === 0);
}

function remountFilesystemRO($mountPoint = '/') {
    exec("sudo mount -o remount,ro " . escapeshellarg($mountPoint) . " 2>&1", $output, $returnVar);
    return ($returnVar === 0);
}

function sauvegardeJsonAvecRemount($jsonFile, $data) {
    $mountPoint = '/';  // Modifier si nécessaire selon point de montage

    if (!remountFilesystemRW($mountPoint)) {
        return ['success' => false, 'message' => 'Impossible de passer le système de fichiers en lecture-écriture.'];
    }

    if (!backupFile($jsonFile)) {
        remountFilesystemRO($mountPoint);
        return ['success' => false, 'message' => 'Sauvegarde de sécurité impossible.'];
    }

    $result = file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    remountFilesystemRO($mountPoint);

    if ($result === false) {
        return ['success' => false, 'message' => 'Écriture du fichier JSON échouée.'];
    }

    return ['success' => true, 'message' => 'Sauvegarde réalisée avec succès.'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = file_get_contents($jsonFile);
    $data = json_decode($content, true);
    if ($data === null) die('Erreur JSON : ' . json_last_error_msg());

    foreach ($data as $key => $val) {
        if (isset($_POST[$key])) $data[$key] = $_POST[$key];
    }

    $saveResult = sauvegardeJsonAvecRemount($jsonFile, $data);

    if (!$saveResult['success']) {
        $message = '<div class="alert alert-danger text-center p-2 mb-3">' . htmlspecialchars($saveResult['message']) . '</div>';
    } else {
        $message = '<div class="alert alert-success text-center p-2">' . htmlspecialchars($saveResult['message']) . '</div>';
    }
} else {
    $content = file_get_contents($jsonFile);
    $data = json_decode($content, true);
    if ($data === null) die('Erreur JSON : ' . json_last_error_msg());

    if (empty($data['Mode'])) {
        $data['Mode'] = 'FM';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Configuration Node_Info</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
   body {
    background-color: #f8f9fa;
    padding: 30px;
    font-family: Arial, sans-serif;
    color: #444;
    border: 1px solid #DDDDE0 !important;
    border-radius: 15px !important;
    max-width: 1200px !important;
    margin: 20px auto !important;
    box-sizing: border-box;
}
    h2 {
        background-color: #3498db;
        color: white;
        padding: 18px 0;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 40px;
        font-weight: 700;
        box-shadow: 0 3px 8px rgb(0 0 0 / 0.2);
        user-select: none;
    }
    .input-group-text {
        min-width: 120px;
        background-color: #d9e9fb;
        color: #19569d;
        font-weight: 600;
        user-select: none;
    }
    .form-control:focus {
        border-color: #1872d1;
        box-shadow: 0 0 8px rgba(24,115,209,.4);
    }
    input[readonly] {
        background-color: #e9ecef !important;
        color: #6c757d !important;
        cursor: not-allowed;
    }
    .aide-champ {
        font-size: 12px;
        color: #6c757d;
        font-style: italic;
        margin-top: 2px;
        user-select: text;
    }
    button[type=submit] {
        background-color: #2980b9;
        border: none;
        padding: 14px 44px;
        border-radius: 10px;
        color: white;
        font-weight: 500;
        box-shadow: 0 5px 15px rgb(38 149 255 / 0.6);
        transition: background-color 0.3s ease;
        display: block;
        margin: 40px auto 0;
    }
    button[type=submit]:hover {
        background-color: #3498db;
    }
    footer.page-footer {
        background-color: #ecf0f1;
        text-align: center;
        font-size: 0.9rem;
        color: #7f8c8d;
        padding: 12px 10px;
        box-shadow: 0 -2px 7px rgb(0 0 0 / 0.07);
        position: fixed;
        bottom: 0;
        width: 100%;
        left: 0;
        user-select: none;
        z-index: 9999;
    }
    footer.page-footer a {
        color: #2874f0;
        text-decoration: none;
    }
    footer.page-footer a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<?= $message ?>
<form method="post" action="">
<?php 
    $ctcss_codes = [
        "67.0", "69.3", "71.9", "74.4", "77.0", "79.7", "82.5", "85.4", "88.5",
        "91.5", "94.8", "97.4", "100.0", "103.5", "107.2", "110.9", "114.8",
        "118.8", "123.0", "127.3", "131.8", "136.5", "141.3", "146.2", "151.4",
        "156.7", "162.2", "167.9", "173.8", "179.9", "186.2", "192.8", "203.5",
        "210.7", "218.1", "225.7", "233.6", "241.8"
    ];
?>
<?php foreach ($data as $key => $value): ?>
    <?php if ($key === 'Echolink') continue; ?>
    <?php if (!in_array($key, $champs_a_cacher)): ?>
        <?php if (in_array($key, $champs_avec_cadre)): ?>
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text" id="label-<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($key) ?></span>
                    <?php if ($key === 'Type'): ?>
                        <select class="form-select" name="Type" id="Type" aria-label="Type">
                            <?php for ($i = 1; $i <= 20; $i++): ?>
                                <option value="<?= $i ?>" <?= ($value == $i) ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    <?php elseif ($key === 'Mode'): ?>
                        <input type="text" class="form-control" name="Mode" id="Mode" value="<?= htmlspecialchars($value) ?>" readonly aria-describedby="label-Mode">
                    <?php elseif ($key === 'CTCSS'): ?>
                        <select class="form-select" name="CTCSS" id="CTCSS" aria-label="CTCSS">
                            <option value="">-- Aucun --</option>
                            <?php foreach ($ctcss_codes as $code): ?>
                                <option value="<?= $code ?>" <?= ($value == $code) ? 'selected' : '' ?>><?= $code ?> Hz</option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ($key === 'Location'): ?>
                        <select class="form-select" name="Location" id="Location" aria-label="Location">
                            <option value="">-- Sélectionnez votre région --</option>
                            <?php foreach ($regions as $region): ?>
                                <option value="<?= htmlspecialchars($region) ?>" <?= ($value == $region) ? 'selected' : '' ?>><?= htmlspecialchars($region) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control" name="<?= htmlspecialchars($key) ?>" id="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>" aria-describedby="label-<?= htmlspecialchars($key) ?>">
                    <?php endif; ?>
                </div>
                <?php if (isset($aides_champs[$key])): ?>
                    <div class="aide-champ"><?= $aides_champs[$key] ?></div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
        <?php endif; ?>
    <?php else: ?>
        <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
    <?php endif; ?>
<?php endforeach; ?>
<button type="submit" class="btn w-40">Sauvegarder les modifications</button>
</form>
<p class="text-center mt-3 text-muted" style="max-width:650px;margin:auto;">Sauvegarde automatique dans /opt/rolink/conf/ à chaque modification.</p>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
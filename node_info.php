<?php
$jsonFile = '/opt/rolink/conf/rolink.json';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = "";
$champs_a_cacher = ['pl_in', 'pl_out', 'rx_frq', 'tx_frq', 'isx', 'tip', 'LinkedTo'];

$champs_avec_cadre = [
    'Sysop', 'Name', 'LAT', 'LONG', 'nodeLocation', 'Location', 'Locator',
    'RXFREQ', 'TXFREQ', 'Website', 'Mode', 'Type', //'Echolink',
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
    //'Echolink' => 'Numéro Echolink si applicable.',
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = file_get_contents($jsonFile);
    $data = json_decode($content, true);
    if ($data === null) die('Erreur JSON : ' . json_last_error_msg());

    foreach ($data as $key => $val) {
        if (isset($_POST[$key])) $data[$key] = $_POST[$key];
    }

    if (!backupFile($jsonFile)) {
        $message = "<p style='color:red;'>Erreur sauvegarde impossible.</p>";
    }

    $res = file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    if ($res === false) {
        $message .= "<p style='color:red;'>Erreur écriture JSON.</p>";
    } else {
        $message .= "<p style='color:green;'>Modifications sauvegardées.</p>";
    }
} else {
    $content = file_get_contents($jsonFile);
    $data = json_decode($content, true);
    if ($data === null) die('Erreur JSON : ' . json_last_error_msg());

    // Valeur par défaut "FM" pour Mode si vide
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
<style>
    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background-color: #fafafa;
        padding: 20px;
    }

    .titre-node-info {
        text-align: center;
        background-color: #ffc966;
        padding: 15px 0;
        margin: 30px auto 40px;
        width: 600px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 26px;
        color: #333;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }

    .cadre-champ {
        border: 1px solid rgba(0, 0, 0, 0.12);
        background-color: #fefefe;
        padding: 12px 20px;
        margin-bottom: 20px;
        border-radius: 8px;
        max-width: 700px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 20px;
    }

    label {
        width: 150px;
        font-weight: 600;
        color: #444;
        user-select: none;
    }

    input[type=text], select {
        flex: 1;
        padding: 8px 12px;
        font-size: 16px;
        border: 1px solid #bbb;
        border-radius: 6px;
        transition: all 0.3s ease;
        outline-offset: 2px;
    }

    input[type=text]:focus, select:focus {
        border-color: #ffb900;
        box-shadow: 0 0 8px rgba(255,185,0,0.4);
    }

    button[type=submit] {
        display: block;
        margin: 30px auto 0;
        background-color: #ff8c00;
        color: white;
        padding: 14px 32px;
        border: none;
        border-radius: 40px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 700;
        box-shadow: 0 4px 10px rgba(255,140,0,0.6);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    button[type=submit]:hover {
        background-color: #ffa733;
        box-shadow: 0 6px 14px rgba(255,167,51,0.8);
    }

    .aide-champ {
        font-size: 13px;
        color: #666;
        margin-left: 10px;
        margin-top: -12px;
        margin-bottom: 10px;
        font-style: italic;
    }

    @media(max-width: 680px) {
        .titre-node-info {
            width: 90%;
        }
        .cadre-champ {
            flex-direction: column;
            align-items: flex-start;
            max-width: 100%;
        }
        label {
            width: 100%;
            margin-bottom: 6px;
        }
        .aide-champ {
            margin-left: 0;
            font-size: 12px;
            margin-top: 0;
        }
        input[type=text], select {
            width: 100%;
        }
    }

    .page-footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        background-color: #f8f9fa;
        box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
    }
    .text-center {
        text-align: center;
    }
    .small {
        font-size: 0.8rem;
    }
    .p-2 {
        padding: 0.5rem;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .text-primary {
        color: #007bff;
    }
</style>
</head>
<body>
<h2 class="titre-node-info">Configuration Node_Info</h2>
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
    <?php if ($key === 'Echolink') continue; // Ne pas afficher Echolink ?>
    <?php if (!in_array($key, $champs_a_cacher)): ?>
        <?php if (in_array($key, $champs_avec_cadre)): ?>
            <div class="cadre-champ">
                <label for="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($key) ?></label>
                <?php if ($key === 'Type'): ?>
                    <select name="Type" id="Type">
                        <?php for ($i = 1; $i <= 20; $i++): ?>
                            <option value="<?= $i ?>" <?= ($value == $i) ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                <?php elseif ($key === 'Mode'): ?>
                    <input type="text" name="Mode" id="Mode" value="FM" readonly style="background-color: #ddd; color: #555; cursor: not-allowed;">
                <?php elseif ($key === 'CTCSS'): ?>
                    <select name="CTCSS" id="CTCSS">
                        <option value="">-- Aucun --</option>
                        <?php
                        foreach ($ctcss_codes as $code) {
                            $selected = ($value == $code) ? 'selected' : '';
                            echo "<option value=\"$code\" $selected>$code Hz</option>";
                        }
                        ?>
                    </select>
                <?php elseif ($key === 'Location'): ?>
                    <select name="Location" id="Location">
                        <option value="">-- Sélectionnez votre région --</option>
                        <?php foreach ($regions as $region): ?>
                            <option value="<?= htmlspecialchars($region) ?>" <?= ($value == $region) ? 'selected' : '' ?>><?= htmlspecialchars($region) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="text" name="<?= htmlspecialchars($key) ?>" id="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                <?php endif; ?>
                <?php if (isset($aides_champs[$key])): ?>
                    <p class="aide-champ"><?= $aides_champs[$key] ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <label for="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($key) ?></label>
            <input type="text" name="<?= htmlspecialchars($key) ?>" id="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>"><br>
        <?php endif; ?>
    <?php else: ?>
        <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
    <?php endif; ?>
<?php endforeach; ?>
<button type="submit">Sauvegarder les modifications</button>
</form>
<p style="font-size:small; color:gray;">Sauvegarde automatique dans /opt/rolink/conf/ à chaque modification.</p>

<footer class="page-footer fixed-bottom font-small bg-light">
    <div class="text-center small p-2">
        2024 Copyright <a class="text-primary" target="_blank" href="https://github.com/yo6nam/RoLinkX-Dashboard">Razvan / YO6NAM</a> - Modification par FRS077 en 2025 pour le réseau RNFA
        <?php
        $versionFile = __DIR__ . '/version';
        if (is_readable($versionFile)) {
            $version = trim(file_get_contents($versionFile));
            echo " - Dashboard version $version";
        }
        ?>
    </div>
</footer>

</body>
</html>

<?php
$jsonFile = '/opt/rolink/conf/rolink.json';

// Debug (production : commenter)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$champs_a_cacher = ['pl_in', 'pl_out', 'rx_frq', 'tx_frq', 'isx', 'tip'];
$champs_avec_cadre = ['Sysop', 'Name', 'LAT', 'LONG', 'nodeLocation', 'Location', 'Locator', 'RXFREQ', 'TXFREQ', 'Website', 'Mode', 'Type', 'CTCSS', 'DefaultTG'];

$aides_champs = [
    'Sysop' => 'Nom du propriétaire.',
    'Name' => 'Nom du Node (ex: Jean).',
    'LAT' => 'Latitude décimale. <a href="https://www.coordonnees-gps.fr/" target="_blank">coordonnees-gps.fr</a>',
    'LONG' => 'Longitude décimale. <a href="https://www.coordonnees-gps.fr/" target="_blank">coordonnees-gps.fr</a>',
    'nodeLocation' => 'Ville (dépt) ex: Lens (62)',
    'Locator' => 'Locator radio. <a href="https://www.f4hxn.fr/qra-locator/" target="_blank">F4HXN</a>',
    'RXFREQ' => 'Fréquence RX (MHz)',
    'TXFREQ' => 'Fréquence TX (MHz)',
    'Website' => 'URL site web',
    'Type' => '1-20 (couleur carte)',
    'CTCSS' => 'Code CTCSS',
    'DefaultTG' => 'Talk Group défaut',
    'Location' => 'Région (ex: Bretagne)'
];

$regions = ['Auvergne-Rhône-Alpes','Bourgogne-Franche-Comté','Bretagne','Centre-Val de Loire','Corse','Grand Est','Hauts-de-France','Île-de-France','Normandie','Nouvelle-Aquitaine','Occitanie','Pays de la Loire','Provence-Alpes-Côte d\'Azur'];

function backupFile($filePath) {
    return copy($filePath, dirname($filePath).'/rolink_backup_'.date('dmY_His').'.json');
}

function remountRW() { exec('sudo mount -o remount,rw / 2>&1', $o, $r); return $r===0; }
function remountRO() { exec('sudo mount -o remount,ro / 2>&1', $o, $r); return $r===0; }

function saveJson($jsonFile, $data) {
    if (!remountRW() || !backupFile($jsonFile)) {
        remountRO(); return ['success'=>false, 'msg'=>'Sauvegarde impossible'];
    }
    $ok = file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) !== false;
    remountRO();
    return ['success'=>$ok, 'msg'=>$ok ? 'Sauvegarde OK' : 'Erreur écriture'];
}

$content = file_get_contents($jsonFile);
$data = json_decode($content, true) ?: die('Erreur JSON');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach($data as $k=>$v) if(isset($_POST[$k])) $data[$k] = $_POST[$k];
    $message = '<div class="alert alert-' . (saveJson($jsonFile,$data)['success'] ? 'success' : 'danger') . ' text-center p-2 mb-3">' . htmlspecialchars(saveJson($jsonFile,$data)['msg']) . '</div>';
} else {
    $data['Mode'] ??= 'FM';
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
    background:#f8f9fa; padding:30px; font-family:Arial,sans-serif; color:#444;
    border:3px solid #1872d1 !important; border-radius:15px !important;
    max-width:1200px !important; margin:20px auto !important; box-sizing:border-box;
}
h2 {background:#3498db; color:white; padding:18px 0; border-radius:10px; text-align:center; margin-bottom:40px; font-weight:700; box-shadow:0 3px 8px rgba(0,0,0,0.2); user-select:none;}
.input-group-text {min-width:120px; background:#d9e9fb !important; color:#19569d; font-weight:600; border:2px solid #1872d1 !important; user-select:none;}
.form-control {border:2px solid #1872d1 !important;}
.form-control:focus {border-color:#0d5aa0 !important; box-shadow:0 0 8px rgba(13,90,160,.5);}
input[readonly] {background:#e9ecef !important; color:#6c757d !important; cursor:not-allowed;}
.aide-champ {font-size:12px; color:#6c757d; font-style:italic; margin-top:2px;}
button[type=submit] {background:#2980b9; border:none; padding:14px 44px; border-radius:10px; color:white; font-weight:500; box-shadow:0 5px 15px rgba(38,149,255,0.6); transition:background-color .3s; display:block; margin:40px auto 0;}
button[type=submit]:hover {background:#3498db;}
</style>
</head>
<body>
<?= $message ?? '' ?>
<h2>Configuration Node_Info</h2>
<form method="post">
<?php 
$ctcss = ["67.0","69.3","71.9","74.4","77.0","79.7","82.5","85.4","88.5","91.5","94.8","97.4","100.0","103.5","107.2","110.9","114.8","118.8","123.0","127.3","131.8","136.5","141.3","146.2","151.4","156.7","162.2","167.9","173.8","179.9","186.2","192.8","203.5","210.7","218.1","225.7","233.6","241.8"];
foreach($data as $key=>$value):
    if($key==='Echolink' || in_array($key,$champs_a_cacher)){ echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">'; continue; }
    if(!in_array($key,$champs_avec_cadre)) continue;
?>
<div class="mb-4">
    <div class="input-group">
        <span class="input-group-text"><?= htmlspecialchars($key) ?></span>
        <?php if($key==='Type'): ?>
            <select class="form-select" name="Type"><?= str_repeat('<option value="'.$i.'" '.($value==$i?'selected':'').'>'.$i.'</option>', range(1,20)) ?>
        <?php elseif($key==='Mode'): ?>
            <input type="text" class="form-control" name="Mode" value="<?= htmlspecialchars($value) ?>" readonly>
        <?php elseif($key==='CTCSS'): ?>
            <select class="form-select" name="CTCSS"><option value="">-- Aucun --</option><?php foreach($ctcss as $c): ?><option value="<?= $c ?>" <?= $value==$c?'selected':'' ?>><?= $c ?> Hz</option><?php endforeach; ?></select>
        <?php elseif($key==='Location'): ?>
            <select class="form-select" name="Location"><option value="">-- Région --</option><?php foreach($regions as $r): ?><option value="<?= htmlspecialchars($r) ?>" <?= $value==$r?'selected':'' ?>><?= $r ?></option><?php endforeach; ?></select>
        <?php else: ?>
            <input type="text" class="form-control" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
        <?php endif; ?>
    </div>
    <?php if(isset($aides_champs[$key])): ?><div class="aide-champ"><?= $aides_champs[$key] ?></div><?php endif; ?>
</div>
<?php endforeach; ?>
<button type="submit">Sauvegarder</button>
</form>
<p class="text-center mt-3 text-muted" style="max-width:650px;margin:auto;">Sauvegarde auto /opt/rolink/conf/</p>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

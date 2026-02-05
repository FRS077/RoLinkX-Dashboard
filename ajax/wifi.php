<?php
/*
 *   RoLinkX Dashboard v3.7 - Wi-Fi management module (FIX iOS XR819)
 *   Copyright (C) 2024 by Razvan Marin YO6NAM / [www.xpander.ro](https://www.xpander.ro)
 *
 *   Fix iOS : ieee80211w=0 global + CCMP dans network (pas global qui crash)
 */

include __DIR__ . '/../includes/functions.php';
$wpaFile    = '/etc/wpa_supplicant/wpa_supplicant.conf';
$wpaTemp    = '/tmp/wpa_supplicant.tmp';
$maxNetworks = 5;
$weHaveData = false;

/* Get POST vars */
for ($i = 1; $i <= $maxNetworks; $i++) {
    ${"wn$i"} = isset($_POST["wn$i"]) ? filter_input(INPUT_POST, "wn$i", FILTER_SANITIZE_ADD_SLASHES) : '';
    ${"wk$i"} = isset($_POST["wk$i"]) ? filter_input(INPUT_POST, "wk$i", FILTER_SANITIZE_ADD_SLASHES) : '';
}

function wpa_passphrase($ssid, $passphrase)
{
    $bin = hash_pbkdf2('sha1', $passphrase, $ssid, 4096, 32, true);
    return bin2hex($bin);
}

function getSSIDs($wpaFile, $maxNetworks)
{
    $storedSSID = [];
    $storedPwds = [];
    $wpaBuffer = file_get_contents($wpaFile);

    preg_match_all('/ssid="(.*)"/', $wpaBuffer, $resultSSID);
    preg_match_all('/psk=(".*?"|\\S+)/', $wpaBuffer, $resultPWDS);

    if (empty($resultSSID[1]) || empty($resultPWDS[1])) {
        return false;
    }

    for ($i = 0; $i < $maxNetworks; $i++) {
        if (isset($resultSSID[1][$i])) {
            $storedSSID[] = $resultSSID[1][$i];
        }
        if (isset($resultPWDS[1][$i])) {
            $storedPwds[] = trim($resultPWDS[1][$i], '"');
        }
    }

    return [$storedSSID, $storedPwds];
}

/* Check for user input data */
for ($i = 1; $i <= $maxNetworks; $i++) {
    if (${"wn$i"} || ${"wk$i"}) {
        $weHaveData = true;
        break;
    }
}

$ssidList = getSSIDs($wpaFile, $maxNetworks);

$storedNetwork = [];
$storedAuthKey = [];

for ($i = 0; $i < $maxNetworks; $i++) {
    $storedNetwork[$i] = $ssidList ? ($ssidList[0][$i] ?? '') : '';
    $storedAuthKey[$i] = $ssidList ? ($ssidList[1][$i] ?? '') : '';
}

/* Networks and Validation */
for ($i = 1; $i <= $maxNetworks; $i++) {
    ${"network$i"} = $storedNetwork[$i - 1];
    ${"authKey$i"} = $storedAuthKey[$i - 1];

    if (${"wn$i"} == '-') {
         ${"network$i"} = '';
    } elseif (${"wn$i"} && strlen(${"wk$i"}) < 8) {
		echo 'Network <b>'. ${"wn$i"} .'</b> : Clé de sécurité réseau non valide !';
		exit(1);
    } elseif (${"wn$i"} && ${"wn$i"} != $storedNetwork[$i - 1]) {
        ${"network$i"} = ${"wn$i"};
    }

    if (${"wk$i"} && ${"wk$i"} != $storedAuthKey[$i - 1]) {
        ${"authKey$i"} = ${"wk$i"};
    }
}

/* wpa_supplicant.conf CORRIGÉ pour iOS + XR819 (pas de crash) */
$wpaData = 'ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev
update_config=1
ap_scan=1
fast_reauth=1
country=FR
# Fix iPhone XR819 (global seulement)
ieee80211w=0' . PHP_EOL;

for ($i = 1; $i <= $maxNetworks; $i++) {
    if (!empty(${"network$i"})) {
        $psk = (strlen(${"authKey$i"}) < 32 || ctype_xdigit(${"authKey$i"})) ? wpa_passphrase(${"network$i"}, ${"authKey$i"}) : ${"authKey$i"};
        $wpaData .= 'network={
	ssid=' . json_encode(${"network$i"}) . '
	psk=' . $psk . '
	key_mgmt=WPA-PSK
	scan_ssid=1
	priority=1
	# CCMP dans network (sûr)
	pairwise=CCMP
	group=CCMP
	proto=RSN
}' . PHP_EOL;
    }
}

if ($weHaveData) {
    toggleFS(true);
    file_put_contents($wpaTemp, $wpaData);
    exec("/usr/bin/sudo /usr/bin/cp $wpaTemp $wpaFile");
    toggleFS(false);
    echo '✅ Config Wi-Fi iOS sauvée ! Redémarre : sudo wpa_cli -i wlan0 reconfigure';
} else {
    echo 'Aucune modification.';
}
?>

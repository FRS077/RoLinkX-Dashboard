<?php
/*
 *   RoLinkX Dashboard v3.7 - Wi-Fi management module (FIX iOS XR819 + CRASH)
 *   Copyright (C) 2024 by Razvan Marin YO6NAM / [www.xpander.ro](https://www.xpander.ro)
 *
 *   Fix iOS : ieee80211w=0 + CCMP network only. Fix crash: quotes ssid/psk + restart service
 */

include __DIR__ . '/../includes/functions.php';
$wpaFile    = '/etc/wpa_supplicant/wpa_supplicant.conf';
$wpaTemp    = '/tmp/wpa_supplicant.tmp';
$maxNetworks = 5;
$weHaveData = false;

/* Get POST vars */
for ($i = 1; $i <= $maxNetworks; $i++) {
    ${"wn$i"} = isset($_POST["wn$i"]) ? filter_input(INPUT_POST, "wn$i", FILTER_SANITIZE_STRING) : '';
    ${"wk$i"} = isset($_POST["wk$i"]) ? $_POST["wk$i"] : '';  // No sanitize for psk hex/pass
}

function wpa_passphrase($ssid, $passphrase) {
    $bin = hash_pbkdf2('sha1', $passphrase, $ssid, 4096, 32, true);
    return bin2hex($bin);
}

function getSSIDs($wpaFile, $maxNetworks) {
    $storedSSID = [];
    $storedPwds = [];
    $wpaBuffer = @file_get_contents($wpaFile);
    if (!$wpaBuffer) return false;

    preg_match_all('/ssid[\\s]*=[\\s]*["\']([^"\']+)["\']/', $wpaBuffer, $resultSSID);
    preg_match_all('/psk[\\s]*=[\\s]*([0-9a-fA-F]{64}|[^\\s]+)/', $wpaBuffer, $resultPWDS);

    for ($i = 0; $i < min($maxNetworks, count($resultSSID[1])); $i++) {
        $storedSSID[] = $resultSSID[1][$i];
    }
    for ($i = 0; $i < min($maxNetworks, count($resultPWDS[1])); $i++) {
        $storedPwds[] = $resultPWDS[1][$i];
    }
    return [array_pad($storedSSID, $maxNetworks, ''), array_pad($storedPwds, $maxNetworks, '')];
}

/* Check input */
for ($i = 1; $i <= $maxNetworks; $i++) {
    if (!empty(${"wn$i"}) || !empty(${"wk$i"})) {
        $weHaveData = true;
        break;
    }
}

$ssidList = getSSIDs($wpaFile, $maxNetworks);
$storedNetwork = $ssidList ? $ssidList[0] : array_fill(0, $maxNetworks, '');
$storedAuthKey = $ssidList ? $ssidList[1] : array_fill(0, $maxNetworks, '');

/* Update networks */
for ($i = 0; $i < $maxNetworks; $i++) {
    $network = $storedNetwork[$i];
    $authKey = $storedAuthKey[$i];
    $newSSID = ${"wn".($i+1)};
    $newKey = ${"wk".($i+1)};

    if ($newSSID === '-') {
        $network = '';
    } elseif ($newSSID && (strlen($newKey) < 8 || strlen($newKey) > 64)) {
        die('Erreur: Clé pour "' . htmlspecialchars($newSSID) . '" invalide (8-63 chars ou 64 hex).');
    } elseif ($newSSID) {
        $network = $newSSID;
    }
    if ($newKey && $newKey !== $authKey) {
        $authKey = $newKey;
    }
    ${"network".($i+1)} = $network;
    ${"authKey".($i+1)} = $authKey;
}

/* Build config */
$wpaData = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev\n".
          "update_config=1\n".
          "ap_scan=1\n".
          "fast_reauth=1\n".
          "country=FR\n".
          "# Fix iPhone XR819\n".
          "ieee80211w=0\n";

for ($i = 1; $i <= $maxNetworks; $i++) {
    if (!empty(${"network$i"})) {
        $ssid = addslashes(${"network$i"});
        $pass = ${"authKey$i"};
        $psk = (strlen($pass) == 64 && ctype_xdigit($pass)) ? $pass : wpa_passphrase(${"network$i"}, $pass);
        $wpaData .= "network={\n".
                    "    ssid=\"{$ssid}\"\n".
                    "    psk={$psk}\n".
                    "    key_mgmt=WPA-PSK\n".
                    "    scan_ssid=1\n".
                    "    priority=1\n".
                    "    pairwise=CCMP\n".
                    "    group=CCMP\n".
                    "    proto=RSN\n".
                    "}\n";
    }
}

if ($weHaveData) {
    if (!function_exists('toggleFS')) {
        function toggleFS($enable) {
            exec("sudo mount -o remount," . ($enable ? "rw" : "ro") . " /");
        }
    }
    toggleFS(true);
    file_put_contents($wpaTemp, $wpaData);
    exec("sudo cp {$wpaTemp} {$wpaFile}");
    exec("sudo systemctl restart wpa_supplicant@wlan0 || sudo killall wpa_supplicant && sudo wpa_supplicant -B -i wlan0 -c {$wpaFile}");
    toggleFS(false);
    echo "Config sauvée et Wi-Fi redémarré !";
} else {
    echo "Aucun changement.";
}
?>

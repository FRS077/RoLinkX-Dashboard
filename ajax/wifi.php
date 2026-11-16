<?php
/*
 *   RoLinkX Dashboard v3.7
 *   Copyright (C) 2024 by Razvan Marin YO6NAM / www.xpander.ro
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */

/*
 * Wi-Fi management module (ajax/wifi.php)
 *
 * POST : wn1..wn5 (SSID, "-" pour supprimer), wk1..wk5 (clé)
 *
 * Le fichier wpa_supplicant.conf est analysé bloc par bloc : l'en-tête
 * (country, update_config...) et les réseaux non modifiés (y compris ceux
 * ajoutés par WPS ou au-delà du 5e) sont conservés tels quels.
 *
 * Règle sudoers nécessaire (inchangée) :
 *   www-data ALL=(root) NOPASSWD: /usr/bin/cp /tmp/wpa_supplicant.tmp /etc/wpa_supplicant/wpa_supplicant.conf, \
 *       <chemin de wpa_cli, voir "which wpa_cli"> -i wlan0 reconfigure
 */

include __DIR__ . '/../includes/functions.php';

const WPA_FILE     = '/etc/wpa_supplicant/wpa_supplicant.conf';
const WPA_TEMP     = '/tmp/wpa_supplicant.tmp';
const WPA_LOCK     = '/tmp/rolinkx_wifi.lock';
const MAX_NETWORKS = 5;

const DEFAULT_HEADER = "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev\nupdate_config=1\nap_scan=1\nfast_reauth=1\ncountry=FR";

function fail($msg)
{
    echo $msg;
    exit(1);
}

/* Protection : POST uniquement + en-tête X-Requested-With (envoyé par jQuery, non forgeable
   depuis un autre site sans CORS). L'authentification reste celle de ton dashboard / serveur web. */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    fail('Méthode non autorisée');
}
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(403);
    fail('Requête refusée (en-tête X-Requested-With manquant)');
}

/* ---------- Fonctions utilitaires ---------- */
function wpa_passphrase($ssid, $passphrase)
{
    return bin2hex(hash_pbkdf2('sha1', $passphrase, $ssid, 4096, 32, true));
}

/* Décode un SSID du fichier : "texte" ou hexadécimal */
function parseSsid($v)
{
    $v = trim($v);
    if (preg_match('/^"(.*)"$/s', $v, $m)) {
        return $m[1];
    }
    if (preg_match('/^[0-9a-fA-F]+$/', $v) && strlen($v) % 2 === 0) {
        return hex2bin($v);
    }
    return $v;
}

/* Encode un SSID pour le fichier : texte si ASCII simple, sinon hexadécimal
   (json_encode produisait des \uXXXX, incompris par wpa_supplicant) */
function formatSsid($s)
{
    return preg_match('/^[\x20\x21\x23-\x7E]+$/', $s) ? '"' . $s . '"' : bin2hex($s);
}

function validSsid($s)
{
    return strlen($s) >= 1 && strlen($s) <= 32 && !preg_match('/[\x00-\x1F\x7F]/', $s);
}

function validKey($k)
{
    if (preg_match('/^[0-9a-fA-F]{64}$/', $k)) {
        return true; // PSK déjà haché
    }
    return strlen($k) >= 8 && strlen($k) <= 63 && preg_match('/^[\x20-\x7E]+$/', $k);
}

/* Analyse le fichier : en-tête + liste de réseaux (ssid, psk, bloc brut) */
function parseConfig($file)
{
    $buf = @file_get_contents($file);
    if ($buf === false) {
        return null;
    }
    $header = $buf;
    $pos = preg_match('/^[ \t]*network[ \t]*=[ \t]*\{/m', $buf, $m, PREG_OFFSET_CAPTURE) ? $m[0][1] : null;
    if ($pos !== null) {
        $header = substr($buf, 0, $pos);
    }
    $header = trim($header);
    if ($header === '') {
        $header = DEFAULT_HEADER;
    }

    $networks = array();
    if (preg_match_all('/network[ \t]*=[ \t]*\{(.*?)\}/s', $buf, $blocks, PREG_SET_ORDER)) {
        foreach ($blocks as $b) {
            $ssid = preg_match('/^[ \t]*ssid[ \t]*=[ \t]*(.+?)[ \t]*$/m', $b[1], $s) ? parseSsid($s[1]) : '';
            $psk  = preg_match('/^[ \t]*psk[ \t]*=[ \t]*(.+?)[ \t]*$/m', $b[1], $p) ? trim($p[1], '"') : '';
            $networks[] = array('ssid' => $ssid, 'psk' => $psk, 'raw' => trim($b[0]));
        }
    }
    return array($header, $networks);
}

/* Compatibilité avec l'ancienne API : [[ssid...], [psk...]] */
function getSSIDs($wpaFile, $maxNetworks)
{
    $cfg = parseConfig($wpaFile);
    if (!$cfg || empty($cfg[1])) {
        return false;
    }
    $ssids = array();
    $pwds  = array();
    foreach (array_slice($cfg[1], 0, $maxNetworks) as $n) {
        $ssids[] = $n['ssid'];
        $pwds[]  = $n['psk'];
    }
    return array($ssids, $pwds);
}

function buildBlock($ssid, $psk)
{
    return "network={\n\tssid=" . formatSsid($ssid) . "\n\tpsk=" . $psk . "\n\tkey_mgmt=WPA-PSK\n\tscan_ssid=1\n}";
}

function writeConfig($data)
{
    if (is_link(WPA_TEMP)) {
        return false; // fichier temporaire détourné
    }
    @unlink(WPA_TEMP);
    if (file_put_contents(WPA_TEMP, $data, LOCK_EX) === false) {
        return false;
    }
    chmod(WPA_TEMP, 0600);
    exec('/usr/bin/sudo /usr/bin/cp ' . escapeshellarg(WPA_TEMP) . ' ' . escapeshellarg(WPA_FILE) . ' 2>&1', $out, $rc);
    @unlink(WPA_TEMP);
    return $rc === 0;
}

/* ---------- Lecture des données envoyées (brutes, sans filtre destructeur) ---------- */
$input   = array();
$haveNew = false;
for ($i = 1; $i <= MAX_NETWORKS; $i++) {
    $wn = isset($_POST["wn$i"]) && is_string($_POST["wn$i"]) ? $_POST["wn$i"] : '';
    $wk = isset($_POST["wk$i"]) && is_string($_POST["wk$i"]) ? $_POST["wk$i"] : '';
    $wn = trim($wn, "\r\n\t");
    $input[$i] = array($wn, $wk);
    if ($wn !== '' || $wk !== '') {
        $haveNew = true;
    }
}

if (!$haveNew) {
    echo 'Aucune nouvelle donnée, rien n\'a été modifié';
    exit;
}

/* Verrou : une seule écriture à la fois */
$lock = fopen(WPA_LOCK, 'c');
if (!$lock || !flock($lock, LOCK_EX)) {
    fail('Impossible d\'obtenir le verrou, réessayez');
}

$cfg = parseConfig(WPA_FILE);
if ($cfg === null) {
    fail('Impossible de lire ' . WPA_FILE);
}
list($header, $networks) = $cfg;

/* Les 5 premiers réseaux = emplacements de l'interface ; le reste est conservé tel quel */
$slots = array();
for ($i = 0; $i < MAX_NETWORKS; $i++) {
    $slots[$i] = isset($networks[$i]) ? $networks[$i] : null;
}
$extras = array_slice($networks, MAX_NETWORKS);

/* ---------- Application et validation ---------- */
for ($i = 1; $i <= MAX_NETWORKS; $i++) {
    list($wn, $wk) = $input[$i];
    $idx = $i - 1;
    $cur = $slots[$idx];

    if ($wn === '-') {
        $slots[$idx] = null;
        continue;
    }
    if ($wn === '' && $wk === '') {
        continue;
    }

    $ssid = ($wn !== '') ? $wn : ($cur ? $cur['ssid'] : '');
    if ($ssid === '') {
        fail('Réseau ' . $i . ' : SSID manquant !');
    }
    if (!validSsid($ssid)) {
        fail('Réseau <b>' . htmlspecialchars($ssid) . '</b> : SSID non valide (1 à 32 caractères) !');
    }

    $ssidChanged = !$cur || $ssid !== $cur['ssid'];

    if ($wk === '') {
        if ($ssidChanged) {
            fail('Network <b>' . htmlspecialchars($ssid) . '</b> : Clé de sécurité réseau non valide !');
        }
        continue; // SSID inchangé, pas de nouvelle clé : rien à faire
    }
    if (!validKey($wk)) {
        fail('Network <b>' . htmlspecialchars($ssid) . '</b> : Clé de sécurité réseau non valide (8 à 63 caractères) !');
    }

    $psk = preg_match('/^[0-9a-fA-F]{64}$/', $wk) ? strtolower($wk) : wpa_passphrase($ssid, $wk);
    if (!$ssidChanged && $cur['psk'] === $psk) {
        continue; // identique à l'existant
    }
    $slots[$idx] = array('ssid' => $ssid, 'psk' => $psk, 'raw' => null);
}

/* ---------- Construction du nouveau fichier ---------- */
$wpaData = $header . PHP_EOL . PHP_EOL;
foreach (array_merge($slots, $extras) as $n) {
    if (!$n || $n['ssid'] === '') {
        continue;
    }
    $wpaData .= ($n['raw'] !== null ? $n['raw'] : buildBlock($n['ssid'], $n['psk'])) . PHP_EOL . PHP_EOL;
}

/* ---------- Écriture ---------- */
toggleFS(true);
try {
    $ok = writeConfig($wpaData);
} finally {
    toggleFS(false);
}

if ($ok) {
    /* Recharge la config dans wpa_supplicant : sans ça, un réseau supprimé reste actif en mémoire
       et réapparaît au prochain save_config (par ex. après un WPS). */
    exec('/usr/bin/sudo wpa_cli -i wlan0 reconfigure 2>&1', $rcOut);
    $reloaded = (bool) preg_match('/^OK$/m', implode("\n", $rcOut));
    echo 'Nouvelles données enregistrées..<br/>' . ($reloaded
        ? 'Configuration Wi-Fi rechargée.'
        : 'Redémarrez le Wi-Fi maintenant ou redémarrez le système !');
} else {
    echo 'Erreur : impossible d\'écrire ' . WPA_FILE . ' (droits sudo ou système de fichiers en lecture seule ?)';
}

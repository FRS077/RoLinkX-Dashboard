<?php
/*
 *   RoLinkX Dashboard v3.68
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
 * SVXLink / EchoLink configuration module
 */

if (empty($_POST) && empty($_GET)) {
    exit;
}

include __DIR__ . '/../includes/functions.php';
$restoreFile  = '/var/www/html/assets/rolink.conf';
$newFile      = '/tmp/rolink.conf.tmp';
$newElFile    = '/tmp/ModuleEchoLink.conf.tmp';
$profilesPath = dirname(__FILE__) . '/../profiles/';
$newProfile   = false;
$changes      = $changesEl      = 0;
$msgOut       = null;
$oldVar       = $newVar       = $oldElVar       = $newElVar       = $profiles       = [];

// Read version installed
$version = version();

// Populate profile from GET vars
$frmLoadProfile = (isset($_GET['lpn'])) ? filter_input(INPUT_GET, 'lpn', FILTER_SANITIZE_ADD_SLASHES) : '';

// Proxy list (EchoLink)
if (isset($_GET['getProxyList'])) {
    $proxyList = @file_get_contents('https://rolink.network/api/echolink/proxyList');
    if ($proxyList !== false) {
        $ipList = json_decode($proxyList, true);
        if (is_array($ipList['proxies']['available'])) {
            echo json_encode($ipList['proxies']['available']);
            exit;
        }
    }
    echo json_encode(["Failed to fetch proxy list."]);
    exit;
}

// Retrieve POST vars (defaults if empty values to avoid locking the config file)
$frmProfile     = (isset($_POST['prn'])) ? filter_input(INPUT_POST, 'prn', FILTER_SANITIZE_ADD_SLASHES) : '';
$frmReflector   = (empty($_POST['ref'])) ? 'f62dmr.fr' : preg_replace('/^(http(s)?:\/\/)?(www.)?|(\/)/i', '', filter_input(INPUT_POST, 'ref', FILTER_SANITIZE_ADD_SLASHES));
$frmPort        = (empty($_POST['prt'])) ? '5300' : filter_input(INPUT_POST, 'prt', FILTER_SANITIZE_NUMBER_INT);
$frmCallsign    = (empty($_POST['cal'])) ? 'USER' : preg_replace('/[^\w-]/', '', filter_input(INPUT_POST, 'cal', FILTER_SANITIZE_ADD_SLASHES));
$frmAuthKey     = (empty($_POST['key'])) ? 'USER' : trim(filter_input(INPUT_POST, 'key', FILTER_SANITIZE_ADD_SLASHES));
$frmBeacon      = (empty($_POST['clb'])) ? 'HOTLINK' : preg_replace('/[^\w-]/', '', filter_input(INPUT_POST, 'clb', FILTER_SANITIZE_ADD_SLASHES));
$frmVoice       = (empty($_POST['vop'])) ? 'fr_FR' : filter_input(INPUT_POST, 'vop', FILTER_SANITIZE_ADD_SLASHES);
$frmShortId     = (empty($_POST['sid'])) ? '0' : filter_input(INPUT_POST, 'sid', FILTER_SANITIZE_ADD_SLASHES);
$frmLongId      = (empty($_POST['lid'])) ? '0' : filter_input(INPUT_POST, 'lid', FILTER_SANITIZE_ADD_SLASHES);
$frmType        = (empty($_POST['tip'])) ? 'Node' : filter_input(INPUT_POST, 'tip', FILTER_SANITIZE_ADD_SLASHES);
$frmBitrate     = (empty($_POST['cbr'])) ? '20000' : filter_input(INPUT_POST, 'cbr', FILTER_SANITIZE_ADD_SLASHES);
$frmRogerBeep   = (empty($_POST['rgr'])) ? '1' : filter_input(INPUT_POST, 'rgr', FILTER_SANITIZE_NUMBER_INT);
$frmRxGPIO      = (empty($_POST['rxp'])) ? 'gpio10' : filter_input(INPUT_POST, 'rxp', FILTER_SANITIZE_ADD_SLASHES);
$frmTxGPIO      = (empty($_POST['txp'])) ? 'gpio7' : filter_input(INPUT_POST, 'txp', FILTER_SANITIZE_ADD_SLASHES);
$frmDefaultTg   = (empty($_POST['dtg'])) ? '59' : trim(filter_input(INPUT_POST, 'dtg', FILTER_SANITIZE_NUMBER_INT));
$frmMonitorTgs  = (empty($_POST['mtg'])) ? '59,62' : filter_input(INPUT_POST, 'mtg', FILTER_SANITIZE_ADD_SLASHES);
$frmTgTimeOut   = (empty($_POST['tgt'])) ? '30' : filter_input(INPUT_POST, 'tgt', FILTER_SANITIZE_NUMBER_INT);
$frmACStatus    = (empty($_POST['acs'])) ? '0' : filter_input(INPUT_POST, 'acs', FILTER_SANITIZE_NUMBER_INT);
$frmDeEmphasis  = (empty($_POST['rxe'])) ? '0' : filter_input(INPUT_POST, 'rxe', FILTER_SANITIZE_NUMBER_INT);
$frmPreEmphasis = (empty($_POST['txe'])) ? '0' : filter_input(INPUT_POST, 'txe', FILTER_SANITIZE_NUMBER_INT);
$frmMasterGain  = (empty($_POST['mag'])) ? '0' : filter_input(INPUT_POST, 'mag', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$frmLimiter     = (empty($_POST['lim'])) ? '0' : filter_input(INPUT_POST, 'lim', FILTER_SANITIZE_NUMBER_FLOAT);
$frmReconnectS  = (empty($_POST['res'])) ? '5' : filter_input(INPUT_POST, 'res', FILTER_SANITIZE_NUMBER_INT);
$frmTxTimeOut   = (empty($_POST['txt'])) ? '240' : filter_input(INPUT_POST, 'txt', FILTER_SANITIZE_NUMBER_INT);
$frmSqlDelay    = (empty($_POST['sqd'])) ? '100' : filter_input(INPUT_POST, 'sqd', FILTER_SANITIZE_NUMBER_INT);
$frmDelProfile  = (empty($_POST['prd'])) ? '' : filter_input(INPUT_POST, 'prd', FILTER_SANITIZE_ADD_SLASHES);
$frmFanStart    = (empty($_POST['fan'])) ? '0' : filter_input(INPUT_POST, 'fan', FILTER_SANITIZE_NUMBER_INT);
$frmModules     = (empty($_POST['mod'])) ? '2' : filter_input(INPUT_POST, 'mod', FILTER_SANITIZE_NUMBER_INT);
$frmTxDelay     = (empty($_POST['txd'])) ? '875' : filter_input(INPUT_POST, 'txd', FILTER_SANITIZE_NUMBER_INT);

/* EchoLink module */
$frmElCallsign        = preg_replace('/[^\w-]/', '', filter_input(INPUT_POST, 'el_cal', FILTER_SANITIZE_ADD_SLASHES));
$frmElPassword        = trim(filter_input(INPUT_POST, 'el_pwd', FILTER_SANITIZE_ADD_SLASHES));
$frmElSysop           = trim(filter_input(INPUT_POST, 'el_sop', FILTER_SANITIZE_ADD_SLASHES));
$frmElLocation        = filter_input(INPUT_POST, 'el_loc', FILTER_SANITIZE_ADD_SLASHES);
$frmElTimeout         = trim(filter_input(INPUT_POST, 'el_to', FILTER_SANITIZE_NUMBER_INT));
$frmElLinkIdleTimeout = trim(filter_input(INPUT_POST, 'el_lit', FILTER_SANITIZE_NUMBER_INT));

$frmElProxyServer   = (empty($_POST['el_pxsrv'])) ? 'the.proxy.server' : preg_replace('/^(http(s)?:\/\/)?(www.)?|(\/)/i', '', filter_input(INPUT_POST, 'el_pxsrv', FILTER_SANITIZE_ADD_SLASHES));
$frmElProxyPort     = trim(filter_input(INPUT_POST, 'el_pxp', FILTER_SANITIZE_NUMBER_INT));
$frmElProxyPassword = trim(filter_input(INPUT_POST, 'el_pxpw', FILTER_SANITIZE_ADD_SLASHES));

/* Process DTMF commands */
if (isset($_POST['dtmfCommand'])) {
    $dtmfCommand = (!empty($_POST['dtmfCommand'])) ? filter_input(INPUT_POST, 'dtmfCommand', FILTER_SANITIZE_ADD_SLASHES) : null;
    if (!is_link('/tmp/dtmf')) {
        echo "Le service HotLink ne fonctionne pas !";
        return false;
    }
    if (!empty($dtmfCommand)) {
        exec('/usr/bin/sudo /usr/bin/chmod guo+rw /tmp/dtmf');
        exec("/usr/bin/echo '$dtmfCommand' >/tmp/dtmf", $reply);
        echo "<b>$dtmfCommand</b> executed!";
    }
    exit(0);
}

/* Process restore command */
if (isset($_POST['restore'])) {
    if (!is_file($restoreFile)) {
        echo "Restauration des données non disponible !";
        exit(1);
    }
    toggleFS(true);
    file_put_contents('/tmp/rolink.conf.tmp', file_get_contents($restoreFile));
    exec("/usr/bin/sudo /usr/bin/cp /tmp/rolink.conf.tmp /opt/rolink/conf/rolink.conf");
    toggleFS(false);
    serviceControl('rolink.service', 'restart');
    echo "Configuration du HotLink restaurée aux valeurs par défaut";
    exit(0);
}

// Add the backup reflector if using RoLink.Network
$backupReflector = 'backup.rolink.network';
$reflectors      = explode(',', $frmReflector);
if (array_intersect(['rolink.network', 'svx.439100.ro'], $reflectors)) {
    if (!in_array($backupReflector, $reflectors)) {
        $reflectors[] = $backupReflector;
    }
}
$frmReflector = implode(',', $reflectors);

// Get current variables
$oldCfg = file_get_contents($cfgFile);
preg_match('/(CALLSIGN=")(\S+)"/', $oldCfg, $varCallSign);
preg_match('/(HOST=)(\S+)/', $oldCfg, $varReflector);
preg_match('/(^PORT=)(\d+)/m', $oldCfg, $varPort);
preg_match('/(AUTH_KEY=)"(\S+)"/', $oldCfg, $varAuthKey);
preg_match('/(CALLSIGN=)(\w\S+)/', $oldCfg, $varBeacon);
preg_match('/(DEFAULT_LANG=)(\S+)/', $oldCfg, $varVoicePack);
preg_match('/(SHORT_IDENT_INTERVAL=)(\d+)/', $oldCfg, $varShortIdent);
preg_match('/(LONG_IDENT_INTERVAL=)(\d+)/', $oldCfg, $varLongIdent);
preg_match('/(OPUS_ENC_BITRATE=)(\d+)/', $oldCfg, $varCodecBitRate);
preg_match('/(RGR_SOUND_ALWAYS=)(\d+)/', $oldCfg, $varRogerBeep);
preg_match('/(GPIO_SQL_PIN=)(\S+)/', $oldCfg, $varRxGPIO);
preg_match('/(PTT_PIN=)(\S+)/', $oldCfg, $varTxGPIO);
preg_match('/(MONITOR_TGS=)(.+)/', $oldCfg, $varMonitorTgs);
preg_match('/(TG_SELECT_TIMEOUT=)(\d+)/', $oldCfg, $varTgSelTimeOut);
preg_match('/(SQL_DELAY=)(\d+)/', $oldCfg, $varSqlDelay);
preg_match('/(TIMEOUT=)(\d+)\nTX/', $oldCfg, $varTxTimeout);
// Since 1.7.99.62
preg_match('/(HOSTS=)(\S+)/', $oldCfg, $varRefHosts);
preg_match('/(HOST_PORT=)(\d+)/', $oldCfg, $varPorts);
// Since 1.7.99.68-r2
preg_match('/(ANNOUNCE_CONNECTION_STATUS=)(\d+)/', $oldCfg, $announceConnectionStatus);
// Power Hotspot
preg_match('/(DEEMPHASIS=)(\d+)\n/', $oldCfg, $varDeEmphasis);
preg_match('/(PREEMPHASIS=)(\d+)\n/', $oldCfg, $varPreEmphasis);
preg_match('/(MASTER_GAIN=)(-?\d+(\.\d{1,2})?)\n/', $oldCfg, $varMasterGain);
preg_match('/(LIMITER_THRESH=)(-?\d+)\n/', $oldCfg, $varLimiter);
preg_match('/(RECONNECT_SECONDS=)(\d+)\n/', $oldCfg, $varReconnectSeconds);
// Since 1.7.99.86-2
preg_match('/(FAN_START=)(\d+)/', $oldCfg, $varFanStart);
// Since 1.7.99.88-2
preg_match('/(#?)(MODULES=)(\S+)/', $oldCfg, $varModules);
// Since 1.7.99.91-4
preg_match('/(DEFAULT_TG=)(.+)/', $oldCfg, $varDefaultTg);
$frmDefaultTg = (empty($frmDefaultTg)) ? '226' : $frmDefaultTg;
preg_match('/(TX_DELAY=)(\d+)/', $oldCfg, $varTxDelay);

// Get current EchoLink module variables
$oldElCfg = file_get_contents($cfgELFile);
preg_match('/CALLSIGN=(\S+)/', $oldElCfg, $elCallSign);
preg_match('/PASSWORD=(\S+)/', $oldElCfg, $elPassword);
preg_match('/SYSOPNAME=(.*)/', $oldElCfg, $elSysop);
preg_match('/TIMEOUT=(\d+)/', $oldElCfg, $elTimeout);
preg_match('/LOCATION=(.*)/', $oldElCfg, $elLocation);
preg_match('/LINK_IDLE_TIMEOUT=(.*)/', $oldElCfg, $elLinkIdleTimeout);
preg_match('/(#?)(PROXY_SERVER=)(.*)/', $oldElCfg, $elProxyServer);
preg_match('/(#?)(PROXY_PORT=)(\d+)/', $oldElCfg, $elProxyPort);
preg_match('/(#?)(PROXY_PASSWORD=)(\S+)/', $oldElCfg, $elProxyPassword);

// Safe category values
$reflectorValue    = (isset($varReflector[2])) ? $varReflector[2] : '';
$portValue         = (isset($varPort[2])) ? $varPort[2] : '';
$callSignValue     = (isset($varCallSign[2])) ? $varCallSign[2] : '';
$authKeyValue      = (isset($varAuthKey[2])) ? $varAuthKey[2] : '';
$beaconValue       = (isset($varBeacon[2])) ? $varBeacon[2] : '';
$voicePackValue    = (isset($varVoicePack[2])) ? $varVoicePack[2] : '';
$shortIdentValue   = (isset($varShortIdent[2])) ? $varShortIdent[2] : '';
$longIdentValue    = (isset($varLongIdent[2])) ? $varLongIdent[2] : '';
$codecBitrateValue = (isset($varCodecBitRate[2])) ? $varCodecBitRate[2] : '';
$rogerBeepValue    = (isset($varRogerBeep[2])) ? $varRogerBeep[2] : '';
// Since 1.7.99.62
$refHostsValue = (isset($varRefHosts[2])) ? $varRefHosts[2] : '';
$portsValue    = (isset($varPorts[2])) ? $varPorts[2] : '';

// Advanced category values
$rxGPIOValue      = (isset($varRxGPIO[2])) ? $varRxGPIO[2] : '';
$txGPIOValue      = (isset($varTxGPIO[2])) ? $varTxGPIO[2] : '';
$monitorTgsValue  = (isset($varMonitorTgs[2])) ? $varMonitorTgs[2] : '';
$tgSelectTOValue  = (isset($varTgSelTimeOut[2])) ? $varTgSelTimeOut[2] : '';
$txTimeOutValue   = (isset($varTxTimeout[2])) ? $varTxTimeout[2] : '';
$sqlDelayValue    = (isset($varSqlDelay[2])) ? $varSqlDelay[2] : '';
$txDelayValue     = (isset($varTxDelay[2])) ? $varTxDelay[2] : '';
$acsValue         = (isset($announceConnectionStatus[2])) ? $announceConnectionStatus[2] : null;
$preEmphasisValue = (isset($varPreEmphasis[2])) ? $varPreEmphasis[2] : 0;
$deEmphasisValue  = (isset($varDeEmphasis[2])) ? $varDeEmphasis[2] : 0;
$masterGainValue  = (isset($varMasterGain[2])) ? $varMasterGain[2] : null;
$limiterValue     = (isset($varLimiter[2])) ? $varLimiter[2] : null;
$reconnectSValue  = (isset($varReconnectSeconds[2])) ? $varReconnectSeconds[2] : null;
// Since 1.7.99.86-2
$fanStartValue = (isset($varFanStart[2])) ? $varFanStart[2] : 0;
// Since 1.7.99.88-2 (1.7.99.91-8)
$modulesValue = null;
if ($varModules[1] == '#') {
    $modulesValue = "0";
} else {
    $modulesEnabled = explode(',', strtolower(trim($varModules[3])));
    if (in_array('moduleparrot', $modulesEnabled)) {
        $modulesValue = "1";
    }
    if (in_array('moduleecholink', $modulesEnabled)) {
        $modulesValue = ($modulesValue === "1") ? "9" : "2";
    }
}

// Since 1.7.99.91-4
$defaultTgValue = (isset($varDefaultTg[2]) && !empty($varDefaultTg[2])) ? $varDefaultTg[2] : '226';

/* Profile defaults */
$profiles['reflector']        = $reflectorValue;
$profiles['port']             = $portValue;
$profiles['callsign']         = $callSignValue;
$profiles['key']              = $authKeyValue;
$profiles['beacon']           = $beaconValue;
$profiles['bitrate']          = $codecBitrateValue;
$profiles['type']             = 'nod portabil';
$profiles['shortIdent']       = $shortIdentValue;
$profiles['longIdent']        = $longIdentValue;
$profiles['rogerBeep']        = $rogerBeepValue;
$profiles['connectionStatus'] = $acsValue;
$profiles['defaultTg']        = $defaultTgValue;

/* Convert config of new installs */
if (preg_match('/svx\.ro/', $oldCfg)) {
    $sCfg    = $rCfg    = array();
    $sCfg[1] = '/#H/im';
    $sCfg[2] = '/HOST=/im';
    $sCfg[3] = '/^PORT=/im';
    $rCfg[1] = 'H';
    $rCfg[2] = '#HOST=';
    $rCfg[3] = '#PORT=';
    $oldCfg  = preg_replace($sCfg, $rCfg, $oldCfg);
}

/* Temporary fix(es) */
$oldCfg = preg_replace('/(\#+)(\w)/', '#${2}', $oldCfg);
if (!isset($acsValue)) {
    $oldCfg = preg_replace('/(ANNOUNCE_REMOTE_MIN_INTERVAL=)(\d+)/', '${1}${2}' . "\nANNOUNCE_CONNECTION_STATUS=0", $oldCfg);
}
if (!isset($masterGainValue)) {
    $oldCfg = preg_replace('/(PREEMPHASIS=)(\d+)/', '${1}${2}' . "\nMASTER_GAIN=0", $oldCfg);
}
if (!isset($reconnectSValue)) {
    $oldCfg = preg_replace('/(MUTE_FIRST_TX_REM=)(\d+)/', '${1}${2}' . "\nRECONNECT_SECONDS=0", $oldCfg);
}

/* Process new values */
$oldVar[0] = '/(CALLSIGN=)(\w\S+)/';
$newVar[0] = '${1}' . $frmBeacon;
if ($beaconValue != $frmBeacon) {
    ++$changes;
    $profiles['beacon'] = $frmBeacon;
}

$oldVar[1] = '/(HOST=)(\S+)/';
$newVar[1] = '${1}' . $frmReflector;

if (version_compare($version['number'], '1.7.99.62', '>') && empty($varRefHosts)) {
    // Upgrade config file to new version
    $newVar[1] = '#${1}' . $frmReflector . PHP_EOL . 'HOSTS=' . $frmReflector . ':' . $frmPort;
}
if ($reflectorValue != $frmReflector) {
    ++$changes;
    $profiles['reflector'] = $frmReflector;
}

$oldVar[2] = '/(PORT=)(\d+)/';
$newVar[2] = '${1}' . $frmPort;
if (version_compare($version['number'], '1.7.99.62', '>') && empty($varPorts)) {
    // Upgrade config file to new version
    $newVar[2] = '#${1}' . $frmPort . PHP_EOL . 'HOST_PORT=' . $frmPort;
}
if ($portValue != $frmPort) {
    if ($portsValue != $frmPort) {
        ++$changes;
    }
    $profiles['port'] = $frmPort;
}

$oldVar[3] = '/(CALLSIGN=")(\S+)"/';
$newVar[3] = '${1}' . $frmCallsign . '"';
if ($callSignValue != $frmCallsign) {
    ++$changes;
    $profiles['callsign'] = $frmCallsign;
}

$oldVar[4] = '/(AUTH_KEY=)"(\S+)"/';
$newVar[4] = '${1}"' . $frmAuthKey . '"';
if ($authKeyValue != $frmAuthKey) {
    ++$changes;
    $profiles['key'] = $frmAuthKey;
}

$oldVar[5] = '/(SHORT_IDENT_INTERVAL=)(\d+)/';
$newVar[5] = '${1}' . $frmShortId;
if ($shortIdentValue != $frmShortId) {
    ++$changes;
    $profiles['shortIdent'] = $frmShortId;
}

$oldVar[6] = '/(LONG_IDENT_INTERVAL=)(\d+)/';
$newVar[6] = '${1}' . $frmLongId;
if ($longIdentValue != $frmLongId) {
    ++$changes;
    $profiles['longIdent'] = $frmLongId;
}

$oldVar[7] = '/(OPUS_ENC_BITRATE=)(\d+)/';
$newVar[7] = '${1}' . $frmBitrate;
if ($codecBitrateValue != $frmBitrate) {
    ++$changes;
    $profiles['bitrate'] = $frmBitrate;
}

$oldVar[8] = '/(DEFAULT_LANG=)(\S+)/';
$newVar[8] = '${1}' . $frmVoice;
if ($voicePackValue != $frmVoice) {
    ++$changes;
}

$oldVar[9] = '/(RGR_SOUND_ALWAYS=)(\d+)/';
$newVar[9] = '${1}' . $frmRogerBeep;
if ($rogerBeepValue != $frmRogerBeep) {
    ++$changes;
    $profiles['rogerBeep'] = $frmRogerBeep;
}

$oldVar[10] = '/(GPIO_SQL_PIN=)(\S+)/';
$newVar[10] = '${1}' . $frmRxGPIO;
if ($rxGPIOValue != $frmRxGPIO) {
    ++$changes;
}

$oldVar[11] = '/(PTT_PIN=)(\S+)/';
$newVar[11] = '${1}' . $frmTxGPIO;
if ($txGPIOValue != $frmTxGPIO) {
    ++$changes;
}

$oldVar[12]    = '/(MONITOR_TGS=)(.+)/';
$frmMonitorTgs = preg_replace('/\s{1,}/', ',', $frmMonitorTgs);
$frmMonitorTgs = preg_replace('/,{1,}/', ',', $frmMonitorTgs);
$newVar[12]    = '${1}' . $frmMonitorTgs;
if ($monitorTgsValue != $frmMonitorTgs) {
    ++$changes;
}

$oldVar[13] = '/(TG_SELECT_TIMEOUT=)(\d+)/';
$newVar[13] = '${1}' . $frmTgTimeOut;
if ($tgSelectTOValue != $frmTgTimeOut) {
    ++$changes;
}

$oldVar[14] = '/(SQL_DELAY=)(\d+)/';
$newVar[14] = '${1}' . $frmSqlDelay;
if ($sqlDelayValue != $frmSqlDelay) {
    ++$changes;
}

$oldVar[15] = '/(TIMEOUT=)(\d+)\nTX/';
$newVar[15] = '${1}' . $frmTxTimeOut . PHP_EOL . 'TX';
if ($txTimeOutValue != $frmTxTimeOut) {
    ++$changes;
}

$oldVar[16] = '/(HOSTS=)(\S+)/';
$newVar[16] = '${1}' . $frmReflector . ':' . $frmPort;

$oldVar[17] = '/(HOST_PORT=)(\d+)/';
$newVar[17] = '${1}' . $frmPort;

$oldVar[18] = '/(ANNOUNCE_CONNECTION_STATUS=)(\d+)/';
$newVar[18] = '${1}' . $frmACStatus;
if ($acsValue != (int) $frmACStatus) {
    ++$changes;
    $profiles['connectionStatus'] = $frmACStatus;
}

$oldVar[19] = '/(DEEMPHASIS=)(\d+)/';
$newVar[19] = '${1}' . $frmDeEmphasis;
if ($deEmphasisValue != (int) $frmDeEmphasis) {
    ++$changes;
}

$oldVar[20] = '/(PREEMPHASIS=)(\d+)/';
$newVar[20] = '${1}' . $frmPreEmphasis;
if ($preEmphasisValue != (int) $frmPreEmphasis) {
    ++$changes;
}

$oldVar[21] = '/(MASTER_GAIN=)(-?\d+(\.\d{1,2})?)/';
$newVar[21] = '${1}' . $frmMasterGain;
if ($masterGainValue != (float) $frmMasterGain) {
    ++$changes;
}

$oldVar[22] = '/(LIMITER_THRESH=)(-?\d+)/';
$newVar[22] = '${1}' . $frmLimiter;
if ($limiterValue != (float) $frmLimiter) {
    ++$changes;
}

$oldVar[23] = '/(RECONNECT_SECONDS=)(\d+)/';
$newVar[23] = '${1}' . $frmReconnectS;
if ($reconnectSValue != (int) $frmReconnectS) {
    ++$changes;
}

$oldVar[24] = '/(FAN_START=)(\d+)/';
$newVar[24] = '${1}' . $frmFanStart;
if ($fanStartValue != (int) $frmFanStart) {
    ++$changes;
}

$oldVar[25] = '/(#?)(MODULES=)(\S+)/';
switch ($frmModules) {
    case '0':
        $newVar[25] = '#' . '${2}${3}';
        break;
    case '1':
        $newVar[25] = '${2}' . 'ModuleParrot';
        break;
    case '2':
        $newVar[25] = '${2}' . 'ModuleEchoLink';
        break;
    case '9':
        $newVar[25] = '${2}' . 'ModuleParrot,ModuleEchoLink';
        break;
}
if ($modulesValue != $frmModules) {
    ++$changes;
}

$oldVar[26] = '/(DEFAULT_TG=)(.+)/';
$newVar[26] = '${1}' . $frmDefaultTg;
if ($defaultTgValue != $frmDefaultTg) {
    ++$changes;
    $profiles['defaultTg'] = $frmDefaultTg;
}

$oldVar[27] = '/(TX_DELAY=)(\d+)/';
$newVar[27] = '${1}' . $frmTxDelay;
if ($txDelayValue != $frmTxDelay) {
    ++$changes;
}

/* Configuration info sent to reflector ('type' only) */
if ($cfgRefData['tip'] != $frmType) {
    ++$changes;
    $profiles['type'] = $frmType;
}

/* Create profile */
if (!empty($frmProfile)) {
    $profile     = json_encode($profiles, JSON_PRETTY_PRINT);
    $proFileName = preg_replace('/[^a-zA-Z0-9\-\.]/', '_', $frmProfile) . '.json';
    toggleFS(true);
    file_put_contents($profilesPath . $proFileName, $profile);
    $newProfile = true;
}

/* Load profile */
if (!empty($frmLoadProfile)) {
    $selectedProfile = $profilesPath . $frmLoadProfile;
    if (is_file($selectedProfile)) {
        echo file_get_contents($selectedProfile);
    }
    exit(0);
}

/* Delete profile */
if (!empty($frmDelProfile)) {
    toggleFS(true);
    unlink($profilesPath . $frmDelProfile);
    echo 'Profile "' . basename($frmDelProfile, '.json') . '" has been deleted';
    toggleFS(false);
    exit(0);
}

/* EchoLink stuff */
if ($modulesValue != 0 &&
    !empty($frmElCallsign) &&
    !empty($frmElPassword) &&
    !empty($frmElSysop) &&
    !empty($frmElLocation)) {
    $oldElVar[0] = '/(CALLSIGN=)(\S+)/';
    $newElVar[0] = '${1}' . $frmElCallsign;
    if ($elCallSign[1] != $frmElCallsign) {
        ++$changes;
        ++$changesEl;
    }

    $oldElVar[1] = '/^(PASSWORD=)(\S+)/m';
    $newElVar[1] = '${1}' . $frmElPassword;
    if ($elPassword[1] != $frmElPassword) {
        ++$changes;
        ++$changesEl;
    }

    $oldElVar[2] = '/(SYSOPNAME=)(.*)/';
    $newElVar[2] = '${1}' . $frmElSysop;
    if ($elSysop[1] != $frmElSysop) {
        ++$changes;
        ++$changesEl;
    }

    $oldElVar[3] = '/^(TIMEOUT=)(\d+)/m';
    $newElVar[3] = '${1}' . (empty($frmElTimeout) ? 60 : $frmElTimeout);
    if ($elTimeout[1] != $frmElTimeout) {
        ++$changes;
        ++$changesEl;
    }

    $oldElVar[4] = '/(LOCATION=)(.*)/';
    $newElVar[4] = '${1}' . $frmElLocation;
    if ($elLocation[1] != $frmElLocation) {
        ++$changes;
        ++$changesEl;
    }

    $oldElVar[5] = '/(LINK_IDLE_TIMEOUT=)(\d+)/';
    $newElVar[5] = '${1}' . (empty($frmElLinkIdleTimeout) ? 300 : $frmElLinkIdleTimeout);
    if ($elLinkIdleTimeout[1] != $frmElLinkIdleTimeout) {
        ++$changes;
        ++$changesEl;
    }

    $oldElVar[6]    = '/(#?)(PROXY_SERVER=)(.*)/';
    $disableElProxy = ($frmElProxyServer != 'the.proxy.server') ? '' : '#';
    $newElVar[6]    = $disableElProxy . '${2}' . $frmElProxyServer;
    if ($elProxyServer[3] != $frmElProxyServer) {
        ++$changes;
        ++$changesEl;
    }

    $oldElVar[7] = '/(#?)(PROXY_PORT=)(.*)/';
    $newElVar[7] = $disableElProxy . '${2}' . $frmElProxyPort;
    if ($elProxyPort[3] != $frmElProxyPort) {
        ++$changes;
        ++$changesEl;
    }

    $oldElVar[8] = '/(#?)(PROXY_PASSWORD=)(.*)/';
    $newElVar[8] = $disableElProxy . '${2}' . $frmElProxyPassword;
    if ($elProxyPassword[3] != $frmElProxyPassword) {
        ++$changes;
        ++$changesEl;
    }
}

// Compare current stored values vs new values from form
if ($changes > 0) {
    // Stop SVXLink service before attempting anything
    serviceControl('rolink.service', 'stop');
    $newCfg = preg_replace($oldVar, $newVar, $oldCfg);
    toggleFS(true);
    file_put_contents($newFile, $newCfg);
    exec("/usr/bin/sudo /usr/bin/cp $newFile $cfgFile");
    // Handle EchoLink module changes
    if ($changesEl > 0) {
        $newElCfg = preg_replace($oldElVar, $newElVar, $oldElCfg);
        file_put_contents($newElFile, $newElCfg);
        exec("/usr/bin/sudo /usr/bin/cp $newElFile $cfgELFile");
    }
    // Update json file if decription/type changed
    if ($cfgRefData['tip'] != $frmType) {
        $cfgRefData['tip'] = $frmType;
        $nfoParams         = json_encode($cfgRefData, JSON_PRETTY_PRINT);
        file_put_contents($tmpRefFile, $nfoParams);
        exec("/usr/bin/sudo /usr/bin/cp $tmpRefFile $cfgRefFile");
    }
    // Update json file with signature of using RoLinkX Dashboard
    if (!isset($cfgRefData['isx']) || $cfgRefData['isx'] == 1) {
        $cfgRefData['isx'] = 2;
        $nfoParams         = json_encode($cfgRefData, JSON_PRETTY_PRINT);
        file_put_contents($tmpRefFile, $nfoParams);
        exec("/usr/bin/sudo /usr/bin/cp $tmpRefFile $cfgRefFile");
    }
    $msgOut .= 'Configuration updated (' . $changes . ' change(s) applied)<br/>Redémarrage du service HotLink...';
    $msgOut .= ($newProfile) ? '<br/>Profile saved as ' . basename($proFileName, '.json') : '';

    // All done, start SVXLink service
    serviceControl('rolink.service', 'start');
} else {
    $msgOut .= 'No new data to process.<br/>Keeping original configuration.';
    $msgOut .= ($newProfile) ? '<br/>Profile saved as ' . basename($proFileName, '.json') : '';
}
toggleFS(false);
echo $msgOut;

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
 * Forms module
 * Note : Some code borrowed from
 * https://github.com/RaspAP/raspap-webgui
 * https://gist.github.com/magicbug/bf27fc2c9908eb114b4a
 */
<script>
document.getElementById('updateDash').addEventListener('click', function() {
    fetch('update-dashboard.php', { method: 'POST' })
        .then(response => response.text())
        .then(data => alert(data))
        .catch(error => alert('Erreur : ' + error));
});
</script>

$svxForm = '<h4 class="mt-2 alert alert-warning fw-bold">SVXLink configuration</h4>';
$svxForm .= $profileOption;
$svxForm .= '<div class="input-group input-group-sm mb-1">
      <span class="input-group-text bg-info text-white" style="width: 8rem;">Créer un nouveau profil</span>
      <input id="svx_prn" type="text" class="form-control" placeholder="Donnez un nom à votre profil" aria-label="Profile name" aria-describedby="inputGroup-sizing-sm">
    </div>';
$svxForm .= '<div class="input-group input-group-sm mb-1">
      <span class="input-group-text" style="width: 8rem;">Reflector (IP/DNS)</span>
      <input id="svx_ref" type="text" class="form-control" placeholder="f62dmr.fr" aria-label="Server address" aria-describedby="inputGroup-sizing-sm" ' . $reflectorValue . '>
    </div>
    <div class="input-group input-group-sm mb-1">
      <span class="input-group-text" style="width: 8rem;">Port</span>
      <input id="svx_prt" type="text" class="form-control" placeholder="5300" aria-label="Port" aria-describedby="inputGroup-sizing-sm" ' . $portValue . '>
    </div>
    <div class="input-group input-group-sm mb-1">
      <span class="input-group-text" style="width: 8rem;">Indicatif</span>
      <input id="svx_cal" type="text" class="form-control" placeholder="FRSXXX" aria-label="Callsign" aria-describedby="inputGroup-sizing-sm" ' . $callSignValue . '>
    </div>
    <div id="auth_key" class="input-group input-group-sm mb-1">
      <span class="input-group-text" style="width: 8rem;">Passwd</span>
      <input id="svx_key" type="password" class="form-control" placeholder="nod_portabil" aria-label="Auth Key" aria-describedby="inputGroup-sizing-sm" ' . $authKeyValue . '>
      <button id="show_hide" class="input-group-text" role="button"><i class="icon-visibility" aria-hidden="true"></i></button>
    </div>
    <div class="input-group input-group-sm mb-1">
      <span class="input-group-text" style="width: 8rem;">Indicatif (Balise)</span>
      <input id="svx_clb" type="text" class="form-control" placeholder="FRSXXX" aria-label="Callsign" aria-describedby="inputGroup-sizing-sm" ' . $beaconValue . '>
    </div>';
$svxForm .= '<div class="input-group input-group-sm mb-1">
      <span class="input-group-text" style="width: 8rem;">Roger Beep</span>
      <select id="svx_rgr" class="form-select">
        <option value="0"' . (($rogerBeepValue == 0) ? ' selected' : '') . '>Non</option>
        <option value="1"' . (($rogerBeepValue == 1) ? ' selected' : '') . '>Oui</option>
      </select>
    </div>';
/* Voice language detection/selection */
$svxForm .= '<div class="input-group input-group-sm mb-1">
    <span class="input-group-text" style="width: 8rem;">Pack vocal</span>' . PHP_EOL;
if (is_dir($voicesPath)) {
    $svxForm .= '<select id="svx_vop" class="form-select">' . PHP_EOL;
    foreach (glob($voicesPath . '/*', GLOB_ONLYDIR) as $voiceDir) {
        $availableVoicePacks = str_replace($voicesPath . '/', '', $voiceDir);
        $vsel                = ($availableVoicePacks == $varVoicePack[2]) ? ' selected' : null;
        $svxForm .= '<option value="' . $availableVoicePacks . '"' . $vsel . '>' . $availableVoicePacks . '</option>' . PHP_EOL;
    }
    $svxForm .= '</select>' . PHP_EOL;
    $svxForm .= '<button type="button" id="getVoices" class="btn btn-light btn-lg btn-block">&#128260;</button>' . PHP_EOL;
} else {
    $svxForm .= '<button type="button" id="getVoices" class="btn btn-primary btn-lg btn-block">Download &amp; install voices</button>' . PHP_EOL;
}
$svxForm .= '</div>' . PHP_EOL;
$svxForm .= '
    <div class="input-group input-group-sm mb-1">
      <label class="input-group-text" for="svx_sid" style="width: 8rem;">Balise courte</label>
      <select id="svx_sid" class="form-select">
         <option value="0">Disabled</option>' . PHP_EOL;
/* Generate 5 minutes intervals up to 60 & identify stored value on file */
for ($sid = 5; $sid <= 120; $sid += 5) {
    $sel = ($sid == $varShortIdent[2]) ? ' selected' : null;
    $svxForm .= '<option value="' . $sid . '"' . $sel . '>' . $sid . ' minutes</option>' . PHP_EOL;
}
$svxForm .= '</select>
    </div>
    <div class="input-group input-group-sm mb-1">
      <label class="input-group-text" for="svx_lid" style="width: 8rem;">Balise longue</label>
      <select id="svx_lid" class="form-select">
         <option value="0">Disabled</option>' . PHP_EOL;
/* Generate 5 minutes intervals up to 60 & identify stored value on file */
for ($lid = 5; $lid <= 300; $lid += 5) {
    $sel = ($lid == $varLongIdent[2]) ? ' selected' : null;
    $svxForm .= '<option value="' . $lid . '"' . $sel . '>' . $lid . ' minutes</option>' . PHP_EOL;
}
$svxForm .= '</select>
    </div>';
$svxForm .='<div class="input-group input-group-sm mb-1">
      <span class="input-group-text" style="width: 8rem;">Type</span>
      <select id="svx_tip" class="form-select">
        <option value="">Select a Type</option>
        <option value="EL"';
        if (preg_match('/CALSIGN=EL/',$cfgFileData)) $svxForm .=' selected';
$svxForm .='>EL (Link)</option>
        <option value="ER"';
        if (preg_match('/CALSIGN=ER/',$cfgFileData)) $svxForm .=' selected';
$svxForm .='>ER (Relais)</option>
      </select>
    <div class="input-group input-group-sm mb-3">
      <span class="input-group-text" style="width: 8rem;">Modules</span>
      <select id="svx_mod" class="form-select">
        <option value="0"' . (($modulesValue === '#') ? ' selected' : '') . '>None</option>
        <option value="1"' . ((empty($modulesValue) && in_array('moduleparrot', $modulesEnabled)) ? ' selected' : '') . '>Parrot</option>
        <option value="2"' . ((empty($modulesValue) && in_array('moduleecholink', $modulesEnabled)) ? ' selected' : '') . '>EchoLink</option>
        <option value="9"' . ((empty($modulesValue) && count($modulesEnabled) > 1) ? ' selected' : '') . '>All</option>
      </select>
    </div>';
if (empty($modulesValue) && in_array('moduleecholink', $modulesEnabled)) {
    $svxForm .= '<div class="separator mb-1">EchoLink Module</div>';
    $svxForm .= echoLinkForm();
}
$svxForm .= '<div class="separator mb-1">Avancée</div>';
$svxForm .= '<div class="input-group input-group-sm mb-1">
        <label class="input-group-text" for="svx_rxp" style="width: 8rem;">RX GPIO pin</label>
        <select id="svx_rxp" class="form-select">' . PHP_EOL;
foreach ($svxPinsArray as $rxpin) {
    $inverted     = (strpos($rxpin, '!') !== false) ? ' (inverted)' : null;
    $defaultRxPin = ($rxpin == 'gpio10') ? ' (default)' : null;
	$dynamicData = '<div class="input-group input-group-sm mb-1">
            <span class="input-group-text" style="width: 6.5rem;">Direwolf</span>
            <input type="text" class="form-control ' . (($svcDirewolf == 'active') ? 'text-success' : 'text-danger') . '" value="' . $svcDirewolf . '" readonly>
            ' . (($svcDirewolf == 'active') ? $aprsfiLink : null) . '
        </div>
    <div class="input-group input-group-sm mb-1">
            <span class="input-group-text" style="width: 6.5rem;">GPSD</span>
            <input type="text" class="form-control ' . (($svcGPSD == 'active') ? 'text-success' : 'text-danger') . '" value="' . $svcGPSD . '" readonly>
    </div>';

if ($svcGPSD == 'active' && isset($data['tpv'][0])) {
    $fixDescriptions = [0 => "unknown", 1 => "no fix", 2 => "2D", 3 => "3D"];
    $gpsData         = $data['tpv'][0];
    if ($gpsData['mode'] === 0) {
        return '<meta http-equiv="refresh" content="3"><div class="alert alert-warning text-center" role="alert">Status unknown. Reloading...</div>';
    }

    $fixMode     = $fixDescriptions[$gpsData['mode']];
    $coordinates = number_format($gpsData['lat'], 5) . ', ' . number_format($gpsData['lon'], 5);
    $altitude    = (($gpsData['mode'] == 3) ? round($gpsData['alt']) . ' m' : 'N/A');
    $speed       = (($gpsData['mode'] == 3) ? round($gpsData['speed'] * 3.6) . ' km/h' : 'N/A');

    // Convert reported time to selected timezone (Config page)
    $utcTime     = new DateTime($gpsData['time'], new DateTimeZone("UTC"));
    $timezone    = trim(file_get_contents('/etc/timezone'));
    $eetTimeZone = new DateTimeZone($timezone);
    $utcTime->setTimezone($eetTimeZone);
    $time = $utcTime->format("H:i:s d/m/Y");

    // Maidenhead Locator
    $longitude  = $gpsData['lon'] + 180;
    $latitude   = $gpsData['lat'] + 90;
    $letterA    = ord('A');
    $numberZero = ord('0');
    $locator    = chr($letterA + intval($longitude / 20));
    $locator .= chr($letterA + intval($latitude / 10));
    $locator .= chr($numberZero + intval(($longitude % 20) / 2));
    $locator .= chr($numberZero + intval($latitude % 10));
    $locator .= chr($letterA + intval(($longitude - intval($longitude / 2) * 2) / (2 / 24)));
    $locator .= chr($letterA + intval(($latitude - intval($latitude / 1) * 1) / (1 / 24)));

    $dynamicData .= '<div class="input-group input-group-sm mb-1">
        <span class="input-group-text" style="width: 6.5rem;">Fix mode</span>
        <input type="text" class="form-control" value="' . $fixMode . '" readonly>
    </div>';
    $dynamicData .= ($gpsData['mode'] < 2) ? null : '<div class="input-group input-group-sm mb-1">
        <div class="input-group-prepend input-group-sm">
            <span class="input-group-text" style="width: 6.5rem;">Lat / Lon</span>
        </div>
        <input type="text" class="form-control" value="' . $coordinates . '" readonly>
    </div>
    <div class="input-group input-group-sm mb-1">
        <div class="input-group-prepend input-group-sm">
            <span class="input-group-text" style="width: 6.5rem;">Grid Square</span>
        </div>
        <input type="text" class="form-control" value="' . $locator . '" readonly>
    </div>
    <div class="input-group input-group-sm mb-1">
        <span class="input-group-text" style="width: 6.5rem;">Altitude</span>
        <input type="text" class="form-control" value="' . $altitude . '" readonly>
    </div>
    <div class="input-group input-group-sm mb-1">
        <span class="input-group-text" style="width: 6.5rem;">Speed</span>
        <input type="text" class="form-control" value="' . $speed . '" readonly>
    </div>';
    $dynamicData .= '<div class="input-group input-group-sm mb-1">
        <span class="input-group-text" style="width: 6.5rem;">Time</span>
        <input type="text" class="form-control" value="' . $time . '" readonly>
    </div>';
    $dynamicData .= ($gpsData['mode'] < 2) ? null : '<div class="col-auto fill">
        <div class="map" id="map"></div>
    </div>
    <script>
        var LonLat = ol.proj.fromLonLat([' . $gpsData['lon'] . ',' . $gpsData['lat'] . '])
        var stroke = new ol.style.Stroke({color: "red", width: 2});
        var feature = new ol.Feature(new ol.geom.Point(LonLat))
        var x = new ol.style.Style({
            image: new ol.style.Icon({
            anchor: [0.5, 1],
            crossOrigin: "anonymous",
            src: "assets/img/pin.png",
            })
        })
        feature.setStyle(x)
        var source = new ol.source.Vector({
            features: [feature]
        });
        var vectorLayer = new ol.layer.Vector({
          source: source
        });
        var map = new ol.Map({
          target: "map",
          layers: [
            new ol.layer.Tile({
              source: new ol.source.OSM()
            }),
            vectorLayer
          ],
          view: new ol.View({
            center: LonLat,
            zoom: 10
          })
        });
    </script>' . PHP_EOL;
}
/* Return updates only */
if ($ajax) {
    return $dynamicData;
}
if (isset($_GET['scan'])) {
    echo scanWifi(1);
}

if (isset($_GET['gpsStatus'])) {
    echo aprsForm(1);
}

/* Wi-Fi form */
function getSSIDs()
{
    $storedSSID = null;
    $storedPwds = null;
    $wpaBuffer  = file_get_contents('/etc/wpa_supplicant/wpa_supplicant.conf');
    // Match both plain text passwords and hashed passphrases
    preg_match_all('/ssid=\"(.*)\"/', $wpaBuffer, $resultSSID);
    preg_match_all('/psk=(\".*?\"|\\S+)/', $wpaBuffer, $resultPWDS);
    if (empty($resultSSID) || empty($resultPWDS)) {
        return false;
    }

    foreach ($resultSSID[1] as $key => $ap) {
        if ($key <= 3) {
            $storedSSID[] = $ap;
        }
    }
    foreach ($resultPWDS[1] as $key => $pw) {
        if ($key <= 3) {
            $storedPwds[] = trim($pw, '\"');
        }
    }
    return [$storedSSID, $storedPwds];
}

function scanWifi($ext = 0)
{
    $apList = null;
    exec('/usr/bin/sudo wpa_cli -i wlan0 scan');
    exec('/usr/bin/sudo wpa_cli -i wlan0 scan_results', $reply);
    if (empty($reply)) {
        return;
    }

    array_shift($reply);

    foreach ($reply as $network) {
        $arrNetwork = preg_split("/[\\t]+/", $network);
        if (!isset($arrNetwork[4])) {
            continue;
        }

        $ssid = trim($arrNetwork[4]);
        if (empty($ssid) || preg_match('[\\x00-\\x1f\\x7f\\'\\`\\´\\\"]', $ssid)) {
            continue;
        }
        $networks[$ssid]['ssid'] = $ssid;
        $networks[$ssid]         = array(
            'rssi'     => $arrNetwork[2],
            'protocol' => authType($arrNetwork[3]),
            'channel'  => freqToChan($arrNetwork[1]),
        );
    }

    if (!empty($networks)) {
        $cnt = 1;
        $apList = '<table class="table table-sm"><thead><tr>
            <th scope="col">#</th>
            <th scope="col">SSID</th>
            <th scope="col">RSSI</th>
            <th scope="col">Auth</th>
            <th scope="col">Ch.</th>
            </tr></thead>
            <tbody>';

        foreach ($networks as $name => $data) {
            if ($data['rssi'] >= -80) {
                $lvlQuality = 'class="alert-success"';
            } elseif ($data['rssi'] >= -90) {
                $lvlQuality = 'class="alert-warning"';
            } else {
                $lvlQuality = 'class="alert-light"';
            }

            $apList .= '<tr ' . $lvlQuality . '><th scope="row">' . $cnt . '</th>
                        <td>' . $name . '</td>
                        <td>' . $data['rssi'] . '</td>
                        <td>' . $data['protocol'] . '</td>
                        <td>' . $data['channel'] . '</td>
                        </tr>';
            ++$cnt;
        }
        $apList .= '</tbody></table>';
    }
    return $apList;
}

function authType($type)
{
    $options = array();
    preg_match_all('/\[([^\]]+)\]/s', $type, $matches);

    foreach ($matches[1] as $match) {
        if (preg_match('/^(WPA\d?)/', $match, $protocol_match)) {
            $protocol  = $protocol_match[1];
            $matchArr  = explode('-', $match);
            $options[] = htmlspecialchars($protocol, ENT_QUOTES);
        }
    }

    if (count($options) === 0) {
        return 'Open';
    } else {
        return implode(' / ', $options);
    }
}

function freqToChan($freq)
{
    if ($freq >= 2412 && $freq <= 2484) {
        $channel = ($freq - 2407) / 5;
    } elseif ($freq >= 4915 && $freq <= 4980) {
        $channel = ($freq - 4910) / 5 + 182;
    } elseif ($freq >= 5035 && $freq <= 5865) {
        $channel = ($freq - 5030) / 5 + 6;
    } else {
        $channel = -1;
    }
    if ($channel >= 1 && $channel <= 196) {
        return $channel;
    } else {
        return 'Invalid Channel';
    }
}


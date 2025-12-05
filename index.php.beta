<?php
/*
 *   RoLinkX Dashboard v3.7
 *   Copyright (C) 2024 by Razvan Marin YO6NAM / https://www.xpander.ro
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
 * Index page
 */

$pages = array("wifi", "svx", "sa", "log", "nod", "aprs", "tty", "cfg");
$page  = (null !== filter_input(INPUT_GET, 'p', FILTER_SANITIZE_SPECIAL_CHARS)) ? $_GET['p'] : '';

// Common functions
include __DIR__ . '/includes/functions.php';

// Password protection
dashPassword('check');

// Events
$version    = version();
$eventsData = 'var events=0';
$ajaxData   = 'var auto_refresh = setInterval( function () { cpuData(); gpioStatus(); }, 3000);';
if ($version && $version['date'] > 20231120) {
    $ajaxData   = '';
    $eventsData = 'var events=1; var timeOutTimer=180;';
}

// Detect mobiles
require_once __DIR__ . '/includes/Mobile_Detect.php';
$detect = new Mobile_Detect();

if (in_array($page, $pages)) {
    include __DIR__ . '/includes/forms.php';
} else {
    $config = include 'config.php';
    include __DIR__ . '/includes/status.php';
}

$rolink = (is_file($cfgFile)) ? true : false;

switch ($page) {
    case "wifi":
        $htmlOutput = wifiForm();
        break;
    case "svx":
        $htmlOutput = svxForm();
        break;
    case "sa":
        $htmlOutput = sa818Form();
        break;
    case "aprs":
        $htmlOutput    = aprsForm();
        $extraResource = '<link href="https://cdn.jsdelivr.net/npm/ol@v8.1.0/ol.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/ol@v8.1.0/dist/ol.js"></script>';
        break;
    case "log":
        $htmlOutput = logsForm();
        break;
    case "tty":
        $htmlOutput = ttyForm();
        break;
	case "nod":
        $htmlOutput = nodForm();
        break;
    case "cfg":
        $htmlOutput = cfgForm();
        break;
    default:
        $svxAction  = (getSVXLinkStatus(1)) ? 'Restart' : 'Start';
        $htmlOutput = '<h4 class="m-2 mt-2 alert alert-success fw-bold talker">' . ($detect->isMobile() ? '&nbsp;' : 'Statut') . '<span id="onair" class="badge position-absolute top-50 start-50 translate-middle"></span></h4>
    <div class="card m-2">
    <div class="card-body">';
        $htmlOutput .= ($config['cfgHostname'] == 'true' && $rolink) ? hostName() : null;
        $htmlOutput .= ($config['cfgUptime'] == 'true') ? getUpTime() : null;
        $htmlOutput .= ($config['cfgCpuStats'] == 'true') ? getCpuStats() : null;
        $htmlOutput .= ($config['cfgNetworking'] == 'true') ? networking() : null;
        $htmlOutput .= ($config['cfgSsid'] == 'true') ? getSSID() : null;
        $htmlOutput .= ($config['cfgPublicIp'] == 'true') ? getPublicIP() : null;
        $htmlOutput .= gpioStatus();
        $htmlOutput .= ($config['cfgDetectSa'] == 'true') ? sa818() : null;
        $htmlOutput .= ($config['cfgSvxStatus'] == 'true' && $rolink) ? '<div id="svxStatus">' . getSVXLinkStatus() . '</div>' : null;
        $htmlOutput .= '<div id="refContainer">' . getReflector() . '</div>';
        $htmlOutput .= ($config['cfgRefNodes'] == 'true' && $rolink) ? getRefNodes() : null;
        $htmlOutput .= ($config['cfgCallsign'] == 'true' && $rolink) ? getCallSign() . PHP_EOL : null;
        $htmlOutput .= ($rolink) ? getGPSDongle() . PHP_EOL : null;
        $htmlOutput .= ($config['cfgKernel'] == 'true') ? getKernel() : null;
        $htmlOutput .= ($config['cfgFreeSpace'] == 'true') ? getFreeSpace() : null;
        $htmlOutput .= ($rolink) ? getFileSystem() . PHP_EOL : null;
        $htmlOutput .= ($rolink) ? getRemoteVersion() . PHP_EOL : null;
        $htmlOutput .= ($rolink) ? '<div class="d-grid gap-2 col-7 mx-auto">
    <button id="resvx" class="btn btn-warning btn-lg">' . $svxAction . ' Hotlink</button>
    <button id="endsvx" class="btn btn-dark btn-lg">Stop Hotlink</button>
    <button id="reboot" class="btn btn-primary btn-lg">Reboot</button>
    <button id="halt" class="btn btn-danger btn-lg">Mise hors tension</button>
    </div>
    </div>
    </div>' : null;
        $htmlOutput .= ($config['cfgDTMF'] == 'true') ? dtmfSender() . PHP_EOL : null;
        $ajax = ($config['cfgCpuStats'] == 'true') ? "$(document).ready(function () {
        cpuData();
        gpioStatus();
        $ajaxData
    });" : null;
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="HotLink Dashboard" />
        <meta name="author" content="FRS077" />
        <title>HotLink Dashboard - <?php echo gethostname(); ?></title>
        <!-- FAVICONS -->
        <link rel="apple-touch-icon" sizes="57x57" href="assets/fav/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="assets/fav/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="assets/fav/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="assets/fav/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="assets/fav/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="assets/fav/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="assets/fav/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="assets/fav/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="assets/fav/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="assets/fav/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="assets/fav/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="assets/fav/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="assets/fav/favicon-16x16.png">
        <link rel="manifest" href="manifest.json">
        
        <link href="css/styles.css?_=<?php echo cacheBuster('css/styles.css'); ?>" rel="stylesheet" />
        <link href="css/select2.min.css" rel="stylesheet" />
        <link href="css/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
        <link href="css/jquery.toast.min.css" rel="stylesheet" />
        <link href="css/iziModal.min.css" rel="stylesheet" />
        <?php echo (isset($extraResource)) ? $extraResource . PHP_EOL : null; ?>
        
        <!-- COULEURS RADIOAMATEUR + CORRECTION SA818 -->
        <style>
        /* PALETTE RADIOAMATEUR */
        body {
            background: #0a192fcc !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            color: #cdd6f4 !important;
        }
        
        #wrapper { background: transparent !important; }
        
        #sidebar-wrapper {
            background: #112240cc !important;
            backdrop-filter: blur(15px) !important;
            border-right: 1px solid #3f72afaa !important;
            box-shadow: 0 0 25px #3f72af55 !important;
        }
        
        .sidebar-heading {
            background: linear-gradient(135deg, #284b63, #3f72af) !important;
            color: #61dafb !important;
            border-bottom: 2px solid #7ee787 !important;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.7) !important;
        }
        
        .list-group-item {
            background: #1f2937 !important;
            margin: 4px 12px !important;
            border-radius: 15px !important;
            border: 1px solid #334155 !important;
            color: #cdd6f4 !important;
            backdrop-filter: blur(10px) !important;
            transition: all 0.3s ease !important;
        }
        
        .list-group-item:hover, .list-group-item.active {
            background: linear-gradient(135deg, #7ee78733, #61dafb44) !important;
            border-color: #7ee787 !important;
            transform: translateX(6px) !important;
            box-shadow: 0 8px 25px #7ee78744 !important;
            color: #ffffff !important;
        }
        
        #page-content-wrapper { background: transparent !important; padding: 25px !important; }
        
        #main-content {
            background: #112240cc !important;
            backdrop-filter: blur(20px) !important;
            border-radius: 25px !important;
            border: 1px solid #3f72afaa !important;
            box-shadow: 0 25px 50px #0a192f66 !important;
        }
        
        .card {
            background: #1f2937 !important;
            backdrop-filter: blur(15px) !important;
            border: 1px solid #334155 !important;
            border-radius: 20px !important;
            box-shadow: 0 8px 30px #3f72af33 !important;
        }
        
        .card:hover {
            transform: translateY(-6px) !important;
            border-color: #7ee787aa !important;
            box-shadow: 0 20px 45px #7ee78744 !important;
        }
        
        .alert-success {
            background: rgba(126,231,135,0.2) !important;
            border: 2px solid #7ee787 !important;
            border-radius: 20px !important;
            color: #cdd6f4 !important;
        }
        
        .btn {
            border-radius: 20px !important;
            padding: 14px 28px !important;
            font-weight: 700 !important;
            box-shadow: 0 6px 20px rgba(63,114,175,0.4) !important;
            border: none !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
        }
        
        .btn:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 12px 35px rgba(63,114,175,0.6) !important;
        }
        
        .btn-primary { background: linear-gradient(135deg, #3f72af, #61dafb) !important; }
        .btn-warning { background: linear-gradient(135deg, #f59e0b, #fbbf24) !important; }
        .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626) !important; }
        .btn-dark { background: linear-gradient(135deg, #1f2937, #111827) !important; }
        
        .navbar {
            background: #112240cc !important;
            backdrop-filter: blur(15px) !important;
            border-bottom: 2px solid #3f72af !important;
        }
        
        /* TITRE PRINCIPAL RADIOAMATEUR */
        .main-title {
            text-align: center !important;
            color: #61dafb !important;
            text-shadow: 0 0 20px #61dafb66, 2px 2px 8px rgba(0,0,0,0.8) !important;
            margin: 30px 0 !important;
            font-size: 3em !important;
            font-weight: 800 !important;
            letter-spacing: 2px !important;
        }
        
        /* VERSION CLIGNOTANTE */
        .version-blink {
            animation: blink-radio 1.2s step-end infinite !important;
            color: #7ee787 !important;
            font-weight: 800 !important;
            font-size: 0.9em !important;
            text-shadow: 0 0 15px #7ee787aa !important;
        }
        
        @keyframes blink-radio {
            0%, 45% { opacity: 1; }
            50%, 95% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        .page-footer {
            background: #112240cc !important;
            backdrop-filter: blur(15px) !important;
            border-top: 2px solid #3f72afaa !important;
            color: #cdd6f4 !important;
        }
        
        /* CORRECTION SA818 - POLICE PLUS LISIBLE */
        .sa818-content *,
        .sa818-content {
            color: #cdd6f4 !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8) !important;
        }

        .sa818-content .form-control,
        .sa818-content input,
        .sa818-content select,
        .sa818-content textarea {
            background: #1f2937 !important;
            color: #ffffff !important;
            border: 1px solid #334155 !important;
            border-radius: 10px !important;
        }

        .sa818-content .form-control:focus,
        .sa818-content input:focus,
        .sa818-content select:focus {
            background: #2d3748 !important;
            color: #ffffff !important;
            border-color: #7ee787 !important;
            box-shadow: 0 0 10px #7ee78744 !important;
        }

        .sa818-content label {
            color: #61dafb !important;
            font-weight: 600 !important;
        }

        .sa818-content .btn {
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        @media (max-width: 768px) {
            .main-title { font-size: 2em !important; }
            #main-content { margin: 10px; padding: 20px !important; }
        }
        </style>
    </head>
    <body>
        <div class="d-flex" id="wrapper">
            <div class="border-end bg-white" id="sidebar-wrapper">
                <div class="sidebar-heading border-bottom bg-light fw-bold">
                    <a href="./" class="text-decoration-none" style="color:#61dafb !important">
                        <i class="icon-dashboard" style="font-size:28px;color:#7ee787 !important;vertical-align: middle;padding: 0 6px 6px 0;"></i>HotLink Dashboard
                    </a>
                </div>
                <div class="list-group list-group-flush">
                    <a class="<?php echo ($page == '') ? 'active' : ''; ?> list-group-item list-group-item-action list-group-item-light p-3" href="./">📊 Statut</a>
                    <a class="<?php echo ($page == 'wifi') ? 'active' : ''; ?> list-group-item list-group-item-action list-group-item-light p-3" href="./?p=wifi">📶 WiFi</a>
                    <a class="<?php echo ($page == 'svx') ? 'active' : ''; ?> list-group-item list-group-item-action list-group-item-light p-3" href="./?p=svx">🗣️ SVXLink</a>
                    <a class="<?php echo ($page == 'sa') ? 'active' : ''; ?> list-group-item list-group-item-action list-group-item-light p-3" href="./?p=sa">📻 SA818</a>
                    <a class="<?php echo ($page == 'log') ? 'active' : ''; ?> list-group-item list-group-item-action list-group-item-light p-3" href="./?p=log">📋 Logs</a>
                    <a class="<?php echo ($page == 'tty') ? 'active' : ''; ?> list-group-item list-group-item-action list-group-item-light p-3" href="./?p=tty">💻 Terminal</a>
                    <a class="<?php echo ($page == 'cfg') ? 'active' : ''; ?> list-group-item list-group-item-action list-group-item-light p-3" href="./?p=cfg">⚙️ Config</a>
					<a class="<?php echo ($page == 'nod') ? 'active' : ''; ?> list-group-item list-group-item-action list-group-item-light p-3" href="./?p=nod">ℹ️ Node Info</a>
			   <!-- <a class="<?php echo ($page == 'node_info') ? 'active' : ''; ?> list-group-item list-group-item-action list-group-item-light p-3" href="node_info.php">Node Info</a>-->
					<a class="<?php echo ($page == 'ext') ? 'active' : ''; ?> list-group-item list-group-item-action list-group-item-light p-3" href="http://www.f62dmr.fr/svxrdb/index.php" target="_blank">🌐 RNFA</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="https://www.facebook.com/groups/1067389751809869" target="_blank">📘 Facebook</a>
                </div>
            </div>
            
            <div id="page-content-wrapper">
                <nav <?php echo ($detect->isMobile() ? '' : 'style="display: none !important" '); ?>class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <h1 style="color:#61dafb !important; text-shadow: 2px 2px 6px rgba(97,218,251,0.6);">
                            HotLink Dashboard
                        </h1>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                                <li class="nav-item"><a class="<?php echo ($page == '') ? 'active p-2' : ''; ?> nav-link" style="color:#cdd6f4 !important;" href="./">Statut</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'wifi') ? 'active p-2' : ''; ?> nav-link" style="color:#cdd6f4 !important;" href="./?p=wifi">WiFi</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'svx') ? 'active p-2' : ''; ?> nav-link" style="color:#cdd6f4 !important;" href="./?p=svx">SVXLink</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'sa') ? 'active p-2' : ''; ?> nav-link" style="color:#cdd6f4 !important;" href="./?p=sa">SA818</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'log') ? 'active p-2' : ''; ?> nav-link" style="color:#cdd6f4 !important;" href="./?p=log">Logs</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'tty') ? 'active p-2' : ''; ?> nav-link" style="color:#cdd6f4 !important;" href="./?p=tty">Terminal</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'cfg') ? 'active p-2' : ''; ?> nav-link" style="color:#cdd6f4 !important;" href="./?p=cfg">Config</a></li>
								<li class="nav-item"><a class="<?php echo ($page == 'nod') ? 'active p-2' : ''; ?> nav-link" style="color:#cdd6f4 !important;" href="./?p=nod">Node Info</a></li>

                            </ul>
                        </div>
                    </div>
                </nav>
                
                <div id='main-content' class="container-fluid mb-5">
                    <!-- TITRE PRINCIPAL RADIOAMATEUR -->
                    <h1 class="main-title">🎙️ HotLink Dashboard RNFA</h1>
                    <?php echo $htmlOutput; ?>
                </div>
            </div>
            <div id="sysmsg"></div>
        </div>
        
        <footer class="page-footer fixed-bottom font-small bg-light">
            <div class="text-center small p-3" style="color:#cdd6f4 !important;">
                2024 Copyright <a class="text-primary" target="_blank" href="https://github.com/yo6nam/RoLinkX-Dashboard" style="color:#61dafb !important;">Razvan / YO6NAM</a> 
                - Modification par FRS077 en 2025 pour le réseau RNFA
                <?php
                $versionFile = __DIR__ . '/version';
                if (is_readable($versionFile)) {
                    $version = trim(file_get_contents($versionFile));
                    echo ' - Dashboard version <span class="version-blink">' . htmlspecialchars($version) . '</span>';
                }
                ?>
            </div>
        </footer>
        
        <script><?php echo $eventsData; ?></script>
        <script src="js/jquery.js"></script>
        <script src="js/iziModal.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/select2.min.js"></script>
        <script src="js/scripts.js?_=<?php echo cacheBuster('js/scripts.js'); ?>"></script>
        <?php echo (isset($ajax)) ? '<script>' . $ajax . '</script>' . PHP_EOL : null; ?>
    </body>
</html>

<?php
/*
 *   RoLinkX Dashboard v4.7
 *   Copyright (C) 2025 - 2025  by Razvan Marin YO6NAM / FRS077 Romuald
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

// Events ✅ CORRIGÉ POUR ON-AIR
$version    = version();
$eventsData = 'var events=0';
$ajaxData   = 'var auto_refresh = setInterval( function () { cpuData(); gpioStatus(); }, 3000);';
if ($version && $version['date'] > 20231120) {  // ✅ DATE CORRIGÉE (comme le 1er code)
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

$rolink = (isset($cfgFile) && is_file($cfgFile)) ? true : false;

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
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="assets/fav/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <link href="css/styles.css?_=<?php echo cacheBuster('css/styles.css'); ?>" rel="stylesheet" />
        <link href="css/select2.min.css" rel="stylesheet" />
        <link href="css/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
        <link href="css/jquery.toast.min.css" rel="stylesheet" />
        <link href="css/iziModal.min.css" rel="stylesheet" />
        <style>
            .version-blink {
                font-weight: bold;
                animation: pulse-red 2s infinite;
            }
            @keyframes pulse-red {
                0% { color: #000000; text-shadow: none; transform: scale(1); }
                50% { color: #ff0000; text-shadow: 0 0 10px #ff0000; transform: scale(1.05); }
                100% { color: #000000; text-shadow: none; transform: scale(1); }
            }
        </style>
        <?php echo (isset($extraResource)) ? $extraResource . PHP_EOL : null; ?>
    </head>
    <body>
        <div class="d-flex" id="wrapper">
            <div class="border-end bg-white" id="sidebar-wrapper">
                <div class="sidebar-heading border-bottom bg-light fw-bold">
                    <a href="./" class="text-decoration-none" style="color:purple">
                        <i class="icon-dashboard" style="font-size:26px;color:purple;vertical-align: middle;padding: 0 4px 4px 0;"></i>HotLink Dashboard
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
                    <a class="<?php echo ($page == 'ext') ? 'active' : ''; ?> list-group-item list-group-item-action list-group-item-light p-3" href="http://www.f62dmr.fr/svxrdb/index.php" target="_blank">🌐 RNFA</a>
                    <a class="list-group-item list-group-item-action list-group-item-light p-3" href="https://www.facebook.com/groups/1067389751809869" target="_blank">📘 Facebook</a>
                </div>
            </div>
            <div id="page-content-wrapper">
                <nav <?php echo ($detect->isMobile() ? '' : 'style="display: none !important" '); ?>class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        </button>
                        <h1 class="sidebar-heading bg-light fw-light mt-1 text-dark"><a href="./" class="text-decoration-none" style="color:black">HotLink Dashboard</a></h1>
                        <i class="icon-dashboard" style="font-size:40px;color:purple"></i>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                                <li class="nav-item"><a class="<?php echo ($page == '') ? 'active p-2' : ''; ?> nav-link" href="./">📊 Statut</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'wifi') ? 'active p-2' : ''; ?> nav-link" href="./?p=wifi">📶 WiFi</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'svx') ? 'active p-2' : ''; ?> nav-link" href="./?p=svx">🗣️ SVXLink</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'sa') ? 'active p-2' : ''; ?> nav-link" href="./?p=sa">📻 SA818</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'log') ? 'active p-2' : ''; ?> nav-link" href="./?p=log">📋 Logs</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'tty') ? 'active p-2' : ''; ?> nav-link" href="./?p=tty">💻 Terminal</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'cfg') ? 'active p-2' : ''; ?> nav-link" href="./?p=cfg">⚙️ Config</a></li>
                                <li class="nav-item"><a class="<?php echo ($page == 'nod') ? 'active p-2' : ''; ?> nav-link" href="./?p=nod">ℹ️ Node Info</a></li>
                                <li class="nav-item"><a class="nav-link p-2" href="http://www.f62dmr.fr/svxrdb/index.php" target="_blank">🌐 Dashboard du RNFA</a></li>
                                <li class="nav-item"><a class="nav-link p-2" href="https://www.facebook.com/groups/1067389751809869" target="_blank">📘 Notre groupe Facebook</a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
                <div id='main-content' class="container-fluid mb-5">
                    <?php echo $htmlOutput; ?>
                </div>
            </div>
            <div id="sysmsg"></div>
        </div>
        <footer class="page-footer fixed-bottom font-small bg-light">
            <div class="text-center small p-2">
                2024-2025 Copyright
                <a class="text-primary" target="_blank" href="https://github.com/yo6nam/RoLinkX-Dashboard">
                    Razvan / YO6NAM
                </a>
                - Modifications FRS077 pour le réseau RNFA - Version :
                <?php
                $versionFile = __DIR__ . '/version';
                if (is_readable($versionFile)) {
                    $version = trim(file_get_contents($versionFile));
                    echo ' <span class="version-blink"> ' . $version . '</span>';
                }
                ?>
            </div>
        </footer>

        <!-- 🎄🎇 MESSAGE FESTIF COMPLET - SAPINS + NEIGE + FEUX D'ARTIFICE 🎇🎄 -->
        <div id="newyear-message" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:9999; background:linear-gradient(135deg, #0a0a23 0%, #1a1a3a 50%, #0a0a23 100%); overflow:hidden;">
            <!-- SAPINS ANIMÉS -->
            <div style="position:absolute; bottom:0; left:10%; animation: sway 3s ease-in-out infinite alternate; font-size:80px; z-index:2;">🎄</div>
            <div style="position:absolute; bottom:0; left:30%; animation: sway 3s ease-in-out infinite alternate 0.5s; font-size:60px; z-index:2;">🌲</div>
            <div style="position:absolute; bottom:0; right:15%; animation: sway 3s ease-in-out infinite alternate 1s; font-size:70px; z-index:2;">🎅</div>
            <div style="position:absolute; bottom:0; right:40%; animation: sway 3s ease-in-out infinite alternate 1.5s; font-size:90px; z-index:2;">🌟</div>
            
            <!-- MESSAGE CENTRAL -->
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; color:#fff; font-family:'Arial Black',Arial; font-size:28px; font-weight:bold; text-shadow:0 0 20px #00ff88, 0 0 40px #ff00ff; z-index:3; animation: glow 2s ease-in-out infinite alternate;">
                🎄✨ BONNE FIN D'ANNÉE 2025 À TOUS ! ✨🎄<br><br>
                <span style="font-size:22px; color:#ffd700;">📡 Merci pour votre soutien au réseau RNFA ! 📡</span><br><br>
                <span style="font-size:16px;">RNFA HotLink Dashboard - FRS077</span>
            </div>
            
            <!-- CANVAS NEIGE + FEUX D'ARTIFICE -->
            <canvas id="festive-canvas" style="position:absolute; top:0; left:0; width:100%; height:100%; z-index:1;"></canvas>
        </div>

        <style>
            @keyframes sway {
                0% { transform: rotate(-2deg); }
                100% { transform: rotate(2deg); }
            }
            @keyframes glow {
                0% { text-shadow: 0 0 20px #00ff88, 0 0 40px #ff00ff; }
                100% { text-shadow: 0 0 30px #ff0080, 0 0 60px #00ff88, 0 0 10px #ffffff; }
            }
            @keyframes fireworks {
                0% { transform: scale(0) rotate(0deg); opacity: 1; }
                100% { transform: scale(3) rotate(720deg); opacity: 0; }
            }
        </style>

 <script>
// 🎄 ANIMATION FESTIVE - UNE SEULE FOIS PAR SESSION 🎇
(function() {
    // Vérifier si déjà vu via sessionStorage
    if (sessionStorage.getItem('festiveSeen2025')) {
        return; // Ne rien faire si déjà vu
    }
    
    function createFestiveEffects() {
        const canvas = document.getElementById('festive-canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        // NEIGE
        const snowflakes = [];
        for(let i = 0; i < 200; i++) {
            snowflakes.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height - canvas.height,
                r: Math.random() * 5 + 1,
                speed: Math.random() * 3 + 0.5,
                sway: Math.random() * 30 - 15,
                opacity: Math.random() * 0.5 + 0.3
            });
        }
        
        // FEUX D'ARTIFICE
        const fireworks = [];
        function createFirework() {
            const fw = {
                x: Math.random() * canvas.width,
                y: canvas.height,
                vx: (Math.random() - 0.5) * 10,
                vy: -(Math.random() * 8 + 10),
                particles: [],
                exploded: false,
                colors: ['#ff0080', '#00ff88', '#ffaa00', '#00aaff', '#ff4444']
            };
            fireworks.push(fw);
        }
        
        // Animation principale
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // NEIGE
            ctx.save();
            snowflakes.forEach(snow => {
                ctx.globalAlpha = snow.opacity;
                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.arc(snow.x + Math.sin(snow.y * 0.01) * snow.sway, snow.y, snow.r, 0, Math.PI * 2);
                ctx.fill();
                
                snow.y += snow.speed;
                if(snow.y > canvas.height) snow.y = -snow.r;
            });
            ctx.restore();
            
            // FEUX D'ARTIFICE
            fireworks.forEach((fw, index) => {
                if(!fw.exploded) {
                    fw.x += fw.vx;
                    fw.y += fw.vy;
                    fw.vy += 0.1;
                    
                    if(fw.y < canvas.height * 0.3 || fw.vy > 0) {
                        fw.exploded = true;
                        for(let i = 0; i < 30; i++) {
                            fw.particles.push({
                                x: fw.x,
                                y: fw.y,
                                vx: (Math.random() - 0.5) * 12,
                                vy: (Math.random() - 0.5) * 12,
                                life: 1,
                                color: fw.colors[Math.floor(Math.random() * fw.colors.length)]
                            });
                        }
                    }
                } else if(fw.particles.length > 0) {
                    fw.particles.forEach((p, pIndex) => {
                        p.x += p.vx;
                        p.y += p.vy;
                        p.vy += 0.05;
                        p.life -= 0.02;
                        
                        if(p.life > 0) {
                            ctx.save();
                            ctx.globalAlpha = p.life;
                            ctx.fillStyle = p.color;
                            ctx.beginPath();
                            ctx.arc(p.x, p.y, 3, 0, Math.PI * 2);
                            ctx.fill();
                            ctx.restore();
                        } else {
                            fw.particles.splice(pIndex, 1);
                        }
                    });
                    
                    if(fw.particles.length === 0) {
                        fireworks.splice(index, 1);
                    }
                }
            });
            
            requestAnimationFrame(animate);
        }
        
        // Lancer feux d'artifice toutes les 1.5s
        setInterval(createFirework, 1500);
        animate();
    }

    // Lancer l'animation seulement si pas déjà vue
    document.addEventListener('DOMContentLoaded', function() {
        const msg = document.getElementById('newyear-message');
        msg.style.display = 'block';
        createFestiveEffects();
        
        setTimeout(() => {
            msg.style.opacity = '0';
            msg.style.transition = 'opacity 1s ease-out';
            setTimeout(() => {
                msg.style.display = 'none';
                msg.style.opacity = '1';
                // ✅ MARQUER COMME VU - Bloque pour toute la session
                sessionStorage.setItem('festiveSeen2025', 'true');
            }, 1000);
        }, 10000);
    });

    // Resize canvas
    window.addEventListener('resize', function() {
        const canvas = document.getElementById('festive-canvas');
        if(canvas) {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
    });
})();
</script>
        <!-- 🎄🎇 FIN ANIMATION FESTIVE 🎇🎄 -->

        <script><?php echo $eventsData; ?></script>
        <script src="js/jquery.js"></script>
        <script src="js/iziModal.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/select2.min.js"></script>
        <script src="js/scripts.js?_=<?php echo cacheBuster('js/scripts.js'); ?>"></script>
        <?php echo (isset($ajax)) ? '<script>' . $ajax . '</script>' . PHP_EOL : null; ?>
    </body>
</html>

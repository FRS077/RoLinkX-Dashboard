<?php
// /includes/aide.php - Guide Dashboard HotLink pour f62dmr.fr
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Dashboard HotLink – f62dmr.fr</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            margin: 0 20px 20px 20px; 
            max-width: 900px; 
            margin-left: auto; 
            margin-right: auto;
        }
        h1 { color: #333; border-bottom: 3px solid #0077cc; padding-bottom: 10px; }
        h2, h3 { color: #333; }
        .signature { margin-top: 40px; font-style: italic; color: #666; text-align: center; }
        .download { 
            display: inline-block;
            margin: 20px 0; 
            padding: 12px 20px; 
            background: #0077cc; 
            color: #fff; 
            text-decoration: none; 
            border-radius: 6px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }
        .download:hover { 
            background: #005fa0; 
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        .download.downloading {
            background: #28a745;
            position: relative;
        }
        .download.downloading::after {
            content: " ⏳ Téléchargement...";
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .info-reseau {
            background: #dc3545;
            color: white;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
            font-weight: bold;
            font-size: 1.1em;
            animation: blink 1.5s infinite;
            border: 3px solid #ff0000;
        }
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.6; }
        }
        table { 
            border-collapse: collapse; 
            width: 100%; 
            margin: 20px 0; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table, th, td { border: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        th, td { padding: 12px 10px; text-align: left; }
        ul, ol { margin: 10px 0; padding-left: 25px; }
        strong { color: #0077cc; }
        hr { border: none; height: 2px; background: #eee; margin: 30px 0; }
        @media (max-width: 768px) { body { margin: 0 10px; } }

        /* STYLES DTMF */
        .dtmf-section { margin: 40px 0; padding: 25px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 15px; border: 3px solid #1976d2; }
        .dtmf-title { text-align: center; color: #d32f2f; font-size: 1.5em; margin-bottom: 20px; font-weight: bold; }
        .dtmf-warning { background: #ffebee; border: 2px solid #d32f2f; padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center; }
        .dtmf-box { background: white; border-radius: 10px; padding: 20px; margin: 20px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-left: 5px solid #1976d2; }
        .dtmf-code { background: #d32f2f; color: white; padding: 12px 20px; border-radius: 8px; font-family: monospace; font-size: 1.4em; font-weight: bold; display: inline-block; margin: 10px 5px; cursor: pointer; transition: all 0.3s; box-shadow: 0 3px 6px rgba(0,0,0,0.2); }
        .dtmf-code:hover { background: #b71c1c; transform: scale(1.08); }
        .btn-play-dtmf { background: #1976d2; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-size: 1.1em; font-weight: bold; cursor: pointer; margin: 15px 0; transition: all 0.3s; box-shadow: 0 3px 6px rgba(0,0,0,0.2); }
        .btn-play-dtmf:hover { background: #1565c0; transform: translateY(-2px); }
        .dtmf-explain { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin: 15px 0; border-radius: 5px; }
    </style>
</head>
<body>

<h1>🛠️ Guide d'utilisation — Dashboard HotLink</h1>

<!-- AJOUT UNIQUEMENT ICI -->
<div class="info-reseau">
    ⚠️ RNFA f62dmr.fr:5300 Mot de passe <strong>USER</strong> EN MAJUSCULES !
</div>

<p style="background:#fff3cd; border:2px solid #ffc107; padding:12px; border-radius:6px; font-weight:bold; margin-top:10px;">
    ⚠️ Ces informations concernent exclusivement le réseau RNFA et son HotLink.<br><br>
    Pour toute utilisation avec un autre reflector, il est impératif de prendre contact avec le responsable du réseau concerné pour la configuration adaptée à son installation.<br><br>
    Ce dashboard a été créé pour une utilisation propre à notre installation RNFA. 
    Il est toutefois possible de l'utiliser avec d'autres distributions pour le RRF ou RI49, sous réserve d'être titulaire d'une licence radioamateur.
</p>

<!-- NOUVELLE SECTION DTMF AJOUTÉE ICI (CODE ORIGINAL INTACT CI-DESSOUS) -->
<div class="dtmf-section">
    <h2 style="text-align: center; color: #1976d2; border-bottom: 3px solid #d32f2f; padding-bottom: 10px;">🎵 COMMANDE DTMF HOTLINK</h2>
    
    <div class="dtmf-warning">
        <div style="font-size: 14pt; font-weight: bold; margin-bottom: 10px;">
            ⚠️ <strong>UNIQUEMENT POUR LES HOTLINKS !</strong>
        </div>
        <div style="font-size: 11pt;">
            • Ne PAS utiliser sur relais standards ou nodes<br>
            • HotLink = votre hotspot personnel<br>
            • Vérifiez la doc de votre radio pour DTMF
        </div>
    </div>

    <div class="dtmf-box">
        <h3>🎤 Mode Perroquet (Test Audio)</h3>
        <div style="font-size: 1.2em; font-weight: bold; color: #1976d2; margin-bottom: 15px;">
            🔴 DTMF : <span class="dtmf-code" onclick="playDTMF('1#')">1#</span>
        </div>
        <button class="btn-play-dtmf" onclick="playDTMF('1#')">▶ Jouer "1#"</button>
        <div class="dtmf-explain">
            ✅ Hotspot : "MODE PERROQUET ACTIVÉ" → Vous vous entendez<br>
            ⏱️ 10s sans PTT → retour TG#59
        </div>
    </div>

    <div class="dtmf-box">
        <h3>🌡️ Température Émetteur</h3>
        <div style="font-size: 1.2em; font-weight: bold; color: #1976d2; margin-bottom: 15px;">
            🔴 DTMF : <span class="dtmf-code" onclick="playDTMF('26#')">26#</span>
        </div>
        <button class="btn-play-dtmf" onclick="playDTMF('26#')">▶ Jouer "26#"</button>
        <div class="dtmf-explain">
            ✅ Annonce : "Température XX degrés"<br>
            ⏱️ 10s → retour TG#59
        </div>
    </div>

    <div class="dtmf-box">
        <h3>🔢 Changement TalkGroup</h3>
        <div style="font-size: 1.2em; font-weight: bold; color: #1976d2; margin-bottom: 15px;">
            🔴 DTMF : <span class="dtmf-code" onclick="playDTMF('55162#')">551<TG>#</span><br>
            Ex: TG62 → <span class="dtmf-code" onclick="playDTMF('55162#')">55162#</span><br>
            TG100 → <span class="dtmf-code" onclick="playDTMF('551100#')">551100#</span>
        </div>
        <button class="btn-play-dtmf" onclick="playDTMF('55162#')">▶ Jouer "55162#"</button>
        <div class="dtmf-explain">
            ✅ Hotspot : "Vous êtes sur TG XX"<br>
            ⏱️ 30s sans PTT → retour TG#59
        </div>
    </div>
</div>

<p><strong>Réalisé pour le réseau f62dmr.fr</strong></p>
<p><strong>Date :</strong> Mars 2026 | <strong>Contact support :</strong> <a href="mailto:contact.amc62@orange.fr">contact.amc62@orange.fr</a></p>

<div style="text-align: center; margin: 30px 0;">
    <a class="download" id="downloadBtn" href="http://hotlink/doc/Guide Dashboard HotLink.pdf" download>
        📥 Télécharger le guide complet en PDF
    </a>
</div>

<hr>

<!-- TOUT LE RESTE DU CODE ORIGINAL INTACT -->
<h2>📊 Introduction</h2>
<p>Ce guide vous accompagne dans l'utilisation du dashboard HotLink. Chaque section est décrite avec les actions à réaliser et les précautions à prendre.</p>

<h2>📡 Section : Statut</h2>
<p>Cette section permet de visualiser uniquement l'état de <strong>connectivité</strong> du HotLink :</p>
<ul>
    <li>État de connexion (en ligne / hors ligne)</li>
    <li>Réseau actif</li>
    <li>Informations de liaison</li>
    <li>
        En cliquant sur <strong>Reflecteur</strong>, vous pouvez vérifier si le hotspot est connecté ou non.
        Un simple clic permet également de visualiser les nodes connectés au Reflector RNFA.<br><br>
        Vous pouvez également y retrouver les commandes suivantes :
        <ul>
            <li>Restart du HotLink</li>
            <li>Stop du HotLink</li>
            <li>Reboot</li>
            <li>Mise hors tension</li>
        </ul>
        Il est d'ailleurs recommandé d'éteindre correctement le HotLink avec la mise hors tension.
    </li>
</ul>

<h2>📶 Section : Configuration du Wi-Fi</h2>
    
<h3>Procédure de configuration</h3>
<ol>
    <li>Le <strong>scan automatique</strong> se lance dès l'ouverture de la section et recherche les réseaux Wi-Fi disponibles.</li>
    <li>Une fois le réseau souhaité trouvé, renseignez les informations suivantes :
        <ul>
            <li><strong>Nom (SSID)</strong> : nom du réseau Wi-Fi de votre box Internet ou partage de connexion</li>
            <li><strong>Clé (Password)</strong> : mot de passe de votre réseau Wi-Fi</li>
        </ul>
    </li>
    <li>Cliquez sur <strong><button>Sauvegarder</button></strong> pour valider la configuration.</li>
</ol>

<div style="background:#fff3cd; border:2px solid #ffc107; padding:12px; border-radius:6px; margin:20px 0;">
    <strong>⚠️ ATTENTION :</strong> Wi-Fi 2.4 GHz uniquement (pas 5 GHz) !
</div>

<h2>🔧 Section : SVXLink Configuration</h2>
<h3>Paramètres modifiables</h3>
<p>Tous les paramètres de cette section sont <strong>modifiables sans risque</strong> pour le système.</p>

<h3>Procédure après modification</h3>
<ol>
    <li>Après modification d'un ou plusieurs paramètres, <strong>il est important de sauvegarder</strong> la modification.</li>
    <li>Si vous êtes sur le <strong>RNFA</strong>, après les modifications il faut <strong>absolument faire une sauvegarde</strong>.</li>
    <li>Dans <strong>Reflector (IP/DNS)</strong>, mettez l'adresse du serveur : <strong>f62dmr.fr</strong>.</li>
    <li><strong>Créez un nouveau profil</strong> et nommez-le <strong>RNFA</strong>, puis <strong>sauvegardez</strong>.</li>
</ol>

<h2>📻 Section : Programmation du SA818 / SA868</h2>
<p>Cette section permet de configurer l'émetteur/récepteur selon la version installée sur votre HotLink. Configuration <strong>sans risque</strong>.</p>

<h2>💻 Section : Terminal</h2>
<table>
    <tr><th>Login</th><td><code>root</code></td></tr>
    <tr><th>Mot de passe</th><td><code>1234</code></td></tr>
</table>
<p><strong>Réservé aux utilisateurs avertis Linux.</strong></p>

<h2>✅ Récapitulatif des bonnes pratiques</h2>
<table>
    <tr><th>Section</th><th>Point clé</th></tr>
    <tr><td>Wi-Fi</td><td>Toujours sauvegarder</td></tr>
    <tr><td>SVXLink</td><td>Profil RNFA + f62dmr.fr</td></tr>
</table>

<p class="signature">
    Document réalisé par FRS077 pour f62dmr.fr — Mars 2026
</p>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const downloadBtn = document.getElementById('downloadBtn');
    
    downloadBtn.addEventListener('click', function(e) {
        e.preventDefault();
        downloadBtn.classList.add('downloading');
        downloadBtn.textContent = '⏳ Téléchargement en cours...';
        
        const link = document.createElement('a');
        link.href = 'http://hotlink/doc/Guide Dashboard HotLink.pdf';
        link.download = 'Guide-Dashboard-HotLink.pdf';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        setTimeout(() => {
            downloadBtn.classList.remove('downloading');
            downloadBtn.textContent = '📥 Télécharger le guide complet en PDF';
        }, 2000);
    });
});

// FONCTION DTMF
function playDTMF(sequence) {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const tones = {
        '1': {f1:697, f2:1209}, '2': {f1:770, f2:1336}, '5': {f1:770, f2:941},
        '6': {f1:852, f2:1477}, '0': {f1:941, f2:1336}, '#': {f1:941, f2:1477}
    };
    let time = 0;
    sequence.split('').forEach(key => {
        if (tones[key]) {
            const osc1 = audioCtx.createOscillator();
            const osc2 = audioCtx.createOscillator();
            osc1.frequency.value = tones[key].f1;
            osc2.frequency.value = tones[key].f2;
            osc1.connect(audioCtx.destination);
            osc2.connect(audioCtx.destination);
            osc1.start(time);
            osc2.start(time);
            setTimeout(() => { osc1.stop(); osc2.stop(); }, 300);
            time += 0.4;
        }
    });
}
</script>

</body>
</html>
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

body{
font-family: Arial, sans-serif;
line-height:1.6;
margin:0 20px 20px 20px;
max-width:900px;
margin-left:auto;
margin-right:auto;
background:#fafafa;
}

/* TITRES */

h1{
color:#333;
border-bottom:3px solid #1976d2;
padding-bottom:10px;
}

h2{
background:#1976d2;
color:white;
padding:10px 15px;
border-radius:6px;
margin-top:35px;
font-size:1.3em;
}

h3{
color:#1976d2;
margin-top:20px;
}

/* BLOCS TEXTE */

p, ul, ol{
background:#f5f7fa;
padding:15px;
border-radius:8px;
border:1px solid #dce3ea;
}

ul,ol{
padding-left:35px;
}

strong{
color:#1976d2;
}

/* TABLE */

table{
border-collapse:collapse;
width:100%;
margin:20px 0;
background:white;
border-radius:8px;
overflow:hidden;
}

th{
background:#1976d2;
color:white;
padding:12px;
}

td{
padding:12px;
border-bottom:1px solid #eee;
}

/* BOUTON DOWNLOAD */

.download{
display:inline-block;
margin:20px 0;
padding:12px 20px;
background:#1976d2;
color:#fff;
text-decoration:none;
border-radius:6px;
font-weight:bold;
cursor:pointer;
transition:0.3s;
}

.download:hover{
background:#0d5aa7;
}

/* INFO RESEAU */

.info-reseau{
background:#dc3545;
color:white;
padding:15px;
border-radius:6px;
margin:20px 0;
text-align:center;
font-weight:bold;
font-size:1.1em;
animation:blink 1.5s infinite;
border:3px solid #ff0000;
}

@keyframes blink{
0%,50%{opacity:1;}
51%,100%{opacity:0.6;}
}

/* FIELDSET DTMF */

fieldset{
border:2px solid #1976d2;
border-radius:10px;
margin:25px 0;
padding:20px;
background:#f5f7fa;
}

fieldset legend{
font-weight:bold;
color:#1976d2;
font-size:1.2em;
padding:0 10px;
background:white;
border-radius:5px;
}

/* CODE DTMF */

.code-dtmf{
background:#d32f2f;
color:white;
padding:8px 15px;
border-radius:6px;
font-family:monospace;
font-size:1.3em;
font-weight:bold;
display:inline-block;
margin:5px;
cursor:pointer;
}

/* BOUTONS */

.btn-dtmf{
background:#1976d2;
color:white;
border:none;
padding:10px 20px;
border-radius:6px;
font-size:1em;
font-weight:bold;
cursor:pointer;
margin:10px 0;
}

/* ALERTES */

.alert{
background:#eef6ff;
border-left:5px solid #1976d2;
padding:12px;
margin:15px 0;
border-radius:6px;
}

.alert-important{
border-left:5px solid #d32f2f;
background:#ffebee;
}

/* SIGNATURE */

.signature{
margin-top:40px;
font-style:italic;
color:#666;
text-align:center;
}

/* MOBILE */

@media (max-width:768px){
body{
margin:0 10px;
}
}

</style>
</head>

<body>

<h1>🛠️ Guide d'utilisation — Dashboard HotLink</h1>

<div class="info-reseau">
⚠️ RNFA f62dmr.fr:5300 Mot de passe <strong>USER</strong> EN MAJUSCULES !
</div>

<p style="background:#fff3cd; border:2px solid #ffc107; padding:12px; border-radius:6px; font-weight:bold; margin-top:10px;">

⚠️ Ces informations concernent exclusivement le réseau RNFA et son HotLink.<br><br>

Pour toute utilisation avec un autre reflector, il est impératif de prendre contact avec le responsable du réseau concerné pour la configuration adaptée à son installation.<br><br>

Ce dashboard a été créé pour une utilisation propre à notre installation RNFA. 
Il est toutefois possible de l'utiliser avec d'autres distributions pour le RRF ou RI49, sous réserve d'être titulaire d'une licence radioamateur.

</p>

<h2>📊 Introduction</h2>

<p>
Ce guide vous accompagne dans l'utilisation du dashboard HotLink. Chaque section est décrite avec les actions à réaliser et les précautions à prendre.
</p>

<h2>📡 Section : Statut</h2>

<ul>

<li>État de connexion (en ligne / hors ligne)</li>

<li>Réseau actif</li>

<li>Informations de liaison</li>

<li>

En cliquant sur <strong>Reflecteur</strong>, vous pouvez vérifier si le hotspot est connecté ou non.

Un simple clic permet également de visualiser les nodes connectés au Reflector RNFA.

<br><br>

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

<li>

Une fois le réseau souhaité trouvé, renseignez :

<ul>

<li><strong>Nom (SSID)</strong></li>

<li><strong>Clé (Password)</strong></li>

</ul>

</li>

<li>Cliquez sur <strong>Sauvegarder</strong></li>

</ol>

<div style="background:#fff3cd; border:2px solid #ffc107; padding:12px; border-radius:6px; margin:20px 0; font-weight:bold;">
⚠️ ATTENTION : Compatibilité Wi-Fi 2.4 GHz uniquement
</div>

<h2>🔧 Section : SVXLink Configuration</h2>

<p>

Tous les paramètres de cette section sont <strong>modifiables sans risque</strong> pour le système.

</p>

<ol>

<li>Après modification, <strong>sauvegarder</strong></li>

<li>Créer profil <strong>RNFA</strong></li>

<li>Reflector : <strong>f62dmr.fr</strong></li>

</ol>

<h2>📻 Section : Programmation du SA818 / SA868</h2>

<p>

Cette section permet de configurer l'émetteur/récepteur selon la version installée sur votre HotLink.

</p>

<h2>💻 Section : Terminal</h2>

<table>

<tr>
<th>Login</th>
<td>root</td>
</tr>

<tr>
<th>Mot de passe</th>
<td>1234</td>
</tr>

</table>

<h2>✅ Récapitulatif des bonnes pratiques</h2>

<table>

<tr>
<th>Section</th>
<th>Point clé</th>
</tr>

<tr>
<td>Wi-Fi</td>
<td>Toujours sauvegarder</td>
</tr>

<tr>
<td>SVXLink</td>
<td>Profil RNFA + f62dmr.fr</td>
</tr>

</table>

<fieldset class="alert-important">

⚠️ LES CODES DTMF CI-DESSOUS SONT UNIQUEMENT POUR LES HOTLINKS !

</fieldset>

<fieldset>

<legend>🎤 MODE PERROQUET</legend>

<span class="code-dtmf">1#</span>

<br><br>

<button class="btn-dtmf" onclick="playPerroquet()">
▶ Jouer DTMF
</button>

</fieldset>

<fieldset>

<legend>🌡️ TEMPÉRATURE ÉMETTEUR</legend>

<span class="code-dtmf">26#</span>

<br><br>

<button class="btn-dtmf" onclick="playTemperature()">
▶ Jouer DTMF
</button>

</fieldset>

<p class="signature">

Document réalisé par FRS077 pour f62dmr.fr — Mars 2026

</p>

<script>

function playPerroquet(){

const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

const tones = {
1:{f1:697,f2:1209},
'#':{f1:941,f2:1477}
};

let time=0;

['1','#'].forEach(key=>{

const osc1=audioCtx.createOscillator();
const osc2=audioCtx.createOscillator();

osc1.frequency.value=tones[key].f1;
osc2.frequency.value=tones[key].f2;

osc1.connect(audioCtx.destination);
osc2.connect(audioCtx.destination);

osc1.start(time);
osc2.start(time);

setTimeout(()=>{
osc1.stop();
osc2.stop();
},300);

time+=0.4;

});

}

function playTemperature(){

const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

const tones = {

'2':{f1:770,f2:1336},
'6':{f1:852,f2:1477},
'#':{f1:941,f2:1477}

};

let time=0;

['2','6','#'].forEach(key=>{

const osc1=audioCtx.createOscillator();
const osc2=audioCtx.createOscillator();

osc1.frequency.value=tones[key].f1;
osc2.frequency.value=tones[key].f2;

osc1.connect(audioCtx.destination);
osc2.connect(audioCtx.destination);

osc1.start(time);
osc2.start(time);

setTimeout(()=>{
osc1.stop();
osc2.stop();
},300);

time+=0.4;

});

}

</script>

</body>
</html>
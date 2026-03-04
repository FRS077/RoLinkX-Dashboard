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
        }
        .download:hover { 
            background: #005fa0; 
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
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
    </style>
</head>
<body>

<h1>🛠️ Guide d'utilisation — Dashboard HotLink</h1>
<p><strong>Réalisé pour le réseau f62dmr.fr</strong></p>
<p><strong>Date :</strong> Mars 2026 | <strong>Contact support :</strong> <a href="mailto:contact.amc62@orange.fr">contact.amc62@orange.fr</a></p>

<div style="text-align: center; margin: 30px 0;">
    <a class="download" href="http://hotlink/doc/Guide Dashboard HotLink.pdf" download>
        📥 Télécharger le guide complet en PDF
    </a>
</div>

<hr>

<h2>📊 Introduction</h2>
<p>Ce guide vous accompagne dans l'utilisation du dashboard HotLink. Chaque section est décrite avec les actions à réaliser et les précautions à prendre.</p>

<h2>📡 Section : Statut</h2>
<p>Cette section permet de visualiser uniquement l'état de <strong>connectivité</strong> du HotLink :</p>
<ul>
    <li>État de connexion (en ligne / hors ligne)</li>
    <li>Réseau actif</li>
    <li>Informations de liaison</li>
</ul>
<p><strong>Aucune action n'est requise dans cette section</strong>, elle est purement informative.</p>

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
    <li>Cliquez sur <strong>Sauvegarder</strong> pour valider la configuration.</li>
</ol>
<p><strong>Important :</strong> N'oubliez pas de sauvegarder après avoir saisi les informations, sinon elles ne seront pas appliquées.</p>

<h2>🔧 Section : SVXLink Configuration</h2>
<h3>Paramètres modifiables</h3>
<p>Tous les paramètres de cette section sont <strong>modifiables sans risque</strong> pour le système.</p>

<h3>Procédure après modification</h3>
<ol>
    <li>Après chaque modification, <strong>créez un nouveau profil</strong>.</li>
    <li>Renseignez obligatoirement <strong>RNFA</strong> pour notre réseau.</li>
    <li>Configurez correctement le <strong>Reflector (IP/DNS)</strong>.</li>
    <li>Les autres paramètres sont librement modifiables selon vos besoins.</li>
</ol>

<h3>🔄 Restauration d'usine</h3>
<p>Il est possible de <strong>revenir aux paramètres d'usine</strong> depuis cette section.</p>
<p><strong>Exception :</strong> Les paramètres du <strong>SA818/SA868</strong> ne seront pas réinitialisés lors d'une restauration d'usine.</p>

<h2>📻 Section : Programmation du SA818 / SA868</h2>
<p>Cette section permet de configurer l'émetteur/récepteur selon la version installée sur votre HotLink :</p>
<ul>
    <li>Configuration pour module SA818</li>
    <li>Configuration pour module SA868</li>
</ul>
<p><strong>Rassurez-vous :</strong> Cette configuration est <strong>sans risque de dysfonctionnement</strong>. Vous pouvez modifier les paramètres en toute sécurité.</p>

<h2>💻 Section : Terminal</h2>
<h3>Accès réservé</h3>
<p>Cette section est réservée aux <strong>utilisateurs avertis</strong> ayant des connaissances Linux.</p>

<h3>Identifiants d'accès</h3>
<table>
    <tr>
        <th>Paramètre</th>
        <th>Valeur</th>
    </tr>
    <tr>
        <td>Login</td>
        <td><code>root</code></td>
    </tr>
    <tr>
        <td>Mot de passe</td>
        <td><code>1234</code></td>
    </tr>
</table>
<p><strong>Attention :</strong> L'utilisation du terminal nécessite des compétences techniques. En cas de doute, contactez le support.</p>

<h2>⚙️ Section : Config</h2>
<p>Cette section contient les paramètres système par défaut du dashboard.</p>
<p><strong>Tous les paramètres sont configurés par défaut.</strong></p>
<p>Aucune action particulière n'est nécessaire dans cette section, sauf si vous souhaitez effectuer une mise à jour manuelle.</p>

<h2>🗺️ Section : Node Info</h2>
<h3>Fonction</h3>
<p>Cette section permet d'envoyer des informations au serveur pour l'affichage sur la carte interactive du site <strong>f62dmr.fr</strong>.</p>

<h3>Caractéristiques</h3>
<ul>
    <li>Envoi des informations <strong>facultatif</strong>.</li>
    <li>Permet de localiser votre nœud sur la carte du réseau.</li>
    <li>Aucune obligation de renseigner ces informations.</li>
</ul>

<h2>📋 Informations complémentaires</h2>
<h3>🔄 Mise à jour automatique</h3>
<p>Le dashboard HotLink effectue une <strong>mise à jour automatique</strong> selon les modalités suivantes :</p>
<ul>
    <li>Fréquence : tous les <strong>dimanches à 3h00 du matin</strong>.</li>
    <li>Condition : le hotspot doit être <strong>en ligne</strong> au moment de la mise à jour.</li>
</ul>

<h3>🛠️ Mise à jour manuelle</h3>
<ol>
    <li>Accédez à la section <strong>Config</strong>.</li>
    <li>Descendez en bas de page.</li>
    <li>Cliquez sur le bouton Mettre à jour le Dashboard.</li>
</ol>

<h3>📞 Support technique</h3>
<ul>
    <li><strong>Email :</strong> <a href="mailto:contact.amc62@orange.fr">contact.amc62@orange.fr</a></li>
    <li>N'hésitez pas à contacter le support avant toute action incertaine.</li>
</ul>

<h2>✅ Récapitulatif des bonnes pratiques</h2>
<table>
    <tr>
        <th>Section</th>
        <th>Point clé</th>
    </tr>
    <tr>
        <td>Configuration Wi-Fi</td>
        <td>Toujours sauvegarder après modification</td>
    </tr>
    <tr>
        <td>SVXLink Config</td>
        <td>Créer nouveau profil avec RNFA après modification</td>
    </tr>
    <tr>
        <td>SA818/SA868</td>
        <td>Pas de risque, configuration libre</td>
    </tr>
    <tr>
        <td>Terminal</td>
        <td>Réservé aux utilisateurs avertis</td>
    </tr>
    <tr>
        <td>Node Info</td>
        <td>Facultatif, pour la carte du réseau</td>
    </tr>
</table>

<p class="signature">
    Document réalisé par FRS077 pour f62dmr.fr — Mars 2026
</p>

</body>
</html>

<?php
exec('sudo /bin/mount -o remount,rw / && sudo /usr/local/bin/update-dash.sh && sudo /bin/mount -o remount,ro /', $output, $return_var);

if ($return_var === 0) {
    ?>
    <style>
        .update-overlay {
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at top, #550000, #000000);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            text-align: center;
        }
        .update-spinner {
            width: 60px;
            height: 60px;
            border: 6px solid rgba(255,255,255,0.2);
            border-top-color: #ff0000;
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
            margin-bottom: 20px;
        }
        .update-bar {
            width: 260px;
            height: 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 10px;
        }
        .update-bar-inner {
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, #ff0000, #ff8800);
            animation: progress-fill 7s forwards; /* plus lent */
        }
        .update-text-main {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .update-text-sub {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes progress-fill {
            from { width: 0; }
            to   { width: 100%; }
        }
    </style>

    <div class="update-overlay">
        <div class="update-spinner"></div>
        <div class="update-text-main">Mise à jour du Dashboard en cours…</div>
        <div class="update-text-sub">Merci de ne pas éteindre le HotLink pendant l’opération.</div>
        <div class="update-bar">
            <div class="update-bar-inner"></div>
        </div>
        <div class="update-text-sub" id="update-status-msg">Préparation…</div>
    </div>

    <script>
        const msg = document.getElementById('update-status-msg');
        const steps = [
            "Téléchargement des fichiers…",
            "Mise à jour des scripts…",
            "Application de la configuration…",
            "Nettoyage et vérifications…",
            "Redémarrage de l’interface…"
        ];
        let i = 0;
        const interval = setInterval(function () {
            i++;
            if (i < steps.length) {
                msg.textContent = steps[i];
            } else {
                clearInterval(interval);
            }
        }, 1300); // change de texte toutes les 1,3 s

        // Redirection plus tardive pour laisser profiter l’animation
        setTimeout(function() {
            window.location.href = '/index.php';
        }, 8000); // 8 secondes
    </script>
    <?php
} else {
    echo "<p style='color:red;font-weight:bold;'>Erreur lors de la mise à jour :</p>";
    echo "<pre style='background:#111;color:#eee;padding:10px;border-radius:4px;max-height:200px;overflow:auto;'>";
    echo htmlspecialchars(implode("\n", $output));
    echo "</pre>";
}
?>

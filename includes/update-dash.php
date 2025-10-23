<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_update'])) {
    if ($_POST['confirm_update'] === 'yes') {
        exec('sudo /bin/mount -o remount,rw / && sudo /usr/local/bin/update-dash.sh && sudo /bin/mount -o remount,ro /');
        echo "Mise à jour effectuée avec succès.";
    } else {
        echo "Mise à jour annulée.";
    }
} else {
    // Affiche le formulaire de confirmation
    echo '<form method="post">
            <p>Voulez-vous vraiment lancer la mise à jour du Dashboard ?</p>
            <button type="submit" name="confirm_update" value="yes">Oui</button>
            <button type="submit" name="confirm_update" value="no">Non</button>
          </form>';
}
?>

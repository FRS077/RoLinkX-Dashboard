<?php
exec('sudo /bin/mount -o remount,rw / && sudo /usr/local/bin/update-dash.sh && sudo /bin/mount -o remount,ro /', $output, $return_var);

if ($return_var === 0) {
    // Redirection vers index.php après succès
    header('Location: /index.php');
    exit;
} else {
    // En cas d'erreur, affichage du message
    echo "Erreur lors de la mise à jour :<br>" . implode("<br>", $output);
}
?>

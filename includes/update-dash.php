<?php
exec('sudo /bin/mount -o remount,rw / && sudo /usr/local/bin/update-dash.sh && sudo /bin/mount -o remount,ro /', $output, $return_var);

if ($return_var === 0) {
    echo "Mise à jour effectuée avec succès.";
} else {
    echo "Erreur lors de la mise à jour :<br>" . implode("<br>", $output);
}
?>
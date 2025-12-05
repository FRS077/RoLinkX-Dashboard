<?php
exec('sudo /bin/mount -o remount,rw / && sudo /usr/local/bin/update-dash.sh && sudo /bin/mount -o remount,ro /', $output, $return_var);

if ($return_var === 0) {
    echo "<p>Mise à jour effectuée avec succès. Vous allez être redirigé vers la page d'accueil...</p>";
    echo "<script>
            setTimeout(function() {
                window.location.href = '/index.php';
            }, 3000);
          </script>";
} else {
    echo "Erreur lors de la mise à jour :<br>" . implode("<br>", $output);
}
?>

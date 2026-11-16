<?php
/**
 * WPS (Push Button) - connexion Wi-Fi via wpa_cli
 *
 * @author FRS3843 Didier
 * @copyright Groupe RNFA
 */

require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

const WPS_IF       = 'wlan0';
const WPS_STATE    = '/tmp/rolinkx_wps.json';
const WPS_LOCK     = '/tmp/rolinkx_wps.lock';
const WPS_WIFI_LOCK = '/tmp/rolinkx_wifi.lock';
const WPS_TIMEOUT  = 125;

const WPS_CONFIG = '/etc/wpa_supplicant/wpa_supplicant.conf';
const WPS_TEMP   = '/tmp/wpa_supplicant.tmp';

const WPS_HEADER =
    "ctrl_interface=DIR=/var/run/wpa_supplicant GROUP=netdev\n" .
    "update_config=1\n" .
    "ap_scan=1\n" .
    "fast_reauth=1\n" .
    "country=FR\n";

/* ---------- Réponses ---------- */

function wpsReply($data, $code = 200)
{
    http_response_code($code);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

    exit;
}

/* ---------- Contrôles de requête ---------- */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    wpsReply(array(
        'state' => 'error',
        'msg'   => 'Méthode non autorisée',
    ), 405);
}

if (
    !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    wpsReply(array(
        'state' => 'error',
        'msg'   => 'Requête refusée : en-tête X-Requested-With manquant',
    ), 403);
}

if (!function_exists('toggleFS')) {
    wpsReply(array(
        'state' => 'error',
        'msg'   => 'Fonction toggleFS() absente de includes/functions.php',
    ), 500);
}

$action = isset($_POST['action']) && is_string($_POST['action'])
    ? $_POST['action']
    : '';

if (!in_array($action, array('start', 'status', 'cancel'), true)) {
    wpsReply(array(
        'state' => 'error',
        'msg'   => 'Action inconnue',
    ), 400);
}

/* ---------- Commandes wpa_cli ---------- */

function wpsCli($args)
{
    $output = array();
    $code = 0;

    exec(
        '/usr/bin/sudo -n wpa_cli -i ' .
        escapeshellarg(WPS_IF) . ' ' . $args . ' 2>&1',
        $output,
        $code
    );

    return trim(implode("\n", $output));
}

function wpsCliOk($output)
{
    return preg_match('/^OK\r?$/m', $output) === 1
        && stripos($output, 'FAIL') === false;
}

function wpsCountNetworks()
{
    $count = 0;

    foreach (explode("\n", wpsCli('list_networks')) as $line) {
        if (preg_match('/^\d+\t/', $line)) {
            $count++;
        }
    }

    return $count;
}

function wpsStatus()
{
    $status = array();

    foreach (explode("\n", wpsCli('status')) as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $status[trim($key)] = trim($value);
        }
    }

    return $status;
}

/* ---------- Verrous ---------- */

function wpsAcquireLock($path)
{
    if (is_link($path)) {
        throw new RuntimeException(
            'Verrou refusé : lien symbolique détecté pour ' . $path
        );
    }

    $handle = @fopen($path, 'c');

    if ($handle === false) {
        throw new RuntimeException(
            'Impossible de créer le verrou ' . $path
        );
    }

    if (!flock($handle, LOCK_EX)) {
        fclose($handle);

        throw new RuntimeException(
            'Impossible de prendre le verrou ' . $path
        );
    }

    return $handle;
}

function wpsReleaseLock($handle)
{
    if (is_resource($handle)) {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

/* ---------- Création de la configuration absente ---------- */

function wpsEnsureConfig()
{
    clearstatcache(true, WPS_CONFIG);

    if (is_link(WPS_CONFIG)) {
        throw new RuntimeException(
            'Configuration en lien symbolique : aucune création automatique.'
        );
    }

    if (file_exists(WPS_CONFIG)) {
        if (!is_file(WPS_CONFIG)) {
            throw new RuntimeException(
                'Le chemin de configuration existe mais n’est pas un fichier.'
            );
        }

        return false;
    }

    /*
     * Si PHP ne peut pas inspecter le répertoire, une absence
     * apparente n'est pas une preuve que le fichier manque.
     */
    $directory = dirname(WPS_CONFIG);

    if (
        !is_dir($directory) ||
        !is_readable($directory) ||
        !is_executable($directory)
    ) {
        throw new RuntimeException(
            'Impossible de vérifier si la configuration existe. ' .
            'Vérifier les droits du répertoire ' . $directory
        );
    }

    /*
     * Création exclusive du temporaire : ne pas écraser
     * un temporaire existant ou suivre un lien symbolique.
     */
    $oldMask = umask(0077);
    $temp = @fopen(WPS_TEMP, 'x');
    umask($oldMask);

    if ($temp === false) {
        throw new RuntimeException(
            'Impossible de créer ' . WPS_TEMP .
            ' : fichier déjà présent ou droits insuffisants.'
        );
    }

    try {
        $written = fwrite($temp, WPS_HEADER);

        if ($written !== strlen(WPS_HEADER) || !fflush($temp)) {
            throw new RuntimeException(
                'Écriture incomplète de la configuration temporaire.'
            );
        }

        fclose($temp);
        $temp = null;

        /*
         * Le verrou partagé avec wifi.php protège les écritures
         * du dashboard. Ne pas modifier le fichier simultanément
         * depuis un autre outil.
         */
        clearstatcache(true, WPS_CONFIG);

        if (file_exists(WPS_CONFIG) || is_link(WPS_CONFIG)) {
            throw new RuntimeException(
                'La configuration est apparue entre-temps. Relancer le WPS.'
            );
        }

        $output = array();
        $code = 0;

        exec(
            '/usr/bin/sudo -n /usr/bin/cp ' .
            escapeshellarg(WPS_TEMP) . ' ' .
            escapeshellarg(WPS_CONFIG) . ' 2>&1',
            $output,
            $code
        );

        if ($code !== 0) {
            throw new RuntimeException(
                'Création de la configuration impossible : ' .
                trim(implode("\n", $output))
            );
        }

        clearstatcache(true, WPS_CONFIG);

        if (!is_file(WPS_CONFIG)) {
            throw new RuntimeException(
                'La copie a terminé mais la configuration est introuvable.'
            );
        }

        return true;
    } finally {
        if (is_resource($temp)) {
            fclose($temp);
        }

        @unlink(WPS_TEMP);
    }
}

/* ---------- État de suivi ---------- */

function wpsWriteState($state)
{
    if (is_link(WPS_STATE)) {
        return false;
    }

    $json = json_encode($state, JSON_INVALID_UTF8_SUBSTITUTE);

    return $json !== false
        && file_put_contents(WPS_STATE, $json, LOCK_EX) !== false;
}

/* ---------- Traitement ---------- */

$wpsLock = null;
$wifiLock = null;
$result = null;
$httpCode = 200;

try {
    $wpsLock = wpsAcquireLock(WPS_LOCK);

    /*
     * Même verrou que wifi.php pour éviter une suppression
     * de réseau pendant une opération de configuration WPS.
     */
    $wifiLock = wpsAcquireLock(WPS_WIFI_LOCK);

    /* ---------- START ---------- */

    if ($action === 'start') {
        wpsCli('wps_cancel');
        @unlink(WPS_STATE);

        $created = false;

        try {
            toggleFS(true);
            $created = wpsEnsureConfig();

            if ($created) {
                $reload = wpsCli('reconfigure');

                if (!wpsCliOk($reload)) {
                    throw new RuntimeException(
                        'Configuration créée, mais rechargement impossible : ' .
                        $reload .
                        ' ; vérifier que wpa_supplicant est démarré ' .
                        'et utilise ' . WPS_CONFIG
                    );
                }
            }
        } finally {
            toggleFS(false);
        }

        /*
         * Active l'autorisation d'enregistrer dans le service
         * déjà en cours, sans réécrire les réseaux existants.
         */
        $update = wpsCli('set update_config 1');

        if (!wpsCliOk($update)) {
            throw new RuntimeException(
                'Impossible d’activer update_config=1 : ' . $update
            );
        }

        $status = wpsStatus();

        if (!isset($status['wpa_state'])) {
            throw new RuntimeException(
                'Impossible de lire l’état de wpa_supplicant sur ' . WPS_IF
            );
        }

        $initial = array(
            't'    => time(),
            'n'    => wpsCountNetworks(),
            'ssid' => isset($status['ssid']) ? $status['ssid'] : '',
            'id'   => isset($status['id']) ? $status['id'] : '',
        );

        if (!wpsWriteState($initial)) {
            throw new RuntimeException(
                'Impossible de créer le fichier de suivi WPS.'
            );
        }

        $output = wpsCli('wps_pbc');

        if (!wpsCliOk($output)) {
            @unlink(WPS_STATE);

            throw new RuntimeException(
                'Impossible de lancer le WPS : ' .
                ($output !== '' ? $output : 'aucune réponse')
            );
        }

        $result = array(
            'state'     => 'pending',
            'remaining' => WPS_TIMEOUT,
            'msg'       => $created
                ? 'Configuration recréée. Appuyez sur le bouton WPS de la box.'
                : 'Appuyez sur le bouton WPS de la box.',
        );
    }

    /* ---------- CANCEL ---------- */

    elseif ($action === 'cancel') {
        $output = wpsCli('wps_cancel');

        if (!wpsCliOk($output)) {
            throw new RuntimeException(
                'Impossible d’annuler le WPS : ' . $output
            );
        }

        @unlink(WPS_STATE);

        $result = array(
            'state' => 'idle',
            'msg'   => 'WPS annulé',
        );
    }

    /* ---------- STATUS ---------- */

    else {
        $initial = null;

        if (is_file(WPS_STATE) && !is_link(WPS_STATE)) {
            $initial = json_decode(
                (string) file_get_contents(WPS_STATE),
                true
            );
        }

        if (
            !is_array($initial) ||
            !isset(
                $initial['t'],
                $initial['n'],
                $initial['ssid'],
                $initial['id']
            )
        ) {
            $result = array('state' => 'idle');
        } else {
            $elapsed = time() - (int) $initial['t'];
            $status = wpsStatus();
            $count = wpsCountNetworks();

            $done = isset($status['wpa_state'])
                && $status['wpa_state'] === 'COMPLETED';

            $newNetwork = $count > (int) $initial['n'];

            $newSsid = isset($status['ssid'])
                && $status['ssid'] !== ''
                && $status['ssid'] !== $initial['ssid'];

            $newId = isset($status['id'])
                && $status['id'] !== ''
                && $status['id'] !== $initial['id'];

            if ($done && ($newNetwork || $newSsid || $newId)) {
                $saved = false;
                $detail = '';

                try {
                    toggleFS(true);

                    /*
                     * Si le fichier a disparu pendant la procédure,
                     * le recréer sans reconfigure : il ne faut pas
                     * effacer le réseau WPS actuellement en mémoire.
                     */
                    wpsEnsureConfig();

                    $update = wpsCli('set update_config 1');

                    if (!wpsCliOk($update)) {
                        throw new RuntimeException(
                            'Activation update_config impossible : ' . $update
                        );
                    }

                    $save = wpsCli('save_config');
                    $saved = wpsCliOk($save);

                    if (!$saved) {
                        $detail = 'save_config : ' .
                            ($save !== '' ? $save : 'aucune réponse');
                    }
                } catch (Throwable $error) {
                    $saved = false;
                    $detail = $error->getMessage();
                } finally {
                    toggleFS(false);
                }

                @unlink(WPS_STATE);

                $ssid = isset($status['ssid'])
                    ? $status['ssid']
                    : '?';

                $result = array(
                    'state' => 'success',
                    'saved' => $saved,
                    'msg'   => $saved
                        ? 'Connecté à ' . $ssid . ' (réseau enregistré)'
                        : 'Connecté à ' . $ssid .
                          ', mais NON enregistré (' . $detail . ')',
                );
            } elseif ($elapsed > WPS_TIMEOUT) {
                wpsCli('wps_cancel');
                @unlink(WPS_STATE);

                $result = array(
                    'state' => 'timeout',
                    'msg'   => 'Délai dépassé : aucune nouvelle connexion ' .
                               'détectée. Si la box était déjà connectée, ' .
                               'ce contrôle peut ne pas distinguer un nouvel ' .
                               'appairage WPS.',
                );
            } else {
                $result = array(
                    'state'     => 'pending',
                    'remaining' => max(0, WPS_TIMEOUT - $elapsed),
                );
            }
        }
    }
} catch (Throwable $error) {
    $result = array(
        'state' => 'error',
        'msg'   => $error->getMessage(),
    );
} finally {
    wpsReleaseLock($wifiLock);
    wpsReleaseLock($wpsLock);
}

wpsReply($result, $httpCode);
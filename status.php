<?php
require_once __DIR__.'/config.php';         
require_once __DIR__.'/tools.php';        
require_once __DIR__.'/functions.php';
    $ip= Get_user_IP();
    $net1= cidr_match($ip, "192.168.0.0/16");
    $net2= cidr_match($ip, "172.16.0.0/12");
    $net3= cidr_match($ip, "127.0.0.0/8");
    $net4= cidr_match($ip, "10.0.0.0/8");
    $net5= cidr_match($ip, REMOTEIP."/32");

    // NOUVELLE FONCTION pour tester f62dmr.fr:5300
    function isDMRServerOnline($host = 'f62dmr.fr', $port = 5300, $timeout = 3) {
        $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }
        return false;
    }
?>
<div style="width:180px;background-color:#232d69;margin-top:8px;"><span style="font-weight: bold;font-size:14px;color:white;">Info SVXLink</span></div>
<fieldset style="width:175px;background-color:#1d2658;margin-top:6px;;margin-bottom:0px;margin-left:0px;margin-right:3px;font-size:12px;border-top-left-radius: 10px; border-top-right-radius: 10px;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;  border: 2px solid white;">

<?php

if (isProcessRunning('svxlink')) {

    echo "<table style=\"margin-top:15px;margin-bottom:15px;\">\n";
    echo "<tr><th><span style=\"font-size:12px;\">Logiques actives</span></th></tr>\n";

    $svxConfigFile = SVXCONFPATH."/".SVXCONFIG;

    if (fopen($svxConfigFile, 'r')) {$svxconfig = parse_ini_file($svxConfigFile, true, INI_SCANNER_RAW); 
    }
    $logics = explode(",", $svxconfig['GLOBAL']['LOGICS']);
    foreach ($logics as $key) {
        echo "<tr><td height=\"20px\" style=\"background:#3f4c84;\"><span style=\"color:yellow;font-weight: bold;\">".$key."</span></td></tr>";
    }
    echo "</table>\n";
    echo "<table style=\"margin-top:2px;margin-bottom:13px;\">\n";
    if (($system_type=="IS_DUPLEX") && (isset($svxconfig['RepeaterLogic']['MODULES']))) { $modules = explode(",", str_replace('Module', '', $svxconfig['RepeaterLogic']['MODULES'])); 
    }
    elseif (($system_type=="IS_SIMPLEX") && (isset($svxconfig['SimplexLogic']['MODULES']))) { $modules = explode(",", str_replace('Module', '', $svxconfig['SimplexLogic']['MODULES'])); 
    }
    else
    { $modules=""; 
    }
    
    $modecho = "False";
    if ($modules !=="") {
        define("SVXMODULES", $modules);
        $admodules = getActiveModules();
        echo "<tr><th><span style=\"font-size:12px;\">Modules chargés</span></th></tr>\n";
        foreach ($modules as $key) {
            if ($admodules[$key]=="On") {
                $activemod="<td height=\"20px\" style=\"background:MediumSeaGreen;color:#464646;font-weight: bold;\">";
            } else {
                  $activemod="<td height=\"20px\" style=\"background:#3f4c84;color:yellow;font-weight: bold;\">";
            }

            echo "<tr>".$activemod."".$key."</td></tr>";

            if ($key=="EchoLink") {$modecho ="True"; 
            }
        }

    } else {
        echo "<tr><td style=\"background: #ffffed;\" ><span style=\"color:#b0b0b0;\"><b>No Modules loaded</span></td></tr>";
    }
    echo "</table>\n";

    // only if we have an active reflector connection
    if ($reflector_active) {
        $tgtmp = trim(getSVXTGTMP());
        echo "<form method=\"post\"><table colspan=2 style=\"margin-top:4px;margin-bottom:13px;\">\n";
        $tgdefault = $svxconfig['ReflectorLogic']['DEFAULT_TG'];
        $tgmon = explode(",", $svxconfig['ReflectorLogic']['MONITOR_TGS']);
        echo "<tr><th  height=\"20px\" width=50%>TG Default</th><td style=\"background:#3f4c84;color:yellow;font-weight:bold;\">".$tgdefault."</td></tr>\n";
        echo "<tr><th width=50%>TG Monitor</th><td style=\"background:#3f4c84;color:yellow;font-weight:600;\">";
        echo "<div style=\"white-space:normal;\">";
        foreach ($tgmon as $key) {
        if ($net1 == true || $net2 == true || $net3 == true || $net4 == true || $net5 == true) {
            echo "<button title=\"Activez le groupe ".str_replace("+","",$key)."\" style=\"color:yellow;font-size:8.5pt;font-weight:600;\" type=submit id=jumptoA name=jmptoA class=active_id value=\"".str_replace("+","",$key)."\">".$key."</button>";
           } else { 
             echo $key." "; }
        }
        if ($net1 == true || $net2 == true || $net3 == true || $net4 == true || $net5 == true) {
        echo "<button title=\"Aktywuj grupę ".$tgtmp."\" style=\"color:cyan;font-size:8.5pt;font-weight:600;\" type=submit id=jumptoA name=jmptoA class=active_id value=\"".$tgtmp."\">".$tgtmp."</button>";
        } else {
               echo "<span style=\"background: #ffffed;color:cyan;font-size:8.5pt;font-weight: bold;\">".$tgtmp."</span>";}
        echo "</div></td></tr>\n";

        $tgselect = trim(getSVXTGSelect());
        if ($tgselect=="0") {$tgselect="";
        }
        echo "<tr><th height=\"20px\" width=50%>TG Active</th><td style=\"background:#3f4c84;color:#08dc6e;font-weight: bold;\">".$tgselect."</td></tr>\n";
        echo "</table></form>";
    }

    if (($system_type=="IS_DUPLEX") && ($svxconfig['RepeaterLogic']['TX'] !== "NONE")) {
        echo "<table  style=\"margin-bottom:13px;\"><tr><th>Repeater Status</th></tr><tr>";
        echo getTXInfo();
        echo getRXMute();
        echo "</table>\n"; 
    }
    elseif (($system_type=="IS_SIMPLEX") && ($svxconfig['SimplexLogic']['TX'] !== "NONE")) {
        echo "<table  style=\"margin-bottom:13px;\"><tr><th>Dernier statut TRX</th></tr><tr>";
        echo getTXInfo();
        echo getRXMute();
        echo "</table>\n"; 
    }

    echo "<table style=\"margin-top:10px;margin-bottom:13px;\"><tr><th colspan=2 >Info Système</th></tr><tr>";
    echo "<td colspan=2 style=\"background:#3f4c84;\"><div style=\"margin-top:4px;margin-bottom:4px;white-space:normal;color:#000000;font-weight: bold;\">"; 
    echo "<span style=\"color:cyan;\">Dernier Reboot</span><br><span style=\"color:yellow;\">",exec('uptime -s')."</span>";
    echo "</div></td></tr>";
    if ($system_type == "IS_DUPLEX") {
        echo "<td colspan=2 style=\"background:#3f4c84;\"><div style=\"margin-top:4px;margin-bottom:4px;white-space:normal;color:#08dc6e;font-weight: bold;\">";
        echo "Mode: duplex";
        echo "</div></td></tr>";
    }
    if ($system_type == "IS_SIMPLEX") {
        echo "<td colspan=2 style=\"background:#3f4c84;\"><div style=\"margin-top:4px;margin-bottom:4px;white-space:normal;color:#08dc6e;font-weight:bold;\">";
        echo "Mode: simplex";
        echo "</div></td></tr>";
    }
    if ($net1 == true || $net2 == true || $net3 == true || $net4 == true || $net5 == true) {
        echo "<td colspan=2 style=\"background:#3f4c84;\"><div style=\"margin-top:4px;margin-bottom:4px;white-space:normal;color:#f95e87;font-weight: bold;\">";
        echo "<span style=\"color:cyan;font-weight: bold;\">Niveau d'accès à DsB:</span><BR>Full/Intranet/VPN";
        echo "</div></td></tr>";
        
        // NOUVEAU : Statut serveur DMR f62dmr.fr:5300
        if (isDMRServerOnline()) {
            echo "<td colspan=2 style=\"background:#3f4c84;\"><div style=\"margin-top:4px;margin-bottom:4px;white-space:normal;color:#08dc6e;font-weight: bold;\">";
            echo "<span style=\"color:cyan;font-weight: bold;\">Statut f62dmr.fr:5300</span><BR><span style=\"color:#08dc6e;font-size:14px;font-weight: bold;\">ONLINE</span>";
            echo "</div></td></tr>";
        } else {
            echo "<td colspan=2 style=\"background:#3f4c84;\"><div style=\"margin-top:4px;margin-bottom:4px;white-space:normal;color:#f95e87;font-weight: bold;\">";
            echo "<span style=\"color:cyan;font-weight: bold;\">Statut f62dmr.fr:5300</span><BR><span style=\"color:#f95e87;font-size:14px;font-weight: bold;\">OFFLINE</span>";
            echo "</div></td></tr>";
        }
    }
    echo "</table>\n";
} else {
    echo "<span style=\"color:#f95e87;font-size:13.5px;font-weight: bold;\"><br>SVXLink n'est pas<br>en cours d'exécution</span><br><br>";
}
?>
</fieldset>

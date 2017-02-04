<?php
require_once "../prolog/settings.php";

$str = $_REQUEST['strError'].'  REMOTE_ADDR = ' . $_SERVER['REMOTE_ADDR'] . ';  SERVER_PROTOCOL = ' . $_SERVER['SERVER_PROTOCOL'] . ';  REQUEST_METHOD = ' . $_SERVER['REQUEST_METHOD'] . "\n";
if( $jsLogLevel === 2 || $jsLogLevel === 4 ) {
    $tab = "\t";
    $errorStack = "\t" . str_replace("\n", "\n" . $tab, trim($_REQUEST['errorStack'])) . "\n";
    $str .= $errorStack;
}

file_put_contents($siteDirOnPC . '/error_log', $str, FILE_APPEND);

?>
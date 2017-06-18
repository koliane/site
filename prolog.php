<?php
require_once "prolog/settings.php";
require_once "prolog/connect_db.php";
require_once "prolog/php_write_log.php";
require_once "prolog/func_p.php";

if( empty( $pdo ) )
	$pdo = connectToDB();
?>
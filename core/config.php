<?php
//Database 
$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "bearhug123";
$db_name = "bearhug_monitor";
//MySQL Connector
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if (mysqli_connect_errno()) {
	echo "Database Error";
	exit;
}
date_default_timezone_set('Asia/Jakarta');
?>
<?php
$DB_USER="logd"; //Database Username
$DB_PASS="logd"; //Database Password
$DB_HOST="localhost"; //Database Hostname
$DB_NAME="logd"; //Database Databasename
$DB_USEDATACACHE = 1;
$DB_DATACACHEPATH = "/var/tmp/logd";

if ($DB_USER.$DB_PASS.$DB_HOST.$DB_NAME == ""){
	echo "You must edit the dbconnect.php file to set it up for your database.";
}
?>

<?php
// PRIVATE_CODE
// translator ready
// addnews ready
// mail ready
require_once("dbconnect.php");
require_once("lib/http.php");

$remotes = array(
	// [redacted by archivist]
	// [redacted by archivist]
	'192.168.0.1'=>true,
	'127.0.0.1'=>true,
);

if ($remotes[$_SERVER['REMOTE_ADDR']]) {
	if (httpget('op')==""){
		$name = "logdbackup-".date("Y-m-d H:i").".sql.gz";
		header("Content-Type: application/x-gzip; name=\"".htmlentities($name)."\";");
		header("Content-Disposition: attachment/download; filename=\"".htmlentities($name)."\";");
		$cmd = "/usr/local/mysql/bin/mysqldump -u \"$DB_USER\" --pass=\"$DB_PASS\" -h \"$DB_HOST\" \"$DB_NAME\" -f -q | gzip -9";
		flush();
		e_exec($cmd);
	}else{
		//e_exec("webalizer -c ~lotgd/webalizer.conf 2>&1 > /dev/null");
		e_exec("webalizer -c ~lotgd/webalizer.conf");
	}
}else{
	echo $_SERVER['REMOTE_ADDR']." cannot use this script.";
}

function e_exec($cmd){
  $fp=popen("$cmd 2>&1",'r');
	fpassthru($fp);
}

?>

<?php

define("ALLOW_ANONYMOUS",true);

require_once("common.php");
savesetting("newdaySemaphore",gmdate("Y-m-d H:i:s"));
modulehook("newday-runonce",array());

if (getsetting("usedatacache",0)){
	$handle = opendir($datacachefilepath);
	while (($file = readdir($handle)) !== false) {
		if (substr($file,0,strlen(DATACACHE_FILENAME_PREFIX)) ==
				DATACACHE_FILENAME_PREFIX){
			$fn = $datacachefilepath."/".$file;
			$fn = preg_replace("'//'","/",$fn);
			$fn = preg_replace("'\\\\'","\\",$fn);
			if (is_file($fn) &&
					filemtime($fn) < strtotime("-24 hours")){
				unlink($fn);
			}else{
			}
		}
	}
}

//Clean-up old mails
$sql = "DELETE FROM " . db_prefix("mail") . " WHERE sent<'".date("Y-m-d H:i:s",strtotime("-".getsetting("oldmail",14)."days"))."'";
db_query($sql);

massinvalidate("mail");

?>

<?
//newday runonce
	//Let's do a new day operation that will only fire off for
	//one user on the whole server.
		//check last time we did this vs now to see if it was a different game day.
	$lastnewdaysemaphore = convertgametime(strtotime(getsetting("newdaySemaphore","0000-00-00 00:00:00") . " +0000"));
	$gametoday = gametime();
	if (gmdate("Ymd",$gametoday)!=gmdate("Ymd",$lastnewdaysemaphore)){
		// it appears to be a different game day, acquire semaphore and
		// check again.
		$sql = "LOCK TABLES " . db_prefix("settings") . " WRITE";
		db_query($sql);
		clearsettings();
		$lastnewdaysemaphore = convertgametime(strtotime(getsetting("newdaySemaphore","0000-00-00 00:00:00") . " +0000"));
			$gametoday = gametime();
		if (gmdate("Ymd",$gametoday)!=gmdate("Ymd",$lastnewdaysemaphore)){
			//we need to run the hook, update the setting, and unlock.
			savesetting("newdaySemaphore",gmdate("Y-m-d H:i:s"));
			$sql = "UNLOCK TABLES";
			db_query($sql);

			//run the hook.
			modulehook("newday-runonce",array());

			//Do some high-load-cleanup

			//Moved from lib/datacache.php
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
			//Expire Chars
			require_once("lib/expire_chars.php");

			//Clean up old mails
			$sql = "DELETE FROM " . db_prefix("mail") . " WHERE sent<'".date("Y-m-d H:i:s",strtotime("-".getsetting("oldmail",14)."days"))."'";
			db_query($sql);
			massinvalidate("mail");


			if (getsetting("expirecontent",180)>0){
			//Clean up debug log, moved from there
                $timestamp = date("Y-m-d H:i:s",strtotime("-".round(getsetting("expirecontent",180)/10,0)." days"));
                $sql = "DELETE FROM " . db_prefix("debuglog") . " WHERE date <'$timestamp' LIMIT 300";
                db_query($sql);
                require_once("lib/gamelog.php");
                gamelog("Cleaned up ".db_affected_rows()." from ".db_prefix("debuglog")." older than $timestamp.",'maintenance');

			//Clean up old comments

				$sql = "DELETE FROM " . db_prefix("commentary") . " WHERE postdate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
				db_query($sql);
				gamelog("Deleted ".db_affected_rows()." old comments.","comment expiration");
			}


		}else{
			//someone else beat us to it, unlock.
			$sql = "UNLOCK TABLES";
			db_query($sql);
		}
	}
?>
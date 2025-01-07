<?php
//newday runonce
	//Let's do a new day operation that will only fire off for
	//one user on the whole server.
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
	$sql = "DELETE FROM " . DB::prefix("mail") . " WHERE sent<'".date("Y-m-d H:i:s",strtotime("-".getsetting("oldmail",14)."days"))."'";
	DB::query($sql);
	massinvalidate("mail");
	// Clean up old bans
	DB::query("DELETE FROM " . DB::prefix("bans") . " WHERE banexpire < \"".date("Y-m-d")."\" AND banexpire>'0000-00-00'");

	if (getsetting("expirecontent",180)>0){
		//Clean up debug log, moved from there
               	$timestamp = date("Y-m-d H:i:s",strtotime("-".round(getsetting("expirecontent",180)/10,0)." days"));
      	        $sql = "DELETE FROM " . DB::prefix("debuglog") . " WHERE date <'$timestamp'";
 	   	DB::query($sql);
       		require_once("lib/gamelog.php");
       		gamelog("Cleaned up ".DB::affected_rows()." from ".DB::prefix("debuglog")." older than $timestamp.",'maintenance');

		//Clean up game log
		$timestamp = date("Y-m-d H:i:s",strtotime("-1 month"));
		$sql = "DELETE FROM ".DB::prefix("gamelog")." WHERE date < '$timestamp' ";
		DB::query($sql);
		gamelog("Cleaned up ".DB::prefix("gamelog")." table removing ".DB::affected_rows()." older than $timestamp.","maintenance");

		//Clean up old comments

		$sql = "DELETE FROM " . DB::prefix("commentary") . " WHERE postdate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
		DB::query($sql);
		gamelog("Deleted ".DB::affected_rows()." old comments.","comment expiration");
		
		//Clean up old moderated comments

		$sql = "DELETE FROM " . DB::prefix("moderatedcomments") . " WHERE moddate<'".date("Y-m-d H:i:s",strtotime("-".getsetting("expirecontent",180)." days"))."'";
		DB::query($sql);
		gamelog("Deleted ".DB::affected_rows()." old moderated comments.","comment expiration");
	}
	if (strtotime(getsetting("lastdboptimize", date("Y-m-d H:i:s", strtotime("-1 day")))) < strtotime("-1 day"))
	require_once("lib/newday/dbcleanup.php");
?>

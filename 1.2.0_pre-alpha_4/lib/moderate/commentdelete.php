<?php
	$comdb  = DB::prefix("commentary");
	$accdb  = DB::prefix("accounts");
	$bandb  = DB::prefix("bans");
	$clandb = DB::prefix("clans");
	
	$comment = httppost('comment');
	if (httppost('delnban')>''){
		$sql = "SELECT DISTINCT uniqueid,author FROM $comdb INNER JOIN $accdb ON acctid=author WHERE commentid IN ('" . join("','",array_keys($comment)) . "')";
		$result = DB::query($sql);
		$untildate = date("Y-m-d H:i:s",strtotime("+3 days"));
		$reason = httppost("reason");
		$reason0 = httppost("reason0");
		$default = "Banned for comments you posted.";
		if ($reason0 != $reason && $reason0 != $default) $reason = $reason0;
		if ($reason=="") $reason = $default;
		while ($row = DB::fetch_assoc($result)){
			$sql = "SELECT * FROM $bandb WHERE uniqueid = '{$row['uniqueid']}'";
			$result2 = DB::query($sql);
			$sql = "INSERT INTO $bandb (uniqueid,banexpire,banreason,banner) VALUES ('{$row['uniqueid']}','$untildate','$reason','".addslashes($user->name)."')";
			$sql2 = "UPDATE $accdb SET loggedin=0 WHERE acctid={$row['author']}";
			if (DB::num_rows($result2)>0){
				$row2 = DB::fetch_assoc($result2);
				if ($row2['banexpire'] < $untildate){
					//don't enter a new ban if a longer lasting one is
					//already here.
					DB::query($sql);
					DB::query($sql2);
				}
			}else{
				DB::query($sql);
				DB::query($sql2);
			}
		}
	} //end delnban
	
	if (!isset($comment) || !is_array($comment)) $comment = array();
	$sql = "SELECT $comdb.*, $accdb.name, $accdb.login, $accdb.clanrank, " .
		"$clandb.clanshort FROM $comdb INNER JOIN $accdb ON ".
		"$accdb.acctid = $comdb.author LEFT JOIN $clandb ON ".
		"$clandb.clanid=$accdb.clanid WHERE commentid IN ('".join("','",array_keys($comment))."')";
	$result = DB::query($sql);
	$invalsections = array();
	while ($row = DB::fetch_assoc($result)){
		$sql = "INSERT LOW_PRIORITY INTO ".DB::prefix("moderatedcomments").
			" (moderator,moddate,comment) VALUES ('{$user->acctid}','".date("Y-m-d H:i:s")."','".addslashes(serialize($row))."')";
		DB::query($sql);
		$invalsections[$row['section']] = 1;
	}
	$sql = "DELETE FROM $comdb WHERE commentid IN ('" . join("','",array_keys($comment)) . "')";
	DB::query($sql);
	$return = httpget('return');
	$return = cmd_sanitize($return);
	$return = substr($return,strrpos($return,"/")+1);
	if (strpos($return,"?")===false && strpos($return,"&")!==false){
		$x = strpos($return,"&");
		$return = substr($return,0,$x-1)."?".substr($return,$x+1);
	}
	foreach($invalsections as $key=>$dummy) {
		invalidatedatacache("comments-$key");
	}
	//update moderation cache
	invalidatedatacache("comments-or11");
	redirect($return);
?>

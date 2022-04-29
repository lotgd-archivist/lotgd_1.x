<?php
function gamelog($message,$category="general",$filed=false){
	static $cleanup = true;
	if ($cleanup){ //test once per page hit at most.
		$cleanup = false;
		if (e_rand(1,100) == 2){
			$timestamp = date("Y-m-d H:i:s",strtotime("-1 month"));
			$sql = "DELETE FROM ".db_prefix("gamelog")." WHERE date < '$timestamp' LIMIT 300";
			db_query($sql);
			gamelog("Cleaned up ".db_prefix("gamelog")." table removing ".db_affected_rows()." older than $timestamp.","maintenance");
		}
	}
	global $session;
	$sql = "INSERT INTO ".db_prefix("gamelog")." (message,category,filed,date,who) VALUES (
		'".addslashes($message)."',
		'".addslashes($category)."',
		'".($filed?"1":"0")."',
		'".date("Y-m-d H:i:s")."',
		'".(int)$session['user']['acctid']."'
	)";
	db_query($sql);
}
?>

<?php
function gamelog($message,$category="general",$filed=false){
	global $user;
	$sql = "INSERT INTO ".DB::prefix("gamelog")." (message,category,filed,date,who) VALUES (
		'".addslashes($message)."',
		'".addslashes($category)."',
		'".($filed?"1":"0")."',
		'".date("Y-m-d H:i:s")."',
		'".(int)$user->acctid."'
	)";
	DB::query($sql);
}
?>

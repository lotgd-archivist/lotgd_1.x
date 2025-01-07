<?php
$sql = "INSERT INTO " . DB::prefix("bans") . " (banner,";
$type = httppost("type");
if ($type=="ip"){
	$sql.="ipfilter";
}else{
	$sql.="uniqueid";
}
$sql.=",banexpire,banreason) VALUES ('" . addslashes($user->name) . "',";
if ($type=="ip"){
	$sql.="\"".httppost("ip")."\"";
}else{
	$sql.="\"".httppost("id")."\"";
}
$duration = (int)httppost("duration");
if ($duration == 0) $duration="0000-00-00";
else $duration = date("Y-m-d", strtotime("+$duration days"));
	$sql.=",\"$duration\",";
$sql.="\"".httppost("reason")."\")";
if ($type=="ip"){
	if ($_SERVER['REMOTE_ADDR'] && substr($_SERVER['REMOTE_ADDR'],0,strlen(httppost("ip"))) ==
			httppost("ip")){
		$sql = "";
		output("You don't really want to ban yourself now do you??");
		output("That's your own IP address!");
	}
}else{
	if ($_COOKIE['lgi']==httppost("id")){
		$sql = "";
		output("You don't really want to ban yourself now do you??");
		output("That's your own ID!");
	}
}
if ($sql!=""){
	DB::query($sql);
	output("%s ban rows entered.`n`n", DB::affected_rows());
	output_notl("%s", DB::error());
	$sql = 'UPDATE `' . DB::prefix('accounts') . '` SET loggedin = 0 WHERE ' . ($type=='ip'?'lastip = \'' . httppost('ip'):'uniqueid = \'' . httppost('id')) . '\'';
	DB::query($sql);
	debuglog("entered a ban: " .  ($type=="ip"?  "IP: ".httppost("ip"): "ID: ".httppost("id")) . " Ends after: $duration  Reason: \"" .  httppost("reason")."\"");
}
?>

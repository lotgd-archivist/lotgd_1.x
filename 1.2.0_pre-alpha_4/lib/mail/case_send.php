<?php
$to = httppost('to');
if ($user->superuser & SU_IS_GAMEMASTER) {
	$from = httppost('from');
	if ($from == "" || is_numeric(trim($from)) || $from == "0") {
		$from = $user->acctid;
	}
} else {
	$from = $user->acctid;
}

$sql = "SELECT acctid FROM " . DB::prefix("accounts") . " WHERE login='$to'";
$result = DB::query($sql);
if(DB::num_rows($result)>0){
	$row1 = DB::fetch_assoc($result);
	if (getsetting("onlyunreadmails",true)) {
		$maillimitsql = "AND seen=0";
	} else {
		$maillimitsql = "";
	}
	$sql = "SELECT count(messageid) AS count FROM " . DB::prefix("mail") . " WHERE msgto='".$row1['acctid']."' $maillimitsql";
	$result = DB::query($sql);
	$row = DB::fetch_assoc($result);
	if ($row['count']>=getsetting("inboxlimit",50)) {
		output("`\$You cannot send that person mail, their mailbox is full!`0`n`n");
	}else{
		$subject = str_replace("`n","",httppost('subject'));
		$body = str_replace("`n","\n",httppost('body'));
		$body = str_replace("\r\n","\n",$body);
		$body = str_replace("\r","\n",$body);
		$body = addslashes(substr(stripslashes($body),0,(int)getsetting("mailsizelimit",1024)));
		require_once("lib/systemmail.php");
		systemmail($row1['acctid'],$subject,$body,$from);
		invalidatedatacache("mail-{$row1['acctid']}");
		output("Your message was sent!`n");
	}
}else{
	output("Could not find the recipient, please try again.`n");
}
if(httppost("returnto")){
	$op="read";
	httpset('op','read');
	$id = httppost('returnto');
	httpset('id',$id);
}else{
	$op="";
	httpset('op', "");
}
?>

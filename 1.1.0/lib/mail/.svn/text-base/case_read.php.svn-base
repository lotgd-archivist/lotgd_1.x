<?
$sql = "SELECT " . db_prefix("mail") . ".*,". db_prefix("accounts"). ".name FROM " . db_prefix("mail") ." LEFT JOIN " . db_prefix("accounts") . " ON ". db_prefix("accounts") . ".acctid=" . db_prefix("mail"). ".msgfrom WHERE msgto=\"".$session['user']['acctid']."\" AND messageid=\"".$id."\"";
$result = db_query($sql);
if (db_num_rows($result)>0){
	$row = db_fetch_assoc($result);
	if ((int)$row['msgfrom']==0){
		$row['name']=translate_inline("`i`^System`0`i");
		// No translation for subject if it's not an array
		if (is_array(@unserialize($row['subject']))) {
			$row['subject'] = unserialize($row['subject']);
			$row['subject'] =
				call_user_func_array("sprintf_translate", $row['subject']);
		}
		// No translation for body if it's not an array
		if (is_array(@unserialize($row['body']))) {
			$row['body'] = unserialize($row['body']);
			$row['body'] =
				call_user_func_array("sprintf_translate", $row['body']);
		}
	}
	if (!$row['seen']) output("`b`#NEW`b`n");
	else output("`n");
	output("`b`2From:`b `^%s`n",$row['name']);
	output("`b`2Subject:`b `^%s`n",$row['subject']);
	output("`b`2Sent:`b `^%s`n",$row['sent']);
	output_notl("<img src='images/uscroll.GIF' width='182' height='11' alt='' align='center'>`n",true);
	output_notl(str_replace("\n","`n",$row['body']));
	output_notl("`n<img src='images/lscroll.GIF' width='182' height='11' alt='' align='center'>`n",true);
		$sql = "UPDATE " . db_prefix("mail") . " SET seen=1 WHERE  msgto=\"".$session['user']['acctid']."\" AND messageid=\"".$id."\"";
	db_query($sql);
	invalidatedatacache("mail-{$session['user']['acctid']}");
		$reply = translate_inline("Reply");
	$del = translate_inline("Delete");
	$unread = translate_inline("Mark Unread");
	$report = translate_inline("Report to Admin");
	$problem = "Abusive Email Report:\nFrom: {$row['name']}\nSubject: {$row['subject']}\nSent: {$row['sent']}\nID: {$row['messageid']}\nBody:\n{$row['body']}";
	rawoutput("<table width='50%' border='0' cellpadding='0' cellspacing='5'><tr>
		<td><a href='mail.php?op=write&replyto={$row['messageid']}' class='motd'>$reply</a></td>
		<td><a href='mail.php?op=del&id={$row['messageid']}' class='motd'>$del</a></td>
		</tr><tr>
		<td><a href='mail.php?op=unread&id={$row['messageid']}' class='motd'>$unread</a></td>");
	// Don't allow reporting of system messages as abuse.
	if ((int)$row['msgfrom']!=0) {
		rawoutput("<td><a href=\"petition.php?problem=".rawurlencode($problem)."&abuse=yes\" class='motd'>$report</a></td>");
	} else {
		rawoutput("<td align='right'>&nbsp;</td>");
	}
	rawoutput("</tr><tr>");
	$sql = "SELECT messageid FROM ".db_prefix("mail")." WHERE msgto='{$session['user']['acctid']}' AND messageid < '$id' ORDER BY messageid DESC LIMIT 1";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		$row = db_fetch_assoc($result);
		$pid = $row['messageid'];
	}else{
		$pid = 0;
	}
	$sql = "SELECT messageid FROM ".db_prefix("mail")." WHERE msgto='{$session['user']['acctid']}' AND messageid > '$id' ORDER BY messageid  LIMIT 1";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		$row = db_fetch_assoc($result);
		$nid = $row['messageid'];
	}else{
		$nid = 0;
	}
	$prev = translate_inline("< Previous");
	$next = translate_inline("Next >");
	rawoutput("<td nowrap='true'>");
	if ($pid > 0) rawoutput("<a href='mail.php?op=read&id=$pid' class='motd'>".htmlentities($prev, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</a>");
	else rawoutput(htmlentities($prev), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	rawoutput("</td><td nowrap='true'>");
	if ($nid > 0) rawoutput("<a href='mail.php?op=read&id=$nid' class='motd'>".htmlentities($next, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</a>");
	else rawoutput(htmlentities($next), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	rawoutput("</td>");
	rawoutput("</tr></table>");
}else{
	output("Eek, no such message was found!");
}
?>
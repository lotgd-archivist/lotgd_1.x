<?php
output("`b`iMail Box`i`b");
if (isset($session['message'])) {
	output($session['message']);
}
$session['message']="";
$mail = db_prefix("mail");
$accounts = db_prefix("accounts");
$sql = "SELECT subject,messageid,".$accounts.".name,msgfrom,seen,sent FROM ".$mail." LEFT JOIN ".$accounts." ON ".$accounts.".acctid=".$mail.".msgfrom WHERE msgto=\"".$session['user']['acctid']."\" ORDER BY sent DESC";
$result = db_query($sql);
$db_num_rows = db_num_rows($result);
if ($db_num_rows>0){
	$no_subject = translate_inline("`i(No Subject)`i");
	rawoutput("<form action='mail.php?op=process' method='post'><table>");
	while($row = db_fetch_assoc($result)) {
		rawoutput("<tr>");
		rawoutput("<td nowrap><input id='checkbox$i' type='checkbox' name='msg[]' value='{$row['messageid']}'><img src='images/".($row['seen']?"old":"new")."scroll.GIF' width='16' height='16' alt='".($row['seen']?"Old":"New")."'></td>",true);
		rawoutput("<td>");
		if ((int)$row['msgfrom']==0){
			$row['name']=translate_inline("`i`^System`0`i");
			// Only translate the subject if it's an array, ie, it came
			// from the game.
			if (is_array(@unserialize($row['subject']))) {
				$row['subject'] = unserialize($row['subject']);
				$row['subject'] =
					call_user_func_array("sprintf_translate", $row['subject']);
			}
		}
		output_notl("<a href='mail.php?op=read&id={$row['messageid']}'>".
				htmlentities(((trim($row['subject']))?$row['subject']:$no_subject), ENT_COMPAT,  getsetting("charset", "ISO-8859-1"))
				."</a>", true);
		rawoutput("</td><td><a href='mail.php?op=read&id={$row['messageid']}'>",true);
		output_notl($row['name']);
		rawoutput("</a></td><td><a href='mail.php?op=read&id={$row['messageid']}'>".date("M d, h:i a",strtotime($row['sent']))."</a></td>",true);
		rawoutput("</tr>");
	}
	rawoutput("</table>");
	$checkall = htmlentities(translate_inline("Check All"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	rawoutput("<input type='button' value=\"$checkall\" class='button' onClick='
			var elements = document.getElementsByName(\"msg[]\");
			for(i = 0; i < elements.length; i++) {
				elements[i].checked = true;
			}
			'>");
	$delchecked = htmlentities(translate_inline("Delete Checked"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	rawoutput("<input type='submit' class='button' value=\"$delchecked\">");
	rawoutput("</form>");
}else{
	output("`iAww, you have no mail, how sad.`i");
}
output("`n`n`iYou currently have %s messages in your inbox.`nYou will no longer be able to receive messages from players if you have more than %s unread messages in your inbox.  `nMessages are automatically deleted (read or unread) after %s days.",db_num_rows($result),getsetting('inboxlimit',50),getsetting("oldmail",14));
?>

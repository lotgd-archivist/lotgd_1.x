<?
output("`b`iMail Box`i`b");
if (isset($session['message'])) {
	output($session['message']);
}
$session['message']="";
$sql = "SELECT subject,messageid," . db_prefix("accounts") . ".name,msgfrom,seen,sent FROM " . db_prefix("mail") . " LEFT JOIN " . db_prefix("accounts") . " ON " . db_prefix("accounts") . ".acctid=" . db_prefix("mail") . ".msgfrom WHERE msgto=\"".$session['user']['acctid']."\" ORDER BY sent DESC";
$result = db_query($sql);
if (db_num_rows($result)>0){
	output_notl("<form action='mail.php?op=process' method='POST'><table>",true);
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		if ((int)$row['msgfrom']==0){
			$row['name']=translate_inline("`i`^System`0`i");
			// Only translate the subject if it's an array, ie, it came
			// from the game.
			if (is_array(@unserialize($row['subject']))) {
				$row['subject'] = unserialize($row['subject']);
				$row['subject'] =
					call_user_func_array("sprintf_translate",
							$row['subject']);
			}
		}
		output_notl("<tr>",true);
		output_notl("<td nowrap><input id='checkbox$i' type='checkbox' name='msg[]' value='{$row['messageid']}'><img src='images/".($row['seen']?"old":"new")."scroll.GIF' width='16' height='16' alt='".($row['seen']?"Old":"New")."'></td>",true);
		output_notl("<td><a href='mail.php?op=read&id={$row['messageid']}'>",true);
		if (trim($row['subject'])=="")
			output("`i(No Subject)`i");
		else
			output_notl($row['subject']);
		output_notl("</a></td><td><a href='mail.php?op=read&id={$row['messageid']}'>",true);
		output_notl($row['name']);
		output_notl("</a></td><td><a href='mail.php?op=read&id={$row['messageid']}'>".date("M d, h:i a",strtotime($row['sent']))."</a></td>",true);
		output_notl("</tr>",true);
	}
	output_notl("</table>",true);
	$checkall = htmlentities(translate_inline("Check All"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	$out="<input type='button' value=\"$checkall\" class='button' onClick='";
	for ($i=$i-1;$i>=0;$i--){
		$out.="document.getElementById(\"checkbox$i\").checked=true;";
	}
	$out.="'>";
	output_notl($out,true);
	$delchecked = htmlentities(translate_inline("Delete Checked"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	output_notl("<input type='submit' class='button' value=\"$delchecked\">",true);
	output_notl("</form>",true);
}else{
	output("`iAww, you have no mail, how sad.`i");
}
output("`n`n`iYou currently have %s messages in your inbox.`nYou will no longer be able to receive messages from players if you have more than %s unread messages in your inbox.  `nMessages are automatically deleted (read or unread) after %s days.",db_num_rows($result),getsetting('inboxlimit',50),getsetting("oldmail",14));
?>
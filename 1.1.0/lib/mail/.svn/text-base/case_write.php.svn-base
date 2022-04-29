<?
$subject=httppost('subject');
$body="";
$row = "";
output_notl("<form action='mail.php?op=send' method='POST'>",true);
$replyto = httpget('replyto');
if ($replyto!=""){
	$sql = "SELECT ". db_prefix("mail") . ".body," . db_prefix("mail") . ".msgfrom, " . db_prefix("mail") . ".subject,". db_prefix("accounts") . ".login, superuser, " . db_prefix("accounts"). ".name FROM " . db_prefix("mail") . " LEFT JOIN " . db_prefix("accounts") . " ON " . db_prefix("accounts") . ".acctid=" . db_prefix("mail") . ".msgfrom WHERE msgto=\"".$session['user']['acctid']."\" AND messageid=\"".$replyto."\"";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		$row = db_fetch_assoc($result);
		if ($row['login']=="") {
			output("You cannot reply to a system message.`n");
			$row=array();
		}
	}else{
		output("Eek, no such message was found!`n");
	}
}
$to = httpget('to');
if ($to!=""){
	$sql = "SELECT login,name, superuser FROM " . db_prefix("accounts") . " WHERE login=\"$to\"";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		$row = db_fetch_assoc($result);
	}else{
		output("Could not find that person.`n");
	}
}
if (is_array($row)){
	if (isset($row['subject']) && $row['subject']!=""){
		if ((int)$row['msgfrom']==0){
			$row['name']=translate_inline("`i`^System`0`i");
			// No translation for subject if it's not an array
			if (is_array(@unserialize($row['subject']))) {
				$row['subject'] = unserialize($row['subject']);
				$row['subject'] =
					call_user_func_array("sprintf_translate",
							$row['subject']);
			}
			// No translation for body if it's not an array
			if (is_array(@unserialize($row['body']))) {
				$row['body'] = unserialize($row['body']);
				$row['body'] =
					call_user_func_array("sprintf_translate",
							$row['body']);
			}
		}
		$subject=$row['subject'];
		if (substr($subject,0,4)!="RE: ") $subject="RE: $subject";
	}
	if (isset($row['body']) && $row['body']!=""){
		$body="\n\n---".translate_inline("Original Message")."---\n".$row['body'];
	}
}
rawoutput("<input type='hidden' name='returnto' value=\"".htmlentities(stripslashes(httpget("replyto")), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">");
$superusers = array();
if (isset($row['login']) && $row['login']!=""){
	output_notl("<input type='hidden' name='to' id='to' value=\"".htmlentities($row['login'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">",true);
	output("`2To: `^%s`n",$row['name']);
	if (($row['superuser'] & SU_GIVES_YOM_WARNING) &&
               !($row['superuser'] & SU_OVERRIDE_YOM_WARNING)) {
		array_push($superusers,$row['login']);
       }
}else{
	output("`2To: ");
	$to = httppost('to');
	$string="%";
	for ($x=0;$x<strlen($to);$x++){
		$string .= substr($to,$x,1)."%";
	}
	$sql = "SELECT login,name, superuser FROM " . db_prefix("accounts") . " WHERE name LIKE '".addslashes($string)."' AND locked=0 ORDER by login='$to' DESC, name='$to' DESC, login";
	$result = db_query($sql);
	if (db_num_rows($result)==1){
		$row = db_fetch_assoc($result);
		output_notl("<input type='hidden' id='to' name='to' value=\"".htmlentities($row['login'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">",true);
		output_notl("`^{$row['name']}`n");
		if (($row['superuser'] & SU_GIVES_YOM_WARNING) &&
                   !($row['superuser'] & SU_OVERRIDE_YOM_WARNING)) {
			array_push($superusers,$row['login']);
           }
	}elseif (db_num_rows($result)==0){
		output("`@No one was found who matches \"%s\".  ",stripslashes($to));
		$try = translate_inline("Please try again");
		output_notl("<a href=\"mail.php?op=address&prepop=".rawurlencode(stripslashes(htmlentities($to, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))))."\">$try</a>.",true);
		popup_footer();
		exit();
	}else{
		output_notl("<select name='to' id='to' onChange='check_su_warning();'>",true);
		$superusers = array();
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			output_notl("<option value=\"".HTMLEntities($row['login'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">",true);
			require_once("lib/sanitize.php");
			output_notl("%s", full_sanitize($row['name']));
			if (($row['superuser'] & SU_GIVES_YOM_WARNING) &&
                       !($row['superuser'] & SU_OVERRIDE_YOM_WARNING)) {
				array_push($superusers,$row['login']);
               }
		}
		output_notl("</select>`n",true);
	}
}
rawoutput("<script language='JavaScript'>
var superusers = new Array();");
while (list($key,$val)=each($superusers)){
	rawoutput("	superusers['".addslashes($val)."'] = true;");
}
rawoutput("</script>");
output("`2Subject:");
rawoutput("<input name='subject' value=\"".HTMLEntities($subject).HTMLEntities(stripslashes(httpget('subject')), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\"><br>");
rawoutput("<div id='warning' style='visibility: hidden; display: none;'>");
output("`2Notice: `^$superusermessage`n");
rawoutput("</div>");
output("`2Body:`n");
rawoutput("<textarea name='body' id='textarea' class='input' cols='60' rows='9' onKeyUp='sizeCount(this);'>".HTMLEntities($body).HTMLEntities(stripslashes(httpget('body')), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</textarea><br>");
$send = translate_inline("Send");
rawoutput("<table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td><input type='submit' class='button' value='$send'></td><td align='right'><div id='sizemsg'></div></td></tr></table>");
output_notl("</form>",true);
$sizemsg = "`#Max message size is `@%s`#, you have `^XX`# characters left.";
$sizemsg = translate_inline($sizemsg);
$sizemsg = sprintf($sizemsg,getsetting("mailsizelimit",1024));
$sizemsgover = "`\$Max message size is `@%s`\$, you are over by `^XX`\$ characters!";
$sizemsgover = translate_inline($sizemsgover);
$sizemsgover = sprintf($sizemsgover,getsetting("mailsizelimit",1024));
$sizemsg = explode("XX",$sizemsg);
$sizemsgover = explode("XX",$sizemsgover);
$usize1 = addslashes("<span>".appoencode($sizemsg[0])."</span>");
$usize2 = addslashes("<span>".appoencode($sizemsg[1])."</span>");
$osize1 = addslashes("<span>".appoencode($sizemsgover[0])."</span>");
$osize2 = addslashes("<span>".appoencode($sizemsgover[1])."</span>");
	rawoutput("
<script language='JavaScript'>
	var maxlen = ".getsetting("mailsizelimit",1024).";
	function sizeCount(box){
		var len = box.value.length;
		var msg = '';
		if (len <= maxlen){
			msg = '$usize1'+(maxlen-len)+'$usize2';
		}else{
			msg = '$osize1'+(len-maxlen)+'$osize2';
		}
		document.getElementById('sizemsg').innerHTML = msg;
	}
	sizeCount(document.getElementById('textarea'));
		function check_su_warning(){
		var to = document.getElementById('to');
		var warning = document.getElementById('warning');
		if (superusers[to.value]){
			warning.style.visibility = 'visible';
			warning.style.display = 'inline';
		}else{
			warning.style.visibility = 'hidden';
			warning.style.display = 'none';
		}
	}
	check_su_warning();
	</script>");
?>
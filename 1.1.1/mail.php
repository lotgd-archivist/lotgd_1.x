<?php
// translator ready
// addnews ready
// mail ready
define("OVERRIDE_FORCED_NAV",true);
require_once("common.php");
require_once("lib/http.php");

tlschema("mail");

$superusermessage = getsetting("superuseryommessage","Asking an admin for gems, gold, weapons, armor, or anything else which you have not earned will not be honored.  If you are experiencing problems with the game, please use the 'Petition for Help' link instead of contacting an admin directly.");

// $sql = "DELETE FROM " . db_prefix("mail") . " WHERE sent<'".date("Y-m-d H:i:s",strtotime("-".getsetting("oldmail",14)."days"))."'";
// db_query($sql);
// really needed to relocated. Performancekiller.
// Ndro with global mail-* invalidation
//


$op = httpget('op');
$id = httpget('id');
if($op=="del"){
	$sql = "DELETE FROM " . db_prefix("mail") . " WHERE msgto='".$session['user']['acctid']."' AND messageid='$id'";
	db_query($sql);
	//<Edo>
	invalidatedatacache("mail-{$session['user']['acctid']}");
	//</Edo>
	header("Location: mail.php");
	exit();
}elseif($op=="process"){
	$msg = httppost('msg');
	if (!is_array($msg) || count($msg)<1){
		$session['message'] = "`\$`bYou cannot delete zero messages!  What does this mean?  You pressed \"Delete Checked\" but there are no messages checked!  What sort of world is this that people press buttons that have no meaning?!?`b`0";
		header("Location: mail.php");
	}else{
		$sql = "DELETE FROM " . db_prefix("mail") . " WHERE msgto='".$session['user']['acctid']."' AND messageid IN ('".join("','",$msg)."')";
		db_query($sql);
		invalidatedatacache("mail-{$session['user']['acctid']}");
		header("Location: mail.php");
		exit();
	}
}elseif ($op=="unread"){
	$sql = "UPDATE " . db_prefix("mail") . " SET seen=0 WHERE msgto='".$session['user']['acctid']."' AND messageid='$id'";
	db_query($sql);
	invalidatedatacache("mail-{$session['user']['acctid']}");
	header("Location: mail.php");
	exit();
}

popup_header("Ye Olde Poste Office");
$inbox = translate_inline("Inbox");
$write = translate_inline("Write");

// Build the initial args array
$args = array();
array_push($args, array("mail.php", $inbox));
array_push($args, array("mail.php?op=address",$write));
// to use this hook,
// just call array_push($args, array("pagename", "functionname"));,
// where "pagename" is the name of the page to forward the user to,
// and "functionname" is the name of the mail function to add
$mailfunctions = modulehook("mailfunctions", $args);

//output_notl("<table width='25%' border='0' cellpadding='0' cellspacing='2'><tr><td><a href='mail.php' class='motd'>$inbox</a></td><td><a href='mail.php?op=address' class='motd'>$write</a></td>", true);
rawoutput("<table width='50%' border='0' cellpadding='0' cellspacing='2'>");
rawoutput("<tr>");
for($i=0;$i<count($mailfunctions);$i++) {
	if (is_array($mailfunctions[$i])) {
		if (count($mailfunctions[$i])==2) {
			$page = $mailfunctions[$i][0];
			$name = $mailfunctions[$i][1]; // already translated
			rawoutput("<td><a href='$page' class='motd'>$name</a></td>");
			// addnav("", $page);
			// No need for addnav since mail function pages are (or should
			// be) outside the page nav system.
		}
	}
}
rawoutput("</tr></table>");
output_notl("`n`n");

if($op=="send"){
require_once("lib/mail/case_send.php");
}

switch ($op) {
case "read":
	require_once("lib/mail/case_read.php");
	break;
case "address":
	output_notl("<form action='mail.php?op=write' method='POST'>",true);
	output("`b`2Address:`b`n");
	$to = translate_inline("To: ");
	$search = htmlentities(translate_inline("Search"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	output_notl("`2$to <input name='to' value=\"".htmlentities(stripslashes(httpget('prepop')), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\"> <input type='submit' class='button' value=\"$search\"></form>",true);
	break;
case "write":
	require_once("lib/mail/case_write.php");
	break;
default:
	require_once("lib/mail/case_default.php");
	break;
}
popup_footer();
?>
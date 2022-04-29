<?php
// addnews ready
// translator ready
// mail ready
require_once("common.php");
require_once("lib/sanitize.php");

tlschema("bio");

checkday();

$ret = httpget('ret');
if ($ret==""){
	$return = "/list.php";
}else{
	$return = cmd_sanitize($ret);
}

$char = httpget('char');
$sql = "SELECT login, name, level, sex, title, specialty, hashorse, acctid, resurrections, bio, dragonkills, race, clanname, clanshort, clanrank FROM " . db_prefix("accounts") . " LEFT JOIN " . db_prefix("clans") . " ON " . db_prefix("accounts") . ".clanid = " . db_prefix("clans") . ".clanid WHERE login='$char'";
$result = db_query($sql);
$target = db_fetch_assoc($result);
$target['login'] = rawurlencode($target['login']);
$id = $target['acctid'];
$target['return_link']=$return;

page_header("Character Biography: %s", full_sanitize($target['name']));

tlschema("nav");
addnav("Return");
tlschema();

if ($session['user']['superuser'] & SU_EDIT_USERS){
	addnav("Superuser");
	addnav("Edit User","user.php?op=edit&userid=$id");
}

modulehook("biotop", $target);

output("`^Biography for %s`^.",$target['name']);
$write = translate_inline("Write Mail");
if ($session['user']['loggedin'])
	rawoutput("<a href=\"mail.php?op=write&to={$target['login']}\" target=\"_blank\" onClick=\"".popup("mail.php?op=write&to={$target['login']}").";return false;\"><img src='images/newscroll.GIF' width='16' height='16' alt='$write' border='0'></a>");
output_notl("`n`n");

if ($target['clanname']>""){
	$ranks = array(
		"`@%s`2 is an `!applicant`2 to `%%s`2`n",
		"`@%s`2 is a `#member`2 of `%%s`2`n",
		"`@%s`2 is an `^officer`2 of `%%s`2`n",
		"`@%s`2 is a `&leader`2 of `%%s`2`n"
	);
	$ranks = translate_inline($ranks);
	output_notl($ranks[$target['clanrank']],$target['name'],$target['clanname']);
}

output("`^Title: `@%s`n",$target['title']);
output("`^Level: `@%s`n",$target['level']);
output("`^Resurrections: `@%s`n",$target['resurrections']);

$race = translate_inline($target['race']);
output("`^Race: `@%s`n",$race);

$genders = array("Male","Female");
$genders = translate_inline($genders);
output("`^Gender: `@%s`n",$genders[$target['sex']]);

$specialties = modulehook("specialtynames",
		array(""=>translate_inline("Unspecified")));
output("`^Specialty: `@%s`n",$specialties[$target['specialty']]);

$sql = "SELECT * FROM " . db_prefix("mounts") . " WHERE mountid='{$target['hashorse']}'";
$result = db_query_cached($sql, "mountdata-{$target['hashorse']}", 3600);
$mount = db_fetch_assoc($result);

$mount['acctid']=$target['acctid'];
$mount = modulehook("bio-mount",$mount);
$none = translate_inline("`iNone`i");
if (!isset($mount['mountname']) || $mount['mountname']=="")
	   $mount['mountname'] = $none;
output("`^Creature: `@%s`0`n",$mount['mountname']);

modulehook("biostat", $target);

if ($target['dragonkills']>0)
	output("`^Dragon Kills: `@%s`n",$target['dragonkills']);

if ($target['bio']>"")
	output("`^Bio: `@`n%s`n",soap($target['bio']));

modulehook("bioinfo", $target);

output("`n`^Recent accomplishments (and defeats) of %s`^",$target['name']);
$result = db_query("SELECT * FROM " . db_prefix("news") . " WHERE accountid={$target['acctid']} ORDER BY newsdate DESC,newsid ASC LIMIT 100");

$odate="";
tlschema("news");
for ($i=0;$i<db_num_rows($result);$i++){
	$row = db_fetch_assoc($result);
	if ($row['arguments'] > "") {
		$arguments = array();
		$base_arguments = unserialize($row['arguments']);
		array_push($arguments, $row['newstext']);
		while(list($key, $val) = each($base_arguments)) {
			array_push($arguments, $val);
		}
		$news = call_user_func_array("sprintf_translate", $arguments);
		rawoutput(tlbutton_clear());
	} else {
		$news = $row['newstext'];
	}
	if ($odate!=$row['newsdate']){
		output_notl("`n`b`@%s`0`b`n",
				date("D, M d", strtotime($row['newsdate'])));
		$odate=$row['newsdate'];
	}
	output_notl("`@$news`0`n");
}
tlschema();

if ($ret==""){
	$return = substr($return,strrpos($return,"/")+1);
	tlschema("nav");
	addnav("Return");
	addnav("Return to the warrior list",$return);
	tlschema();
}else{
	$return = substr($return,strrpos($return,"/")+1);
	tlschema("nav");
	addnav("Return");
	if ($return=="list.php") {
		addnav("Return to the warrior list",$return);
	} else {
		addnav("Return whence you came",$return);
	}
	tlschema();
}

modulehook("bioend", $target);
page_footer();

?>

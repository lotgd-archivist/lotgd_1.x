<?php
//addnews ready
// mail ready
require_once("common.php");
require_once("lib/showform.php");
require_once("lib/datetime.php");
require_once("lib/http.php");
require_once("lib/sanitize.php");
require_once("lib/names.php");

tlschema("user");
check_su_access(SU_EDIT_USERS);

$op = httpget('op');

if ($op == "lasthit") {
	// Try and keep user editor and captcha from breaking each other.
	$_POST['i_am_a_hack'] = 'true';
}
page_header("User Editor");

$sort = httpget('sort');
$petition=httpget("returnpetition");
$returnpetition="";
if ($petition != "") $returnpetition = "&returnpetition=$petition";

$gentime = 0;
$gentimecount = 0;

$order = "acctid";
if ($sort!="") $order = "$sort";
$display = 0;
$query = httppost('q');
if ($query === false) $query = httpget('q');

if ($op=="search" || $op== ""){
	require_once("lib/lookup_user.php");
	list($searchresult, $err) = lookup_user($query, $order);
	$op = "";
	if ($err) {
		output($err);
	} else {
		$display = 1;
	}
}


$m = httpget("module");
if ($m) $m = "&module=$m&subop=module";
rawoutput("<form action='user.php?op=search$m' method='POST'>");
output("Search by any field below: ");
rawoutput("<input name='q' id='q'>");
$se = translate_inline("Search");
rawoutput("<input type='submit' class='button' value='$se'>");
rawoutput("</form>");
rawoutput("<script language='JavaScript'>document.getElementById('q').focus();</script>");
addnav("","user.php?op=search$m");
require_once("lib/superusernav.php");
superusernav();
addnav("Bans");
addnav("Add a ban","user.php?op=setupban");
addnav("List/Remove bans","user.php?op=removeban");

// THis doesn't seem to be used, so I'm going to comment it out now
//$msg = httpget('msg');
//if ($msg>"") {
//	output("Message: %s`n", $msg);
//}

// Collect a list of the mounts
$mounts="0," . translate_inline("None");
$sql = "SELECT mountid,mountname,mountcategory FROM " . db_prefix("mounts") .  " ORDER BY mountcategory";
$result = db_query($sql);
while ($row = db_fetch_assoc($result)){
	$mounts.=",{$row['mountid']},{$row['mountcategory']}: ".color_sanitize($row['mountname']);
}

$specialties = array(""=>translate_inline("Undecided"));
$specialties = modulehook("specialtynames", $specialties);
$enum = "";
foreach ($specialties as $key=>$name) {
	if ($enum) $enum .= ",";
	$enum .= "$key,$name";
}

//Inserted for v1.1.0 Dragonprime Edition to extend clan possibilities
$ranks = array(CLAN_APPLICANT=>"`!Applicant`0",CLAN_MEMBER=>"`#Member`0",CLAN_OFFICER=>"`^Officer`0",CLAN_LEADER=>"`&Leader`0");
$ranks = modulehook("clanranks", array("ranks"=>$ranks, "clanid"=>$session['user']['clanid']));
$ranks = $ranks['ranks'];
$rankstring = "";
foreach($ranks as $rankid => $rankname) {
	if ($rankstring != "") $rankstring .= ",";
	$rankstring .= $rankid.",".sanitize($rankname);
}
$userinfo = array(
	"Account info,title",
	"acctid"=>"User id,viewonly",
	"login"=>"Login",
	"newpassword"=>"New Password",
	"emailaddress"=>"Email Address",
	"locked"=>"Account Locked,bool",
	"banoverride"=>"Override Bans for this account,bool",
	"referer"=>"ID of player who referred this player," .
		(($session['user']['superuser'] & SU_EDIT_DONATIONS) ? "int" : "viewonly"),
	"refererawarded"=>"Has the referring player been awarded points,viewonly",

	"Basic user info,title",
	"name"=>"Character Name (Do NOT include ANY title information)",
	"title"=>"Dragonkill Title (prepended to name if Custom Title unset)" . (getsetting("edittitles",1) ? "" : ",hidden"),
	"ctitle"=>"Custom Title (prepended to name if set)",
	"sex"=>"Sex,enum,0,Male,1,Female",
	"age"=>"Days since level 1,int",
	"dragonkills"=>"How many times has slain the dragon,int",
	"dragonage"=>"How old when last killed dragon,int",
	"bestdragonage"=>"Youngest days when killed dragon,int",
	"pk"=>"Has user attacked in pvp?,bool",
	"bio"=>"Bio",

	"Stats,title",
	"level"=>"Level,int",
	"race"=>"Race",
	"experience"=>"Experience,int",
	"hitpoints"=>"Current Hitpoints,int",
	"maxhitpoints"=>"Max Hitpoints,int",
	"turns"=>"Turns left,int",
	"playerfights"=>"Playerfights left,int",
	"attack"=>"Attack (includes weapon damage),int",
	"defense"=>"Defense (includes armor defense),int",
	"spirits"=>"Spirits (display only),enum,-6,Resurrected,-2,Very Low,-1,Low,0,Normal,1,High,2,Very High",
	"resurrections"=>"Resurrections,int",
	"location"=>"Where is the user currently",

	"Specialty,title",
	"specialty"=>"Specialty,enumpretrans,". $enum,

	"Grave Fights,title",
	"deathpower"=>"Favor with Ramius,int",
	"gravefights"=>"Grave fights left,int",
	"soulpoints"=>"Soulpoints (HP while dead),int",

	"Gear,title",
	"gems"=>"Gems,int",
	"gold"=>"Gold in hand,int",
	"goldinbank"=>"Gold in bank,int",
	"transferredtoday"=>"Number of transfers today,int",
	"amountouttoday"=>"Total value of transfers from player today,int",
	"weapon"=>"Weapon Name",
	"weapondmg"=>"Damage of weapon,int",
	"weaponvalue"=>"Purchase cost of weapon,int",
	"armor"=>"Armor Name",
	"armordef"=>"Armor defense,int",
	"armorvalue"=>"Purchase cost of armor,int",

	"Special,title",
	"charm"=>"Charm,int",
	"seendragon"=>"Saw dragon today,bool",
	"seenmaster"=>"Seen master,bool",
	"hashorse"=>"Mount,enumpretrans,".$mounts,
	"fedmount"=>"Fed mount today,bool",
	"boughtroomtoday"=>"Bought a room today,bool",

	"Clan Info,title",
	"clanid"=>"Clan,enumpretrans,0,".translate_inline("None"),
	"clanrank"=>"Clan Rank,enum,$rankstring",
	"clanjoindate"=>"Clan Join Date",

	"Superuser Flags,title",
	"superuser"=>"Superuser Permissions".
	    "<br /><i>For the most part you can only set flags that you yourself possess;".
		"<br />if you try to set one that you don't have; it won't stick.</i>,".
		"bitfield,".
		($session['user']['superuser'] | SU_ANYONE_CAN_SET |
		  ($session['user']['superuser']&SU_MEGAUSER ? 0xFFFFFFFF : 0)).",".
		SU_MEGAUSER.        ",MEGA USER (enable all permissions)* <i>(this applies to any future flags as well)</i>".
		"<br/><b>Editors</b>,".
		SU_EDIT_CONFIG.     ",Edit Game Configurations*,".
		SU_EDIT_USERS.      ",Edit Users*,".
		SU_EDIT_MOUNTS.     ",Edit Mounts,".
		SU_EDIT_CREATURES.  ",Edit Creatures & Taunts,".
		SU_EDIT_EQUIPMENT.  ",Edit Armor & Weapons,".
		SU_EDIT_RIDDLES.    ",Edit Riddles,".
		(file_exists("looteditor.php")? translate_inline(SU_EDIT_ITEMS.",Edit Loot Items,"):"").
		SU_MANAGE_MODULES.  ",Manage Modules".
		"<br/><b>Customer Service</b>,".
		SU_EDIT_PETITIONS.  ",Handle Petitions,".
		SU_EDIT_COMMENTS.   ",Moderate Comments,".
		SU_MODERATE_CLANS.  ",Moderate Clan Commentary,".
		SU_AUDIT_MODERATION.",Audit Moderated Comments,".
        SU_OVERRIDE_YOM_WARNING.",Do NOT display YOM warning for this person,".
		SU_POST_MOTD.       ",Post MoTD's".
		"<br/><b>Donations</b>,".
		SU_EDIT_DONATIONS.  ",Manage Donations*,".
		SU_EDIT_PAYLOG.     ",Manage Payment Log".
		"<br/><b>Game Development</b>,".
		SU_INFINITE_DAYS.   ",Infinite Days*,".
		SU_DEVELOPER.       ",Game Developer* (super powers),".
		SU_IS_TRANSLATOR.   ",Enable Translation Tool,".
		SU_DEBUG_OUTPUT.    ",Debug Output,".
		SU_SHOW_PHPNOTICE.  ",See PHP Notices in debug output,".
		SU_RAW_SQL.         ",Execute Raw SQL*,".
		SU_VIEW_SOURCE.     ",View source code,".
		SU_GIVE_GROTTO.     ",Grotto access (only if not granted implicitly in another permission),".
		SU_NEVER_EXPIRE.    ",Account never expires".
		"<br />* Granting any of these options will hide the user from the HoF",

	"Misc Info,title",
	"regdate"=>"Registered on,viewonly",
	"beta"=>"Willing to participate in beta,bool",
	"laston"=>"Last On (local time),viewonly",
	"lasthit"=>"Last New Day (time is in GMT not local),viewonly",
	"lastmotd"=>"Last MOTD date (local time),viewonly",
	"lastip"=>"Last IP,viewonly",
	"uniqueid"=>"Unique ID,viewonly",
	"gentime"=>"Sum of page gen times,viewonly",
	"gentimecount"=>"Page hits,viewonly",
	"allowednavs"=>"Allowed Navs,viewonly",
	"dragonpoints"=>"Dragon points spent,viewonly",
	"bufflist"=>"Buff List,viewonly",
	"prefs"=>"Preferences,viewonly",
	"donationconfig"=>"Donation buys,viewonly",
	"badguy"=>"Last Badguy,viewonly"
);

$sql = "SELECT clanid,clanname,clanshort FROM " . db_prefix("clans") ." ORDER BY clanshort";
$result = db_query($sql);
while ($row=db_fetch_assoc($result)){
	$userinfo['clanid'].=",{$row['clanid']},<{$row['clanshort']}> {$row['clanname']}";
}

$userid=httpget("userid");
if ($op=="lasthit"){
	$output="";
	$sql = "SELECT output FROM " . db_prefix("accounts_output") . " WHERE acctid='$userid'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	echo str_replace(".focus();",".blur();",str_replace("<iframe src=","<iframe Xsrc=",$row['output']));
	exit();
}elseif ($op=="savemodule"){
	//save module settings.
	$userid = httpget('userid');
	$module = httpget('module');
	$post = httpallpost();
	$post = modulehook("validateprefs", $post, true, $module);
	if (isset($post['validation_error']) && $post['validation_error']) {
		tlschema("module-$module");
		$post['validation_error'] =
			translate_inline($post['validation_error']);
		tlschema();
		output("Unable to change settings: `\$%s`0", $post['validation_error']);
	} else {
		reset($post);
		while (list($key,$val)=each($post)){
			output("Setting %s to %s`n", $key, stripslashes($val));
			$sql = "REPLACE INTO " . db_prefix("module_userprefs") . " (modulename,userid,setting,value) VALUES ('$module','$userid','$key','$val')";
			db_query($sql);
		}
		output("`^Preferences for module %s saved.`n", $module);
	}
	$op = "edit";
	httpset("op", "edit");
	httpset("subop", "module", true);
}elseif ($op=="special"){
	if (httppost("newday") !=""){
#		$offset = "-".(24 / (int)getsetting("daysperday",4))." hours";
#		$newdate = date("Y-m-d H:i:s",strtotime($offset));
#		$sql = "UPDATE " . db_prefix("accounts") . " SET lasthit='$newdate' WHERE acctid='$userid'";
		$sql = "UPDATE " . db_prefix("accounts") . " SET lasthit='0000-00-00 00:00:00' WHERE acctid='$userid'";
		db_query($sql);
	}elseif(httppost("fixnavs")!=""){
		$sql = "UPDATE " . db_prefix("accounts") . " SET allowednavs='', restorepage='', specialinc='' WHERE acctid='$userid'";
		db_query($sql);
		$sql = "DELETE FROM ".db_prefix("accounts_output")." WHERE acctid='$userid';";
		db_query($sql);
	} elseif(httppost("clearvalidation")!=""){
		$sql = "UPDATE " . db_prefix("accounts") . " SET emailvalidation='' WHERE acctid='$userid'";
		db_query($sql);
	}

	$op = "edit";
	httpset("op", "edit");
}elseif ($op=="save"){
	$sql = "";
	$updates=0;
	$oldvalues = stripslashes(httppost('oldvalues'));
	$oldvalues = unserialize($oldvalues);
	// Handle recombining the old name
	$otitle = $oldvalues['title'];
	if ($oldvalues['ctitle']) $otitle = $oldvalues['ctitle'];
	$oldvalues['name'] = $otitle . ' ' . $oldvalues['name'];

	$post = httpallpost();
	reset($post);
	while (list($key,$val)=each($post)){
		if (isset($userinfo[$key])){
			if ($key=="newpassword" ){
				if ($val>"") {
					$sql.="password=\"".md5(md5($val))."\",";
					$updates++;
					output("Password value has been updated.`n");
					debuglog($session['user']['name']."`0 changed password to $val",$userid);
					if ($session['user']['acctid']==$userid) {
						$session['user']['password']=md5(md5($val));
					}
				}
			}elseif ($key=="superuser"){
				$value = 0;
				while (list($k,$v)=each($val)){
					if ($v) $value += (int)$k;
				}

				//strip off an attempt to set privs that the user doesn't
				//have authority to set.
				$stripfield = ((int)$oldvalues['superuser'] | $session['user']['superuser'] | SU_ANYONE_CAN_SET | ($session['user']['superuser'] & SU_MEGAUSER ? 0xFFFFFFFF : 0));
				$value = $value & $stripfield;

				//put back on privs that the user used to have but the
				//current user can't set.
				$unremovable = ~ ((int)$session['user']['superuser'] | SU_ANYONE_CAN_SET | ($session['user']['superuser'] & SU_MEGAUSER ? 0xFFFFFFFF : 0));
				$filteredunremovable = (int)$oldvalues['superuser'] & $unremovable;
				$value = $value | $filteredunremovable;
				if ((int)$value != (int)$oldvalues['superuser']){
					$sql.="$key = \"$value\",";
					$updates++;
					output("Superuser values have changed.`n");
					if ($session['user']['acctid']==$userid) {
						$session['user']['superuser']=$value;
					}
					debuglog($session['user']['name']."`0 changed superuser to ".show_bitfield($value),$userid) . "`n";
					debug("superuser has changed to $value");
				}
			} elseif ($key=="name" && stripslashes($val)!=$oldvalues[$key]) {
				$updates++;
				$tmp = sanitize_colorname(getsetting("spaceinname", 0),
						stripslashes($val), true);
				$tmp = preg_replace("/[`][cHw]/", "", $tmp);
				$tmp = sanitize_html($tmp);
				if ($tmp != stripslashes($val)) {
					output("`\$Illegal characters removed from player name!`0`n");
				}
				if (soap($tmp) != ($tmp)) {
					output("`^The new name doesn't pass the bad word filter!`0");
				}

				$newname = change_player_name($tmp, $oldvalues);
				$sql.="$key = \"".addslashes($newname)."\",";
				output("Changed player name to %s`0`n", $newname);
				debuglog($session['user']['name'] . "`0 changed player name to $newname`0", $userid);
				$oldvalues['name']=$newname;
				if ($session['user']['acctid']==$userid) {
					$session['user']['name'] = $newname;
				}
			} elseif ($key=="title" && stripslashes($val)!=$oldvalues[$key]) {
				$updates++;
				$tmp = sanitize_colorname(true, stripslashes($val), true);
				$tmp = preg_replace("/[`][cHw]/", "", $tmp);
				$tmp = sanitize_html($tmp);
				if ($tmp != stripslashes($val)) {
					output("`\$Illegal characters removed from player title!`0`n");
				}
				if (soap($tmp) != ($tmp)) {
					output("`^The new title doesn't pass the bad word filter!`0");
				}

				$newname = change_player_title($tmp, $oldvalues);
				$sql.="$key = \"$val\",";
				output("Changed player title from %s`0 to %s`0`n", $oldvalues['title'], $tmp);
				$oldvalues[$key]=$tmp;
				if ($newname != $oldvalues['name']) {
					$sql.="name = \"".addslashes($newname)."\",";
					output("Changed player name to %s`0 due to changed dragonkill title`n", $newname);
					debuglog($session['user']['name'] . "`0 changed player name to $newname`0 due to changed dragonkill title", $userid);
					$oldvalues['name']=$newname;
					if ($session['user']['acctid']==$userid) {
						$session['user']['name'] = $newname;
					}
				}
				if ($session['user']['acctid']==$userid) {
					$session['user']['title'] = $tmp;
				}
			} elseif ($key=="ctitle" && stripslashes($val)!=$oldvalues[$key]) {
				$updates++;
				$tmp = sanitize_colorname(true, stripslashes($val), true);
				$tmp = preg_replace("/[`][cHw]/", "", $tmp);
				$tmp = sanitize_html($tmp);
				if ($tmp != stripslashes($val)) {
					output("`\$Illegal characters removed from custom title!`0`n");
				}
				if (soap($tmp) != ($tmp)) {
					output("`^The new custom title doesn't pass the bad word filter!`0");
				}
				$newname = change_player_ctitle($tmp, $oldvalues);
				$sql.="$key = \"$val\",";
				output("Changed player ctitle from %s`0 to %s`0`n", $oldvalues['ctitle'], $tmp);
				$oldvalues[$key]=$tmp;
				if ($newname != $oldvalues['name']) {
					$sql.="name = \"".addslashes($newname)."\",";
					output("Changed player name to %s`0 due to changed custom title`n", $newname);
					debuglog($session['user']['name'] . "`0 changed player name to $newname`0 due to changed custom title", $userid);
					$oldvalues['name']=$newname;
					if ($session['user']['acctid']==$userid) {
						$session['user']['name'] = $newname;
					}
				}
				if ($session['user']['acctid']==$userid) {
					$session['user']['ctitle'] = $tmp;
				}
			}elseif ($key=="oldvalues"){
				//donothing.
			}elseif ($oldvalues[$key]!=stripslashes($val)){
				$sql.="$key = \"$val\",";
				$updates++;
				output("%s has changed to %s.`n", $key, stripslashes($val));
				debuglog($session['user']['name']."`0 changed $key to $val",$userid);
				if ($session['user']['acctid']==$userid) {
					$session['user'][$key]=stripslashes($val);
				}
			}
		}
	}

	$sql=substr($sql,0,strlen($sql)-1);
	$sql = "UPDATE " . db_prefix("accounts") . " SET " . $sql . " WHERE acctid=\"$userid\"";

	$petition = httpget("returnpetition");
	if ($petition!="")
		addnav("","viewpetition.php?op=view&id=$petition");
	addnav("","user.php");

	if ($updates>0){
		db_query($sql);
		debug("Updated $updates fields in the user record with:\n$sql");
		output("%s fields in the user's record were updated.", $updates);
	}else{
		output("No fields were changed in the user's record.");
	}
	$op = "edit";
	httpset($op, "edit");
}

if ($op=="edit"){
	$result = db_query("SELECT * FROM " . db_prefix("accounts") . " WHERE acctid='$userid'");
	$row = db_fetch_assoc($result);
	$petition=httpget("returnpetition");
	if ($petition != "")
		$returnpetition = "&returnpetition=$petition";

	if ($petition !=""){
		addnav("Navigation");
		addnav("Return to the petition","viewpetition.php?op=view&id=$petition");
	}

	addnav("Operations");
	addnav("View last page hit","user.php?op=lasthit&userid=$userid",false,true);
	addnav("Display debug log","user.php?op=debuglog&userid=$userid$returnpetition");
	addnav("View user bio","bio.php?char=".rawurlencode($row['login'])."&ret=".urlencode($_SERVER['REQUEST_URI']));
	if ($session['user']['superuser'] & SU_EDIT_DONATIONS) {
		addnav("Add donation points","donators.php?op=add1&name=".rawurlencode($row['login'])."&ret=".urlencode($_SERVER['REQUEST_URI']));
	}

	addnav("","user.php?op=edit&userid=$userid$returnpetition");
	addnav("Bans");
	addnav("Set up ban","user.php?op=setupban&userid={$row['acctid']}");
	if (httpget("subop")==""){
		rawoutput("<form action='user.php?op=special&userid=$userid$returnpetition' method='POST'>");
		addnav("","user.php?op=special&userid=$userid$returnpetition");
		$grant = translate_inline("Grant New Day");
		rawoutput("<input type='submit' class='button' name='newday' value='$grant'>");
		$fix = translate_inline("Fix Broken Navs");
		rawoutput("<input type='submit' class='button' name='fixnavs' value='$fix'>");
		$mark = translate_inline("Mark Email As Valid");
		rawoutput("<input type='submit' class='button' name='clearvalidation' value='$mark'>");
		rawoutput("</form>");

		//Show a user's usertable
		rawoutput("<form action='user.php?op=save&userid=$userid$returnpetition' method='POST'>");
		addnav("","user.php?op=save&userid=$userid$returnpetition");
		$save = translate_inline("Save");
		rawoutput("<input type='submit' class='button' value='$save'>");
		if ($row['loggedin']==1 && $row['laston']>date("Y-m-d H:i:s",strtotime("-".getsetting("LOGINTIMEOUT",900)." seconds"))){
			output_notl("`\$");
			rawoutput("<span style='font-size: 20px'>");
			output("`\$Warning:`0");
			rawoutput("</span>");
			output("`\$This user is probably logged in at the moment!`0");
		}
		// Okay, munge the display name down to just the players name sans
		// title
		$row['name'] = get_player_basename($row);
		$info = showform($userinfo,$row);
		rawoutput("<input type='hidden' value=\"".htmlentities(serialize($info), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" name='oldvalues'>");
		rawoutput("</form>");

		output("`n`nLast Page Viewed:`n");
		rawoutput("<iframe src='user.php?op=lasthit&userid=$userid' width='100%' height='400'>");
		output("You need iframes to view the user's last hit here.");
		output("Use the link in the nav instead.");
		rawoutput("</iframe>");
	}elseif(httpget("subop")=="module"){
		//Show a user's prefs for a given module.
		addnav("Operations");
		addnav("Edit user","user.php?op=edit&userid=$userid$returnpetition");
		$module = httpget('module');
		$info = get_module_info($module);
		if (count($info['prefs']) > 0) {
			$data = array();
			$msettings = array();
			while (list($key,$val)=each($info['prefs'])){
				// Handle vals which are arrays.
				if (is_array($val)) {
					$v = $val[0];
					$x = explode("|", $v);
					$val[0] = $x[0];
					$x[0] = $val;
				} else {
					$x = explode("|",$val);
				}
				$msettings[$key] = $x[0];
				// Set up the defaults as well.
				if (isset($x[1])) $data[$key] = $x[1];
			}
			$sql = "SELECT * FROM " . db_prefix("module_userprefs") ." WHERE modulename='$module' AND userid='$userid'";
			$result = db_query($sql);
			while ($row = db_fetch_assoc($result)){
				$data[$row['setting']] = $row['value'];
			}
			rawoutput("<form action='user.php?op=savemodule&module=$module&userid=$userid$returnpetition' method='POST'>");
			addnav("","user.php?op=savemodule&module=$module&userid=$userid$returnpetition");
			tlschema("module-$module");
			showform($msettings,$data);
			tlschema();
			rawoutput("</form>");
		}else{
			output("The $module module doesn't appear to define any user preferences.");
		}
	}

	module_editor_navs('prefs', "user.php?op=edit&subop=module&userid=$userid$returnpetition&module=");

	addnav("","user.php?op=lasthit&userid=$userid");
}elseif($op=="setupban"){
	$sql = "SELECT name,lastip,uniqueid FROM " . db_prefix("accounts") . " WHERE acctid=\"$userid\"";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	if ($row['name']!="")
		output("Setting up ban information based on `\$%s`0", $row['name']);
	rawoutput("<form action='user.php?op=saveban' method='POST'>");
	output("Set up a new ban by IP or by ID (recommended IP, though if you have several different users behind a NAT, you can try ID which is easily defeated)`n");
	rawoutput("<input type='radio' value='ip' id='ipradio' name='type' checked>");
	output("IP: ");
	rawoutput("<input name='ip' id='ip' value=\"".HTMLEntities($row['lastip'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">");
	output_notl("`n");
	rawoutput("<input type='radio' value='id' name='type'>");
	output("ID: ");
	rawoutput("<input name='id' value=\"".HTMLEntities($row['uniqueid'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">");
	output("`nDuration: ");
	rawoutput("<input name='duration' id='duration' size='3' value='14'>");
	output("Days (0 for permanent)`n");
	$reason = httpget("reason");
	if ($reason == "")
		$reason=translate_inline("Don't mess with me.");
	output("Reason for the ban: ");
	rawoutput("<input name='reason' size=50 value=\"$reason\">");
	output_notl("`n");
	$pban = translate_inline("Post ban");
	$conf = translate_inline("Are you sure you wish to issue a permanent ban?");
	rawoutput("<input type='submit' class='button' value='$pban' onClick='if (document.getElementById(\"duration\").value==0) {return confirm(\"$conf\");} else {return true;}'>");
	rawoutput("</form>");
	output("For an IP ban, enter the beginning part of the IP you wish to ban if you wish to ban a range, or simply a full IP to ban a single IP`n`n");
	addnav("","user.php?op=saveban");
	if ($row['name']!=""){
		$id = $row['uniqueid'];
		$ip = $row['lastip'];
		$name = $row['name'];
		output("`0To help locate similar users to `@%s`0, here are some other users who are close:`n", $name);
		output("`bSame ID (%s):`b`n", $id);
		$sql = "SELECT name, lastip, uniqueid, laston, gentimecount FROM " . db_prefix("accounts") . " WHERE uniqueid='".addslashes($id)."' ORDER BY lastip";
		$result = db_query($sql);
		while ($row = db_fetch_assoc($result)){
			output("`0• (%s) `%%s`0 - %s hits, last: %s`n", $row['lastip'],
					$row['name'], $row['gentimecount'],
					reltime(strtotime($row['laston'])));
		}
		output_notl("`n");

		$oip = "";
		$dots = 0;
		output("`bSimilar IP's`b`n");
		for ($x=strlen($ip); $x>0; $x--){
			if ($dots>1) break;
			$thisip = substr($ip,0,$x);
			$sql = "SELECT name, lastip, uniqueid, laston, gentimecount FROM " . db_prefix("accounts") . " WHERE lastip LIKE '$thisip%' AND NOT (lastip LIKE '$oip') ORDER BY uniqueid";
			//output("$sql`n");
			$result = db_query($sql);
			if (db_num_rows($result)>0){
				output("• IP Filter: %s ", $thisip);
				rawoutput("<a href='#' onClick=\"document.getElementById('ip').value='$thisip'; document.getElementById('ipradio').checked = true; return false\">");
				output("Use this filter");
				rawoutput("</a>");
				output_notl("`n");
				while ($row=db_fetch_assoc($result)){
					output("&nbsp;&nbsp;",true);
					output("• (%s) [%s] `%%s`0 - %s hits, last: %s`n",
							$row['lastip'], $row['uniqueid'], $row['name'],
							$row['gentimecount'],
							reltime(strtotime($row['laston'])));
				}
				output_notl("`n");
			}
			if (substr($ip,$x-1,1)==".") {
				$x--;
				$dots++;
			}
			$oip = $thisip."%";
		}
	}
}elseif ($op=="del"){
	$sql = "SELECT name from " . db_prefix("accounts") . " WHERE acctid='$userid'";
	$res = db_query($sql);
	require_once("lib/charcleanup.php");
	char_cleanup($userid, CHAR_DELETE_MANUAL);
	while ($row = db_fetch_assoc($res)) {
		addnews("`#%s was unmade by the gods.", $row['name'], true);
		debuglog("deleted user" . $row['name'] . "'0");
	}
	$sql = "DELETE FROM " . db_prefix("accounts") . " WHERE acctid='$userid'";
	db_query($sql);
	output( db_affected_rows()." user deleted.");
}elseif($op=="saveban"){
	$sql = "INSERT INTO " . db_prefix("bans") . " (banner,";
	$type = httppost("type");
	if ($type=="ip"){
		$sql.="ipfilter";
	}else{
		$sql.="uniqueid";
	}
	$sql.=",banexpire,banreason) VALUES ('" . addslashes($session['user']['name']) . "',";
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
		if (substr($_SERVER['REMOTE_ADDR'],0,strlen(httppost("ip"))) ==
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
		db_query($sql);
		output("%s ban rows entered.`n`n", db_affected_rows());
		output_notl("%s", db_error(LINK));
		debuglog("entered a ban: " .  ($type=="ip"?  "IP: ".httppost("ip"): "ID: ".httppost("id")) . " Ends after: $duration  Reason: \"" .  httppost("reason")."\"");
	}
}elseif($op=="delban"){
	$sql = "DELETE FROM " . db_prefix("bans") . " WHERE ipfilter = '".httpget("ipfilter"). "' AND uniqueid = '".httpget("uniqueid")."'";
	db_query($sql);
	redirect("user.php?op=removeban");
}elseif($op=="removeban"){
	$subop = httpget("subop");
	$none = translate_inline('NONE');
	if ($subop=="xml"){
		header("Content-Type: text/xml");
		$sql = "SELECT DISTINCT " . db_prefix("accounts") . ".name FROM " . db_prefix("bans") . ", " . db_prefix("accounts") . " WHERE (ipfilter='".addslashes(httpget("ip"))."' AND " .
			db_prefix("bans") . ".uniqueid='" .
			addslashes(httpget("id"))."') AND ((substring(" .
			db_prefix("accounts") . ".lastip,1,length(ipfilter))=ipfilter " .
			"AND ipfilter<>'') OR (" .  db_prefix("bans") . ".uniqueid=" .
			db_prefix("accounts") . ".uniqueid AND " .
			db_prefix("bans") . ".uniqueid<>''))";
		$r = db_query($sql);
		echo "<xml>";
		$number=db_num_rows($r);
		for ($x=0;$x<$number;$x++){
			$ro = db_fetch_assoc($r);
			echo "<name name=\"";
			echo urlencode(appoencode("`0{$ro['name']}"));
			echo "\"/>";
		}
		if (db_num_rows($r)==0)
			echo "<name name=\"$none\"/>";
		echo "</xml>";
		exit();
	}

	db_query("DELETE FROM " . db_prefix("bans") . " WHERE banexpire < \"".date("Y-m-d")."\" AND banexpire>'0000-00-00'");
	$duration =  httpget("duration");
	if ($duration=="") {
		$since = " WHERE banexpire <= '".date("Y-m-d H:i:s",strtotime("+2 weeks"))."' AND banexpire > '0000-00-00'";
			output("`bShowing bans that will expire within 2 weeks.`b`n`n");
	}else{
		if ($duration=="forever") {
			$since="";
			output("`bShowing all bans`b`n`n");
		}else{
			$since = " WHERE banexpire <= '".date("Y-m-d H:i:s",strtotime("+".$duration))."' AND banexpire > '0000-00-00'";
			output("`bShowing bans that will expire within %s.`b`n`n",$duration);
		}
	}
	addnav("Will Expire Within");
	addnav("1 week","user.php?op=removeban&duration=1+week");
	addnav("2 weeks","user.php?op=removeban&duration=2+weeks");
	addnav("3 weeks","user.php?op=removeban&duration=3+weeks");
	addnav("4 weeks","user.php?op=removeban&duration=4+weeks");
	addnav("2 months","user.php?op=removeban&duration=2+months");
	addnav("3 months","user.php?op=removeban&duration=3+months");
	addnav("4 months","user.php?op=removeban&duration=4+months");
	addnav("5 months","user.php?op=removeban&duration=5+months");
	addnav("6 months","user.php?op=removeban&duration=6+months");
	addnav("1 year","user.php?op=removeban&duration=1+year");
	addnav("2 years","user.php?op=removeban&duration=2+years");
	addnav("4 years","user.php?op=removeban&duration=4+years");
	addnav("Forever","user.php?op=removeban&duration=forever");
	$sql = "SELECT * FROM " . db_prefix("bans") . " $since ORDER BY banexpire";
	$result = db_query($sql);
	rawoutput("<script language='JavaScript'>
	function getUserInfo(ip,id,divid){
		var filename='user.php?op=removeban&subop=xml&ip='+ip+'&id='+id;
		//set up the DOM object
		var xmldom;
		if (document.implementation &&
				document.implementation.createDocument){
			//Mozilla style browsers
			xmldom = document.implementation.createDocument('', '', null);
		} else if (window.ActiveXObject) {
			//IE style browsers
			xmldom = new ActiveXObject('Microsoft.XMLDOM');
		}

		xmldom.async=false;
		xmldom.load(filename);
		var output='';
		for (var x=0; x<xmldom.documentElement.childNodes.length; x++){
			output = output + unescape(xmldom.documentElement.childNodes[x].getAttribute('name').replace(/\\+/g,' ')) +'<br>';
		}
		document.getElementById('user'+divid).innerHTML=output;
	}
	</script>
	");
	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
	$ops = translate_inline("Ops");
	$bauth = translate_inline("Ban Author");
	$ipd = translate_inline("IP/ID");
	$dur = translate_inline("Duration");
	$mssg = translate_inline("Message");
	$aff = translate_inline("Affects");
	$l = translate_inline("Last");

	rawoutput("<tr class='trhead'><td>$ops</td><td>$bauth</td><td>$ipd</td><td>$dur</td><td>$mssg</td><td>$aff</td><td>$l</td></tr>");
	$number1=db_num_rows($result);
	for ($i=0;$i<$number1;$i++){
		$row = db_fetch_assoc($result);
		$liftban = translate_inline("Lift&nbsp;ban");
		$showuser = translate_inline("Click&nbsp;to&nbsp;show&nbsp;users");
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
		rawoutput("<td><a href='user.php?op=delban&ipfilter=".URLEncode($row['ipfilter'])."&uniqueid=".URLEncode($row['uniqueid'])."'>");
		output_notl("%s", $liftban, true);
		rawoutput("</a>");
		addnav("","user.php?op=delban&ipfilter=".URLEncode($row['ipfilter'])."&uniqueid=".URLEncode($row['uniqueid']));
		rawoutput("</td><td>");
		output_notl("`&%s`0", $row['banner']);
		rawoutput("</td><td>");
		output_notl("%s", $row['ipfilter']);
		output_notl("%s", $row['uniqueid']);
		rawoutput("</td><td>");
			// "43200" used so will basically round to nearest day rather than floor number of days
		$expire= sprintf_translate("%s days",
				round((strtotime($row['banexpire'])+43200-strtotime("now"))/86400,0));
		if (substr($expire,0,2)=="1 ")
			$expire= translate_inline("1 day");
		if (date("Y-m-d",strtotime($row['banexpire'])) == date("Y-m-d"))
			$expire=translate_inline("Today");
		if (date("Y-m-d",strtotime($row['banexpire'])) ==
				date("Y-m-d",strtotime("1 day")))
			$expire=translate_inline("Tomorrow");
		if ($row['banexpire']=="0000-00-00")
			$expire=translate_inline("Never");
		output_notl("%s", $expire);
		rawoutput("</td><td>");
		output_notl("%s", $row['banreason']);
		rawoutput("</td><td>");
		$file = "user.php?op=removeban&subop=xml&ip={$row['ipfilter']}&id={$row['uniqueid']}";
		rawoutput("<div id='user$i'><a href='$file' target='_blank' onClick=\"getUserInfo('{$row['ipfilter']}','{$row['uniqueid']}',$i); return false;\">");
		output_notl("%s", $showuser, true);
		rawoutput("</a></div>");
		addnav("",$file);
		rawoutput("</td><td>");
		output_notl("%s", relativedate($row['lasthit']));
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
}elseif ($op=="debuglog"){

	if ($petition !=""){
		addnav("Navigation");
		addnav("Return to the petition","viewpetition.php?op=view&id=$petition");
	}

	$sql = "SELECT count(id) AS c FROM " . db_prefix("debuglog") . " WHERE actor=$userid or target=$userid";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$max = $row['c'];

	$start = (int)httpget('start');
	//This statement is not capable of using any indices in MySQL, as MySQL will
	//only use one index per table scan, and an OR on two different columns
	//means that no one index will get all rows, thus a full table scan is always
	//necessary.
	$sql = "SELECT
		".db_prefix("debuglog").".*,
		a1.name as actorname,
		a2.name as targetname
	FROM " . db_prefix("debuglog") . "
	LEFT JOIN " . db_prefix("accounts") . " AS a1
	ON
		a1.acctid=" . db_prefix("debuglog").".actor
	LEFT JOIN " . db_prefix("accounts") . " AS a2
	ON
		a2.acctid=" . db_prefix("debuglog"). ".target
	WHERE
		" . db_prefix("debuglog") . ".actor=$userid
	OR	" . db_prefix("debuglog") . ".target=$userid
	ORDER by
		" . db_prefix("debuglog") . ".date DESC,
		" . db_prefix("debuglog") . ".id ASC
	LIMIT $start,500";
	$next = $start+500;
	$prev = $start-500;
	addnav("Operations");
	addnav("Edit user info","user.php?op=edit&userid=$userid$returnpetition");
	addnav("Refresh", "user.php?op=debuglog&userid=$userid&start=$start$returnpetition");
	addnav("Debug Log");
	if ($next < $max) {
		addnav("Next page","user.php?op=debuglog&userid=$userid&start=$next$returnpetition");
	}
	if ($start > 0) {
		addnav("Previous page",
				"user.php?op=debuglog&userid=$userid&start=$prev$returnpetition");
	}
	$result = db_query($sql);
	$odate = "";
	$number2=db_num_rows($result);
	for ($i=0; $i<$number2; $i++) {
		$row = db_fetch_assoc($result);
		$dom = date("D, M d",strtotime($row['date']));
		if ($odate != $dom){
			output_notl("`n`b`@%s`0`b`n", $dom);
			$odate = $dom;
		}
		$time = date("H:i:s", strtotime($row['date']))." (".
			reltime(strtotime($row['date'])).")";
		output_notl("`#%s (%s) `^%s - `&%s`7 %s`0", $row['field'],
				$row['value'], $time, $row['actorname'], $row['message']);

		if ($row['target'])
			output(" \-- Recipient = `\$%s`0", $row['targetname']);
		output_notl("`n");
	}
}elseif ($op==""){
	if ($display == 1){
		$q = "";
		if ($query) {
			$q = "&q=$query";
		}
		$ops=translate_inline("Ops");
		$acid =translate_inline("AcctID");
		$login =translate_inline("Login");
		$nm =translate_inline("Name");
		$lev =translate_inline("Level");
		$lon =translate_inline("Last On");
		$hits =translate_inline("Hits");
		$lip =translate_inline("Last IP");
		$lid =translate_inline("Last ID");
		$email =translate_inline("Email");
		$ed = translate_inline("Edit");
		$del = translate_inline("Del");
		$conf = translate_inline("Are you sure you wish to delete this user?");
		$ban = translate_inline("Ban");
		$log = translate_inline("Log");

		rawoutput("<table>");
		rawoutput("<tr class='trhead'><td>$ops</td><td><a href='user.php?sort=acctid$q'>$acid</a></td><td><a href='user.php?sort=login$q'>$login</a></td><td><a href='user.php?sort=name$q'>$nm</a></td><td><a href='user.php?sort=level$q'>$lev</a></td><td><a href='user.php?sort=laston$q'>$lon</a></td><td><a href='user.php?sort=gentimecount$q'>$hits</a></td><td><a href='user.php?sort=lastip$q'>$lip</a></td><td><a href='user.php?sort=uniqueid$q'>$lid</a></td><td><a href='user.php?sort=emailaddress$q'>$email</a></td></tr>");
		addnav("","user.php?sort=acctid$q");
		addnav("","user.php?sort=login$q");
		addnav("","user.php?sort=name$q");
		addnav("","user.php?sort=level$q");
		addnav("","user.php?sort=laston$q");
		addnav("","user.php?sort=gentimecount$q");
		addnav("","user.php?sort=lastip$q");
		addnav("","user.php?sort=uniqueid$q");
		$rn=0;
		$oorder = "";
		$number3=db_num_rows($searchresult);
		for ($i=0;$i<$number3;$i++){
			$row=db_fetch_assoc($searchresult);
			$laston = relativedate($row['laston']);
			$loggedin =
				(date("U") - strtotime($row['laston']) <
				 getsetting("LOGINTIMEOUT",900) && $row['loggedin']);
			if ($loggedin)
				$laston=translate_inline("`#Online`0");
			$row['laston']=$laston;
			if ($row[$order]!=$oorder) $rn++;
			$oorder = $row[$order];
			rawoutput("<tr class='".($rn%2?"trlight":"trdark")."'>");
			rawoutput("<td nowrap>");
			rawoutput("[ <a href='user.php?op=edit&userid={$row['acctid']}$m'>$ed</a> | <a href='user.php?op=del&userid={$row['acctid']}' onClick=\"return confirm('$conf');\">$del</a> | <a href='user.php?op=setupban&userid={$row['acctid']}'>$ban</a> | <a href='user.php?op=debuglog&userid={$row['acctid']}'>$log</a> ]");
			addnav("","user.php?op=edit&userid={$row['acctid']}$m");
			addnav("","user.php?op=del&userid={$row['acctid']}");
			addnav("","user.php?op=setupban&userid={$row['acctid']}");
			addnav("","user.php?op=debuglog&userid={$row['acctid']}");
			rawoutput("</td><td>");
			output_notl("%s", $row['acctid']);
			rawoutput("</td><td>");
			output_notl("%s", $row['login']);
			rawoutput("</td><td>");
			output_notl("`&%s`0", $row['name']);
			rawoutput("</td><td>");
			output_notl("`^%s`0", $row['level']);
			rawoutput("</td><td>");
			output_notl("%s", $row['laston']);
			rawoutput("</td><td>");
			output_notl("%s", $row['gentimecount']);
			rawoutput("</td><td>");
			output_notl("%s", $row['lastip']);
			rawoutput("</td><td>");
			output_notl("%s", $row['uniqueid']);
			rawoutput("</td><td>");
			output_notl("%s", $row['emailaddress']);
			rawoutput("</td></tr>");
			$gentimecount+=$row['gentimecount'];
			$gentime+=$row['gentime'];
		}
		rawoutput("</table>");
		output("Total hits: %s`n", $gentimecount);
		output("Total CPU time: %s seconds`n", round($gentime,3));
		output("Average page gen time is %s seconds`n", round($gentime/max($gentimecount,1),4));
	}
}
page_footer();

function show_bitfield($val){
	$out = "";
	$v = 1;
	for ($i=0; $i < 32; $i++){
		$out.= (int)$val & (int)$v?"1":"0";
		$v *= 2;
	}
	return($out);
}
?>

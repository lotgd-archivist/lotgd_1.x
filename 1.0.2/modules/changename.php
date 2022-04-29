<?php
// translator ready
// addnews ready
// mail ready

require_once("common.php");
require_once("lib/villagenav.php");
require_once("lib/http.php");
require_once ("lib/names.php");
require_once("lib/systemmail.php");

// check_su_access(SU_EDIT_COMMENTS);

function changename_getmoduleinfo(){
	$info = array(
		"name"=>"Name Change module",
		"version"=>"1.0",
		"author"=>"Shannon Brown",
		"category"=>"Administrative",
		"download"=>"core_module",
		"settings"=>array(
			"Name Change Settings,title",
			"basename"=>"What should we use as the basis for the newname?|NewName",
			"addcount"=>"Number appended to last new name,int|0",
		),
		"prefs"=>array(
			"Name Change User Preferences,title",
			"lastplayer"=>"Player id this moderator last examined,int|",
			"curname"=>"Current name of player being modified.|",
		)
	);
	return $info;
}

function changename_install(){
	module_addhook("header-moderate");
	module_addhook("bioend");
	return true;
}

function changename_uninstall(){
	return true;
}

function changename_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "header-moderate":
		if ($session['user']['superuser']  & SU_EDIT_COMMENTS) {
			tlschema('nav');
			addnav("Other");
			tlschema();
			addnav("Check Most Recent Names","runmodule.php?module=changename");
		}
		break;
	case "bioend":
		if ($session['user']['superuser'] & SU_EDIT_COMMENTS) {
			set_module_pref("lastplayer", $args['acctid']);
			set_module_pref("frombio", 1);
			set_module_pref("curname", $args['name']);
			addnav("Rename player");
			addnav(" ?Rename player `\$BE CAREFUL!","runmodule.php?module=changename&op=rename");
		}
	}
	return $args;
}

function changename_run(){
	global $session;
	check_su_access(SU_EDIT_COMMENTS);
	$op = httpget("op");

	page_header("Name Change");
	output("`&`c`bName Change`b`c");
	villagenav();
	addnav(",?Comment Moderation", "moderate.php");
	if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO){
		addnav("X?`bSuperuser Grotto`b","superuser.php");
	} 
	if ($op=="") {
		$lastplayer = getsetting("newestplayer", "");
		set_module_pref("lastplayer",$lastplayer);
		$sql = "select name from " . db_prefix("accounts") . " where acctid='$lastplayer'";
		$res = db_query($sql);
		if (db_num_rows($res)) {
			$row = db_fetch_assoc($res);
			$curname = $row['name'];
			set_module_pref("curname",$curname);
			output("`n`n`n`b`@Current player name:`0 %s `b",$curname);
			output("`n`n`b`\$WARNING: `^Ensure you are changing the correct name.");
			addnav("Rename player");
			addnav("Rename player `\$BE CAREFUL!","runmodule.php?module=changename&op=rename");
		} else {
			output("`n`n`n`b`\$No such player -- They were possibly deleted for some reason.`b");
		}
		addnav("Other players");
		addnav("Check previous player id","runmodule.php?module=changename&op=reverse");
	}elseif($op=="reverse"){
		$lastplayer = get_module_pref("lastplayer");
		debug("Lastplayer before loop: $lastplayer");
		while (1) {
			$lastplayer--;
			$sql = "select name,dragonkills from " . db_prefix("accounts") . " where acctid='$lastplayer'";
			$res = db_query($sql);
			if (db_num_rows($res) == 0) continue;
			
			$row = db_fetch_assoc($res);
			if($row['dragonkills'] != 0) continue;
			break;

		}
		debug("Lastplayer after loop: $lastplayer");
		$curname = $row['name'];
		set_module_pref("curname",$curname);
		set_module_pref("lastplayer",$lastplayer);
		debug("Lastplayer: $lastplayer");
		output("`n`n`n`b`@Current player name:`0 %s `b",$curname);
		output("`n`n`b`\$WARNING: `^Ensure you are changing the correct name.");
		addnav("Rename player");
		addnav("Rename player `\$BE CAREFUL!","runmodule.php?module=changename&op=rename");
		addnav("Other players");
		addnav("Check previous player id","runmodule.php?module=changename&op=reverse");
	}elseif ($op=="rename"){
		// We need to do the query again here to make sure that we have the
		// right things to pass down into the bottom code.
		$addcount=get_module_setting("addcount");
		$lastplayer=get_module_pref("lastplayer");
		$basename = get_module_setting("basename");
		$addcount++;
		set_module_setting("addcount",$addcount);
		$sql = "select name,title,ctitle,dragonkills from " . db_prefix("accounts") . " where acctid='$lastplayer'";
		$res = db_query($sql);
		$valid = 0;
		if (db_num_rows($res)) {
			$row = db_fetch_assoc($res);
			if ($row['name'] == get_module_pref("curname")) {
				$valid = 1;
				$newnamestring= $basename.$addcount;
				$new=change_player_name($newnamestring, $row);
				$sql = "update " . db_prefix("accounts") . " set name='$new' where acctid='$lastplayer'";
				db_query($sql);
				output("Player %s renamed to %s.",$row['name'],$new);
				debuglog("Player {$row['name']} renamed to $new by {$session['user']['name']}");
				systemmail($lastplayer,array("Change of Name"),array("`2Your name was found to be inappropriate for our family-friendly server, and has been changed to %s.`n`nPlease use the \"Petition for Help\" link to request a new, more appropriate name.`n`n`^Should you not request a new name within five days, your character may be deleted.", $new));
			}
		}
		if (!$valid) {
			output("`@Something has gone wrong.`n`n");
			output("It is possible that someone else has already changed this players name, or that the player was deleted.");
		}
		addnav("Check previous player id","runmodule.php?module=changename&op=reverse");
	}
	page_footer();
}

?>
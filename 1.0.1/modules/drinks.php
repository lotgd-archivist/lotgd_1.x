<?php
// translator ready
// addnews ready
// mail ready
require_once("lib/e_rand.php");
require_once("lib/showform.php");
require_once("lib/http.php");
require_once("lib/buffs.php");

/*
 * Date:	Mar 07, 2004
 * Version:	1.0
 * Author:	JT Traub
 * Email:	jtraub@dragoncat.net
 * Purpose:	Provide basic drinks and drunkeness handling.
 *			Subsumes some of the functionality from the drinks module by
 *			John J. Collins (collinsj@yahoo.com)
 *
 * Date:	Mar 09, 2004
 * Version:	1.1
 * Purpose:	Remove the 'activate' field
 */

function drinks_getmoduleinfo(){
	$info = array(
		"name"=>"Exotic Drinks",
		"author"=>"John J. Collins<br>Heavily modified by JT Traub",
		"category"=>"Inn",
		"download"=>"core_module",
		"settings"=>array(
			"Drink Module Settings,title",
			"hardlimit"=>"How many hard drinks can a user buy in a day?,int|3",
			"maxdrunk"=>"How drunk before Cedrik won't serve you?,range,0,100,1|66"
		),
		"prefs"=>array(
			"Drink Module User Preferences,title",
			"drunkeness"=>"Drunkeness,range,0,100,1|0",
			"harddrinks"=>"How many hard drinks has the user bought today?,int|0",
			"canedit"=>"Has access to the drinks editor,bool|0",
			"noslur"=>"Don't slur speach when drunk,bool|0",
		),
		"version"=>"1.1"
	);
	return $info;
}

function drinks_install(){
	if (db_table_exists(db_prefix("drinks"))) {
		debug("Drinks table already exists");
	}else{
		debug("Creating drinks table");
		$sqls = array(
			"CREATE TABLE " . db_prefix("drinks") . " (
				drinkid smallint(6) NOT NULL auto_increment,
				name varchar(25) NOT NULL default '',
				active tinyint(4) NOT NULL default '0',
				costperlevel int(11) NOT NULL default '0',
				hpchance tinyint(4) NOT NULL default '0',
				turnchance tinyint(4) NOT NULL default '0',
				alwayshp tinyint(4) NOT NULL default '0',
				alwaysturn tinyint(4) NOT NULL default '0',
				drunkeness tinyint(4) NOT NULL default '0',
				harddrink tinyint(4) NOT NULL default '0',
				hpmin int(11) NOT NULL default '0',
				hpmax int(11) NOT NULL default '0',
				hppercent int(11) NOT NULL default '0',
				turnmin int(11) NOT NULL default '0',
				turnmax int(11) NOT NULL default '0',
				remarks text NOT NULL default '',
				buffname varchar(50) NOT NULL default '',
				buffrounds tinyint(4) NOT NULL default '0',
				buffroundmsg varchar(75) NOT NULL default '',
				buffwearoff varchar(75) NOT NULL default '',
				buffatkmod text NOT NULL,
				buffdefmod text NOT NULL,
				buffdmgmod text  NOT NULL,
				buffdmgshield text NOT NULL,
				buffeffectfailmsg varchar(255) NOT NULL default '',
				buffeffectnodmgmsg varchar(255) NOT NULL default '',
				buffeffectmsg varchar(255) NOT NULL default '',
				PRIMARY KEY  (drinkid)) TYPE=MyISAM",
			"INSERT INTO " . db_prefix("drinks") . " VALUES (0, 'Ale', 1, 10, 2, 1, 0, 0, 33, 0, 0, 0, 10, 1, 1, 'Cedrik pulls out a glass, and pours a foamy ale from a tapped barrel behind him.  He slides it down the bar, and you catch it with your warrior-like reflexes.`n`nTurning around, you take a big chug of the hearty draught, and give {lover} an ale-foam mustache smile.`n`n', '`#Buzz', 10, 'You\'ve got a nice buzz going.', 'Your buzz fades.', '1.25', '0', '0', '0', '', '', '')",
			"INSERT INTO " . db_prefix("drinks") . " VALUES (0, 'Habanero Martini', 1, 15, 0, 0, 1, 1, 50, 1, -5, 15, 0.0, -1, 1, 'Cedrik pulls out a bottle labeled with 3 X\'s and a chile pepper and pours a miniscule shot into your glass.  You toss it back and grimace as smoke floods out of your ears.', '`\$Hot Hands', 12, 'You feel like your hands are about to burn off.', 'Finally, your hands are no longer burning.', '1.1', '.9', '1.5', '0', '', '', '')",
			"INSERT INTO " . db_prefix("drinks") . " VALUES (0, 'Mule Daniels', 1, 25, 2, 3, 0, 0, 50, 1, -10, -1, 0.0, 1, 3, 'Cedrik drags a large pony-keg out from behind the bar and pours a slug into a cast iron cup which rattles as the thick liquid is poured into it.  You toss it back in a gulp and make a face like a mule kicked you hard in the gut.  From across the room, you hear {lover} laugh at you.', '`#Mulekick', 15, 'You hear a donkey braying in the distance', 'That donkey finally shuts up.', '0', '0', '1.3', '1.3', 'Your head rings as the donkey kicks you instead.', 'That mule would have kicked {badguy} to the moon, but it missed!', '{badguy} sees`$ {damage}`) stars as the mule kicks him over the moon.')"
		);
		while (list($key,$sql)=each($sqls)){
			db_query($sql);
		}
	}

	// See if we're migrating from an old version of the drinks code with a
	// buffactivate field
	$sql = "DESCRIBE ". db_prefix("drinks");
	$result = db_query($sql);
	while($row = db_fetch_assoc($result)) {
		if ($row['Field']=="buffactivate"){
			debug("Dropping buffactivate from the drinks table.");
			$sql = "ALTER TABLE " . db_prefix("drinks") . " DROP buffactivate";
			db_query($sql);
		} // end if
		if ($row['Field']=="hppercent" && $row['Type']=="float") {
			debug("Altering {$row['Field']} from float to int in the drinks table.");
			$sql = "UPDATE " . db_prefix("drinks") . " SET hppercent=hppercent*100";
			db_query($sql);
			$sql = "ALTER TABLE " . db_prefix("drinks") . " CHANGE {$row['Field']} {$row['Field']} int(11) NOT NULL DEFAULT 0";
			db_query($sql);
		}
		if (($row['Field']=="buffatkmod" || $row['Field']=="buffdefmod" ||
			 $row['Field']=="buffdmgmod" || $row['Field']=="buffdmgshield") &&
			($row['Type'] == "float")) {
			debug("Altering {$row['Field']} from float to text in the drinks table.");
			$sql = "ALTER TABLE " . db_prefix("drinks") . " CHANGE {$row['Field']} {$row['Field']} text NOT NULL";
			db_query($sql);
		}
	} // end while


	// Install the hooks.
	module_addhook("ale");
	module_addhook("newday");
	module_addhook("superuser");
	module_addhook("header-graveyard");
	module_addhook("commentary");
	module_addhook("soberup");
	module_addhook("dragonkill");
	return true;
}

function drinks_uninstall() {
	debug("Dropping table drinks");
	$sql = "DROP TABLE IF EXISTS " . db_prefix("drinks");
	db_query($sql);
	debug("Dropping objprefs related to drinks");
	$sql = "DELETE FROM " . db_prefix("module_objprefs") .
		" WHERE objtype='drinks'";
	db_query($sql);
	return true;
}

function drinks_dohook($hookname,$args) {
	global $session;

	switch($hookname) {
	case "dragonkill":
		set_module_pref("drunkeness",0);
		break;
	case "ale":
		$texts = drinks_gettexts();
		$drinktext = modulehook("drinks-text",$texts);
		
		$drunk = get_module_pref("drunkeness");
		$drunklist = array(
				-1=>"stone cold sober",
				0=>"quite sober",
				1=>"barely buzzed",
				2=>"pleasantly buzzed",
				3=>"almost drunk",
				4=>"barely drunk",
				5=>"solidly drunk",
				6=>"sloshed",
				7=>"hammered",
				8=>"really hammered",
				9=>"almost unconscious",
				10=>"about to pass out");
		$drunklist = translate_inline($drunklist);
		$drunk = round($drunk/10-.5, 0);
		if ($drunk > 10) $drunk = 10;
		$hard = "";
		if (get_module_pref('harddrinks')>=get_module_setting('hardlimit')) {
			tlschema($drinktexts['schemas']['toomany']);
			output_notl("`n`n");
			output($drinktexts['toomany']);
			output_notl("`n");
			$hard = "AND harddrink=0";
		}
		output("`n`n`7You now feel %s.`n`n", $drunklist[$drunk]);
		$sql = "SELECT * FROM " . db_prefix("drinks") . " WHERE active=1 $hard ORDER BY costperlevel";
		$result = db_query($sql);
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$row['allowdrink'] = 1;
			$row = modulehook("drinks-check", $row);
			if ($row['allowdrink']) {
				$drinkcost = $row['costperlevel']*$session['user']['level'];
				// No hotkeys on drinks.  Too easy for them to interfere
				// with and modify stock navs randomly.
				addnav(array(" ?%s  (`^%s`0 gold)", $row['name'], $drinkcost),
						"runmodule.php?module=drinks&act=buy&id={$row['drinkid']}");
			}
		}
		break;
	case "newday":
		set_module_pref("harddrinks", 0);
		$drunk = get_module_pref("drunkeness");
		if ($drunk > 66) {
			output("`n`&Coming off of a hangover, you `\$lose 1`& forest fight today.`n");
			$args['turnstoday'] .= ", Hangover: -1";
			$session['user']['turns']--;
			// Sanity check
			if ($session['user']['turns'] < 0) $session['user']['turns'] = 0;
		}
		set_module_pref("drunkeness",0);
		break;
	case "header-graveyard":
		set_module_pref("drunkeness",0);
		break;
	case "soberup":
		$soberval = $args['soberval'];
		$sobermsg = $args['sobermsg'];
		$drunk = get_module_pref("drunkeness");
		if ($drunk > 0) {
			$drunk = round($drunk * $soberval, 0);
			set_module_pref("drunkeness", $drunk);
			if ($sobermsg) {
				if ($args['schema']) tlschema($args['schema']);
				output($sobermsg);
				if ($args['schema']) tlschema();
			}
		}
		break;
	case "commentary":
		$drunk = get_module_pref("drunkeness");
		if ($drunk > 50) {
			$args['commenttalk'] = "drunkenly {$args['commenttalk']}";
		}
		$commentline = $args['commentline'];
		if (substr($commentline, 0, 1) != ":" &&
				substr($commentline, 0, 2) != "::" &&
				substr($commentline, 0, 3) != "/me" &&
				$drunk > 0) {
			$args['commentline'] = drinks_drunkenize($commentline, $drunk);
		}
		break;
	case "superuser":
		if (($session['user']['superuser'] & SU_EDIT_USERS) || get_module_pref("canedit")) {
			addnav("Module Configurations");
			// Stick the admin=true on so that when we call runmodule it'll
			// work to let us edit drinks even when the module is deactivated.
			addnav("Drinks Editor","runmodule.php?module=drinks&act=editor&admin=true");
		}
		break;
	}//end select
	return $args;
}//end function

function drinks_gettexts() {
	global $session;
	$iname = getsetting("innname", LOCATION_INN);
	$drinktext = array(
			"title"=>"$iname",
			"barkeep"=>"Cedrik",
			"return"=>"",
			"demand"=>"Pounding your fist on the bar, you demand another drink",
			"toodrunk"=>" but Cedrik continues to clean the glass he was working on.  \"`%You've had enough ".($session['user']['sex']?"lass":"lad").",`0\" he declares.",
			"toomany"=>"Cedrik eyes you critically. \"`%Ya've had enough of the hard stuff, my friend.  No more of that for you today.`0\"",
			"drinksubs"=>array(),
		);
	$schemas = array(
		'title'=>"module-drinks",
		'barkeep'=>"module-drinks",
		'return'=>"module-drinks",
		'demand'=>"module-drinks",
		'toodrunk'=>"module-drinks",
		'toomany'=>"module-drinks",
		'drinksubs'=>"module-drinks",
		);
	$drinktext['schemas'] = $schemas;
	return $drinktext;
}

function drinks_run(){
	global $session;
	$act = httpget('act');
	if ($act=="editor"){
		drinks_editor();
	}elseif ($act=="buy"){
		$texts = drinks_gettexts();
		$drinktext = modulehook("drinks-text",$texts);
	
		tlschema($drinktext['schemas']['title']);
		page_header($drinktext['title']);
		rawoutput("<span style='color: #9900FF'>");
		output_notl("`c`b");
		output($drinktext['title']);
		output_notl("`b`c");
		tlschema();
		$drunk = get_module_pref("drunkeness");
		$end = ".";
		if ($drunk > get_module_setting("maxdrunk"))
			$end = ",";
		tlschema($drinktext['schemas']['demand']);
		$drinktext['demand'] = translate_inline($drinktext['demand']);
		tlschema();
		output_notl("%s$end", $drinktext['demand']);
		$drunk = get_module_pref("drunkeness");
		if ($drunk > get_module_setting("maxdrunk")) {
			tlschema($drinktext['schemas']['toodrunk']);
			output($drinktext['toodrunk']);
			tlschema();
		} else {
			$sql = "SELECT * FROM " . db_prefix("drinks") . " WHERE drinkid='".httpget('id')."'";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$drinkcost = $session['user']['level'] * $row['costperlevel'];
			if ($session['user']['gold'] >= $drinkcost) {
				$drunk = get_module_pref("drunkeness");
				$drunk += $row['drunkeness'];
				set_module_pref("drunkeness", $drunk);
				$session['user']['gold'] -= $drinkcost;
				debuglog("spent $drinkcost on {$row['name']}");
				$remark = str_replace("{lover}",
						($session['user']['sex'] ? "Seth" : "Violet"),
						$row['remarks']);
				if (count($drinktext['drinksubs']) > 0) {
					$keys = array_keys($drinktext['drinksubs']);
					$vals = array_values($drinktext['drinksubs']);
					$remark = preg_replace($keys, $vals, $remark);
				}
				output($remark);
				output_notl("`n`n");
				if ($row['harddrink']) {
					$drinks = get_module_pref("harddrinks");
					set_module_pref("harddrinks", $drinks+1);
				}
				$givehp = 0;
				$giveturn = 0;
				if ($row['hpchance']>0 || $row['turnchance']>0) {
					$tot = $row['hpchance'] + $row['turnchance'];
					$c = e_rand(1, $tot);
					if ($c <= $row['hpchance'] && $row['hpchance']>0)
						$givehp = 1;
					else
						$giveturn = 1;
				}
				if ($row['alwayshp']) $givehp = 1;
				if ($row['alwaysturn'])  $giveturn = 1;
				if ($giveturn) {
					$turns = e_rand($row['turnmin'], $row['turnmax']);
					$oldturns = $session['user']['turns'];
					$session['user']['turns'] += $turns;
					// sanity check
					if ($session['user']['turns'] < 0)
						$session['user']['turns'] = 0;

					if ($oldturns < $session['user']['turns']) {
						output("`&You feel vigorous!`n");
					} else if ($oldturns > $session['user']['turns']) {
						output("`&You feel lethargic!`n");
					}
				}
				if ($givehp) {
					$oldhp = $session['user']['hitpoints'];

					// Check for percent increase first
					if ($row['hppercent'] != 0.0) {
						$hp = round($session['user']['maxhitpoints'] *
								($row['hppercent']/100), 0);
					} else {
						$hp = e_rand($row['hpmin'], $row['hpmax']);
					}
					$session['user']['hitpoints'] += $hp;
					// Sanity check
					if ($session['user']['hitpoints'] < 1)
						$session['user']['hitpoints'] = 1;

					if ($oldhp < $session['user']['hitpoints']) {
						output("`&You feel healthy!`n");
					} else if ($oldhp > $session['user']['hitpoints']) {
						output("`&You feel sick!`n");
					}
				}
				$buff = array();
				$buff['name'] = $row['buffname'];
				$buff['rounds'] = $row['buffrounds'];
				if ($row['buffwearoff'])
					$buff['wearoff'] = $row['buffwearoff'];
				if ($row['buffatkmod'])
					$buff['atkmod'] = $row['buffatkmod'];
				if ($row['buffdefmod'])
					$buff['defmod'] = $row['buffdefmod'];
				if ($row['buffdmgmod'])
					$buff['dmgmod'] = $row['buffdmgmod'];
				if ($row['buffdmgshield'])
					$buff['damageshield'] = $row['buffdmgshield'];
				if ($row['buffroundmsg'])
					$buff['roundmsg'] = $row['buffroundmsg'];
				if ($row['buffeffectmsg'])
					$buff['effectmsg'] = $row['buffeffectmsg'];
				if ($row['buffeffectnodmgmsg'])
					$buff['effectnodmgmsg'] = $row['buffeffectnodmgmsg'];
				if ($row['buffeffectfailmsg'])
					$buff['effectfailmsg'] = $row['buffeffectfailmsg'];
				$buff['schema'] = "module-drinks";
				apply_buff('buzz',$buff);
			} else {
				output("You don't have enough money.  How can you buy %s if you don't have any money!?!", $row['name']);
			}
		}
		rawoutput("</span>");
		if ($drinktext['return']>""){
			tlschema($drinktext['schemas']['return']);
			addnav($drinktext['return'],$drinktext['returnlink']);
			tlschema();
		}else{
			addnav("I?Return to the Inn","inn.php");
			addnav("Go back to talking to Cedrik","inn.php?op=bartender");
		}
		require_once("lib/villagenav.php");
		villagenav();
		page_footer();
	}
}

// Support functions
function drinks_editor(){
	global $mostrecentmodule;
	if (!get_module_pref("canedit")) check_su_access(SU_EDIT_USERS);

	page_header("Drink Editor");
	require_once("lib/superusernav.php");
	superusernav();
	addnav("Drink Editor");
	addnav("Add a drink","runmodule.php?module=drinks&act=editor&op=add&admin=true");
	$op = httpget('op');
	$drinkid = httpget('drinkid');
	$header = "";
	if ($op != "") {
		addnav("Drink Editor Main","runmodule.php?module=drinks&act=editor&admin=true");
		if ($op == 'add') {
			$header = translate_inline("Adding a new drink");
		} else if ($op == 'edit') {
			$header = translate_inline("Editing a drink");
		}
	} else {
		$header = translate_inline("Current drinks");
	}
	output_notl("`&<h3>$header`0</h3>", true);
	$drinksarray=array(
		"Drink,title",
		"drinkid"=>"Drink ID,hidden",
		"name"=>"Drink Name",
		"costperlevel"=>"Cost per level,int",
		"hpchance"=>"Chance of modifying HP (see below),range,0,10,1",
		"turnchance"=>"Chance of modifying turns (see below),range,0,10,1",
		"alwayshp"=>"Always modify hitpoints,bool",
		"alwaysturn"=>"Always modify turns,bool",
		"drunkeness"=>"Drunkeness,range,1,100,1",
		"harddrink"=>"Is drink hard alchohol?,bool",
		"hpmin"=>"Min HP to add (see below),range,-20,20,1",
		"hpmax"=>"Max HP to add (see below),range,-20,20,1",
		"hppercent"=>"Modify HP by some percent (see below),range,-25,25,5",
		"turnmin"=>"Min turns to add (see below),range,-5,5,1",
		"turnmax"=>"Max turns to add (see below),range,-5,5,1",
		"remarks"=>"Remarks",
		"buffname"=>"Name of the buff",
		"buffrounds"=>"Rounds buff lasts,range,1,20,1",
		"buffroundmsg"=>"Message each round of buff",
		"buffwearoff"=>"Message when buff wears off",
		"buffatkmod"=>"Attack modifier of buff",
		"buffdefmod"=>"Defense modifier of buff",
		"buffdmgmod"=>"Damage modifier of buff",
		"buffdmgshield"=>"Damage shield modifier of buff",
		"buffeffectfailmsg"=>"Effect failure message (see below)",
		"buffeffectnodmgmsg"=>"No damage message (see below)",
		"buffeffectmsg"=>"Effect message (see below)",
	);
	if($op=="del"){
		$sql = "DELETE FROM " . db_prefix("drinks") . " WHERE drinkid='$drinkid'";
		module_delete_objprefs('drinks', $drinkid);
		db_query($sql);
		$op = "";
		httpset('op', "");
	}
	if($op=="save"){
		$subop = httpget("subop");
		if ($subop=="") {
			$drinkid = httppost("drinkid");
			list($sql, $keys, $vals) = postparse($drinksarray);
			if ($drinkid > 0) {
				$sql = "UPDATE " . db_prefix("drinks") . " SET $sql WHERE drinkid='$drinkid'";
			} else {
				$sql = "INSERT INTO " . db_prefix("drinks") . " ($keys) VALUES ($vals)";
			}
			db_query($sql);
			if (db_affected_rows()> 0) {
				output("`^Drink saved!");
			} else {
				$str = db_error();
				if ($str == "") {
					output("`^Drink not saved: no changes detected.");
				} else {
					output("`^Drink not saved: `\$%s`0", $sql);
				}
			}
		} elseif ($subop == "module") {
			$drinkid = httpget("drinkid");
			// Save module settings
			$module = httpget("editmodule");
			// This should obey the same rules as the configuration editor
			// So disabling
			//$sql = "DELETE FROM " . db_prefix("module_objprefs") . " WHERE objtype='drinks' AND objid='$drinkid' AND modulename='$module'";
			//db_query($sql);
			$post = httpallpost();
			reset($post);
			while(list($key, $val)=each($post)) {
				set_module_objpref("drinks", $drinkid,$key, $val, $module);
			}
			output("`^Saved.");
		}
		if ($drinkid) {
			$op = "edit";
			httpset("drinkid", $drinkid, true);
		} else {
			$op = "";
		}
		httpset('op', $op);
	}
	if ($op == "activate") {
		$sql = "UPDATE " . db_prefix("drinks") . " SET active=1 WHERE drinkid='$drinkid'";
		db_query($sql);
		$op = "";
		httpset('op', "");
	}
	if ($op == "deactivate") {
		$sql = "UPDATE " . db_prefix("drinks") . " SET active=0 WHERE drinkid='$drinkid'";
		db_query($sql);
		$op = "";
		httpset('op', "");
	}
	if ($op==""){
		$op = translate_inline("Ops");
		$id = translate_inline("Id");
		$nm = translate_inline("Name");
		$dkn = translate_inline("Drunkeness");
		$hard = translate_inline("Hard Alchohol?");
		$edit = translate_inline("Edit");
		$deac = translate_inline("Deactivate");
		$act = translate_inline("Activate");
		$conf = translate_inline("Are you sure you wish to delete this drink?");
		$del = translate_inline("Del");
		rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
		rawoutput("<tr class='trhead'>");
		rawoutput("<td>$op</td><td>$id</td><td>$nm</td><td>$dkn</td><td>$hard</td>");
		rawoutput("</tr>");
		$sql = "SELECT drinkid,active,name,drunkeness,harddrink FROM " . db_prefix("drinks") . " ORDER BY drinkid";
		$result= db_query($sql);
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$id = $row['drinkid'];
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
			rawoutput("<td nowrap>[ <a href='runmodule.php?module=drinks&act=editor&op=edit&drinkid=$id&admin=true'>$edit</a>");
			addnav("","runmodule.php?module=drinks&act=editor&op=edit&drinkid=$id&admin=true");
			if ($row['active']) {
				rawoutput(" | <a href='runmodule.php?module=drinks&act=editor&op=deactivate&drinkid=$id&admin=true'>$deac</a>");
				addnav("","runmodule.php?module=drinks&act=editor&op=deactivate&drinkid=$id&admin=true");
			} else {
				rawoutput(" | <a href='runmodule.php?module=drinks&act=editor&op=activate&drinkid=$id&admin=true'>$act</a>");
				addnav("","runmodule.php?module=drinks&act=editor&op=activate&drinkid=$id&admin=true");
			}

			rawoutput(" | <a href='runmodule.php?module=drinks&act=editor&op=del&drinkid=$id&admin=true' onClick='return confirm(\"$conf\");'>$del</a> ]</td>");
			addnav("","runmodule.php?module=drinks&act=editor&op=del&drinkid=$id&admin=true");
			output_notl("<td>`^%s</td>`0", $id, true);
			output_notl("<td>`&%s`0</td>", $row['name'], true);
			output_notl("<td>`^%s`0</td>", $row['drunkeness'], true);
			$hard = translate_inline("`^No");
			if ($row['harddrink']) $hard = translate_inline("`\$Yes");
			output_notl("<td>%s`0</td>", $hard, true);
			rawoutput("</tr>");
		}
		rawoutput("</table>");
	}
	$subop= httpget("subop");
	if($op=="edit"){
		addnav("Drink properties", "runmodule.php?module=drinks&act=editor&op=edit&drinkid=$drinkid&admin=true");
		module_editor_navs("prefs-drinks", "runmodule.php?module=drinks&act=editor&drinkid=$drinkid&op=edit&subop=module&editmodule=");
		if ($subop=="module") {
			$module = httpget("editmodule");
			$oldmodule = $mostrecentmodule;
			rawoutput("<form action='runmodule.php?module=drinks&act=editor&op=save&subop=module&editmodule=$module&drinkid=$drinkid&admin=true' method='POST'>");
			module_objpref_edit('drinks', $module, $drinkid);
			$mostrecentmodule = $oldmodule;
			rawoutput("</form>");
			addnav("", "runmodule.php?module=drinks&act=editor&op=save&subop=module&editmodule=$module&drinkid=$drinkid&admin=true");
		} elseif ($subop=="") {
				$sql = "SELECT * FROM " . db_prefix("drinks") . " WHERE drinkid='".httpget('drinkid')."'";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
		}
	}elseif ($op=="add"){
		/* We're adding a new drink, make an empty row */
		$row = array();
		$row['drinkid'] = 0;
	}

	if (($op == "edit" || $op == "add") && $subop=="") {
		rawoutput("<form action='runmodule.php?module=drinks&act=editor&op=save&admin=true' method='POST'>");
		addnav("","runmodule.php?module=drinks&act=editor&op=save&admin=true");
		showform($drinksarray,$row);
		rawoutput("</form>");
		output("`\$NOTE:`7 Make sure that you know what you are doing when modifying or adding drinks.`n");
		output("Just because the drinks have a lot of options, doesn't mean you have to use all of them`n`n");
		output("`2Drink ID: `7This field is used internally and should be unique.`n");
		output("`2Name: `7The name of the drink the user will see.`n");
		output("`2Cost per level: `7This value times the users level is the drink cost.`n");
		output("`2Chance of modifying HP: `7If set, this is the number of chances out of the total of this and the turn chance for HP getting modified.`n");
		output("`2Chance of modifying turns: `7If set, this is the number of chances out of the total of this and the HP chance for turns getting modified.`n");
		output("`2Always modify HP: `7If set, hitpoints will be modified.  Should not be set alongside HP chance above.`n");
		output("`2Always modify turns: `7If set, turns will be modified.  Should not be set alongside turn chance above.`n");
		output("`2Drunkeness: `7How drunk will this make the player.`n");
		output("`2Hard Drink: `7Users are only allowed a certain number of hard drinks per day regardless of drunkeness.`n");
		output("`2Min HP to add: `7If we are modifying hitpoints, and if HP percent isn't set, use this and the HP max value to pick a random amount of HP to add.  Can be negative.`n");
		output("`2Max HP to add: `7If we are modifying hitpoints and if HP percent isn't set, use this and the HP min value to pick a random amount of HP to add.  Can be negative.`n");
		output("`2HP percent: `7If we are modifying hitpoints and if this is set, the users hitpoints are modified by this percentage.  Can be negative.`n");
		output("`2Min turns to add: `7If we are modifying turns, use this and the turn max value to pick a random amount of turns to add.  Can be negative.`n");
		output("`2Max turns to add: `7If we are modifying turns, use this and the turn min value to pick a random amount of turns to add.  Can be negative.`n");
		output("`2Remarks: `7Text displayed to the user when they order the drink.`n");
		output("`2Buff name: `7What is this buff called.`n");
		output("`2Buff rounds: `7How many rounds this buff lasts.`n");
		output("`2Buff round message: `7What message should show as each round occurs.`n");
		output("`2Buff wearoff: `7What message is shown when this buff wears off.`n");
		output("`2Buff attack modifier: `7Multiplier to modify attack points by? 1.0 is no modification, 2.0 doubles their attack points.`n");
		output("`2Buff defense modifier: `7Multiplier to modify defense points by? 1.0 is no modification, 2.0 doubles their defense points.`n");
		output("`2Buff damage modifier: `7Multiplier to modify damage by? 1.0 is no modification, 2.0 doubles their damage points. This is `\$VERY POTENT`7!`n");
		output("`2Buff damage shield modifier: `7When you are hit, deals damage to your opponent based on damage done to you. 1.0 deals identical damage, 2.0 deals double damage back to the opponent.`n");
		output("`2Effect failure message: Message if this buff fails. (Only used with damage shield)`n");
		output("`2Effect no damage message: Message if no damage is done. (Only used with damage shield)`n");
		output("`2Effect message: What shows when this buff has an effect. (Only used with damage shield)`n`n");
	}
	page_footer();
}

function drinks_drunkenize($commentary,$level){
	if (get_module_pref("noslur")) return $commentary;
	$straight = $commentary;
	$replacements=0;
	while ($replacements/strlen($straight) < ($level)/500 ){
		$slurs = array("a"=>"aa","e"=>"ee","f"=>"ff","h"=>"hh","i"=>"iy","l"=>"ll","m"=>"mm","n"=>"nn","o"=>"oo","r"=>"rr","s"=>"sss","u"=>"oo","v"=>"vv","w"=>"ww","y"=>"yy","z"=>"zz");
		if (e_rand(0,9)) {
			$letter = array_rand($slurs);
			$x = strpos(strtolower($commentary),$letter);
			if ($x!==false &&
				substr($comentary,$x,5)!="*hic*" &&
				substr($commentary,max($x-1,0),5)!="*hic*" &&
				substr($commentary,max($x-2,0),5)!="*hic*" &&
				substr($commentary,max($x-3,0),5)!="*hic*" &&
				substr($commentary,max($x-4,0),5)!="*hic*") {
				if (substr($commentary,$x,1)<>strtolower($letter))
					$slurs[$letter] = strtoupper($slurs[$letter]);
				else
					$slurs[$letter] = strtolower($slurs[$letter]);
				$commentary = substr($commentary,0,$x).
					$slurs[$letter].substr($commentary,$x+1);
				$replacements++;
			}
		}else{
			$x = e_rand(0,strlen($commentary));
			if (substr($commentary,$x,5)=="*hic*") {$x+=5; }
			if (substr($commentary,max($x-1,0),5)=="*hic*") {$x+=4; }
			if (substr($commentary,max($x-2,0),5)=="*hic*") {$x+=3; }
			if (substr($commentary,max($x-3,0),5)=="*hic*") {$x+=2; }
			if (substr($commentary,max($x-4,0),5)=="*hic*") {$x+=1; }
			$commentary = substr($commentary,0,$x).
				"*hic*".substr($commentary,$x);
			$replacements++;
		}//end if
	}//end while
	//get rid of spare *'s in *hic**hic*
	while (strpos($commentary,"*hic**hic*"))
		$commentary = str_replace("*hic**hic*","*hic*hic*",$commentary);
	return $commentary;
}

?>

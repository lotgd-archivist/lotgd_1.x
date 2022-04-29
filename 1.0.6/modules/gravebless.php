<?php
/*  Title:    Ramius' Blessing
 *  Author:   Ben "blarg" Wong (blargeth -at- gmail -dot- com)
 *
 *  Description:
 *  This is a favour reward from Ramius. For a (default) cost of 500 favor,
 *  one can seek to have their weapon or armor blessed, which is kept until
 *  DK. However, there is a (default) 1% chance of Ramius cursing the item
 *  instead. Inspired by the rumour that there already exists something to
 *  get at 500 favour. :)
 *
 *  Version History:
 *  1.0 (2005/08/15)  - adjusted decay calcs as recommended by Kendaer
 *                    - added setting for a base multiplier to start the 
 *                      buff at (default of 1.0)
 *                    - official release
 *
 *  0.3 (2005/08/11)  - adjusted range settings for the curse chance
 *                    - added a requirement for a minimum number of 
 *                      resurrections before use (default of one)
 *                    - other minor tweaks and fixes
 *
 *  0.2 (2005/08/04)  - added a decay rate to the blessed buff effect
 *                    - changed to use the dragonkilltext hook
 *                    - other minor tweaks and fixes
 *
 *  0.1 (2005/07/20)  - first alpha release
 */

require_once("lib/http.php");
require_once("lib/buffs.php");

function gravebless_getmoduleinfo() {
	$info = array(
		"name"=>"Ramius' Blessing",
		"author"=>"Ben Wong",
		"version"=>"1.0",
		"category"=>"Graveyard",
		"download"=>"core_module",
		"description"=>"For a (large) favor cost, Ramius will bless (possibly curse) a player's armor or weapon.",
		"settings"=>array(
			"Ramius' Blessing Settings,title",
			"blesscost"=>"Cost in favor for a blessing from Ramius,int|500",
			"minrez"=>"Minimum number of resurrections the player must have accrued in a DK,range,0,10,1|0",
			"cursechance"=>"Percent chance the blessing becomes a curse,range,0,50,1|1",
			"basemultiplier"=>"Base multiplier to start the buff at,floatrange,0.25,2.0,0.25|1.0",
			"Note: This value is used as a multiplier to lifetap and damageshield.,note"
		),
		"prefs"=>array(
			"Ramius' Blessing Preferences,title",
			"ramiusblessed"=>"Has the player been blessed by Ramius this DK?,bool|0",
			"ramiuscursed"=>"Has the player been cursed by Ramius this DK?,bool|0",
			"ramiusarmor"=>"Was this done to the armor?,bool|0",
			"ramiusweapon"=>"Was this done to the weapon?,bool|0",
			"firstday"=>"Is this the first day of use for the buff?,bool|0",
			"currmultiplier"=>"Current buff effect multiplier,float|0"
		),
	);
	return $info;
}

function gravebless_install() {
	module_addhook("ramiusfavors");
	module_addhook("dragonkilltext");
	module_addhook("newday");
	return true;
}

function gravebless_uninstall() {
	return true;
}

function gravebless_dohook($hookname, $args) {
	global $session;
	$blesscost = get_module_setting("blesscost");
	$minrez = get_module_setting("minrez");
	$playerfavor = $session['user']['deathpower'];
	$playerrezzes = $session['user']['resurrections'];
	$blessed = get_module_pref("ramiusblessed");
	$cursed = get_module_pref("ramiuscursed");
	$ramiusarmor = get_module_pref("ramiusarmor");
	$ramiusweapon = get_module_pref("ramiusweapon");
  
	switch ($hookname) {
	case "dragonkilltext":
		if ($blessed || $cursed) {
			output("`n`nYou seem to hear a very faint humming noise, but as you look around for the source, it quickly fades to silence.");
		}
		set_module_pref("ramiusblessed", 0);
		set_module_pref("ramiuscursed", 0);
		set_module_pref("ramiusarmor", 0);
		set_module_pref("ramiusweapon", 0);
		set_module_pref("currmultiplier", 0);
		break;
	
	case "ramiusfavors":
		// only show the link if they have enough favor, haven't already
		// used it this DK, and have enough rezzes
		if (($playerfavor >= $blesscost) &&
				(!($blessed || $cursed)) && ($playerrezzes >= $minrez)) {
			addnav("Ramius Favors");
			addnav(array("Seek a Blessing (%s favor)", $blesscost),
					"runmodule.php?module=gravebless&blessop=bless");
		}
		break;

	case "newday":
		debug("gravebless debug: blessed=$blessed, cursed=$cursed, armor=$ramiusarmor, weapon=$ramiusweapon");

		if ($blessed) {
			$multiplier = get_module_pref("currmultiplier");
			$firstday = get_module_pref("firstday");
			if ($firstday) {
				set_module_pref("firstday",0);
			} else {
				$basemult = get_module_setting("basemultiplier");
				// start out with a quicker reduction, down to 20%
				if ($multiplier > 0.2) {
					$multiplier = round($multiplier - $basemult / 7, 2);
				} else {
					// flatten out the decay after 20%
					$multiplier = round($multiplier * 0.75, 2);
				}
				if ($multiplier < 0.01) {
					$multiplier = 0.01;  // bottoms out at 1% until DK
				}
				set_module_pref("currmultiplier", $multiplier);
			}
			debug("gravebless debug: multiplier=$multiplier");
			if ($ramiusarmor) {
				$ramiusbuff = array("name"=>"`\$Ramius' Blessed Armor",
									"rounds"=>-1,
									"defmod"=>1.25,
									"damageshield"=>$multiplier,
									"roundmsg"=>"`\$Your armor hums to life in response to the battle!",
									"effectmsg"=>"`\$You feel a shock course through your armor as it deflects {damage} damage back to {badguy}!",
									"effectnodmg"=>"",
									"effectfailmsg"=>"",
									"schema"=>"module-gravebless");
			} elseif ($ramiusweapon) {
				$ramiusbuff = array("name"=>"`\$Ramius' Blessed Weapon",
									"rounds"=>-1,
									"atkmod"=>1.25,
									"lifetap"=>$multiplier,
									"roundmsg"=>"`\$Your weapon hums to life as you swing it at {badguy}!",
									"effectmsg"=>"`\$You feel a shock course through your weapon as it heals you for {damage}!",
									"effectnodmg"=>"",
									"effectfailmsg"=>"",
									"schema"=>"module-gravebless");
			} else {
				debug("Error: Ramius bless flagged, but not armor or weapon (gravebless)");
			}
		} elseif ($cursed) {
			if ($ramiusarmor) {
				$ramiusbuff = array("name"=>"`4Ramius' Cursed Armor",
									"rounds"=>-1,
									"defmod"=>0.8,
									"badguyatkmod"=>1.25,
									"roundmsg"=>"`4Your armor hums to life in response to the battle!",
									"schema"=>"module-gravebless");
			} elseif ($ramiusweapon) {
				$ramiusbuff = array("name"=>"`4Ramius' Cursed Weapon",
									"rounds"=>-1,
									"atkmod"=>0.8,
									"badguydefmod"=>1.25,
									"roundmsg"=>"`4Your weapon hums to life as you swing it at {badguy}!",
									"schema"=>"module-gravebless");
			} else {
				debug("Error: Ramius curse flagged, but not armor or weapon (gravebless)");
			}
		} else {
			debug("neither blessed nor cursed are flagged (gravebless)");
		}
		if ($blessed || $cursed) {
			output("`nYou think you can hear a faint humming sound as you don your armor and grab your weapon. ");
			output("Your %s even feels slightly warmer to the touch than you remember.`n", 
				   translate_inline($ramiusarmor==1?"armor":"weapon"));
			apply_buff("ramiusbuff", $ramiusbuff);
		}
		break;
	}
	return $args;
}

function gravebless_run() {
	global $session;
	page_header("Seek a Blessing from Ramius");
	$blessop = httpget("blessop");
	$blesscost = get_module_setting("blesscost");
	$cursechance = get_module_setting("cursechance");
	$basemultiplier = get_module_setting("basemultiplier");
  
	if ($blessop == "bless") {
		output("`\$Ramius`) speaks, \"`7You are a most persistent mortal.");
		output("For that, I shall grant you a blessing upon your equipment. ");
		output("Perhaps it will enhance your survivability in the waking realm.`)\"");
		addnav("Bless Armor", "runmodule.php?module=gravebless&blessop=armor");
		addnav("Bless Weapon", "runmodule.php?module=gravebless&blessop=weapon");

	} elseif ($blessop == "armor") {
		output("`\$Ramius`) looks over the battered armor you hand him and remarks, \"`7It is no small wonder you visit so often.\"");
		output("`)Closing his eyes, he waves his hand over the armor and quietly speaks a few words you cannot make out.");
		
		$randcurse = e_rand(1,100);
		if ($randcurse > $cursechance) {
			// armor successfully blessed
			output("`)He finishes the incantation and sits still for a moment.`n`n");
			output("\"`7There,`)\" he says, handing you back your armor.");
			output("\"`7Perhaps this will help you when you next awaken.`)\"`n`n");
			output("`)You put on the armor and notice that it feels slightly warmer to the touch than you remember.");
			debuglog("spent $blesscost favor on armor blessing from Ramius");
			$session['user']['deathpower'] -= $blesscost;
			set_module_pref("ramiusblessed", 1);
			set_module_pref("ramiusarmor", 1);
			set_module_pref("currmultiplier",$basemultiplier);  // start at returning 3/4 x enemy damage (default)
			set_module_pref("firstday",1);
		} else {  // cursed armor!
			output("`)You hear a slight cough as he finishes the incantation and looks at your armor.`n`n");
			output("\"`7There,`)\" he says, handing it back to you.");
			output("\"`7Perhaps this will make you stronger when you next awaken.`)\"`n`n");
			output("`)You put on the armor and notice that it feels slightly warmer to the touch than you remember.");
			debuglog("spent $blesscost favor on armor curse from Ramius");
			$session['user']['deathpower'] -= $blesscost;
			set_module_pref("ramiuscursed", 1);
			set_module_pref("ramiusarmor", 1);
		}

	} elseif ($blessop == "weapon") {
		output("`\$Ramius`) looks over the puny weapon you hand him and comments, \"`7I am impressed that you make any progress at all.\"");
		output("`)Closing his eyes, he waves his hand over the weapon and quietly speaks a few words you cannot make out.");

		$randcurse = e_rand(1,100);
		if ($randcurse > $cursechance) {
			// weapon successfully blessed
			output("`)He finishes the incantation and sits still for a moment.`n`n");
			output("\"`7There,`)\" he says, handing you back your weapon.");
			output("\"`7Perhaps this will help you when you next awaken.`)\"`n`n");
			output("`)You grasp the weapon and notice that it feels slightly warmer to the touch than you remember.");
			debuglog("spent $blesscost favor on weapon blessing from Ramius");
			$session['user']['deathpower'] -= $blesscost;
			set_module_pref("ramiusblessed", 1);
			set_module_pref("ramiusweapon", 1);
			set_module_pref("currmultiplier",$basemultiplier);  // start at healing 3/4 x damage dealt (default)
			set_module_pref("firstday",1);
		} else { // cursed weapon!
			output("`)You hear a slight cough as he finishes the incantation and looks at your weapon.`n`n");
			output("\"`7There,`)\" he says, handing it back to you.");
			output("\"`7Perhaps this will make you stronger when you next awaken.`)\"`n`n");
			output("`)You grasp the weapon and notice that it feels slightly warmer to the touch than you remember.");
			debuglog("spent $blesscost favor on weapon curse from Ramius");
			$session['user']['deathpower'] -= $blesscost;
			set_module_pref("ramiuscursed", 1);
			set_module_pref("ramiusweapon", 1);
		}

	} else {
		output("`)Looking up at his grim visage, you reconsider asking for a blessing. ");
		output("As you turn to leave, `\$Ramius`) speaks, ");
		output("\"`7An impressive feat, mortal. You should not have been able to get here. Please Petition.`)\"");
	}
  
	addnav("Places");
	addnav("S?Land of the Shades","shades.php");
	addnav("G?Return to the Graveyard","graveyard.php");
	addnav("M?Return to the Mausoleum","graveyard.php?op=enter");
	page_footer();
}
?>

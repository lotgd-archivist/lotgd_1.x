<?php
/*  Title:    Local Statues
 *  Author:   Ben "blarg" Wong (blargeth -at- gmail -dot- com)
 *  Credits:  Based on the regular Village Statue module by Eric Stevens
 *
 *  Description:
 *  This module adds the most recent Dragon Killer from a specific race to
 *  that village's description. This *ONLY* takes into account the core races 
 *  (Dwarf, Elf, Human, Troll, Felyne, Storm Giant). By default, Felynes and 
 *  Storm Giants are *NOT* included in the statues. 
 *
 *  Version History:
 *  1.0 (2006/03/08)  - minor tweaks
 *                    - official release
 *
 *  0.2 (2005/08/27)  - changed to lookup the city names from their 
 *                      respective module settings
 *                    - fixes for the database caching stuff
 *                    - other minor tweaks and fixes
 *
 *  0.1 (2005/08/26)  - first alpha release
 */

function localstatues_getmoduleinfo(){
	$info = array("name"=>"Local Statues",
				  "author"=>"Ben Wong",
				  "version"=>"1.0",
				  "category"=>"Village",
				  "download"=>"http://www.dragoncat.net/~blarg/logd/localstatues.zip",
				  "description"=>"Diplays smaller statues of the most recent hero in that hero's home town",
				  "requires"=>array("racedwarf"=>"1.0|By Eric Stevens, part of the core download",
									"raceelf"=>"1.0|By Eric Stevens, part of the core download",
									"racehuman"=>"1.0|By Eric Stevens, part of the core download",
									"racetroll"=>"1.0|By Eric Stevens, part of the core download",
									"cities"=>"1.0|By Eric Stevens, part of the core download"),
				  "settings"=>array("Local Statues Settings,title",
									"dwarfhero"=>"Who is the statue of in Qexelcrag?|0",
									"elfhero"=>"Who is the statue of in Glorfindal?|0",
									"humanhero"=>"Who is the statue of in Romar?|0",
									"trollhero"=>"Who is the statue of in Glukmoore?|0",
									"Note: The above are used to store the account IDs of their respective heroes,note",
									"includecats"=>"Include Felynes with the Human heroes?,bool|0",
									"includegiants"=>"Include Storm Giants with the Dwarven heroes?,bool|0"),
				  "prefs"=>array("Local Statues Prefs, title",
								 "homecity"=>"The player's home city at time of DK|")

				 );
	return $info;
}

function localstatues_install(){
	module_addhook("village");
	module_addhook("dk-preserve");
	module_addhook("namechange");
	return true;
}

function localstatues_uninstall(){
	return true;
}

function localstatues_dohook($hookname, $args) {
	global $session;
	$capital = getsetting("villagename", LOCATION_FIELDS);
	$dwarfhero = get_module_setting("dwarfhero");
	$elfhero = get_module_setting("elfhero");
	$humanhero = get_module_setting("humanhero");
	$trollhero = get_module_setting("trollhero");
	$includecats = get_module_setting("includecats");
	$includegiants = get_module_setting("includegiants");

	switch($hookname){
	case "village":
		if ($session['user']['location'] == $capital) break;

		$location = $session['user']['location'];
		if ($location == get_module_setting('villagename', 'racedwarf') && $dwarfhero > 0) {
			$foundhero = 1;
			$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$dwarfhero'";
			$result = db_query_cached($sql, "dwarfhero");
		} elseif ($location == get_module_setting('villagename', 'raceelf') && $elfhero > 0) {
			$foundhero = 1;
			$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$elfhero'";
			$result = db_query_cached($sql, "elfhero");
		} elseif ($location == get_module_setting('villagename', 'racehuman') && $humanhero > 0) {
			$foundhero = 1;
			$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$humanhero'";
			$result = db_query_cached($sql, "humanhero");
		} elseif ($location == get_module_setting('villagename', 'racetroll') && $trollhero > 0) {
			$foundhero = 1;
			$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid='$trollhero'";
			$result = db_query_cached($sql, "trollhero");
		} else {
			$foundhero = 0;
		}

		if ($foundhero) {
			$row = db_fetch_assoc($result);
			if ($location == get_module_setting('villagename', 'racedwarf')) {
				output("`n`3Off to the side, you see a Dwarf carving at a large gem in the likeness of the latest hero of %s, `&%s`3! ", $location, $row['name']);
				output("Scattered about him on the ground are the remnants of some of his previous works, though you can't tell who they were.`0`n");
			} elseif ($location == get_module_setting('villagename', 'raceelf')) {
				output("`n`6Among the trees, you can see an Elfin woodcrafter whittling a block of wood into the likeness of the latest Elven hero, `&%s`6! ", $row['name']);
				output("Around her are several piles of broken wood pieces covered in shavings and sawdust from her previous works.`0`n");
			} elseif ($location == get_module_setting('villagename', 'racehuman')) {
				output("`n`7You can see a Human on a sidewalk painting a large portrait of the latest hero from %s, `&%s`7! ", $location, $row['name']);
				output("There are piles of broken frames and torn canvases all around her, which you suppose are all that remains of her previous paintings.`0`n");
			} elseif ($location == get_module_setting('villagename', 'racetroll')) {
				output("`n`2There is a Troll sitting off in a side patch of mud, shaping the mess into a vague resemblance of the latest Troll hero, `&%s`2! ", $row['name']);
				output("You notice several other piles of mud that might have been his past efforts, though they are now unrecognizable.`0`n");
			} else {
				debug("Error: in village-foundhero, location=$location (localstatues)");
			}
		}
		break;

	case "dk-preserve":
		$race = $session['user']['race'];
		get_module_pref("homecity", "cities");

		if ($race == "Dwarf") {
			set_module_setting("dwarfhero", $session['user']['acctid']);
			invalidatedatacache("dwarfhero");
		} elseif ($race == "Elf") {
			set_module_setting("elfhero", $session['user']['acctid']);
			invalidatedatacache("elfhero");
		} elseif ($race == "Human") {
			set_module_setting("humanhero", $session['user']['acctid']);
			invalidatedatacache("humanhero");
		} elseif ($race == "Troll") {
			set_module_setting("trollhero", $session['user']['acctid']);
			invalidatedatacache("trollhero");
		} elseif ($race == "Felyne" && $includecats) {
			set_module_setting("humanhero", $session['user']['acctid']);
			invalidatedatacache("humanhero");
		} elseif ($race == "Storm Giant" && $includegiants) {
			set_module_setting("dwarfhero", $session['user']['acctid']);
			invalidatedatacache("dwarfhero");
		} else {
			debug("Error: in dk-preserve, race=$race, includecats=$includecats, includegiants=$includegiants (localstatues)");
		}
		
		break;

	case "namechange":
		$account = $session['user']['acctid'];
		if ($dwarfhero == $account) {
			invalidatedatacache("dwarfhero");
		} elseif ($elfhero == $account) {
			invalidatedatacache("elfhero");
		} elseif ($humanhero == $account) {
			invalidatedatacache("humanhero");
		} elseif ($trollhero == $account) {
			invalidatedatacache("trollhero");
		} else {
			debug("Error: in namechange, account=$account (localstatues)");
		}
		break;
	}
	return $args;
}

?>

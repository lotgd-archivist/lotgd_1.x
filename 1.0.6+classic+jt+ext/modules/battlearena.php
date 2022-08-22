<?php
// PRIVATE_CODE
// translation ready
// addnews ready
// mail ready

function battlearena_getmoduleinfo(){
	$info = array(
		"name"=>"Battle Arena",
		"version"=>"1.0",
		"author"=>"Sneakabout",
		"category"=>"Gardens",
		//"download"=>"core_module",
		"settings"=>array(
			"Battle Arena Settings,title",
			"ispit"=>"Is there a Pit?,bool|1",
		),
		"prefs"=>array(
            "cantalk"=>"Can this person talk in the Arena?,bool|1",
            "canfight"=>"Can this person fight in the Pit?,bool|0",
            "cancontrol"=>"Can this person control who talks?,bool|0",
        )
	);
	return $info;
}

function battlearena_install(){
	module_addhook("newday");
	module_addhook("insertcomment");
	module_addhook("gardens");
	module_addhook("moderate");
	module_addhook("bioinfo");
	return true;
}

function battlearena_uninstall(){
	return true;
}

function battlearena_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
	case "insertcomment":
		$op = httpget('op');
		if (($op=="arena")&&(!get_module_pref("cantalk"))) {
			$args['mute']=1;
			$args['mutemsg']=translate_inline("`n`n`@As you go to act you notice one of the guards glaring at you, and you decide to hold your peace.`n`n");
		} elseif (($op=="pit")&&(!get_module_pref("canfight"))) {
			$args['mute']=1;
			$args['mutemsg']=translate_inline("`n`n`@As you go to act you notice one of the guards glaring at you, and you decide to hold your peace.`n`n");
		}
		break;
	case "gardens":
		output("`\$Through a well-guarded gateway, you can see and hear the sounds of battle from the arena nearby.");
		output("Hardy warriors stride through, checking their weapons as they enter and securing their armour.`n`n");
		addnav("Go to the Arena","runmodule.php?module=battlearena&op=arena");
		break;
	case "bioinfo":
		if (get_module_pref("cancontrol") ||
				($session['user']['superuser'] & SU_EDIT_COMMENTS)) {
			$id = $args['acctid'];
			addnav("Arena Options");
			if (get_module_pref("cantalk",false,$id)) {
				addnav("Arena-Only Mute", "runmodule.php?module=battlearena&op=arenamute&id=$id");
			} else {
				addnav("Arena-Only Unmute", "runmodule.php?module=battlearena&op=arenaunmute&id=$id");
			}
			if (get_module_setting("ispit",false,$id)) {
				if (!get_module_pref("canfight",false,$id)) {
					addnav("Pit Enable", "runmodule.php?module=battlearena&op=pitenable&id=$id");
				} else {
					addnav("Pit Mute","runmodule.php?module=battlearena&op=pitmute&id=$id");
				}
			}
		}
		break;
	case "moderate":
		$args['pit'] = translate_inline("Fighting Pit");
		$args['arena'] = translate_inline("Grand Arena");
		$args['arenarules'] = translate_inline("Guard's Hut");
		break;
	case "newday":
		set_module_pref("canfight",0);
		set_module_pref("cantalk",1);
		break;
	}
	return $args;
}

function battlearena_runevent($type){
}

function battlearena_run(){
	global $session;
	$op = httpget('op');
	$id=httpget("id");
	require_once("lib/sanitize.php");
	require_once("lib/commentary.php");
	addcommentary();
	
	switch($op){
	case "arenamute":
		page_header("Muting In Arena");
		output("The player has now been muted, they will not be able to speak in the Arena until the new day.");
		set_module_pref("cantalk",0,false,$id);
		addnav("Return to the Arena", "runmodule.php?module=battlearena&op=arena");
		break;
	case "arenaunmute":
		page_header("Unmuting In Arena");
		output("The player has now been unmuted, they will be able to speak in the Arena.");
		set_module_pref("cantalk",1,false,$id);
		addnav("Return to the Arena","runmodule.php?module=battlearena&op=arena");
		break;
	case "pitenable":
		page_header("Unmuting In Pit");
		output("The player has now been unmuted, they will be able to speak in the Pit.");
		set_module_pref("canfight",1,false,$id);
		addnav("Return to the Pit","runmodule.php?module=battlearena&op=pit");
		break;
	case "pitmute":
		page_header("Muting In Pit");
		output("The player has now been muted, they will not be able to speak in the Pit.");
		set_module_pref("canfight",0,false,$id);
		addnav("Return to the Pit","runmodule.php?module=battlearena&op=pit");
		break;
	case "arena":
		page_header("The Grand Arena");
		output("`\$You stride through the gateway, nodding to the watchful guards either side, into the Grand Arena.");
		output("This great bowl has ranks of seating surrounding a large central area, covered in sand for good reason, where various warriors are sparring and arguing with each other.");
		output("You wince a little at some of wounds being inflicted, verbal and material, and glance at the stands while you take your seat, where arguments are breaking out even among the spectators, though most are merely talking amongst themselves.`n`n");
		output("As you make yourself comfortable, a guard walks up and reminds you that all combatants in the Arena should remain civilised, and that the Laws of Battle should be read before participating in combat.`n`n");
		if (get_module_setting("ispit")) {
			output("As you watch, two warriors begin fighting in earnest, and they are halted by the guards and led into the Fighting Pit through a locked door.");
			output("You glimpse a tunnel leading downwards before the door is closed, and notice a small wooden door leading to the Pit's gallery.`n`n");
			addnav("Go to the Pit","runmodule.php?module=battlearena&op=pit");
		}
		addnav("Laws of Battle","runmodule.php?module=battlearena&op=rules");
		addnav("Return to the Gardens","gardens.php");
		commentdisplay("", "arena","Make your Challenge",30,"shouts");
		break;
	case "rules":
		page_header("The Guard's Tower");
		output("`&On one side of the arena is a small building which seems to be the base of operations for the many guards which roam this area.");
		output("Posted near the door is a scroll with a list of the Laws of Battle:`n`n");
		output("`2The Laws of Battle:`n`n");
		output("Rule One: The arena is not for the kind of actions generally prohibited in this realm, explicit content or excessively violent content will merit action from the staff as well as from the guards.`n`n");
		output("Rule Two: You may only fight with the abilities you would reasonably possess (Hint: Farmboys may well possess less abilities than Gods).");
		output("However, no warriors in this arena are all-powerful, and any with delusions that they are may well be proven wrong by the guards.`n`n");
		output("Rule Three: Any weapons used must be appropriate to the time, the guards may take action on seeing illegal weaponry.`n`n");
		output("Rule Four: You may not control another character's actions, either by stating them for them or through other more mystical means.");
		output("Speaking through two characters is severely frowned upon.`n`n");
		output("Rule Five: Any genuinely personal dislike or grievance should not be dealt with in this arena or in any part of the game.");
		output("Remember to have fun.`n`n");
		if (get_module_pref("cancontrol") ||
				($session['user']['superuser'] & SU_EDIT_COMMENTS)) {
			commentdisplay("Nearby you talk quietly with your fellow guards.`n`n", "arenarules","Talk Quietly",10,"mutters");
		} else {
			output("Nearby you can see some guards talking quietly.");
		}
		addnav("Return to the Arena","runmodule.php?module=battlearena&op=arena");
		break;
	case "pit":
		page_header("The Fighting Pit");
		if (get_module_pref("canfight")) {
			output("`\$Ushered by the guards down a stone-walled corridor, the passage opens into a dank, stone-walled, circular, torch-lit chamber, shadows dancing on the walls making the guards posted around the edges seem larger than they are.");
			output("Through a grille in the roof you can see the eyes of spectators silently watching all that goes on.`n`n");
		} else {
			output("`7You duck through the small wooden door and past the guards down a narrow passage.");
			output("Eventually, after several twists and turns, the passage opens up to a circular room with a low ceiling and a large grille in the center.");
			output("From this vantage point, you can see and hear the warriors fighting below.`n`n");
		}
		addnav("Return to the Arena","runmodule.php?module=battlearena&op=arena");
		commentdisplay("", "pit","Fight!",30,"snarls");
		break;
	}
	page_footer();
}
?>

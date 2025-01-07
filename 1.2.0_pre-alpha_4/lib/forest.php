<?php
// addnews ready
// translator ready
// mail ready
require_once("lib/villagenav.php");

function forest($noshowmessage=false) {
	global $user;

	$basetext = "`c`7`bThe Forest`b`0`cThe Forest, home to evil creatures and evildoers of all sorts.`n`n" . 
		"The thick foliage of the forest restricts your view to only a few yards in most places. " . 
		"The paths would be imperceptible except for your trained eye. " . 
		"You move as silently as a soft breeze across the thick moss covering the ground, wary to avoid stepping on a " . 
		"twig or any of the numerous pieces of bleached bone that populate the forest floor, lest you betray your presence " .
		"to one of the vile beasts that wander the forest.`n";

	$origtexts = array(
		"text"=>$basetext,
		"healer"=>"H?Healer's Hut",
		"fight_kill"=>"L?Look for Something to Kill",
		"fight_slumm"=>"S?Go Slumming",
		"fight_thrill"=>"T?Go Thrillseeking",
		"fight_suicide"=>"*?Search `\$Suicidally`0",
		"search_dragon"=>"G?`@Seek Out the Green Dragon",
		);
	$schemas = array(
		"text"=>"forest",
		"healer"=>"forest",
		"fight"=>"forest",
		"search_dragon"=>"forest",
		);
	// Now store the schemas
	$origtexts['schemas'] = $schemas;
	$texts = modulehook("foresttext",$origtexts);
	$schemas = $texts['schemas'];

	tlschema($schemas['healer']);
//	mass_module_prepare(array("forest", "validforestloc"));
	addnav("Heal");
	addnav($texts['healer'],"healer.php");
	tlschema();
	tlschema($schemas['fight']);
	addnav("Fight");
	addnav($texts['fight_kill'],"forest.php?op=search");
	if ($user->level>1)
		addnav($texts['fight_slumm'],"forest.php?op=search&type=slum");
	addnav($texts['fight_thrill'],"forest.php?op=search&type=thrill");
	if (getsetting("suicide", 0)) {
		if (getsetting("suicidedk", 10) <= $user->dragonkills) {
			addnav($texts['fight_suicide'], "forest.php?op=search&type=suicide");
		}
	}
	tlschema();
	if ($user->level>=Settings::getsetting('maxlevel', 15)  && $user->seendragon==0){
		// Only put the green dragon link if we are a location which
		// should have a forest.   Don't even ask how we got into a forest()
		// call if we shouldn't have one.   There is at least one way via
		// a superuser link, but it shouldn't happen otherwise.. We just
		// want to make sure however.
		$isforest = 0;
		$vloc = modulehook('validforestloc', array());
		foreach(array_keys($vloc) as $i) {
			if ($user->location == $i) {
				$isforest = 1;
				break;
			}
		}
		tlschema($schemas['search_dragon']);
		if ($isforest || count($vloc)==0) {
			addnav($texts['search_dragon'],"forest.php?op=dragon");
		}
		tlschema();
	}
	addnav("Other");
	villagenav();
	if ($noshowmessage!=true){
		tlschema($schemas['text']);
		output($texts['text']);
		modulehook("forest-desc");
		tlschema();
	}
	modulehook("forest", array());
	module_display_events("forest", "forest.php");
	tlschema();
}

?>

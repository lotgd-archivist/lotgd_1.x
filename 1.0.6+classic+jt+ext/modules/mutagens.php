<?php
// PRIVATE_CODE
// addnews ready
// mail ready
//This is a hub module for competing blood borne pathogens which affect
// things such as lycanthropy (werewolfism), vampirism, etc.

function mutagens_getmoduleinfo(){
	$info = array(
		"name"=>"Mutatious Blood Pathogens",
		"author"=>"Eric Stevens & JT Traub",
		"version"=>"1.0",
		"category"=>"Mutations",
		// This actually doesn't have any dependancies now.
		//"requires"=>array(
		//	"moons"=>"1.0|by JT Traub, part of the core download",
		//),
		"settings"=>array(
			"infect_chance"=>"Percent chance for infection event,floatrange,0,10,0.25|3",
			"immune_chance"=>"Percent chance the player is immune (player rolls again each time the module re-activates),floatrange,0,25,0.5|7",
		),
		"prefs"=>array(
			"pathogen"=>"Name of the pathogen|None",
			"immune"=>"Player is immune,bool|0",
			"infected"=>"Player in infected,bool,0",
		),
	);
	return $info;
}

function mutagens_chance() {
	if (get_module_pref("immune", "mutagens")) return 0;
	$pathogens = modulehook("mutagens-pathogens", array());
	// If we have no pathogens which are active today, punt
	if (count($pathogens) == 0) return false;
	$chance = (float)get_module_setting("immune_chance", "mutagens");
	if (get_module_pref("infected", "mutagens")) $chance /= 2.0;
	return $chance;
}

function mutagens_install(){
	//module_addhook("newday-runonce");
	module_addhook("newday");
	module_addhook("village");
	//module_addhook("forestsearch");
	module_addeventhook("forest", "require_once(\"modules/mutagens.php\"); return mutagens_chance()");
	return true;
}

function mutagens_uninstall(){
	return true;
}

function mutagens_dohook($hookname,$args){
	switch($hookname){
	//case "newday-runonce":
		// This doesn't work for a couple of reasons.
		// One, the full moon is a couple of days long (basically from
		// <cycle>*.5 to <cycle>*.67).  Also, different pathogens might
		// have different 'active' times.  This should probaby be handled
		// by a specific module.  Maybe each of three pathogens is tied
		// to a specific moon (3 different forms of lyncanthropy?)
		/*
		$phase1 = get_module_setting("moon1place","moons");
		$phase2 = get_module_setting("moon2place","moons");
		$phase3 = get_module_setting("moon3place","moons");
		$max1 = get_module_setting("moon1cycle","moons");
		$max2 = get_module_setting("moon2cycle","moons");
		$max3 = get_module_setting("moon3cycle","moons");
		$full1 = round($max1/2,0);
		$full2 = round($max2/2,0);
		$full3 = round($max3/2,0);
		if ($phase1 == $full1 || $phase2 == $full2 || $phase3 == $full3){
			set_module_setting("mutagens_active",1);
		}else{
			set_module_setting("mutagens_active",0);
		}
		break;
		*/
	case "newday":
		// Set the user as possibly immune today.   A user which is immune on
		// a given day will be unable to be infected by any new pathogen.
		// It has no effect on existing pathogens.  We set this each and
		// every day since pathogens can be active on any given day.

		// User is possibly immune, set them correctly.
		if (r_rand(0, 100) < get_module_setting("immune_chance")) {
			$immune = 1;
		} else {
			$immune = 0;
		}
		set_module_pref("immune", $immune);

	// A module which implements a pathogen will determine if it activates
	// on a newday and if the user is already infected.  immune only deals
	// with picking up a new pathogen.
	//	//centralize the activity logic for these modules
	//	if (get_module_setting("mutagens_active")){
	//		debug("Mutagens are active today!");
	//		if (e_rand(0,1000) < get_module_setting("immune_chance")*10){
	//			$immune = 1;
	//		}else{
	//			$immune = 0;
	//		}
	//		modulehook("mutagens-newday",array("immune"=>$immune));
	//	}
		break;
	//case "village":
		// Each mutagen should probably hook where it needs to go.
		// rather than nested hooks
	//	if (get_module_setting("mutagens_active")){
	//		modulehook("mutagens-village");
	//	}
	//	break;
		/*
	case "forestsearch":
		if (e_rand(0,10000) < get_module_setting("infect_chance")*100){
			$pathogens = modulehook("mutagens-pathogens",array());
			if (count($pathogens)>0){
				//the player gets infected.  Get a list of pathogens and choose one randomly.
				$idx = e_rand(0,count($pathogens)-1);
				sort($pathogens);
				$pathogen = $pathogens[$idx];
				set_module_pref("pathogen",$pathogen);
				modulehook("mutagens-infect",array("pathogen"=>$pathogens));
				addnav("Continue","forest.php");
				page_footer();
			}else{
				debug("The mutagens module is installed and tried to infect you, but found no pathogens");
			}
		}
		*/
		break;
	}
	return $args;
}

function mutagens_runevent()
{
	$pathogens = modulehook("mutagens-pathogens", array());
	if(count($pathogens) > 0) {
		// Something cool we could do here is to have various pathogens be
		// more or less effective if any are active.
		// However, for now, have them all be equally effective.
		$idx = e_rand(0, count($pathogens)-1);
		// This sort is not necessary
		//sort($pathogens);
		$pathogen = $pathogens[$idx];
		set_module_pref("pathogen", $pathogen);
		modulehook("mutagens-infect", array("pathogen"=>$pathogen));
		// Since this is an event, and an infect shouldn't give any
		// avoidance chance, we don't need to do addnavs or footers here.
		// the event code will drop us back into the forest correctly.
	} else {
		debug("The mutagens module is installed and tried to infect you but found no pathogens.");
		// Give a default, no-op event.
		output("You here a sudden noise behind you, but whirl around seeing nothing.");
	}
}

function mutagens_run(){
	
}

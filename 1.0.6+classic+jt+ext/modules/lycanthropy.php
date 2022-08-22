<?php
// PRIVATE_CODE
// addnews ready
// mail ready

function lycanthropy_getmoduleinfo(){
	$info = array(
		"name"=>"Lycanthropy",
		"category"=>"Mutations", 
		"version"=>"1.0",
		"author"=>"Eric Stevens & JT Traub",
		"requires"=>array(
			"moons"=>"1.0|By JT Traub, part of the core download",
			"mutagens"=>"1.0|By Eric Stevens, part of the core download",
		),
		"download"=>"core_module",
	);
	return $info;
}
function lycanthropy_install(){
	module_addhook("newday");
	module_addhook("mutagens-pathogens");
	module_addhook("village");
	module_addhook("mutagens-infect");
	return true;
}
function lycanthropy_uninstall(){
	return true;
}

function lycanthropy_dohook($hookname,$args){
	$pathogen = "Lycanthropia";
	switch($hookname){
	case "newday":
		if (get_module_pref("pathogen","mutagens")==$pathogen){
			output("`n`6With astonishing agony your skin breaks as fur tears its way from the inside out, and your face distorts into a long wolf's snout, bearing infectious canines.");
			output("Raising your head, a long howl erupts from your throat, and there is little left in you to remind anyone who you were.`n");
			apply_buff("racialbenefit",array(
				"name"=>"`6$pathogen`0",
				"regen"=>"round(<level>/2)",
				"allowinpvp"=>1,
				"rounds"=>-1,
				"schema"=>"module-lycanthropy",
				"effectmsg"=>"You regenerate for {damage} health.",
				"effectnodmgmsg"=>"You have no wounds to regenerate.",
				)
			);
		}
		break;
	case "village":
		if (get_module_pref("pathogen","mutagens")==$pathogen){
			output("`n`\$You feel a blood lust.`n");
			apply_buff("racialbenefit",array(
				"name"=>"`6$pathogen`0",
				"regen"=>"round(<level>/2)",
				"allowinpvp"=>1,
				"rounds"=>-1,
				"schema"=>"module-lycanthropy",
				"effectmsg"=>"You regenerate for {damage} health.",
				"effectnodmgmsg"=>"You have no wounds to regenerate.",
				)
			);
		}
		break;
	case "mutagens-pathogens":
		// Check if this pathogen is active today
		// If so, put it into the array.
		$phase1 = get_module_setting("moon1place","moons");
		$phase2 = get_module_setting("moon2place","moons");
		$phase3 = get_module_setting("moon3place","moons");
		$max1 = get_module_setting("moon1cycle","moons");
		$max2 = get_module_setting("moon2cycle","moons");
		$max3 = get_module_setting("moon3cycle","moons");
		$full1 = ($phase1 >= $max1*.5 && $phase1 < $max1*.62)?1:0;
		$full2 = ($phase2 >= $max2*.5 && $phase2 < $max2*.62)?1:0;
		$full3 = ($phase3 >= $max3*.5 && $phase3 < $max3*.62)?1:0;
		if ($full1 || $full2 || $full3) {
			array_push($args,$pathogen);
		}
		break;
	case "mutagens-infect":
		if ($args['pathogen']==$pathogen){
			output("`6As you creep through the forest, the normal sounds of creatures suddenly whiffs out like a candle.");
			output("You halt in your tracks, ready for anything.");
			output("Slowly the forest darkens as though a heavy storm approaches.");
			output("With out warning, a great wolf bursts from the underbrush, knocking you to the ground.");
			output("With a searing pain, it sinks its razor-sharp teeth into your shoulder.");
			output("Raising your %s you attempt to strike at the gigantic beast, but it is already gone",$session['user']['weapon']);
			output("You are injured and bleeding, though you have had worse.");
			$session['user']['hitpoints'] -=
				round($session['user']['maxhitpoints']/3,0);
			if ($session['user']['hitpoints'] <= 0)
				$session['user']['hitpoints']=1;
		}
		break;
	}
	return $args;
}
?>

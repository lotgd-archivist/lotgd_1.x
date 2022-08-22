<?php

function creaturestats_getmoduleinfo(){
	$info = array(
		"name"=>"Modify Creature Stats",
		"author"=>"JT Traub",
		"version"=>"1.1",
		"category"=>"Administrative",
		"download"=>"",
		"prefs-creatures"=>array(
			"Modify Creature Stats,title",
			"`\$Please note:`0  It is very very easy to make a creature which is too hard or gives too much reward.`nPlease be `\$VERY VERY`0 careful in how you change things.`n`nAlso -- at low levels even one or two points can be a major change.  Think about the game balance.,note",
			"These are flat adds (or subs for negatives) not multipliers,note",
			"addhit"=>"Add how many hit points?,int|0",
			"addatk"=>"Add how much attack?,int|0",
			"adddef"=>"Add how much defense?,int|0",
			"addgold"=>"Add how much gold?,int|0",
			"If you specify 250 for the gemchance then killing this creature will give 2 gems and 50% (plus the normal chance) of finding a third gem,note",
			"addgem"=>"Add how much chance to finding a gem from this creature?,int|0",
		),
	);
	return $info;
}

function creaturestats_install(){
	module_addhook("creatureencounter");
	module_addhook("alter-gemchance");
	return true;
}

function creaturestats_uninstall(){
	return true;
}

function creaturestats_dohook($hookname,$args){
	global $session;

	switch($hookname){
	case "creatureencounter":
		$args['creaturegold'] +=
			get_module_objpref("creatures",  $args['creatureid'], "addgold");
		if ($args['creaturegold'] < 0) $args['creaturegold'] = 0;
		$args['creaturehealth'] +=
			get_module_objpref("creatures",  $args['creatureid'], "addhit");
		if ($args['creaturehealth'] < 1) $args['creaturehealth'] = 1;
		$args['creatureattack'] +=
			get_module_objpref("creatures",  $args['creatureid'], "addatk");
		if ($args['creatureattack'] < 1) $args['creatureattack'] = 1;
		$args['creaturedefense'] +=
			get_module_objpref("creatures",  $args['creatureid'], "adddef");
		if ($args['creaturedefense'] < 1) $args['creaturedefense'] = 1;
		break;
	case "alter-gemchance":
		if ($args['type'] != "forest") break;
		if (!isset($args['creatureid'])) break;
		$args['chance'] +=
			get_module_objpref("creatures", $args['creatureid'], "addgem");
		if ($args['chance'] < 0) $args['chance'] = 0;
		break;
	}
	return $args;
}

function creaturestats_run(){
}
?>

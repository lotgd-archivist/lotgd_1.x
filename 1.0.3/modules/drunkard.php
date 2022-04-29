<?php
// translator ready
// addnews ready
// mail ready

function drunkard_getmoduleinfo(){
	$info = array(
		"name"=>"Drunkard",
		"version"=>"1.0",
		"author"=>"JT Traub<br>w/ mods suggested by Jason Still",
		"category"=>"Inn Specials",
		"download"=>"core_module",
		"settings"=>array(
			"Drunkard Event Settings,title",
			"spillchance"=>"Chance that the drunk spills beer on you.,range,0,100,1|30",
			"maxseen"=>"How many times per day (0 unlimited),int|2"
		),
		"prefs"=>array(
			"Drunkard User Preferences,title",
			"seen"=>"How many times has the drunkard been seen?,int|0"
		),
	);
	return $info;
}

function drunkard_chance() {
	if (get_module_pref("seen", "drunkard") <
			get_module_setting("maxseen", "drunkard"))
		return 100;
	return 0;
}

function drunkard_install(){
	module_addeventhook("inn", "require_once(\"modules/drunkard.php\"); return drunkard_chance();");
	module_addhook("newday");
	return true;
}

function drunkard_uninstall(){
	return true;
}

function drunkard_dohook($hookname,$args){
	if ($hookname == "newday") {
		set_module_pref("seen", 0);
	}
	return $args;
}

function drunkard_runevent($type)
{
	global $session;
	$chance = get_module_setting("spillchance");
	$roll = e_rand(1, 100);
	$seen = get_module_pref("seen");
	set_module_pref("seen", $seen+1);
	output("`5A very drunk patron stumbles into you as you make your way across the crowded room.`n`n");
	if ($roll < $chance) {
		// He spills on you
		output("`5He is carrying a nearly full glass of ale.");
		output("As he collides with you, it spills all over your nearly clean clothes!");
		if ($session['user']['sex'] == SEX_MALE) {
			output("As you look up, you notice `%Violet`5 watching you and are embarrassed that she is trying to keep from laughing out loud at your mishap.");
		} else {
			output("As you look up, you notice `^Seth`5 watching you and are embarrassed that he is trying to keep from laughing out loud at your mishap.");
		}
		output("`n`n`&You `\$lose 1`& charm point.");
		if ($session['user']['charm'] > 0)
			$session['user']['charm']--;
	} else {
		// You're safe
		output("`5Fortunately his glass was already empty.");
		output("You escort him over to a chair where he can sit without running into everyone else.");
		if ($session['user']['sex'] == SEX_MALE) {
			output("As you stand up, you catch `%Violet's`5 eye and she gives you a big smile for your kindness.");
		} else {
			output("As you stand up, you catch `^Seth's`5 eye and he gives you a big smile for your kindness.");
		}
		output("`n`n`&You gain `^1`& charm.");
		$session['user']['charm']++;
	}
}

function drunkard_run(){
}
?>

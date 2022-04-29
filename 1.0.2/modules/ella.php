<?php
// translator ready
// mail ready
// addnews ready

/* Ella's Dance Studio */
/* ver 1.0 by Shannon Brown => SaucyWench -at- gmail -dot- com */

/* 6th Nov 2004 */

require_once("lib/villagenav.php");
require_once("lib/http.php");

function ella_getmoduleinfo(){
	$info = array(
		"name"=>"Ella's Dance Studio",
		"version"=>"1.0",
		"author"=>"Shannon Brown",
		"category"=>"Village",
		"download"=>"core_module",
		"settings"=>array(
			"Ella's Dance Studio - Settings,title",
			"danceday"=>"How often can the player dance? 1=daily ~ 2=every second day,range,1,10,1|3",
			"ellaloc"=>"Where does Ella appear,location|".getsetting("villagename", LOCATION_FIELDS),
		),
		"prefs"=>array(
			"Ella's Dance Studio User Preferences,title",
			"candance"=>"Can the player dance today?,int|1",
			"dayswait"=>"Days the player must wait till next lesson,int|0",
		)
	);
	return $info;
}

function ella_install(){
	module_addhook("newday");
	module_addhook("village");
	module_addhook("changesetting");
	return true;
}

function ella_uninstall(){
	return true;
}

function ella_dohook($hookname,$args){
	global $session;

	switch($hookname){
	case "changesetting":
		if ($args['setting'] == "villagename") {
			if ($args['old'] == get_module_setting("ellaloc")) {
				set_module_setting("ellaloc", $args['new']);
			}
		}
		break;
	case "newday":
		$dayswait=get_module_pref("dayswait");
		// let's just correct if something goes wrong somehow.
		if ($dayswait<0) $dayswait = 0;
		if ($dayswait>0) $dayswait--;
		set_module_pref("dayswait",$dayswait);
		if ($dayswait==0) set_module_pref("candance",1);
		break;
	case "village":
		// Moved the check for candance down into the run.  Buildings
		// shouldn't just vanish!
		if ($session['user']['location'] == get_module_setting("ellaloc")){
			tlschema($args['schemas']['marketnav']);
			addnav($args['marketnav']);
			tlschema();
			addnav("Ella's Dance Studio","runmodule.php?module=ella");
		}
		break;
	}	
	return $args;
}

function ella_run() {
	global $session;
	$op = httpget("op");

	$candance = get_module_pref("candance");

	page_header("Dance Studio");
	output("`&`c`bLady Ella's Dance Studio`b`c");

	if (!$candance) {
		output("`7Your muscles are still too stiff and sore from your last lesson.`n");
		output("`7Perhaps in a day or so, you'll feel up to another lesson.");
	} elseif ($op == "") {
		output("`7A statuesque teacher in intricately-beaded garb stands at one end of a small studio, intently watching the movements of the  dancers in the room.");
		output("Partnered and solo dancers sway and spin in fast rhythms, matching their movements to the piano that sings from one side of the room, where a delicate Felyne moves her paws to create the sound.`n`n");
		output("Noting your interest, Lady Ella smiles at you as she walks towards you.`n`n");
		output("\"`&Lovely movement, everyone, keep going, I'll have you all as polished performers yet!`7\"`n`n");
		output("`7She approaches you and beckons you into her office.");
		if ($session['user']['maxhitpoints']>=(($session['user']['level']*10)+5)) {
			output("Once there, she explains what she can offer.");
			output("\"`&You'll like our lessons very much.");
			output("We pride ourselves in making sure it's fun AND helpful.");
			output("Who knows, you might land the %s of your dreams!`n`n",
					translate_inline($session['user']['sex']?"man":"girl"));
			output("Now, it takes effort to learn to dance, so you can't expect to have the time for training so much at the muscles.");
			if ($session['user']['sex']) {
				output("So, are you sure you want to make that commitment and learn to make the men chase you?`7\"");
			} else {
			output("So, are you sure you want to make that commitment and learn to sweep the ladies off their feet?`7\"");
			}
			output("`qYou realize she's asking for a sacrifice of strength.");
			output("`qYou ponder for a moment on whether you want to make that permanent sacrifice.");
			addnav("Take Lesson","runmodule.php?module=ella&op=dance");
		}else{
			output("Once there, she regards you gravely.");
			output("\"`&Darling, there's nothing I love more than teaching someone the love of the dance.");
			output("I'd be more than happy to let you watch, but by the looks of those pins on you, you'd fall over from the exertion of actually dancing with us today.`7\"`n`n");
			output("You nod your understanding, that you perhaps need more hitpoints to get the most out of the strenuous training she has to offer.");
		}
	}else {
		output("`7You agree to take a dancing lesson today, and eagerly move forward to join the other dancers.`n`n");
		output("The music begins slowly, giving you the chance to start gradually, but it quickly becomes difficult and tiring.");
		output("You're not sure how impressed %s`7 would be with your efforts, but you're determined not to give up too easily.`n`n",$session['user']['sex']?"`^Seth":"`%Violet");
		output("After the lesson concludes, you feel weary, and wonder whether you have the strength to keep this up for many weeks to come.");
		output("You sure hope %s`7 appreciates your efforts today.`n`n",$session['user']['sex']?"`^Seth":"`%Violet");
		output("Your maximum hitpoints have permanently decreased.");
		output("You feel `5charming!");
		$session['user']['hitpoints']-=5;
		if ($session['user']['hitpoints'] < 1) $session['user']['hitpoints']=1;
		$session['user']['maxhitpoints']-=5;
		$session['user']['charm']+=5;
		set_module_pref("candance",0);
		$danceday=get_module_setting("danceday");
		set_module_pref("dayswait",$danceday);
	}
	villagenav();
	page_footer();
}
?>
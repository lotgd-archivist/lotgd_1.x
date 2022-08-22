<?php
function smokescreen_getmoduleinfo(){
	$info = array(
		"name"=>"Smoke Screen",
		"version"=>"1.0",
		"author"=>"JT Traub",
		"category"=>"Lodge",
		"download"=>"",
		"settings"=>array(
			"Smokescreen Settings,title",
			"cost"=>"Smokescreen cost in Donation points,int|100",
			"chancefail"=>"Chance that the Smokescreen will not work,range,0,100,1|20",
		),
		"prefs"=>array(
			"Smokescreen Preferences,title",
			"hassmoke"=>"How many Smokescreens does this player have,int|0",
		)
	);
	return $info;
}

function smokescreen_install(){
	module_addhook("pointsdesc");
	module_addhook("lodge");
	module_addhook("footer-forest");
	module_addhook("fightnav-specialties");
	module_addhook("apply-specialties");
	module_addhook("battle-victory");
	module_addhook("battle-defeat");
	return true;
}

function smokescreen_uninstall(){
	return true;
}

function smokescreen_dohook($hookname,$args){
	global $session;

	switch($hookname){
	case "pointsdesc":                
		$args['count']++;
		$format = $args['format'];
		$str = translate("A Smokescreen (%s points) that allows you to create a diversion.");
		$str = sprintf($str, get_module_setting("cost"));
		output($format, $str, true);
		break;
	case "lodge":          
		$cost = get_module_setting("cost");
		addnav(array("Buy a Smokescreen (%s points)",$cost),
				"runmodule.php?module=smokescreen&op=start");
		break;
	case "fightnav-specialties":
		$hassmoke = get_module_pref("hassmoke");
		if ($hassmoke <= 0) break;
		if (is_buff_active("smoke")) break;
		$script = $args['script'];
		addnav("Diversion");
		addnav(array("Create smokescreen (%s ampules left)", $hassmoke),
				$script."op=fight&skill=smoke&l=1");
		break;
	case "apply-specialties":
		$skill = httpget("skill");
		if ($skill == "smoke") {
			$hassmoke = get_module_pref("hassmoke");
			if ($hassmoke <= 0) break;
			increment_module_pref("hassmoke", -1);
			$chancefail = get_module_setting("chancefail");
			if (e_rand(1,100) < $chancefail) {
				// This was a dud!
				output("`)You toss the ampule that J. C. Petersen gave you, but nothing happens!  It must have been a dud!`0");
				break;
			}
			apply_buff("smoke", array(
				"startmsg"=>"`)You toss the ampule that J. C. Petersen gave you and a cloud of smoke billows outward.`0",
				"name"=>"Smokescreen",
				"rounds"=>-1,
				"expireafterfight"=>1,
				"badguyatkmod"=>.9,
				"badguydefmod"=>.9,
				"badguydmgmod"=>.95,
				"schema"=>"module-smokescreen"
			));
		}
		break;
	case "footer-forest":
		$hassmoke= get_module_pref("hassmoke");
		if ($hassmoke <= 0) break;
		// If the thieves module isn't active, break
		if (!is_module_active("thieves")) break;
		// If we are not in the thieves module, we cannot throw a smokescreen
		// to create a diversion!
		if ($session['user']['specialinc'] != 'module:thieves') break;
		// If we are an elf and the elf-friend option is on, there is no
		// need for a smoke screen
		if ($session['user']['race'] == 'Elf' &&
				get_module_setting("elffriend", "thieves")) break;
		$op = httpget('op');
		// If we are just encountering the bandits, give the option!
		if ($op=="" || $op=="search") {
			output("`n`nYou recall purchasing a Smoke ampule earlier. ");
			output("Maybe it'll help!`0");
			addnav("Diversion");
			addnav(array("Create smokescreen (%s ampules left)", $hassmoke),
					"runmodule.php?module=smokescreen&op=throw");
		}
		break;
	}
	return $args;
}

function smokescreen_run(){
	global $session;
	$op = httpget('op');

	switch ($op){
	case "start":
		$cost = get_module_setting("cost");
		page_header("Hunter's Lodge");
		$pointsavailable = $session['user']['donation'] -
			$session['user']['donationspent'];
		if ($pointsavailable >= $cost){
			output("`7J. C. Petersen motions you over to a small shelf along a log wall. ");
			output("Atop the shelf are many small, grey ampules. ");
			output("He reaches up and takes one down for you to see.`n`n");
			output("`\$\"This is a `)smoke`\$ ampule.  I have a fairly large supply of these.  When thrown, they have a high liklihood of creating a large screen of smoke which can confuse your enemies or create a diversion when you need one! Do you wish to purchase one?\"");
			$hassmoke = get_module_pref("hassmoke");
			if ($hassmoke == 1) {
				output("`n`nYou currently have 1 ampule in your possession.");
			} else {
				output("`n`nYou currently have %s ampules in your possession.", $hassmoke);
			}
			addnav("Purchase Smoke Ampule");
			addnav("Yes","runmodule.php?module=smokescreen&op=yes");
			addnav("No","runmodule.php?module=smokescreen&op=no");
		} else {
			output("`7J. C. Petersen shakes his head sadly and tells you to come back when you have more credit built up before attempting to buy this item.`0");
			addnav("Return");
			addnav("L?Return to the Lodge","lodge.php");
		}
		break;
	case "yes":
		page_header("Hunter's Lodge");
		output("`7J. C. Petersen grins, and places the `)smoke`7 ampule into your hand.`n`n");
		output("He says, `\$\"Come back when you need more!\"`0");
		$cost = get_module_setting("cost");
		$session['user']['donationspent'] += $cost;
		increment_module_pref("hassmoke");
		addnav("Return");
		addnav("L?Return to the Lodge","lodge.php");
		break;
	case "no":
		page_header("Hunter's Lodge");
		output("`7J. C. Petersen places the `)smoke`7 ampule back atop the shelf.`n`n");
		output("`\$\"As you wish, but let me know if you change your mind.\"`0");
		addnav("Return");
		addnav("L?Return to the Lodge","lodge.php");
		break;
	case "throw":
		page_header("Smokescreen");
		$chancefail = get_module_setting("chancefail");
		output("`6Quickly reaching into a pocket, you pull out a `)smoke`6 ampule and throw it at the feet of the thieves!`n`n");
		increment_module_pref("hassmoke", -1);
		debuglog("threw a Smokescreen to try and escape Lonestrider's Thieves");
		if (e_rand(1,100) > $chancefail) {
			output("`6A thick `)smokescreen`6 quickly encompases the area, and you hear the coughs of `\$Lonestrider`6 and his men as they try to make their way through to get you.`n`n");
			output("`^Not willing to stay here a second longer, you quickly make your escape!`0");
			$session['user']['specialinc']="";
			addnav("Back to the Forest", "forest.php");
		}else{
			output("`6Nothing happens, and the thieves laugh at your feeble effort!");
			output("They swiftly surround you from all sides, and you have no choice but to fight!`0");
			addnav("Stand and Fight!","forest.php?op=stand");
		}
		break;
	}
	page_footer();
}	
?>

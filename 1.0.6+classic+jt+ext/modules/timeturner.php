<?php

function timeturner_getmoduleinfo() {
	$info = array(
		"name"=>"Time Turner",
		"version"=>"1.0",
		"author"=>"JT Traub",
		"category"=>"Lodge",
		"download"=>"",
		"settings"=>array(
			"Time Turner Settings,title",
			"cost"=>"Cost in Donation points,int|500",
			"maxdays"=>"After how many days of feebleness do effects show?,range,3,20,1|5",
			"daysperuse"=>"How many days of feebleness are gained by use?,range,3,20,1|3",
		),
		"prefs"=>array(
			"Time Turner Preferences,title",
			"count"=>"How many Time Turners does this player have,int|0",
			"feeble"=>"How many days of feebleness have accumulated,int|0",
			"affected"=>"Is the feeble buff in effect?,bool|0",
		)
	);
	return $info;
}

function timeturner_install() {
	module_addhook("village");
	module_addhook("newday");
	module_addhook("pointsdesc");
	module_addhook("lodge");
	return true;
}

function timeturner_uninstall() {
	return true;
}

function timeturner_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "pointsdesc":
		$args['count']++;
		$format = $args['format'];
		$str = translate("A Time Turner (%s points). This item grants a new day, but there might be a cost!");
		$str = sprintf($str, get_module_setting("cost"));
		output($format, $str, true);
		break;
	case "lodge":
		$cost = get_module_setting("cost");
		addnav(array("Buy a Time Turner (%s points)",$cost),
					"runmodule.php?module=timeturner&op=start");
		break;
	case "village":
		$count = get_module_pref("count");
		if ($count >= 1) {
			tlschema($args['schemas']['othernav']);
			addnav($args['othernav']);
			tlschema();
			addnav(array("U?Use a Time Turner (`#%s`0 left)", $count),
					"runmodule.php?module=timeturner&op=use");
		}
		break;
	case "newday":
		$feeb = get_module_pref("feeble");
		debug("Newday Feeble: $feeb");
		if ($feeb > 0) {
			increment_module_pref("feeble", -1);
			$feeb--;
			debug("Modified Feeble: $feeb");
			if ($feeb >= get_module_setting("maxdays") ||
					($feeb >= 0 && get_module_pref("affected"))) {
				output("`n`\$Your muscles and bones feel weak, as if you are far older than your real age!`0`n");
				apply_buff("feeble", array(
					"name"=>"Feeble",
					"atkmod"=>.25,
					"defmod"=>.25,
					"dmgmod"=>.25,
					"rounds"=>-1,
				));
				set_module_pref("affected", 1);
			} elseif ($feeb > (get_module_setting("maxdays") -
						get_module_setting("daysperuse"))) {
				output("`n`\$The aches in your muscles make you think that old age is catching up to you too soon.`0`n");
			}
			if ($feeb <= 0) {
				set_module_pref("feeble", 0);
				set_module_pref("affected", 0);
			}
		}
		break;
	}

	return $args;
}

function timeturner_run(){
	global $session;
	page_header("Hunter's Lodge");
	$op = httpget('op');

	switch ($op){
	case "start":
		$cost = get_module_setting("cost");
		$count = get_module_pref("count");
		$pointsavailable = $session['user']['donation'] -
			$session['user']['donationspent'];
		if ($pointsavailable >= $cost){
			output("`^J. C. Petersen reaches up and blows the thick layer of dust off a small, copper box.  Within the box are tiny silver charms shaped like hourglasses.`n`n");
			output("`\$\"These are `^Time Turners`\$.  When broken, they release a potent magic which will rewind time to the beginning of the day.  Be `^warned`\$ though, play with time too much and you might become old and frail far before your time!  Do you wish to purchase one?\"`^");
			if ($count == 1) {
				output("`n`nYou currently have 1 Time Turner.`n");
			} else {
				output("`n`nYou currently have %d Time Turners.`n", $count);
			}
			addnav("Purchase one?");
			addnav("Yes","runmodule.php?module=timeturner&op=yes");
			addnav("No","runmodule.php?module=timeturner&op=no");
		} else {
			output("`^J. C. Petersen stares at you for a moment then looks away as you realize that you don't have enough points to purchase this item.");
			addnav("Return");
			addnav("L?Return to the Lodge","lodge.php");
		}
		break;
	case "yes":
		$cost = get_module_setting("cost");
		output("`^J. C. Petersen smiles, and drops one of the charms into your hand.`n`n");
		output("`^You carefully place the charm into your pocket, and remind yourself to remember that it is there.`n`n");
		$session['user']['donationspent'] += $cost;
		increment_module_pref("count");
		addnav("Return");
		addnav("L?Return to the Lodge","lodge.php");
		break;
	case "no":
		output("`^J. C. Petersen glances up at you from the box.`n`n");
		output("`\$\"Well, I will have some in stock for a while if you ever change your mind.\"");
		addnav("Return");
		addnav("L?Return to the Lodge","lodge.php");
		break;
	case "use":
		page_header("Time Turner");
		increment_module_pref("count", -1);
		increment_module_pref("feeble", get_module_setting("daysperuse"));
		$location = $session['user']['location'];
		debuglog("Used a Time Turner.");
		output("`^Taking one of the silver hourglass charms from your pocket, you hold it up to the light.`n`n");
		output("The hourglass begins to spin faster and faster and you get dizzy watching it.`n`n");
		output("After a few minutes, you are so dizzy that you need to sit down.`n`n");
		output("As you collapse to the ground, the charm gives off a burst of light. ");
		output("When your eyes clear, you look up and see the familiar sights of %s, but you could swear you've seen all of this before!", $location);
		$feeble = get_module_pref("feeble");
		if ($feeble > (get_module_setting("maxdays") -
						 get_module_setting("daysperuse") - 1)) {
			output("`n`n`\$Your muscles ache and you get the feeling that you might be using this magic too often!`0`n");
			debug("Feeble: $feeble");
		}
		$session['user']['age']--;
		addnav("C?Continue","newday.php");
		break;
	}
	page_footer();
}	
?>

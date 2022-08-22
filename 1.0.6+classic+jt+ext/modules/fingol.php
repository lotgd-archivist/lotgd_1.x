<?php

function fingol_getmoduleinfo(){
	$info = array(
		"name"=>"Amulet of Fingol",
		"version"=>"1.0",
		"author"=>"JT Traub",
		"category"=>"Lodge",
		"download"=>"",
		"settings"=>array(
			"Amulet of Fingol Settings,title",
			"days"=>"Average number of game days the amulet will last,range,1,10,1|3",
			"cost"=>"Amulet cost in Donation points,int|150",
			"trainapply"=>"Will the amulet help in master fights,bool|1",
			"pvpapply"=>"Will the amulet help in PvP,bool|1",
		),
		"prefs"=>array(
			"Amulet of Fingol Preferences,title",
			"hasamulet"=>"Does this player have an amulet,bool|0",
			"amuletdays"=>"Number of days this player has had the amulet,int|0",
			"curdays"=>"Number of days this current amulet will last,int|0",
		)
	);
	return $info;
}

function fingol_install(){
	module_addhook("newday");
	module_addhook("pointsdesc");
	module_addhook("lodge");
	return true;
}

function fingol_uninstall(){
	return true;
}

function fingol_dohook($hookname,$args){
	global $session;
	$hasamulet = get_module_pref("hasamulet");
	$amuletdays = get_module_pref("amuletdays");
	switch($hookname){
	case "pointsdesc":
		$args['count']++;
		$format = $args['format'];
		$str = translate("An Amulet of Fingol (%s points).  This amulet lasts a random amount of time (average %s days) and has slightly random positive effects.");
		$str = sprintf($str, get_module_setting("cost"),
				get_module_setting("days"));
		output($format, $str, true);
		break;
	case "lodge":
		if (!$hasamulet) {
			$cost = get_module_setting("cost");
			addnav(array("Buy an Amulet of Fingol (%s points)",$cost),
					"runmodule.php?module=fingol&op=start");
		}
		break;
	case "newday":
		$atkbuff = e_rand(5,10);
		$defbuff = (15 - $atkbuff);
		$ffmod = e_rand(1,3);        
		$days = get_module_pref("curdays");
		if ($hasamulet) {
			$amuletdays++;
			set_module_pref("amuletdays",$amuletdays);
		}

		if ($hasamulet && ($amuletdays > $days)) {
			set_module_pref("hasamulet",0);
			set_module_pref("amuletdays",0);
			output("`n`n`2Your Amulet of Fingol has vanished as if by magic!`n`0");
		}elseif ($hasamulet && ($amuletdays <= $days)) {
			output("`n`n`2The amulet of Fingol around your neck shivers against your skin.`n`0");
			$session['user']['turns']+=$ffmod;

			if ($ffmod==1) {
				output("`2You gain the power to slay `^another`2 creature in the `@forest`2 today!`n`0");
			}else{
				output("`2You gain the power to slay `^%s`2 more creatures in the `@forest`2 today!`n`0", $ffmod);
			}

			apply_buff("amuletbuff",
				array("name"=>"`2Amulet of Fingol`0",
					"atkmod"=>(1+($atkbuff  / 100)),
					"defmod"=>(1+($defbuff  / 100)),
					"rounds"=>-1,
					"allowinpvp"=>get_module_setting("pvpapply"),
					"allowintrain"=>get_module_setting("trainapply"),
					"schema"=>"module-fingol",
				)
			);
		}
		break;
	}

	return $args;
}

function fingol_run(){
	global $session;
	page_header("Hunter's Lodge");
	$cost = get_module_setting("cost");
	$hasamulet = get_module_pref("hasamulet");
	$amuletdays = get_module_pref("amuletdays");
	$op = httpget('op');

	switch ($op){
	case "start":
		$pointsavailable = $session['user']['donation'] -
			$session['user']['donationspent'];
		if ($pointsavailable >= $cost && $hasamulet == 0){
			output("`2J. C. Petersen opens a small, pewter box.  Inside are a few small gleaming silver amulets with a strange face depicted upon them.`n`n");
			output("`\$\"These amulets are very rare, mysterious and quite unpredictable.  Each one is an `2Amulet of Fingol`\$, and are known to strengthen the wearer.  You will wake up one day and find it gone though, but they seem to stay around for a few days at least. Anyway, do you wish to purchase one?\"");
			addnav("Purchase one");
			addnav("Yes","runmodule.php?module=fingol&op=yes");
			addnav("No","runmodule.php?module=fingol&op=no");
		} else {
			output("`2J. C. Petersen raises one eyebrow at you and stares at you until you realize that you don't have enough points to purchase this item.");
		}
		break;
	case "yes":
		output("`2J. C. Petersen reaches into the box, and pulls out a silver amulet on a leather thong.`n`n");
		output("`\$\"This should serve you well in your travels.\"`n`n");
		output("`2You place the amulet around your neck, vaguely disconcerted as the face appears to watch you.`n`n");
		output("J. C. Petersen tells you, `\$\"The power of the amulet will only show itself each morning as you awaken.  Thank you for your purchase!\"");
		$session['user']['donationspent'] += $cost;
		$days = get_module_setting("days");
		$min = $days-2;
		$max = $days+2;
		if ($min < 1) $min = 1;
		if ($max > 12) $max = 12;
		$days = e_rand($min, $max);
		set_module_pref("curdays", $days);
		set_module_pref("hasamulet",1);
		break;
	case "no":
		output("`2J. C. Petersen glances up at you from the box.`n`n");
		output("`\$\"This is no ordinary amulet. You'll be quite sorry if you do not purchase one at some point.\"`2");
		break;
	}
	addnav("Return");
	addnav("L?Return to the Lodge","lodge.php");
	page_footer();
}	
?>

<?php
// addnews ready
// translator ready
// mail ready

/* Petra the Inker */
/* ver 1.0 */
/* originally by Shannon Brown => SaucyWench -at- gmail -dot- com */
/* 90% of it actually fixed up by JT Traub */

/* 23 Aug 2004 */

// This module checks for a feather given to the player by Lauri
// in the Kitchen of DOOM module, however, you do not need to have
// the kitchen module installed for Petra to function.

// 3rd Sept ver 1.1 Also interfaces with Matthias, the Astute

require_once("lib/villagenav.php");
require_once("lib/http.php");

function petra_getmoduleinfo(){
	//added 2 lines to settings for custom tats....
	//plus code in 2 other places (marked below)
	$info = array(
		"name"=>"Petra the Tattoo Artist",
		"version"=>"1.2",
		"author"=>"Shannon Brown<br>Custom Tats added by `#Lonny Luberts",
		"category"=>"Village",
		"download"=>"core_module",
		"settings"=>array(
			"Petra the Tattoo Artist - Settings,title",
			"healnumber"=>"Number of days required to heal a tattoo,range,4,50,1|20",
			"cost"=>"Cost in gems per tattoo,range,2,30,1|10",
			"petraloc"=>"Where does the Petra appear,location|".getsetting("villagename", LOCATION_FIELDS),
			"customtat1"=>"Custom Tattoo 1,text|hummingbird",
			"customtat1color"=>"Custom Tattoo 1 Color Version,text|`!hummingbird",
		),
		"prefs"=>array(
			"Petra the Tattoo Artist User Preferences,title",
			"tatnumber"=>"Number of tattoos the player has,int|0",
			"lasttat"=>"Days remaining for a player to heal,int|0",
			"tatname"=>"List of recent tattoos,viewonly|none",
		)
	);
	return $info;
}

function petra_install(){
	module_addhook("newday");
	module_addhook("bioinfo");
	module_addhook("village");
	module_addhook("changesetting");
	return true;
}

function petra_uninstall(){
	return true;
}

function petra_colortat($tname) {
	$custom1 = get_module_setting('customtat1');
	switch ($tname) {
	case 'heart': return "`4heart"; break;
	case 'daisy': return "`!daisy"; break;
	case 'rose': return "`\$rose"; break;
	case 'skull': return "`)skull"; break;
	case 'symbol': return "`%symbol"; break;
	case 'star': return "`^star"; break;
	case 'swan': return "`&swan"; break;
	case 'snake': return "`@snake"; break;
	case 'tiger': return "`7t`Qi`7g`Qe`7r"; break;
	//custom tat code
	case $custom1: return get_module_setting('customtat1color'); break;
	default: return "`)$tname";
	}
}

function petra_dohook($hookname,$args){
	global $session;

	switch($hookname){
	case "changesetting":
		if ($args['setting'] == "villagename") {
			if ($args['old'] == get_module_setting("petraloc")) {
				set_module_setting("petraloc", $args['new']);
			}
		}
		break;
	case "newday":
		$lasttat=get_module_pref("lasttat");
		if ($lasttat>1) {
			// decrement the healing days remaining (time since the tattoo
			// was bought).
			$lasttat--;
			set_module_pref("lasttat",$lasttat);
			output("`n`6Your tattoo is gradually healing.`n");
			output("`&You `\$lose`& some hitpoints due to the pain.`n");
			$session['user']['hitpoints']*=0.8;
		} elseif (get_module_pref("lasttat")==1) {
			// tattoo is healed
			set_module_pref("lasttat",0);
			output("`n`6Your tattoo has finally healed.`n");
		}
		break;
	case "village":
		if ($session['user']['location'] == get_module_setting("petraloc")){
			tlschema($args['schemas']['marketnav']);
			addnav($args['marketnav']);
			tlschema();
			addnav("Petra's Tattoo Parlor","runmodule.php?module=petra");
		}
		break;
	case "bioinfo":
		$tname = get_module_pref("tatname", "petra", $args['acctid']);
		if (!$tname) break;
		$biotatname = @unserialize($tname);
		if (!is_array($biotatname)) {
			if ($tname == "none") $biotatname = array();
			else $biotatname = array($tname=>1);
			set_module_pref("tatname", serialize($biotatname), "petra",
					$args['acctid']);
		}
		if (count($biotatname)) {
			output_notl("`n");
			foreach($biotatname as $name=>$val) {
				output("`&%s `3 has been tattooed with a %s`3.`n",
						$args['name'], petra_colortat($name));
			}
			output_notl("`n");
		}
		break;
	}	
	return $args;
}

function petra_run() {
	global $session;
	$op = httpget("op");
	$cost=get_module_setting("cost");
	$costfeather=$cost-1;
	$lasttat=get_module_pref("lasttat");
	$feather=get_module_pref("feather","kitchen");

	$tname = get_module_pref("tatname");
	$curtats = unserialize($tname);
	if (!is_array($curtats)) {
		if ($tname == "none") $curtats = array();
		else $curtats = array($tname=>1);
		set_module_pref("tatname", serialize($curtats));
	}

	page_header("Tattoo Parlor");
	output("`&`c`bPetra, the Ink Artist`b`c");

	if ($op == "" && $lasttat==0) {
		output("`7A tiny elf looks up from a sketch pad and smiles at you as you enter the shop.");
		output("On every spare surface are intricate designs of animals, symbols, and words, in every color of the rainbow.`n`n");
		output("Noting your interest, he calls for Petra, who emerges from behind a screen.`n`n");
		output("`7Petra's blue hair surrounds her like an aura. \"`&Good day, dear %s`&! So lovely to see you. What beautiful work of art can I grace you with today?`7\"`n`n", $session['user']['name']);
		output("`7The elf gets back to his drawing as you gaze about and try to decide.");
		// different tattoos go here
		$canbuy = 0;
		if (!$curtats['heart']) {
			addnav(array("Get %s", translate_inline("Heart")),
					"runmodule.php?module=petra&op=yes&des=heart");
			$canbuy = 1;
		}
		if (!$curtats['daisy']) {
			addnav(array("Get %s", translate_inline("Blue Daisy")),
					"runmodule.php?module=petra&op=yes&des=daisy");
			$canbuy = 1;
		}
		if (!$curtats['rose']) {
			addnav(array("Get %s", translate_inline("Rose")),
					"runmodule.php?module=petra&op=yes&des=rose");
			$canbuy = 1;
		}
		if (!$curtats['skull']) {
			addnav(array("Get %s", translate_inline("Skull")),
					"runmodule.php?module=petra&op=yes&des=skull");
			$canbuy = 1;
		}
		if (!$curtats['symbol']) {
			addnav(array("Get %s", translate_inline("Symbol")),
					"runmodule.php?module=petra&op=yes&des=symbol");
			$canbuy = 1;
		}
		if (!$curtats['star']) {
			addnav(array("Get %s", translate_inline("Star")),
					"runmodule.php?module=petra&op=yes&des=star");
			$canbuy = 1;
		}
		if (!$curtats['swan']) {
			addnav(array("Get %s", translate_inline("Swan")),
					"runmodule.php?module=petra&op=yes&des=swan");
			$canbuy = 1;
		}
		if (!$curtats['snake']) {
			addnav(array("Get %s", translate_inline("Snake")),
					"runmodule.php?module=petra&op=yes&des=snake");
			$canbuy = 1;
		}
		if (!$curtats['tiger']) {
			addnav(array("Get %s", translate_inline("Tiger")),
					"runmodule.php?module=petra&op=yes&des=tiger");
			$canbuy = 1;
		}
		//custom tat code
		if (!$curtats[get_module_setting('customtat1')]) {
			addnav(array("Get %s",ucfirst(get_module_setting('customtat1'))),
					"runmodule.php?module=petra&op=yes&des=".
					get_module_setting('customtat1'));
			$canbuy = 1;
		}
		if ($canbuy) {
			addnav("Don't get a tattoo today",
					"runmodule.php?module=petra&op=nope");
		} else {
			output("`7You browse through the designs, but see that you already have one of each of the designs adorning your body.");
			output("`7Perhaps Petra will come up with some new designs soon, but for now, you will have to be content with the ink you already wear.`n`n");
			output("`7Sadly, you return to town.");
			villagenav();
		}

	} elseif ($op == "") {
		output("`7You step in to admire the lovely designs on the walls, but you are still sore from the last inking you had done.`n`n");
		output("Perhaps once you are healed, you can think about getting another.");
		villagenav();
	} elseif ($op == "yes") {
		$tatname = httpget("des");
		output("`7Petra nods with a smile. \"`&Of course! It's a lovely design, isn't it? I quite like it myself, in fact, I may just get my assistant to ink it on my...");
		if ($feather==1 && $session['user']['gems']>=$costfeather) {
			output("Oh! That feather in your hair, it's lovely! I'll do the %s for 1 gem less, if you'll give me that feather!\"`n`n", $tatname);
			addnav(array("Pay %s gems", $cost),"runmodule.php?module=petra&op=full&des=$tatname");
			addnav(array("Give %s gems and the feather", $costfeather),"runmodule.php?module=petra&op=give&des=$tatname");
			addnav("Don't get a tattoo today","runmodule.php?module=petra&op=nope");
		} elseif ($session['user']['gems']>=$cost) {
			output("Oh! Never mind! Now, let's get this %s organised. The price is %s gems.\"`n`n", $tatname, $cost);
			addnav(array("Pay %s gems", $cost),"runmodule.php?module=petra&op=full&des=$tatname");
			addnav("Don't get a tattoo today","runmodule.php?module=petra&op=nope");
		} else {
			output("Oh! Never mind! Now, let's get this %s organised. The price is %s gems.`n`n", $tatname, $cost);
			output("`7Petra stops as she sees your face fall.`n`n");
			output("\"`&You don't have %s gems, do you? Oh dear, I am sorry. Perhaps you'll come back another time?\"`n`n",$cost);
			output("`7You realise you don't have much choice in the matter.");
			villagenav();
		}
	} elseif ($op == "nope") {
		output("`7You're more than a little afraid of getting a tattoo, and you just want to get out of there.`n`n");
		output("`7Petra thanks you for visiting.");
		villagenav();
	} else {
		$tatname = httpget("des");
		// Cannot do this above or else they will be marked as tattood
		// even if they couldn't pay.
		$curtats[$tatname] = 1;
		set_module_pref("tatname",serialize($curtats));
		if ($op == "give") {
			set_module_pref("feather",0,"kitchen");
			// need to check if module exists on this server
			if (is_module_active("matthias")){
				$astute=get_module_pref("astuteness","matthias");
				$astute--;
				set_module_pref("astuteness",$astute,"matthias");
			}
			$session['user']['gems']-=$costfeather;
			debuglog("spent $costfeather gems and a feather on a tattoo of a $tatname");
		} else { 
			$session['user']['gems']-=$cost;
			debuglog("spent $cost gems on a tattoo of a $tatname");
		}

		$session['user']['hitpoints']*=0.2;
		if ($session['user']['hitpoints']<=0) {
			$session['user']['hitpoints']=1;
		}

		// add a bit of randomness to the healing.
		$healnumber= get_module_setting("healnumber");
		$lasttat=$healnumber + e_rand(-3, +3);
		if ($lasttat < 3) $lasttat = 3;

		$tatnumber=get_module_pref("tatnumber");
		$tatnumber++;
		set_module_pref("tatnumber",$tatnumber);
		output("`7Petra motions to a chair, and you desperately try to relax as she leans over you.");
		output("You can't bear to look as she begins to work.");
		output("You're suffering for the art!`n`n");
		output("`7The work feels like it takes years, as you feel her every movement.");
		output("Finally, she announces that the %s is finished.", $tatname);
		output("You survey your reddened skin, and although it is very painful, you're quite pleased with the result.`n`n");
		output("`7You're far too sore to move very fast until it heals properly.`n`n");
		output("You `\$lose`7 a lot of your hitpoints!`n`n");
		set_module_pref("lasttat",$lasttat);
		villagenav();
	}
	page_footer();
}
?>

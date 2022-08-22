<?php

function specialtydrunkenmaster_getmoduleinfo() {
	$info = array(
		"name" => "Specialty - Drunken Master",
		"author" => "Aelia, with Iori",
		"version" => "1.21+jt",
		"download" => "http://dragonprime.net",
		"category" => "AW-Specialties",
		"settings"=> array(
			"Specialty - Drunken Master Settings,title",
			"mindk"=>"How many DKs do you need before the specialty is available?,int|10",
			"cost"=>"How many donation points does each dk as the specialty cost?,int|100",
			"dmlevelincrement"=>"How many successful level-ups result in one Class Proficiency point?,int|14",
			"uselogscale"=>"Set this to make the abilities come quicker in the earlier levels.,bool,0",
		),
		"prefs" => array(
			"Specialty - Drunken Master User Prefs,title",
			"dmlevelpoints"=>"Current level points - x points = 1 level,int|0",
			"dmlevel"=>"Current Drunken Master level,int|0",
			"cooldown"=>"Cooldown for skills,int|0",
			"buys"=>"How many times this specialty can be chosen,int|0",
		),
		"requires"=>array(
			"drinks"=>"1.1|By John J. Collins, from the core download",
		),
	);
	return $info;
}

function specialtydrunkenmaster_install() {
	module_addhook("choose-specialty");
	module_addhook("set-specialty");
	module_addhook_priority("fightnav-specialties",49);
	module_addhook("apply-specialties");
	module_addhook("incrementspecialty");
	module_addhook("specialtynames");
	module_addhook("specialtycolor");
	module_addhook("pointsdesc");
	module_addhook("newday");
	module_addhook_priority("soberup", 49, "specialtydrunkenmaster_dohook2");
	module_addhook("soberup");
	module_addhook("battle");
	module_addhook("battle-victory");
	module_addhook_priority("charstats",51);
	module_addhook("classhof");
	module_addhook("classhofspec");
	module_addhook("drunkenhof");
	module_addhook("drunkenhofspec");
	module_addhook("lodge");
	return true;
}

function specialtydrunkenmaster_uninstall() {
	// Reset the specialty of anyone who had this specialty so they get to
	// rechoose at new day
	$sql = "UPDATE " . db_prefix("accounts") . " SET specialty='' WHERE specialty='AW-DM'";
	db_query($sql);
	return true;
}




function specialtydrunkenmaster_dohook($hookname,$args) {
	global $session,$resline;
	$spec = "AW-DM";
	$name = "Drunken Master";
	$ccode = "`Q";
	$ccode2 = "`q";
	switch ($hookname) {
		case "classhof":
		case "drunkenhof":
			addnav("$ccode$name`0","runmodule.php?module=classhof&op=hof&spec=$spec");
			$args['any'] = 1;
		break;

		case "classhofspec":
		case "drunkenhofspec":
			if ($args['spec'] == $spec) {
				$args['modulename']="specialtydrunkenmaster";
				$args['levelname']="dmlevel";
				$args['classname']=$name;
				$args['ccode']="$ccode";
			}
		break;
		

		case "charstats":
			if ($session['user']['specialty'] == $spec) {
				require_once("modules/drunkenmaster/charstats.php");
			}
		break;

		case "pointsdesc":
			$args['count']++;
			$format = $args['format'];
			if (get_module_setting("mindk")) {
				$str = sprintf_translate("`7After %s DKs, the `QDrunken Master `7specialty is available for %s points per time selected.", get_module_setting("mindk"), get_module_setting("cost"));
			} else {
				$str = sprintf_translate("`7The `QDrunken Master `7specialty is available for %s points per time selected.", get_module_setting("cost"));
			}
			output($format, $str, true);
		break;

		case "lodge":
			if (get_module_pref("dmlevel") <= 56 && (!get_module_setting("mindk") ||
					($session['user']['dragonkills'] >= get_module_setting("mindk")))) {
				addnav(array("Drunken Master (%s points)", get_module_setting("cost")), "runmodule.php?module=specialtydrunkenmaster&op=purchase");
			}
		break;

		case "newday":
			if ($session['user']['specialty'] == $spec) {
				require_once("modules/drunkenmaster/newday.php");
			}
		break;

		case "soberup":
			if ($session['user']['specialty'] == $spec) {
				require_once("modules/drunkenmaster/soberup.php");
			}
		break;

		case "choose-specialty":
			if ($session['user']['specialty'] == "") {
				$buys = get_module_pref("buys");
				$dmlevel = get_module_pref("dmlevel");
				if ($buys > 0 || $dmlevel > 56) {
					addnav("$ccode$name`0","newday.php?setspecialty=".$spec."$resline");
					$t1 = translate_inline("Drinking like a fiend without a thought to your age.");
					$t2 = appoencode(translate_inline("$ccode$name`0"));
					rawoutput("<a href='newday.php?setspecialty=$spec$resline'>$t1 ($t2)</a><br>");
					addnav("","newday.php?setspecialty=$spec$resline");
				}
			}
		break;

		case "set-specialty":
			if ($session['user']['specialty'] == $spec) {
				$dmlevel = get_module_pref("dmlevel");
				page_header($name);
				output("`%There is more to drinking than just getting drunk.");
				output("You realized this truism early in life.");
				output("Through rigorous training, you have increased your drinking capacity to near superhuman levels.");
				output("Along with your stamina you have also developed a unique way of fighting that makes you hard to hit, and even harder to predict.");
				output("Behold the power of the $ccode"."Drunken Master`%.`0");

				output("`n`n`i`7Your current Class Proficiency Rating for this specialty is `^%s`7.`0`i`n`n",$dmlevel);

				if ($dmlevel > 56) {
					output ("`nYou have learned all there is to be a Drunken Master. You may now select this specialty at any time.");
				} else {
					increment_module_pref("buys", -1);
					$buys = get_module_pref("buys");
					if ($buys == 1) {
						output("`n`nYou have purchased the ability to be a Drunken Master %s more time.", get_module_pref("buys"));
					} else {
						output("`n`nYou have purchased the ability to be a Drunken Master %s more times.", get_module_pref("buys"));
					}
				}
			}
		break;

		case "specialtycolor":
				$args[$spec] = $ccode;
		break;

		case "specialtynames":
			$args[$spec] = translate_inline($name);
		break;


		case "incrementspecialty":
			if ($session['user']['specialty'] == $spec) {
				require_once("modules/drunkenmaster/incrementspecialties.php");
			}
		break;

		case "battle-victory":
			set_module_pref("cooldown",0);
		break;

		case "battle":
			if ($session['user']['specialty'] == $spec) {
				$cooldown = get_module_pref("cooldown");
				if ($cooldown > 0) {
					$auto = httpget('auto');
					if ($auto == 'five') $cooldown -= 5;
					else if ($auto == 'ten') $cooldown -= 10;
					else $cooldown--;
					if ($cooldown < 0)
						$cooldown = 0;
					set_module_pref("cooldown",$cooldown);
				}
			}
		break;

		case "fightnav-specialties":
			if ($session['user']['specialty'] == $spec) {
				$cooldown = get_module_pref("cooldown");
				if ($cooldown > 0) {
					require_once("modules/drunkenmaster/cooldownnavs.php");
				}
				else {
					require_once("modules/drunkenmaster/nocooldownnavs.php");
				}
			}
		break;

		case "apply-specialties":
			$skill = httpget('skill');
			if ($skill==$spec) {
				require_once("modules/drunkenmaster/apply-specialties.php");
			}
		break;
	}
	return $args;
}

function specialtydrunkenmaster_dohook2($hookname,$args) {
	global $session,$resline;
	$spec = "AW-DM";
	switch ($hookname) {
		case "soberup":
			if ($session['user']['specialty'] == $spec)
				$args['soberval'] += (1 - $args['soberval'] )/2;
		break;
	}
	return $args;
}

function specialtydrunkenmaster_run()
{
	global $session;
	$cost = get_module_setting("cost");
	$op = httpget("op");

	if ($op == "purchase") {
		page_header("Hunter's Lodge");
		output("`7J. C. Petersen eyes you up and down quickly, \"`&The path of a Drunken Master weaves weirdly through the world.  The token to train from their master will cost you %s points for every time you walk that path.  Are you sure you wish to stumble down that path?`7\"`n`n", $cost);
		addnav("Buy the token?");
		addnav("Yes", "runmodule.php?module=specialtydrunkenmaster&op=confirm");
		addnav("No", "lodge.php");
	} elseif ($op == "confirm") {
		page_header("Hunter's Lodge");
		addnav("L?Return to the Lodge", "lodge.php");
		$points = $session['user']['donation'] -
			$session['user']['donationspent'];
		if ($points >= $cost) {
			output("`7J. C. Petersen hands you a small brass token with the face of `)Blarg`7 on one side and the words '1 lesson' stamped on the other.  \"`&There you go.  When next you look for a path to follow, the way of the Drunken Master will be available to you.`7\"");
			increment_module_pref("buys");
			$buys = get_module_pref("buys");
			$session['user']['donationspent'] += $cost;
			output("`n`nYou may now follow the path of the Drunken Master %s times.", $buys);
		} else {
			output("`7J. C. Petersen frowns slightly, \"`&You do not seem to have the required %s points.  Please return later when you have acquired them and I'll be happy to sell you a token.`7\"", $cost);
		}
	}
	page_footer();
}

?>

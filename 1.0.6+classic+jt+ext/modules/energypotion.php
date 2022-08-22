<?php

function energypotion_getmoduleinfo()
{
	$info = array(
		"name"=>"Energy Potion",
		"category"=>"Lodge",
		"author"=>"JT Traub",
		"version"=> "1.0",
		"download"=>"",
		"requires"=>array(
		),
		"settings"=>array(
			"Lodge Energy Potion Settings,title",
			"fcost"=>"Donation cost for buying potion,int|30",
			"fcount"=>"Max number of fights,int|3",
			"zchance"=>"Percent chance for 0 fights,int|10",
			"echance"=>"Percent chance for an extra fight,int|20",
		),
		"prefs"=>array(
			"Lodge Energy Potion Preferences,title",
			"numbought"=>"How many potions has the user bought over time?,int|0",
		),
	);
   return $info;
}

function energypotion_install()
{
   module_addhook("lodge");
   module_addhook("pointsdesc");
   return true;
}

function energypotion_uninstall()
{
   return true;
}

function energypotion_dohook($hookname, $args)
{
	global $session;

	switch ($hookname) {
	case "pointsdescr":
		$format = $args['format'];
		$args['count']++;
		$str = translate("For %s points you can buy a potion granting an additional forest fight with a small chance of getting none and a chance of getting multiple extra fights.");
		$str = sprintf($str, get_module_setting("fcost"));
		output($format, $str, true);
		$args['count']++;
		break;
	case "lodge":
		addnav(array("Energy Potion (%s points)",
					get_module_setting("fcost")),
				"runmodule.php?module=energypotion&op=purchase");
		break;
	}
	return $args;
}

function energypotion_run()
{
	global $session;
	$op = httpget('op');
	page_header("Hunter's Lodge");
	switch($op) {
	case "purchase":
		$cost = get_module_setting("fcost");
		$pointsavailable = $session['user']['donation'] -
			$session['user']['donationspent'];
		if ($pointsavailable < $cost) {
			output("`7J. C. Petersen smiles sorrowfully at you, `\$\"Perhaps you should return after you have more credit.\"`0");
			break;
		}
		output("`\$\"Would you like to purchase a potion of additional energy for another fight today?  Be warned you have a small chance of this having no effect, but it might also boost your energy enough for multiple fights!\"");
		addnav(array("Purchase potion?"));
		addnav("Yes", "runmodule.php?module=energypotion&op=yes");
		addnav("No", "runmodule.php?module=energypotion&op=no");
		break;
	case "yes":
		$cost = get_module_setting("fcost");
		if ($cost) {
			increment_module_pref("numbought");
			$session['user']['donationspent'] += $cost;
			output("`7J. C. Petersen smiles at you.`n");
			output("`\$\"Bottoms up.\"`7 he says.`0`n");
			$val = e_rand(0, 100);
			$zchance = get_module_setting("zchance");
			$echance = get_module_setting("echance");
			debug("Val: $val");
			$num = 1;
			if ($val <= $zchance) {
				$num = 0;
			} else if ($val <= $zchance+$echance) {
				$num++;
				$i = 2;
				$max = get_module_setting("fcount");
				while($i < $max) {
					$val = e_rand(0, 100);
					debug("Val: $val");
					if ($val < $echance) {
						$num++;
					}
					$i++;
				}
			}
			if ($num) {
				output("You down the potion and feel more energized!`n");
				if ($num > 1) {
					output("`&You gain `^%d`& more forest fights.", $num);
				} else {
					output("`&You gain `^%d`& more forest fight.", $num);
				}
			} else {
				output("You down the potion and feel no different!");
			}
			$session['user']['turns']+= $num;
		} else {
			output("`7J. C. Petersen looks at you oddly and seems to have a hard time understanding you.`0");
		}
		break;
	case "no":
		output("`7J. C. Petersen frowns slightly, `\$\"Well, do come back if you change your mind.\"`0");
		break;
	}
	addnav("Return");
	addnav("L?Return to the Lodge","lodge.php");
	page_footer();
}

?>

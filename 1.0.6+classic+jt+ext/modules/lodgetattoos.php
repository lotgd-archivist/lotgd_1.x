<?php

function lodgetattoos_getmoduleinfo()
{
	$info = array(
		"name"=>"Lodge Tattoos",
		"category"=>"Lodge",
		"author"=>"JT Traub",
		"version"=> "1.0",
		"download"=>"",
		"requires"=>array(
			"petra"=>"1.3|by Shannon Brown and dying, distributed with the core code"
		),
		"settings"=>array(
			"Lodge Tattoo Settings,title",
			"pdragcost"=>"Donation cost for the Purple Dragon tattoo,int|1000",
			"billcost"=>"Donation cost for the $100 bill tattoo,int|2500",
			"pentcost"=>"Donation cost for the pentacle tatoo,int|3000",
			"billgems"=>"How much additional chance does the bill tattoo give for finding gems?,range,1,100,1|4",
			"pdraggold"=>"What percent extra gold does the dragon tattoo give?,range,1,100,1|5",
			"pentpct"=>"What percent of favor does the pentacle let you keep across DK?,range,1,100,1|50",
		),
		"prefs"=>array(
			"Lodge Tattoo Preferences,title",
			"boughtpdrag"=>"Has the user purchased the purple dragon,bool|0",
			"boughtbill"=>"Has the user purchased the $100 bill,bool|0",
			"boughtpent"=>"Has the user purchased the pentacle,bool|0",
		),
	);
   return $info;
}

function lodgetattoos_install()
{
   module_addhook("petraavail");
   module_addhook("petraadded");
   module_addhook("petradescr");
   module_addhook("petracolor");
   module_addhook("petrareadablename");
   module_addhook("lodge");
   module_addhook("pointsdesc");
   module_addhook("alter-gemchance");
   module_addhook("creatureencounter");
   module_addhook("newday");
   module_addhook("dk-preserve");
   return true;
}

function lodgetattoos_uninstall()
{
   return true;
}

function lodgetattoos_dohook($hookname, $args)
{
	global $session;

	switch ($hookname) {
	case "dk-preserve":
		$tname = get_module_pref("tatname", "petra");
		$curtats = unserialize($tname);
		if (!is_array($curtats)) {
			if ($tname == "none") $curtats = array();
			else $curtats = array($tname=>1);
			set_module_pref("tatname", serialize($curtats), "petra");
		}
		if(isset($curtats['pentacle'])) {
			$args['deathpower'] = 1;
			$death = $session['user']['deathpower'];
			$pct = get_module_setting("pentpct");
			$death = (int)($death * ($pct/100));
			$session['user']['deathpower'] = $death;
		}
		break;
	case "petraavail":
		// Make sure we have Petra.  We *should* but, just in case.
		require_once("modules/petra.php");
		if (!isset($args['tattoos']['purpdrag']) &&
				get_module_pref('boughtpdrag')) {
			petra_addnav("purpdrag", translate_inline("Purple Dragon"));
			$args['canbuy'] = 1;
		}
		if (!isset($args['tattoos']['bill100']) &&
				get_module_pref('boughtbill')) {
			petra_addnav("bill100", translate_inline("100\$ bill"));
			$args['canbuy'] = 1;
		}
		if (!isset($args['tattoos']['pentacle']) &&
				get_module_pref('boughtpent')) {
			petra_addnav("pentacle", translate_inline("Mystic Pentacle"));
			$args['canbuy'] = 1;
		}
		break;
	case "petraadded":
		break;
	case "petrareadablename":
		if ($args['tatname'] == "bill100") {
			$args['readname'] = "$100 bill";
		} elseif ($args['tatname'] == "purpdrag") {
			$args['readname'] = "purple dragon";
		} elseif ($args['tatname'] == "pentacle") {
			$args['readname'] = "mystic pentacle";
		}
		break;
	case "petradescr":
		if ($args['tname']=="purpdrag") {
			/* [add translated purple dragon description] */
			$args['tattoodescr'] = "";
		} elseif ($args['tname']=="bill100") {
			/* [add translated 100$ bill description] */
			$args['tattoodescr'] = "";
		} elseif ($args['tname'] == "pentacle") {
			/* [add translated mystic pentacle description] */
			$args['tattoodescr'] = "";
		}
		break;
	case "petracolor":
		if ($args['tname']=="purpdrag") {
			$args['colortat'] = "`5purple dragon `3coiled around `)a sword`0";
		} elseif ($args['tname']=="bill100") {
			$args['colortat'] = "`2a `@\$100`2 bill with `%Moon`5Childe`2's face`0";
		} elseif ($args['tname']=="pentacle") {
			$args['colortat'] = "`!a blue `#glowing `!pentacle`0";
		}
		break;
	case "pointsdescr":
		$format = $args['format'];
		$args['count']++;
		$str = translate("For %s points you can buy a Purple Dragon Tattoo design which Petra can then create.");
		$str = sprintf($str, get_module_setting("pdragcost"));
		output($format, $str, true);
		$args['count']++;
		$str = translate("For %s points you can buy a Tattoo of a \$100 Bill which Petra can then create.");
		$str = sprintf($str, get_module_setting("billcost"));
		output($format, $str, true);
		$args['count']++;
		$str = translate("For %s points you can buy a Tattoo of a Mystical Pentacle which Petra can then create.");
		$str = sprintf($str, get_module_setting("pentcost"));
		output($format, $str, true);
		break;
	case "lodge":
		if (!get_module_pref("boughtpdrag")) {
			addnav(array("Purple Dragon Tattoo (%s points)",
						get_module_setting("pdragcost")),
					"runmodule.php?module=lodgetattoos&op=purchase&tat=1");
		}
		if (!get_module_pref("boughtbill")) {
			addnav(array("\$100 Bill Tattoo (%s points)",
						get_module_setting("billcost")),
					"runmodule.php?module=lodgetattoos&op=purchase&tat=2");
		}
		if (!get_module_pref("boughtpent")) {
			addnav(array("Mystical Pentacle Tattoo (%s points)",
						get_module_setting("pentcost")),
					"runmodule.php?module=lodgetattoos&op=purchase&tat=3");
		}
		break;
	case "alter-gemchance":
		$tname = get_module_pref("tatname", "petra");
		$curtats = unserialize($tname);
		if (!is_array($curtats)) {
			if ($tname == "none") $curtats = array();
			else $curtats = array($tname=>1);
			set_module_pref("tatname", serialize($curtats), "petra");
		}
		if(isset($curtats['bill100'])) {
			$args['chance'] += get_module_setting("billgems");
		}
		break;
	case "creatureencounter":
		$tname = get_module_pref("tatname", "petra");
		$curtats = unserialize($tname);
		if (!is_array($curtats)) {
			if ($tname == "none") $curtats = array();
			else $curtats = array($tname=>1);
			set_module_pref("tatname", serialize($curtats), "petra");
		}
		if(isset($curtats['purpdrag'])) {
			$gain = (100 - get_module_setting("pdraggold"))/100;
			$args['creaturegold'] += round($args['creaturegold']*$gain,0);
		}
		break;
	case "newday":
		$tname = get_module_pref("tatname", "petra");
		$curtats = unserialize($tname);
		if (!is_array($curtats)) {
			if ($tname == "none") $curtats = array();
			else $curtats = array($tname=>1);
			set_module_pref("tatname", serialize($curtats), "petra");
		}
		if(isset($curtats['bill100'])) {
			output("`n`&You feel `@Generous`&.`n");
			apply_buff("generosity",array(
				"name"=>"`@Generosity`0",
				"rounds"=>-1,
				"schema"=>"module-lodgetattoos",
				"allowinpvp"=>1,
				"allowintrain"=>1,
				"defmod"=>"(<defense>?(1+(1/<defense>)):0)",
			));
		}
		break;
	}
	return $args;
}

function lodgetattoos_run()
{
	global $session;
	$op = httpget('op');
	page_header("Hunter's Lodge");
	switch($op) {
	case "purchase":
		$tat = httpget('tat');
		$cost = 0;
		if ($tat == 1) {
			$cost = get_module_setting("pdragcost");
			$tname = "Purple Dragon";
		} elseif ($tat == 2) {
			$cost = get_module_setting("billcost");
			$tname = "\$100 Bill";
		} elseif ($tat == 3) {
			$cost = get_module_setting("pentcost");
			$tname = "Mystical Pentacle";
		}
		$pointsavailable = $session['user']['donation'] -
			$session['user']['donationspent'];
		if ($pointsavailable < $cost) {
			output("`7J. C. Petersen smiles sorrowfully at you, `\$\"Perhaps you should return after you have more credit.\"`0");
			break;
		}
		if ($tat == 1) {
			output("`7J. C. Petersen holds up a drawing of a glorious purple dragon twined around a sword.`n`n");
		} elseif ($tat == 2) {
			output("`7J. C. Petersen holds up a ink sketch of a \$100 bill.`n`n");
		} elseif ($tat == 3) {
			output("`7J. C. Petersen holds up a ink sketch of a ornate mystical pentacle.`n`n");
		} else {
			output("`7J. C. Petersen looks at you oddly and seems to have a hard time understanding you.`0");
			break;
		}
		output("`\$\"Would you like to purchase a copy of this tattoo design?  Petra would be happy to make it into a tattoo for her normal fee if you do.\"");
		addnav(array("Purchase %s", $tname));
		addnav("Yes", "runmodule.php?module=lodgetattoos&op=yes&tat=$tat");
		addnav("No", "runmodule.php?module=lodgetattoos&op=no&tat=$tat");
		break;
	case "yes":
		$tat = httpget('tat');
		$cost = 0;
		if ($tat == 1) {
			$cost = get_module_setting("pdragcost");
			$tname = "Purple Dragon";
			$setting = "boughtpdrag";
		} elseif ($tat == 2) {
			$cost = get_module_setting("billcost");
			$tname = "\$100 Bill";
			$setting = "boughtbill";
		} elseif ($tat == 3) {
			$cost = get_module_setting("pentcost");
			$tname = "Mystical Pentacle";
			$setting = "boughtpent";
		}
		if ($cost) {
			set_module_pref($setting, 1);
			$session['user']['donationspent'] += $cost;
			output("`7J. C. Petersen smiles at you.`n");
			output("`\$\"I'm sure you'll really like your new tattoo once Petra is finished.  You should visit her soon to get the work started!\"`0");
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

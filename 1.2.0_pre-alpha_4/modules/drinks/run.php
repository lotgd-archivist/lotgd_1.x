<?php
function drinks_run_private(){
	require_once("modules/drinks/misc_functions.php");
	require_once("lib/partner.php");

	global $user;
	$partner = get_partner();
	$act = httpget('act');
	if ($act=="editor"){
		drinks_editor();
	}elseif ($act=="buy"){
		$texts = drinks_gettexts();
		$drinktext = modulehook("drinks-text",$texts);

		tlschema($drinktext['schemas']['title']);
		page_header($drinktext['title']);
		rawoutput("<span style='color: #9900FF'>");
		output_notl("`c`b");
		output($drinktext['title']);
		output_notl("`b`c");
		tlschema();
		$drunk = get_module_pref("drunkeness");
		$end = ".";
		if ($drunk > get_module_setting("maxdrunk"))
			$end = ",";
		tlschema($drinktext['schemas']['demand']);
		$remark = translate_inline($drinktext['demand']);
		$remark = str_replace("{lover}",$partner."`0", $remark);
		$remark = str_replace("{barkeep}", $drinktext['barkeep']."`0", $remark);
		tlschema();
		output_notl("%s$end", $remark);
		$drunk = get_module_pref("drunkeness");
		if ($drunk > get_module_setting("maxdrunk")) {
			tlschema($drinktext['schemas']['toodrunk']);
			$remark = translate_inline($drinktext['toodrunk']);
 			tlschema();
			$remark = str_replace("{lover}",$partner."`0", $remark);
			$remark = str_replace("{barkeep}", $drinktext['barkeep']."`0", $remark);
			output($remark);
			tlschema();
		} else {
			$sql = "SELECT * FROM " . DB::prefix("drinks") . " WHERE drinkid='".httpget('id')."'";
			$result = DB::query($sql);
			$row = DB::fetch_assoc($result);
			$drinkcost = $user->level * $row['costperlevel'];
			if ($user->gold >= $drinkcost) {
				$drunk = get_module_pref("drunkeness");
				$drunk += $row['drunkeness'];
				set_module_pref("drunkeness", $drunk);
				$user->gold -= $drinkcost;
				debuglog("spent $drinkcost on {$row['name']}");
				$remark = str_replace("{lover}",$partner."`0", $row['remarks']);
				$remark = str_replace("{barkeep}", $drinktext['barkeep']."`0", $remark);
				if (count($drinktext['drinksubs']) > 0) {
					$keys = array_keys($drinktext['drinksubs']);
					$vals = array_values($drinktext['drinksubs']);
					$remark = preg_replace($keys, $vals, $remark);
				}
				output($remark);
				output_notl("`n`n");
				if ($row['harddrink']) {
					$drinks = get_module_pref("harddrinks");
					set_module_pref("harddrinks", $drinks+1);
				}
				$givehp = 0;
				$giveturn = 0;
				if ($row['hpchance']>0 || $row['turnchance']>0) {
					$tot = $row['hpchance'] + $row['turnchance'];
					$c = e_rand(1, $tot);
					if ($c <= $row['hpchance'] && $row['hpchance']>0)
						$givehp = 1;
					else
						$giveturn = 1;
				}
				if ($row['alwayshp']) $givehp = 1;
				if ($row['alwaysturn'])  $giveturn = 1;
				if ($giveturn) {
					$turns = e_rand($row['turnmin'], $row['turnmax']);
					$oldturns = $user->turns;
					$user->turns += $turns;
					// sanity check
					if ($user->turns < 0)
						$user->turns = 0;

					if ($oldturns < $user->turns) {
						output("`&You feel vigorous!`n");
					} else if ($oldturns > $user->turns) {
						output("`&You feel lethargic!`n");
					}
				}
				if ($givehp) {
					$oldhp = $user->hitpoints;

					// Check for percent increase first
					if ($row['hppercent'] != 0.0) {
						$hp = round($user->maxhitpoints *
								($row['hppercent']/100), 0);
					} else {
						$hp = e_rand($row['hpmin'], $row['hpmax']);
					}
					$user->hitpoints += $hp;
					// Sanity check
					if ($user->hitpoints < 1)
						$user->hitpoints = 1;

					if ($oldhp < $user->hitpoints) {
						output("`&You feel healthy!`n");
					} else if ($oldhp > $user->hitpoints) {
						output("`&You feel sick!`n");
					}
				}
				$buff = array();
				$buff['name'] = $row['buffname'];
				$buff['rounds'] = $row['buffrounds'];
				if ($row['buffwearoff'])
					$buff['wearoff'] = $row['buffwearoff'];
				if ($row['buffatkmod'])
					$buff['atkmod'] = $row['buffatkmod'];
				if ($row['buffdefmod'])
					$buff['defmod'] = $row['buffdefmod'];
				if ($row['buffdmgmod'])
					$buff['dmgmod'] = $row['buffdmgmod'];
				if ($row['buffdmgshield'])
					$buff['damageshield'] = $row['buffdmgshield'];
				if ($row['buffroundmsg'])
					$buff['roundmsg'] = $row['buffroundmsg'];
				if ($row['buffeffectmsg'])
					$buff['effectmsg'] = $row['buffeffectmsg'];
				if ($row['buffeffectnodmgmsg'])
					$buff['effectnodmgmsg'] = $row['buffeffectnodmgmsg'];
				if ($row['buffeffectfailmsg'])
					$buff['effectfailmsg'] = $row['buffeffectfailmsg'];
				$buff['schema'] = "module-drinks";
				apply_buff('buzz',$buff);
			} else {
				output("You don't have enough money.  How can you buy %s if you don't have any money!?!", $row['name']);
			}
		}
		rawoutput("</span>");
		if ($drinktext['return']>""){
			tlschema($drinktext['schemas']['return']);
			addnav($drinktext['return'],$drinktext['returnlink']);
			tlschema();
		}else{
			tlschema($drinktext['schemas']['return']);
			addnav("I?Return to the Inn","inn.php");
			addnav(array("Go back to talking to %s`0", getsetting("barkeep", "`tCedrik")),"inn.php?op=bartender");
			tlschema();
		}
		require_once("lib/villagenav.php");
		villagenav();
		page_footer();
	}
}
?>

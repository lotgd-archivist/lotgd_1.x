<?php
// addnews ready
// translator ready
// mail ready
require_once("common.php");
require_once("lib/forest.php");
require_once("lib/fightnav.php");
require_once("lib/http.php");
require_once("lib/taunt.php");
require_once("lib/events.php");

tlschema("forest");

$fight = false;
page_header("The Forest");
$dontdisplayforestmessage=handle_event("forest");

$op = httpget("op");

$battle = false;

if ($op=="run"){
	if (e_rand()%3 == 0){
		output ("`c`b`&You have successfully fled your opponent!`0`b`c`n");
		$op="";
		httpset('op', "");
	}else{
		output("`c`b`\$You failed to flee your opponent!`0`b`c");
	}
}

if ($op=="dragon"){
	require_once("lib/partner.php");
	addnav("Enter the cave","dragon.php");
	addnav("Run away like a baby","inn.php?op=fleedragon");
	output("`\$You approach the blackened entrance of a cave deep in the forest, though the trees are scorched to stumps for a hundred yards all around.");
	output("A thin tendril of smoke escapes the roof of the cave's entrance, and is whisked away by a suddenly cold and brisk wind.");
	output("The mouth of the cave lies up a dozen feet from the forest floor, set in the side of a cliff, with debris making a conical ramp to the opening.");
	output("Stalactites and stalagmites near the entrance trigger your imagination to inspire thoughts that the opening is really the mouth of a great leech.`n`n");
	output("You cautiously approach the entrance of the cave, and as you do, you hear, or perhaps feel a deep rumble that lasts thirty seconds or so, before silencing to a breeze of sulfur-air which wafts out of the cave.");
	output("The sound starts again, and stops again in a regular rhythm.`n`n");
	output("You clamber up the debris pile leading to the mouth of the cave, your feet crunching on the apparent remains of previous heroes, or perhaps hors d'oeuvres.`n`n");
	output("Every instinct in your body wants to run, and run quickly, back to the warm inn, and the even warmer %s`\$.", get_partner());
	output("What do you do?`0");
	$session['user']['seendragon']=1;
}

if ($op=="search"){
	checkday();
	if ($session['user']['turns']<=0){
		output("`\$`bYou are too tired to search the forest any longer today.  Perhaps tomorrow you will have more energy.`b`0");
		$op="";
		httpset('op', "");
	}else{
		modulehook("forestsearch", array());
		$args = array(
			'soberval'=>0.9,
			'sobermsg'=>"`&Faced with the prospect of death, you sober up a little.`n",
			'schema'=>'forest');
		modulehook("soberup", $args);
		if (module_events("forest", getsetting("forestchance", 15)) != 0) {
			if (!checknavs()) {
				// If we're showing the forest, make sure to reset the special
				// and the specialmisc
				$session['user']['specialinc'] = "";
				$session['user']['specialmisc'] = "";
				$dontdisplayforestmessage=true;
				$op = "";
				httpset("op", "");
			} else {
				page_footer();
			}
		}else{
			$session['user']['turns']--;
			$battle=true;
			if (e_rand(0,2)==1){
				$plev = (e_rand(1,5)==1?1:0);
				$nlev = (e_rand(1,3)==1?1:0);
			}else{
				$plev=0;
				$nlev=0;
			}
			$type = httpget('type');
			if ($type=="slum"){
				$nlev++;
				output("`\$You head for the section of forest you know to contain foes that you're a bit more comfortable with.`0`n");
			}
			if ($type=="thrill"){
				$plev++;
				output("`\$You head for the section of forest which contains creatures of your nightmares, hoping to find one of them injured.`0`n");
			}
			$extrabuff = 0;
			if ($type=="suicide"){
				if ($session['user']['level'] <= 7) {
					$plev += 1;
					$extrabuf = .25;
				} elseif ($session['user']['level'] < 14) {
					$plev+=2;
					$extrabuf = 0;
				} else {
					$plev++;
					$extrabuff = .4;
				}
				output("`\$You head for the section of forest which contains creatures of your nightmares, looking for the biggest and baddest ones there.`0`n");
			}

			$targetlevel = ($session['user']['level'] + $plev - $nlev );
			if ($targetlevel<1) $targetlevel=1;
			$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE creaturelevel = $targetlevel AND forest=1 ORDER BY rand(".e_rand().") LIMIT 1";
			$result = db_query($sql);
			restore_buff_fields();
			if (db_num_rows($result) == 0) {
				// There is nothing in the database to challenge you, let's
				// give you a doppleganger.
				$badguy = array();
				$badguy['creaturename']=
					"An evil doppleganger of ".$session['user']['name'];
				$badguy['creatureweapon']=$session['user']['weapon'];
				$badguy['creaturelevel']=$session['user']['level'];
				$badguy['creaturegold']=0;
				$badguy['creatureexp'] =
				round($session['user']['experience']/10, 0);
				$badguy['creaturehealth']=$session['user']['maxhitpoints'];
				$badguy['creatureattack']=$session['user']['attack'];
				$badguy['creaturedefense']=$session['user']['defense'];
			} else {
				$badguy = db_fetch_assoc($result);
				require_once("lib/forestoutcomes.php");
				$badguy = buffbadguy($badguy);
				// Okay, they are thrillseeking, let's give them a bit extra
				// exp and gold.
				if ($type == "thrill") {
					// 10% more experience
					$badguy['creatureexp'] =
						round($badguy['creatureexp']*1.1, 0);
					// 10% more gold
					$badguy['creaturegold'] =
						round($badguy['creaturegold']*1.1, 0);
				}
				if ($type == "suicide") {
					// Okay, suicide fights give even more rewards, but
					// are much harder
					// 25% more experience
					$badguy['creatureexp'] =
						round($badguy['creatureexp']*1.25, 0);
					// 25% more gold
					$badguy['creaturegold'] =
						round($badguy['creaturegold']*1.25, 0);
					// Now, make it tougher.
					$mul = 1.25 + $extrabuff;
					$badguy['creatureattack'] =
						round($badguy['creatureattack']*$mul, 0);
					$badguy['creaturedefense'] =
						round($badguy['creaturedefense']*$mul, 0);
					$badguy['creaturehealth'] =
						round($badguy['creaturehealth']*$mul, 0);
					// And mark it as an 'elite' troop.
					$prefixs = array(
							"Elite",
							"Dangerous",
							"Lethal",
							"Savage",
							"Deadly",
							"Malevolent",
							"Malignant");
					$prefixs = translate_inline($prefixs);
					$key = array_rand($prefixs);
					$prefix = $prefixs[$key];
					$badguy['creaturename'] =
						$prefix . " " . $badguy['creaturename'];
				}
			}
			calculate_buff_fields();
			$badguy['playerstarthp']=$session['user']['hitpoints'];
			$badguy['diddamage']=0;
			$badguy['type'] = 'forest';
			$session['user']['badguy']=createstring($badguy);
		}
	}
}

if ($op=="fight" || $op=="run"){
	$battle=true;
}

if ($battle){

	require_once("battle.php");

	if ($victory){
		require_once("lib/forestoutcomes.php");
		$op="";
		httpset('op', "");
		forestvictory($badguy);
		$dontdisplayforestmessage=true;
	}elseif($defeat){
		require_once("lib/forestoutcomes.php");
		forestdefeat($badguy);
	}else{
		fightnav();
	}
}

if ($op==""){
	// Need to pass the variable here so that we show the forest message
	// sometimes, but not others.
	forest($dontdisplayforestmessage);
}
page_footer();
?>

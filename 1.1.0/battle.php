<?php
// translator ready
// addnews ready
// mail ready
require_once("lib/bell_rand.php");
require_once("common.php");
require_once("lib/http.php");
require_once("lib/battle-buffs.php");
require_once("lib/battle-skills.php");
require_once("lib/buffs.php");

//just in case we're called from within a function.  Yuck is this ugly.
global $badguy,$session,$creatureattack,$creatureatkmod, $beta;
global $creaturedefmod,$adjustment,$defmod,$atkmod,$buffset,$atk,$def;

tlschema("battle");

$count = 1;
$auto = httpget('auto');
if ($auto == 'full') $count = -1;
else if ($auto == 'five') $count = 5;
else if ($auto == 'ten') $count = 10;

$badguy = unserialize($session['user']['badguy']);

$adjustment = ($session['user']['level']/$badguy['creaturelevel']);
if ($badguy['type'] == 'pvp' ||
		(isset($badguy['noadjust']) && $badguy['noadjust'] == 1)) {
	$adjustment=1;
}

$op=httpget("op");
$skill=httpget("skill");
$l=httpget("l");

if ($op=="fight"){
	apply_skill($skill,$l);
}




if ($badguy['creaturehealth']>0 && $session['user']['hitpoints']>0) {
	output ("`\$`c`b~ ~ ~ Fight ~ ~ ~`b`c`0");
    if (isset($badguy['hidehitpoints']) && $badguy['hidehitpoints']==1) {
        $health="?";
    } else {
        $health=$badguy['creaturehealth'];
    }
	modulehook("battle", $badguy);
	output("`@You have encountered `^%s`@ which lunges at you with `%%s`@!`0`n`n",$badguy['creaturename'],$badguy['creatureweapon']);
	if ($session['user']['alive']){
		output("`2Level: `6%s`0`n",$badguy['creaturelevel']);
	}else{
		output("`2Level: `6Undead`0`n");
	}

	output("`2`bStart of round:`b`n");
	if ($session['user']['alive']){
		output("`2%s`2's Hitpoints: `6%s`0`n",$badguy['creaturename'],$health);
		output("`2YOUR Hitpoints: `6%s`0`n",$session['user']['hitpoints']);
	}else{
		output("`2%s`2's Soulpoints: `6%s`0`n",$badguy['creaturename'],$health);
		output("`2YOUR Soulpoints: `6%s`0`n",$session['user']['hitpoints']);
	}
}

suspend_buffs((($badguy['type'] == 'pvp')?"allowinpvp":false));

// Now that the buflist is sane, see if we should add in the bodyguard.
$inn = (int)httpget('inn');
if ($badguy['type']=='pvp' && $inn==1) {
	apply_bodyguard($badguy['bodyguardlevel']);
}

// People are abusing this
// So.. let's try something a bit sneakier.
/*
$act = httpget('act');
if (e_rand(1,3)==1 && ($op=="search" || ($badguy['type']=='pvp' && $act=="attack"))) {
	if ($badguy['type']=='pvp'){
		output("`b`^%s`\$'s skill allows them to get the first round of attack!`0`b`n`n",$badguy['creaturename']);
	}else{
		output("`b`^%s`\$ surprises you and gets the first round of attack!`0`b`n`n",$badguy['creaturename']);
	}
	$op="run";
	$surprised=true;
}else{
	if ($op=="search")
		output("`b`\$Your skill allows you to get the first attack!`0`b`n`n");
	$surprised=false;
}
*/

// Let's try this instead.  Biggest change is that it adds possibility of
// being surprised to all fights.
if (!array_key_exists('didsurprise',$badguy) || !$badguy['didsurprise']) {
	// By default, surprise is 50/50
	$surprised = e_rand(0, 1) ? true : false;
	// Now, adjust for slum/thrill
	$type = httpget('type');
	if ($type == 'slum' || $type == 'thrill') {
		$num = e_rand(0, 2);
		$surprised = true;
		if ($type == 'slum' && $num != 2)
			$surprised = false;
		if (($type == 'thrill' || $type=='suicide') && $num == 2)
			$surprised = false;
	}
	if (!$surprised) {
		output("`b`\$Your skill allows you to get the first attack!`0`b`n`n");
	} else {
		if ($badguy['type'] == 'pvp') {
			output("`b`^%s`\$'s skill allows them to get the first round of attack!`0`b`n`n",$badguy['creaturename']);
		}else{
			output("`b`^%s`\$ surprises you and gets the first round of attack!`0`b`n`n",$badguy['creaturename']);
		}
		$op = "run";
	}
	$badguy['didsurprise']=1;
}


// Run through as many rounds as needed.
do {
	//we need to restore and calculate here to reflect changes that happen throughout the course of multiple rounds.
	restore_buff_fields();
	calculate_buff_fields();
	// Run the beginning of round buffs (this also calculates all modifiers)
	$buffset = activate_buffs("roundstart");

	$creaturedefmod=$buffset['badguydefmod'];
	$creatureatkmod=$buffset['badguyatkmod'];
	$atkmod=$buffset['atkmod'];
	$defmod=$buffset['defmod'];

	if ($op=="fight" || $op=="run"){
		// Grab an initial roll.
		$roll = rolldamage();
		if ($op=="fight"){
			$ggchancetodouble = $session['user']['dragonkills'];
			$bgchancetodouble = $session['user']['dragonkills'];

			if ($badguy['creaturehealth']>0 &&
					$session['user']['hitpoints']>0) {
				$buffset = activate_buffs("offense");
				do {
					$creaturedmg = $roll['creaturedmg'];
					if ($badguy['creaturehealth']<=0 ||
							$session['user']['hitpoints']<=0){
						$creaturedmg = 0;
						$selfdmg = 0;
						break;
					}else{
						if ($badguy['type'] != "pvp")
							$creaturedmg =
								report_power_move($atk, $creaturedmg);
						if ($creaturedmg==0){
							output("`4You try to hit `^%s`4 but `\$MISS!`n",$badguy['creaturename']);
							process_dmgshield($buffset['dmgshield'], 0);
							process_lifetaps($buffset['lifetap'], 0);
						}else if ($creaturedmg<0){
							output("`4You try to hit `^%s`4 but are `\$RIPOSTED `4for `\$%s`4 points of damage!`n",$badguy['creaturename'],(0-$creaturedmg));
							$badguy['diddamage']=1;
							$session['user']['hitpoints']+=$creaturedmg;
							process_dmgshield($buffset['dmgshield'],-$creaturedmg);
							process_lifetaps($buffset['lifetap'],$creaturedmg);
						}else{
							output("`4You hit `^%s`4 for `^%s`4 points of damage!`n",$badguy['creaturename'],$creaturedmg);
							$badguy['creaturehealth']-=$creaturedmg;
							process_dmgshield($buffset['dmgshield'],-$creaturedmg);
							process_lifetaps($buffset['lifetap'],$creaturedmg);
						}
					}
					$r = mt_rand(0,100);
					if ($r < $ggchancetodouble &&
							$badguy['creaturehealth']>0 &&
							$session['user']['hitpoints']>0){
						$additionalattack = true;
						$ggchancetodouble -= ($r+5);
						$roll = rolldamage();
					}else{
						$additionalattack = false;
					}
				} while($additionalattack);
			}
		}else if($op=="run" && !$surprised){
			output("`4You are too busy trying to run away like a cowardly dog to try to fight `^%s`4.`n",$badguy['creaturename']);
		}

		//Need to insert this here because of auto-fighting!
		$op = "fight";

		// We need to check both user health and creature health. Otherwise
		// the user can win a battle by a RIPOSTE after he has gone <= 0 HP.
		//-- Gunnar Kreitz
		if ($badguy['creaturehealth']>0 && $session['user']['hitpoints']>0){
			$buffset = activate_buffs("defense");
			do {
				$selfdmg = $roll['selfdmg'];
				if ($badguy['creaturehealth']<=0 &&
						$session['user']['hitpoints']<=0){
					$creaturedmg = 0;
					$selfdmg = 0;
					break;
				}else{
					if ($selfdmg==0){
						output("`^%s`4 tries to hit you but `^MISSES!`n",$badguy['creaturename']);
						process_dmgshield($buffset['dmgshield'], 0);
						process_lifetaps($buffset['lifetap'], 0);
					}else if ($selfdmg<0){
						output("`^%s`4 tries to hit you but you `^RIPOSTE`4 for `^%s`4 points of damage!`n",$badguy['creaturename'],(0-$selfdmg));
						$badguy['creaturehealth']+=$selfdmg;
						process_lifetaps($buffset['lifetap'], -$selfdmg);
						process_dmgshield($buffset['dmgshield'], $selfdmg);
					}else{
						output("`^%s`4 hits you for `\$%s`4 points of damage!`n",$badguy['creaturename'],$selfdmg);
						$session['user']['hitpoints']-=$selfdmg;
						process_dmgshield($buffset['dmgshield'], $selfdmg);
						process_lifetaps($buffset['lifetap'], -$selfdmg);
						$badguy['diddamage']=1;
					}
				}
				$r = mt_rand(0,100);
				if (!isset($bgchancetodouble)) $bgchancetodouble = 0;
				if ($r < $bgchancetodouble &&
						$badguy['creaturehealth']>0 &&
						$session['user']['hitpoints']>0){
					$additionalattack = true;
					$bgchancetodouble -= ($r+5);
					$roll = rolldamage();
				}else{
					$additionalattack = false;
				}
			} while ($additionalattack);
		}
	}

	expire_buffs();
	$creaturedmg=0;
	$selfdmg=0;

	if ($count != 1 && $session['user']['hitpoints'] > 0 &&
			$badguy['creaturehealth'] > 0)
		output("`2`bNext round:`b`n");

	if ($badguy['creaturehealth']<=0){
		$victory=true;
		$defeat=false;
		break;
	}else{
		if ($session['user']['hitpoints']<=0){
			$defeat=true;
			$victory=false;
			break;
		}else{
			$defeat=false;
			$victory=false;
		}
	}
	if ($count != -1) $count--;
} while ($count > 0 || $count == -1);

$badguy['creaturehealth'] = round($badguy['creaturehealth'],0);
if ($session['user']['hitpoints']>0 && $badguy['creaturehealth']>0 &&
		($op=="fight" || $op=="run")){
	output("`2`bEnd of Round:`b`n");
    if (isset($badguy['hidehitpoints']) && $badguy['hidehitpoints']==1) {
        $health="?";
    } else {
        $health=$badguy['creaturehealth'];
    }	
	if ($session['user']['alive']){
		output("`2%s`2's Hitpoints: `6%s`0`n",$badguy['creaturename'],$health);
		output("`2YOUR Hitpoints: `6%s`0`n",$session['user']['hitpoints']);
	}else{
		output("`2%s`2's Soulpoints: `6%s`0`n",$badguy['creaturename'],$health);
		output("`2YOUR Soulpoints: `6%s`0`n",$session['user']['hitpoints']);
	}
}

if ($session['user']['hitpoints'] < 0) $session['user']['hitpoints'] = 0;

if ($victory || $defeat){
	// expire any buffs which cannot persist across fights and
	// unsuspend any suspended buffs
	unsuspend_buffs((($badguy['type']=='pvp')?"allowinpvp":false));
	global $output;
	$badguy['fightoutput'] = $output;
	if ($victory) {
		$badguy = modulehook("battle-victory",$badguy);
		if ($badguy['type']=='pvp') $badguy = modulehook("battle-pvp-victory",$badguy);
	} elseif ($defeat) {
		$badguy = modulehook("battle-defeat",$badguy);
		if ($badguy['type']=='pvp') $badguy = modulehook("battle-pvp-defeat",$badguy);
	}
	unset($badguy['fightoutput']);
}
$session['user']['badguy']=createstring($badguy);
tlschema();
?>

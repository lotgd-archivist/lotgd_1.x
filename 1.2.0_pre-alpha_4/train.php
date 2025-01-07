<?php
//addnews ready
// mail ready
// translator ready
require_once("common.php");
require_once("lib/systemmail.php");
require_once("lib/increment_specialty.php");
require_once("lib/fightnav.php");
require_once("lib/http.php");
require_once("lib/taunt.php");
require_once("lib/substitute.php");
require_once("lib/villagenav.php");
require_once("lib/experience.php");

tlschema("train");

page_header("Bluspring's Warrior Training");

$battle = false;
$victory = false;
$defeat = false;

output("`b`cBluspring's Warrior Training`c`b");

$mid = httpget("master");
if ($mid) {
	$sql = "SELECT * FROM " . DB::prefix("masters") . " WHERE creatureid=$mid";
} else {
	$sql = "SELECT max(creaturelevel) as level FROM " . DB::prefix("masters") . " WHERE creaturelevel <= " . $user->level;
	$res = DB::query($sql);
	$row = DB::fetch_assoc($res);
	$l = $row['level'];

	$sql = "SELECT * FROM " . DB::prefix("masters") . " WHERE creaturelevel=$l ORDER BY RAND(".e_rand().") LIMIT 1";
}

$result = DB::query($sql);
if (DB::num_rows($result) > 0 && $user->level < Settings::getsetting('maxlevel', 15)){
	$master = DB::fetch_assoc($result);
	$mid = $master['creatureid'];
	$master['creaturename'] = stripslashes($master['creaturename']);
	$master['creaturewin'] = stripslashes($master['creaturewin']);
	$master['creaturelose'] = stripslashes($master['creaturelose']);
	$master['creatureweapon'] = stripslashes($master['creatureweapon']);
	if ($master['creaturename'] == "Gadriel the Elven Ranger" &&
			$user->race == "Elf") {
		$master['creaturewin'] = "You call yourself an Elf?? Maybe Half-Elf! Come back when you've been better trained.";
		$master['creaturelose'] = "It is only fitting that another Elf should best me.  You make good progress.";
	}
	$level = $user->level;
	$dks = $user->dragonkills;
	$exprequired=exp_for_next_level($level, $dks);

	$op = httpget('op');
	if ($op==""){
		checkday();
		output("The sound of conflict surrounds you.  The clang of weapons in grisly battle inspires your warrior heart. ");
		output("`n`n`^%s stands ready to evaluate you.`0",
				$master['creaturename']);
		addnav("Question Master","train.php?op=question&master=$mid");
		addnav("M?Challenge Master","train.php?op=challenge&master=$mid");
		if ($user->superuser & SU_DEVELOPER) {
			addnav("Superuser Gain level","train.php?op=challenge&victory=1&master=$mid");
		}
		villagenav();
	}else if($op=="challenge"){
		if (httpget('victory')) {
			$victory=true;
			$defeat=false;
			if ($user->experience < $exprequired)
				$user->experience = $exprequired;
			$user->seenmaster = 0;
		}
		if ($user->seenmaster){
			output("You think that, perhaps, you've seen enough of your master for today, the lessons you learned earlier prevent you from so willingly subjecting yourself to that sort of humiliation again.");
			villagenav();
		}else{
			/* OK, let's fix the multimaster thing */
			$user->seenmaster = 1;
			debuglog("Challenged master, setting seenmaster to 1");

			if ($user->experience>=$exprequired){
				$dk = 0;
				restore_buff_fields();
				foreach($user->dragonpoints as $val) {
					if ($val=="at" || $val=="de") $dk++;
				}
				$dk += (int)(($user->maxhitpoints -
					($user->level*10))/5);

				$dk = round($dk * .33, 0);

				$atkflux = e_rand(0, $dk);
				$atkflux = min($atkflux, round($dk*.25));
				$defflux = e_rand(0, ($dk-$atkflux));
				$defflux = min($defflux, round($dk*.25));

				$hpflux = ($dk - ($atkflux+$defflux)) * 5;
				debug("DEBUG: $dk modification points total.`n");
				debug("DEBUG: +$atkflux allocated to attack.`n");
				debug("DEBUG: +$defflux allocated to defense.`n");
				debug("DEBUG: +".($hpflux/5)."*5 to hitpoints`n");
				calculate_buff_fields();

				$master['creatureattack']+=$atkflux;
				$master['creaturedefense']+=$defflux;
				$master['creaturehealth']+=$hpflux;
				$attackstack['enemies'][0] = $master;
				$attackstack['options']['type'] = 'train';
				$user->badguy=createstring($attackstack);

				$battle=true;
				if ($victory) {
					$badguy = unserialize($user->badguy);
					$badguy = $badguy['enemies'][0];
					output("With a flurry of blows you dispatch your master.`n");
				}
			}else{
				output("You ready your %s and %s and approach `^%s`0.`n`n",$user->weapon,$user->armor,$master['creaturename']);
				output("A small crowd of onlookers has gathered, and you briefly notice the smiles on their faces, but you feel confident. ");
				output("You bow before `^%s`0, and execute a perfect spin-attack, only to realize that you are holding NOTHING!", $master['creaturename']);
				output("`^%s`0 stands before you holding your weapon.",$master['creaturename']);
				output("Meekly you retrieve your %s, and slink out of the training grounds to the sound of boisterous guffaws.",$user->weapon);
				villagenav();
			}
		}
	}else if($op=="question"){
		checkday();
		output("You approach `^%s`0 timidly and inquire as to your standing in the class.",$master['creaturename']);
		if($user->experience>=$exprequired){
			output("`n`n`^%s`0 says, \"Gee, your muscles are getting bigger than mine...\"",$master['creaturename']);
		}else{
			output("`n`n`^%s`0 states that you will need `%%s`0 more experience before you are ready to challenge him in battle.",$master['creaturename'],($exprequired-$user->experience));
		}
		if(Settings::getsetting('displayweaponswarnings', false)) {
			output("`n`n`^ %s `0 carefully inspects your gear and `3 says,", $master['creaturename']);
			if($user->armordef >= $user->level) {
				switch(e_rand(1, 3)) {
					case 1:
						output("`n`n`& Your`0 %s `& is sufficient for leveling up", $user->armor);
					break;
					case 2:
						output("`n`n`& That`0 %s `& of yours is suitable for the next level.", $user->armor);
					break;
					case 3:
						output("`n`n`& Very nice `0 %s `& you have there, it will serve you well.", $user->armor);
					break;
				}
			} else {
				switch(e_rand(1, 5)) {
					case 1:
						output("`n`n`\$ Your`0 %s `\$is weak, you should get better armor. ", $user->armor);
					break;
					case 2:
						output("`n`n`\$ Your`0 %s `\$ my have served you well before but it wont now. ", $user->armor);
					break;
					case 3:
						output("`n`n`\$ Your`0 %s `\$is too weak to advance to the next level, go get better armor for yourself. ", $user->armor);
					break;
					case 4:
						output("`n`n`\$ Nice`0 %s `\$ you have there but it is not strong enough. I suggest you seek something better. ", $user->armor);
					break;
					case 5:
						output("`n`n`\$ That`0 %s `\$ is not good enough, you need to have better armor. ", $user->armor);
					break;
				}
			}
			if($user->weapondmg >= $user->level) {
				switch(e_rand(1, 3)) {
					case 1:
						output("`n`n`& Your`0 %s `& is sufficient for leveling up", $user->weapon);
					break;
					case 2:
						output("`n`n`& Sturdy `0 %s `& you have there, fair enough for the next level.", $user->weapon);
					break;
					case 3:
						output("`n`n`& Your `0 %s `& looks decent enough for the next level.", $user->weapon);
					break;
				}
			} else {
				switch(e_rand(1, 5)) {
					case 1:
						output("`n`n`\$ Your`0 %s `\$is too weak, you need to upgrade your weapon. ", $user->weapon);
					break;
					case 2:
						output("`n`n`\$ I admire  your`0 %s `\$ however it is not good enough, go get something better. ", $user->weapon);
					break;
					case 3:
						output("`n`n`\$ Very nice`0 %s `\$ you have there but you really ought to have something better. ", $user->weapon);
					break;
					case 4:
						output("`n`n`\$ A`0 %s `\$ is not the best weapon for the next level, go get something better. ", $user->weapon);
					break;
					case 5:
						output("`n`n`\$ Your`0 %s `\$ is not suitable for advancement to the next level. ", $user->weapon);
					break;
				}
			}
		}
		addnav("Question Master","train.php?op=question&master=$mid");
		addnav("M?Challenge Master","train.php?op=challenge&master=$mid");
		if ($user->superuser & SU_DEVELOPER) {
			addnav("Superuser Gain level","train.php?op=challenge&victory=1&master=$mid");
		}
		villagenav();
	}else if($op=="autochallenge"){
		addnav("Fight Your Master","train.php?op=challenge&master=$mid");
		output("`^%s`0 has heard of your prowess as a warrior, and heard of rumors that you think you are so much more powerful than he that you don't even need to fight him to prove anything. ",$master['creaturename']);
		output("His ego is understandably bruised, and so he has come to find you.");
		output("`^%s`0 demands an immediate battle from you, and your own pride prevents you from refusing the demand.",$master['creaturename']);
		if ($user->hitpoints<$user->maxhitpoints){
			output("`n`nBeing a fair person, your master gives you a healing potion before the fight begins.");
			$user->hitpoints=$user->maxhitpoints;
		}
		modulehook("master-autochallenge");
		if (getsetting('displaymasternews',1)) addnews("`3%s`3 was hunted down by their master, `^%s`3, for being truant.",$user->name,$master['creaturename']);
	}
	if ($op=="fight"){
		$battle=true;
	}
	if ($op=="run"){
		output("`\$Your pride prevents you from running from this conflict!`0");
		$op="fight";
		$battle=true;
	}

	if($battle){
		require_once("lib/battle-skills.php");
		require_once("lib/extended-battle.php");
		suspend_buffs('allowintrain', "`&Your pride prevents you from using extra abilities during the fight!`0`n");
		suspend_companions("allowintrain");
		if (!$victory) {
			require_once("battle.php");
		}
		if ($victory){
			$badguy['creaturelose']=substitute_array($badguy['creaturelose']);
			output_notl("`b`&");
 	 	 	output($badguy['creaturelose']);
 	 	 	output_notl("`0`b`n");
 	 	 	output("`b`\$You have defeated %s!`0`b`n",$badguy['creaturename']);

			$user->level++;
			$user->maxhitpoints+=10;
			$user->soulpoints+=5;
			$user->attack++;
			$user->defense++;
			// Fix the multimaster bug
			if (getsetting("multimaster", 1) == 1) {
				$user->seenmaster=0;
				debuglog("Defeated master, setting seenmaster to 0");
			}
			output("`#You advance to level `^%s`#!`n",$user->level);
			output("Your maximum hitpoints are now `^%s`#!`n",$user->maxhitpoints);
			output("You gain an attack point!`n");
			output("You gain a defense point!`n");
			if ($user->level<Settings::getsetting('maxlevel', 15)){
				output("You have a new master.`n");
			}else{
				output("None in the land are mightier than you!`n");
			}
			if ($user->referer>0 && ($user->level>=getsetting("referminlevel",4) || $user->dragonkills > 0) && $user->refererawarded<1){
				$sql = "UPDATE " . DB::prefix("accounts") . " SET donation=donation+".getsetting("refereraward",25)." WHERE acctid={$user->referer}";
				DB::query($sql);
				$user->refererawarded=1;
				$subj=array("`%One of your referrals advanced!`0");
				$body=array("`&%s`# has advanced to level `^%s`#, and so you have earned `^%s`# points!", $user->name, $user->level, getsetting("refereraward", 25));
				systemmail($user->referer,$subj,$body);
			}
			increment_specialty("`^");

			// Level-Up companions
			// We only get one level per pageload. So we just add the per-level-values.
			// No need to multiply and/or substract anything.
			if (getsetting("companionslevelup", 1) == true) {
				$newcompanions = $companions;
				foreach ($companions as $name => $companion) {
					$companion['attack'] = $companion['attack'] + $companion['attackperlevel'];
					$companion['defense'] = $companion['defense'] + $companion['defenseperlevel'];
					$companion['maxhitpoints'] = $companion['maxhitpoints'] + $companion['maxhitpointsperlevel'];
					$companion['hitpoints'] = $companion['maxhitpoints'];
					$newcompanions[$name] = $companion;
				}
				$companions = $newcompanions;
			}

			invalidatedatacache("list.php-warsonline");

			addnav("Question Master","train.php?op=question");
			addnav("M?Challenge Master","train.php?op=challenge");
			if ($user->superuser & SU_DEVELOPER) {
				addnav("Superuser Gain level","train.php?op=challenge&victory=1");
			}
			villagenav();
			if ($user->age == 1) {
 	 	 	 	if (getsetting('displaymasternews',1)) addnews("`%%s`3 has defeated ".($user->sex?"her":"his")." master, `%%s`3 to advance to level `^%s`3 after `^1`3 day!!", $user->name,$badguy['creaturename'],$user->level);
 	 	 	} else {
 	 	 	 	if (getsetting('displaymasternews',1)) addnews("`%%s`3 has defeated ".($user->sex?"her":"his")." master, `%%s`3 to advance to level `^%s`3 after `^%s`3 days!!", $user->name,$badguy['creaturename'],$user->level,$user->age);
 	 	 	}
			if ($user->hitpoints < $user->maxhitpoints)
				$user->hitpoints = $user->maxhitpoints;
			modulehook("training-victory", $badguy);
		}elseif($defeat){
			$taunt = select_taunt_array();

			if (getsetting('displaymasternews',1)) addnews("`%%s`5 has challenged their master, %s and lost!`n%s",$user->name,$badguy['creaturename'],$taunt);
			$user->hitpoints=$user->maxhitpoints;
			output("`&`bYou have been defeated by `%%s`&!`b`n",$badguy['creaturename']);
			output("`%%s`\$ halts just before delivering the final blow, and instead extends a hand to help you to your feet, and hands you a complementary healing potion.`n",$badguy['creaturename']);
			$badguy['creaturewin']=substitute_array($badguy['creaturewin']);
			output_notl("`^`b");
			output($badguy['creaturewin']);
			output_notl("`b`0`n");
			addnav("Question Master","train.php?op=question&master=$mid");
			addnav("M?Challenge Master","train.php?op=challenge&master=$mid");
			if ($user->superuser & SU_DEVELOPER) {
				addnav("Superuser Gain level","train.php?op=challenge&victory=1&master=$mid");
			}
			villagenav();
			modulehook("training-defeat", $badguy);
		}else{
		  fightnav(false,false, "train.php?master=$mid");
		}
		if ($victory || $defeat) {
			unsuspend_buffs('allowintrain', "`&You now feel free to make use of your buffs again!`0`n");
			unsuspend_companions("allowintrain");
		}
	}
}else{
	checkday();
	output("You stroll into the battle grounds.");
	output("Younger warriors huddle together and point as you pass by.");
	output("You know this place well.");
	output("Bluspring hails you, and you grasp her hand firmly.");
	output("There is nothing left for you here but memories.");
	output("You remain a moment longer, and look at the warriors in training before you turn to return to the village.");
	villagenav();
}
page_footer();

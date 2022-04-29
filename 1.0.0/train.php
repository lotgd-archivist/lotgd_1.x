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

output("`b`cBluspring's Warrior Training`c`b");
$sql = "SELECT * FROM " . db_prefix("masters") . " WHERE creaturelevel = ".$session['user']['level'];
$result = db_query($sql);
if (db_num_rows($result) > 0){
	$master = db_fetch_assoc($result);
	if ($master['creaturename'] == "Gadriel the Elven Ranger" && $session['user']['race'] == "Elf") {
		$master['creaturewin'] = "You call yourself an Elf?? Maybe Half-Elf! Come back when you've been better trained.";
		$master['creaturelose'] = "It is only fitting that another Elf should best me.  You make good progress.";
	}
	$level = $session['user']['level'];
	$dks = $session['user']['dragonkills'];
	$exprequired=exp_for_next_level($level, $dks);

	$op = httpget('op');
	if ($op==""){
		checkday();
		output("The sound of conflict surrounds you.  The clang of weapons in grisly battle inspires your warrior heart. ");
		output("`n`nYour master is `^%s`0.",$master['creaturename']);
		addnav("Question Master","train.php?op=question");
		addnav("Challenge Master","train.php?op=challenge");
		if ($session['user']['superuser'] & SU_DEVELOPER) {
			addnav("Superuser Gain level","train.php?op=challenge&victory=1");
		}
		villagenav();
	}else if($op=="challenge"){
		if (httpget('victory')) {
			$victory=true;
			$defeat=false;
			if ($session['user']['experience'] < $exprequired)
				$session['user']['experience'] = $exprequired;
			$session['user']['seenmaster'] = 0;
			debuglog("Defeated master, setting seenmaster to 0");
		}
		if ($session['user']['seenmaster']){
			output("You think that, perhaps, you've seen enough of your master for today, the lessons you learned earlier prevent you from so willingly subjecting yourself to that sort of humiliation again.");
			villagenav();
		}else{
			if (getsetting("multimaster",1)==0) {
				$session['user']['seenmaster'] = 1;
				debuglog("Challenged master, setting seenmaster to 1");
			}
			if ($session['user']['experience']>=$exprequired){
				$dk = 0;
				restore_buff_fields();
				while(list($key, $val)=each($session['user']['dragonpoints'])) {
					if ($val=="at" || $val=="de") $dk++;
				}
				$dk += (int)(($session['user']['maxhitpoints'] -
					($session['user']['level']*10))/5);

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
				$master['type'] = 'train';
				$session['user']['badguy']=createstring($master);
 
				$battle=true;
				if ($victory) {
					$badguy = createarray($session['user']['badguy']);
					output("With a flurry of blows you dispatch your master.`n");
				}
			}else{
				output("You ready your %s and %s and approach `^%s`0.`n`n",$session['user']['weapon'],$session['user']['armor'],$master['creaturename']);
				output("A small crowd of onlookers has gathered, and you briefly notice the smiles on their faces, but you feel confident. ");
				output("You bow before `^%s`0, and execute a perfect spin-attack, only to realize that you are holding NOTHING!", $master['creaturename']);
				output("`^%s`0 stands before you holding your weapon.",$master['creaturename']);
				output("Meekly you retrieve your %s, and slink out of the training grounds to the sound of boisterous guffaws.",$session['user']['weapon']);
				villagenav();
				$session['user']['seenmaster']=1;
				debuglog("Challenged master w/o enough exp, setting seenmaster to 1");
			}
		}
	}else if($op=="question"){
		checkday();
		output("You approach `^%s`0 timidly and inquire as to your standing in his class.",$master['creaturename']);
		if($session['user']['experience']>=$exprequired){
			output("`n`n`^%s`0 says, \"Gee, your muscles are getting bigger than mine...\"",$master['creaturename']);
		}else{
			output("`n`n`^%s`0 states that you will need `%%s`0 more experience before you are ready to challenge him in battle.",$master['creaturename'],($exprequired-$session['user']['experience']));
		}
		addnav("Question Master","train.php?op=question");
		addnav("Challenge Master","train.php?op=challenge");
		if ($session['user']['superuser'] & SU_DEVELOPER) {
			addnav("Superuser Gain level","train.php?op=challenge&victory=1");
		}
		villagenav();
	}else if($op=="autochallenge"){
		addnav("Fight Your Master","train.php?op=challenge");
		output("`^%s`0 has heard of your prowess as a warrior, and heard of rumors that you think you are so much more powerful than he that you don't even need to fight him to prove anything. ",$master['creaturename']);
		output("His ego is understandably bruised, and so he has come to find you.");
		output("`^%s`0 demands an immediate battle from you, and your own pride prevents you from refusing his demand.",$master['creaturename']);
		if ($session['user']['hitpoints']<$session['user']['maxhitpoints']){
			output("`n`nBeing a fair person, your master gives you a healing potion before the fight begins.");
			$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		}
		modulehook("master-autochallenge");
		addnews("`3%s`3 was hunted down by their master, `^%s`3, for being truant.",$session['user']['name'],$master['creaturename']);
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
		suspend_buffs('allowintrain',
				"`&Your pride prevents you from using extra abilities during the fight!`0`n");
		if (!$victory) {
			include("battle.php");
		}
		if ($victory){
			$badguy['creaturelose']=substitute_array($badguy['creaturelose']);
			output_notl("`b`&");
 	 	 	output($badguy['creaturelose']); 
 	 	 	output_notl("`0`b`n");
 	 	 	output("`b`\$You have defeated %s!`0`b`n",$badguy['creaturename']);

			$session['user']['level']++;
			$session['user']['maxhitpoints']+=10;
			$session['user']['soulpoints']+=5;
			$session['user']['attack']++;
			$session['user']['defense']++;
			$session['user']['seenmaster']=0;
			debuglog("Defeated master, setting seenmaster to 0");
			output("`#You advance to level `^%s`#!`n",$session['user']['level']);
			output("Your maximum hitpoints are now `^%s`#!`n",$session['user']['maxhitpoints']);
			output("You gain an attack point!`n");
			output("You gain a defense point!`n");
			if ($session['user']['level']<15){
				output("You have a new master.`n");
			}else{
				output("None in the land are mightier than you!`n");
			}
			if ($session['user']['referer']>0 && $session['user']['level']>=getsetting("referminlevel",4) && $session['user']['refererawarded']<1){
				$sql = "UPDATE " . db_prefix("accounts") . " SET donation=donation+".getsetting("refereraward",25)." WHERE acctid={$session['user']['referer']}";
				db_query($sql);
				$session['user']['refererawarded']=1;
				$subj=array("`%One of your referrals advanced!`0");
				$body=array("`&%s`# has advanced to level `^%s`#, and so you have earned `^%s`# points!", $session['user']['name'], $session['user']['level'], getsetting("refereraward", 25));
				systemmail($session['user']['referer'],$subj,$body);
			}
			increment_specialty("`^");
			
			invalidatedatacache("list.php-warsonline");

			addnav("Question Master","train.php?op=question");
			addnav("Challenge Master","train.php?op=challenge");
			if ($session['user']['superuser'] & SU_DEVELOPER) {
				addnav("Superuser Gain level","train.php?op=challenge&victory=1");
			}
			villagenav();
			if ($session['user']['age'] == 1) {
 	 	 	 	addnews("`%%s`3 has defeated ".($session['user']['sex']?"her":"his")." master, `%%s`3 to advance to level `^%s`3 after `^1`3 day!!", $session['user']['name'],$badguy['creaturename'],$session['user']['level']);
 	 	 	} else {
 	 	 	 	addnews("`%%s`3 has defeated ".($session['user']['sex']?"her":"his")." master, `%%s`3 to advance to level `^%s`3 after `^%s`3 days!!", $session['user']['name'],$badguy['creaturename'],$session['user']['level'],$session['user']['age']);
 	 	 	}
			$badguy=array();
			if ($session['user']['hitpoints'] < $session['user']['maxhitpoints'])
				$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
			modulehook("training-victory", array());
		}elseif($defeat){
			$taunt = select_taunt_array();
				
			addnews("`%%s`5 has challenged their master, %s and lost!`n%s",$session['user']['name'],$badguy['creaturename'],$taunt);
			$session['user']['hitpoints']=$session['user']['maxhitpoints'];
			output("`&`bYou have been defeated by `%%s`&!`b`n",$badguy['creaturename']);
			output("`%%s`\$ halts just before delivering the final blow, and instead extends a hand to help you to your feet, and hands you a complimentary healing potion.`n",$badguy['creaturename']);
			$badguy['creaturewin']=substitute_array($badguy['creaturewin']);
			output_notl("`^`b");
			output($badguy['creaturewin']);
			output_notl("`b`0`n");
			addnav("Question Master","train.php?op=question");
			addnav("Challenge Master","train.php?op=challenge");
			if ($session['user']['superuser'] & SU_DEVELOPER) {
				addnav("Superuser Gain level","train.php?op=challenge&victory=1");
			}
			villagenav();
			$session['user']['seenmaster']=1;
			debuglog("Lost to master, setting seenmaster to 1");
			modulehook("training-defeat", array());
		}else{
		  fightnav(false,false);
		}
		if ($victory || $defeat) {
			unsuspend_buffs('allowintrain',
					"`&You now feel free to make use of your buffs again!`0`n");
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
?>

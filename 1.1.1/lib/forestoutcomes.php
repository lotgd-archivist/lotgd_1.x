<?php
// addnews ready
// translator ready
// mail ready
require_once("lib/taunt.php");
require_once("lib/e_rand.php");
require_once("lib/pageparts.php");
require_once("lib/output.php");

function forestvictory($badguy,$denyflawless=false){
	global $session;
	if (getsetting("dropmingold",0)){
		$badguy['creaturegold']=
			e_rand(round($badguy['creaturegold']/4),
					round(3*$badguy['creaturegold']/4));
	}else{
		$badguy['creaturegold']=e_rand(0,$badguy['creaturegold']);
	}
	$expbonus = round(
			($badguy['creatureexp'] *
			 (1 + .25 *
				($badguy['creaturelevel']-$session['user']['level']))) -
			$badguy['creatureexp'],0);
	tlschema("battle");
	$msg = translate_inline($badguy['creaturelose']);
	tlschema();
	output_notl("`b`&%s`0`b`n",$msg);
	output("`b`\$You have slain %s!`0`b`n",$badguy['creaturename']);
	output("`#You receive `^%s`# gold!`n",$badguy['creaturegold']);
	if ($badguy['creaturegold']) {
		debuglog("received gold for slaying a monster.",false,false,"forestwin",$badguy['creaturegold']);
	}
	// No gem hunters allowed!
	$args = modulehook("alter-gemchance", array("chance"=>getsetting("forestgemchance", 25)));
	$gemchances = $args['chance'];
	if ($session['user']['level'] < 15 && e_rand(1,$gemchances) == 1) {
		output("`&You find A GEM!`n`#");
		$session['user']['gems']++;
		debuglog("found gem when slaying a monster.",false,false,"forestwingem",1);
	}
	if ($expbonus>0){
		output("`#***Because of the difficult nature of this fight, you are awarded an additional `^%s`# experience! `n(%s + %s = %s) ",$expbonus,$badguy['creatureexp'],abs($expbonus),$badguy['creatureexp']+$expbonus);
	} elseif ($expbonus<0){
		output("`#***Because of the simplistic nature of this fight, you are penalized `^%s`# experience! `n(%s - %s = %s) ",abs($expbonus),$badguy['creatureexp'],abs($expbonus),$badguy['creatureexp']+$expbonus);
	}
	output("You receive `^%s`# total experience!`n`0",$badguy['creatureexp']+$expbonus);
	$session['user']['gold']+=$badguy['creaturegold'];
	$session['user']['experience']+=($badguy['creatureexp']+$expbonus);
	$creaturelevel = $badguy['creaturelevel'];
	if ($badguy['diddamage']!=1){
		if (!$denyflawless && isset($badguy['denyflawless']) &&
				$badguy['denyflawless'])
			$denyflawless = $badguy['denyflawless'];
		output("`c`b`&~~ Flawless Fight! ~~`0`b`c");
		if ($denyflawless){
			output("`c`\$%s`0`c", translate_inline($denyflawless));
		}elseif ($session['user']['level']<=$creaturelevel){
			output("`c`b`\$You receive an extra turn!`0`b`c`n");
			$session['user']['turns']++;
		}else{
			output("`c`\$A more difficult fight would have yielded an extra turn.`0`c`n");
		}
	}
	if ($session['user']['hitpoints'] <= 0) {
		output("With your dying breath you spy a small stand of mushrooms off to the side.");
		output("You recognize them as some of the ones that the healer had drying in the hut and taking a chance, cram a handful into your mouth.");
		output("Even raw they have some restorative properties.`n");
		$session['user']['hitpoints'] = 1;
	}
}

function forestdefeat($badguy,$where="in the forest"){
	global $session;
	$percent=getsetting('forestexploss',10);
	addnav("Daily news","news.php");
	$taunt = select_taunt_array();
	addnews("`%%s`5 has been slain $where by %s.`n%s",$session['user']['name'],$badguy['creaturename'],$taunt);
	$session['user']['alive']=false;
	debuglog("lost gold when they were slain $where",false,false,"forestlose",-$session['user']['gold']);
	$session['user']['gold']=0;
	$session['user']['hitpoints']=0;
	$session['user']['experience']=round($session['user']['experience']*(1-($percent/100)),0);
	tlschema("battle");
	$msg = translate_inline($badguy['creaturewin']);
	tlschema();
	output_notl("`b`&%s`0`b`n",$msg);
	output("`b`&You have been slain by `%%s`&!!!`n",$badguy['creaturename']);
	output("`4All gold on hand has been lost!`n");
	output("`4%s %% of experience has been lost!`b`n",$percent);
	output("You may begin fighting again tomorrow.");

	page_footer();
}

function buffbadguy($badguy){
	global $session;
	$expflux = round($badguy['creatureexp']/10,0);
	$expflux = e_rand(-$expflux,$expflux);
	$badguy['creatureexp']+=$expflux;

	//make badguys get harder as you advance in dragon kills.
	$dk = 0;
	while(list($key, $val)=each($session['user']['dragonpoints'])) {
		if ($val=="at" || $val=="de") $dk++;
	}
	$dk += (int)(($session['user']['maxhitpoints']-
				($session['user']['level']*10))/5);

	// How many of the dk points should actually be used.
	// We want to add .05 for every 100 dragonkills.
	$add = ($session['user']['dragonkills']/100)*.05;
	$dk = round($dk * (.25 + $add));

	$atkflux = e_rand(0, $dk);
	$defflux = e_rand(0, ($dk-$atkflux));

	$hpflux = ($dk - ($atkflux+$defflux)) * 5;
	$badguy['creatureattack']+=$atkflux;
	$badguy['creaturedefense']+=$defflux;
	$badguy['creaturehealth']+=$hpflux;

	if (getsetting("disablebonuses", 1)) {
		$bonus = 1 + .03*($atkflux+$defflux) + .001*$hpflux;
		$badguy['creaturegold'] = round($badguy['creaturegold']*$bonus, 0);
		$badguy['creatureexp'] = round($badguy['creatureexp']*$bonus, 0);
	}

	$badguy = modulehook("creatureencounter",$badguy);
	debug("DEBUG: $dk modification points total.");
	debug("DEBUG: +$atkflux allocated to attack.");
	debug("DEBUG: +$defflux allocated to defense.");
	debug("DEBUG: +".($hpflux/5)."*5 to hitpoints.");
	return modulehook("buffbadguy",$badguy);
}
?>

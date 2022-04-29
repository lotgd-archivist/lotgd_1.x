<?php
// addnews ready
// translator ready
// mail ready
require_once("common.php");
require_once("lib/forest.php");
require_once("lib/http.php");
require_once("lib/villagenav.php");

tlschema("healer");

$config = unserialize($session['user']['donationconfig']);

$return = httpget("return");
$returnline = $return>""?"&return=$return":"";

page_header("Healer's Hut");
output("`#`b`cHealer's Hut`c`b`n");

$loglev = log($session['user']['level']);
$cost = ($loglev * ($session['user']['maxhitpoints']-$session['user']['hitpoints'])) + ($loglev*10);
$result=modulehook("healmultiply",array("alterpct"=>1.0));
$cost*=$result['alterpct'];
$cost = round($cost,0);

$op = httpget('op');
if ($op==""){
  	checkday();
	output("`3You duck into the small smoke-filled grass hut.");
	output("The pungent aroma makes you cough, attracting the attention of a grizzled old person that does a remarkable job of reminding you of a rock, which probably explains why you didn't notice them until now.");
	output("Couldn't be your failure as a warrior.");
	output("Nope, definitely not.`n`n");
	if ($session['user']['hitpoints'] < $session['user']['maxhitpoints']){
		output("\"`6See you, I do.  Before you did see me, I think, hmm?`3\" the old thing remarks.");
		output("\"`6Know you, I do; healing you seek.  Willing to heal am I, but only if willing to pay are you.`3\"`n`n");
		output("\"`5Uh, um.  How much?`3\" you ask, ready to be rid of the smelly old thing.`n`n");
		output("The old being thumps your ribs with a gnarly staff.  \"`6For you... `$`b%s`b`6 gold pieces for a complete heal!!`3\" it says as it bends over and pulls a clay vial from behind a pile of skulls sitting in the corner.", $cost);
		output("The view of the thing bending over to remove the vial almost does enough mental damage to require a larger potion.");
		output("\"`6I also have some, erm... 'bargain' potions available,`3\" it says as it gestures at a pile of dusty, cracked vials.");
		output("\"`6They'll heal a certain percent of your `idamage`i.`3\"");
		addnav("Potions");
		addnav("`^Complete Healing`0","healer.php?op=buy&pct=100$returnline");
		for ($i=90;$i>0;$i-=10){
			addnav(array("%s%% - %s gold", $i, round($cost*$i/100,0)),"healer.php?op=buy&pct=$i$returnline");
		}
		modulehook('potion');
		tlschema("nav");
		addnav("`bReturn`b");
		if ($return==""){
			addnav("F?Back to the Forest", "forest.php");
			villagenav();
		}elseif ($return=="village.php"){
			villagenav();
		}else{
			addnav("R?Return whence you came",$return);
		}
		tlschema();
	}elseif($session['user']['hitpoints'] == $session['user']['maxhitpoints']){
		output("`3The old creature grunts as it looks your way. \"`6Need a potion, you do not.  Wonder why you bother me, I do.`3\" says the hideous thing.");
		output("The aroma of its breath makes you wish you hadn't come in here in the first place.  You think you had best leave.");
		tlschema("nav");
		if ($return==""){
			forest(true);
		}else{
			addnav("Return");
			if ($return=="village.php")villagenav();
			else addnav("Return whence you came",$return);
		}
		tlschema();
	}else{
		output("`3The old creature glances at you, then in a `^whirlwind of movement`3 that catches you completely off guard, brings its gnarled staff squarely in contact with the back of your head.");
		output("You gasp as you collapse to the ground.`n`n");
		output("Slowly you open your eyes and realize the beast is emptying the last drops of a clay vial down your throat.`n`n");
		output("\"`6No charge for that potion.`3\" is all it has to say.");
		output("You feel a strong urge to leave as quickly as you can.");
		$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
		tlschema("nav");
		if ($return==""){
			forest(true);
		}else{
			addnav("Return");
			if ($return=="village.php")villagenav();
			else addnav("Return whence you came",$return);
		}
		tlschema();
	}
}elseif ($op=="buy"){
	$pct = httpget('pct');
	$newcost=round($pct*$cost/100,0);
	if ($session['user']['gold']>=$newcost){
		$session['user']['gold']-=$newcost;
		debuglog("spent gold on healing",false,false,"healing",$newcost);
		$diff = round(($session['user']['maxhitpoints']-$session['user']['hitpoints'])*$pct/100,0);
		$session['user']['hitpoints'] += $diff;
		output("`3With a grimace, you up-end the potion the creature hands you, and despite the foul flavor, you feel a warmth spreading through your veins as your muscles knit back together.");
		if ($newcost) {
			output("Staggering some, you hand it your gold and are ready to be out of here.");
		} else {
			output("Staggering some you make your way out.");
		}
		output("`n`n`#You have been healed for %s points!", $diff);
		tlschema("nav");
		if ($return==""){
			forest(true);
		}else{
			addnav("Return");
			if ($return=="village.php")villagenav();
			else addnav("Return whence you came",$return);
		}
		tlschema();
	}else{
		output("`3The old creature pierces you with a gaze hard and cruel.");
		output("Your lightning quick reflexes enable you to dodge the blow from its gnarled staff.");
		output("Perhaps you should get some more money before you attempt to engage in local commerce.`n`n");
		output("You recall that the creature had asked for `b`\$%s`3`b gold.", $newcost);
		addnav("Potions");
		addnav("`^Complete Healing`0","healer.php?op=buy&pct=100$returnline");
		for ($i=90;$i>0;$i-=10){
			addnav(array("%s%% - %s gold", $i, round($cost*$i/100,0)),"healer.php?op=buy&pct=$i$returnline");
		}
		modulehook('potion');
		tlschema("nav");
		addnav("`bReturn`b");
		if ($return==""){
			addnav("F?Back to the Forest","forest.php");
			villagenav();
		}elseif ($return=="village.php"){
			villagenav();
		}else{
			addnav("Return whence you came",$return);
		}
		tlschema();
	}
}
output_notl("`0");
page_footer();
?>
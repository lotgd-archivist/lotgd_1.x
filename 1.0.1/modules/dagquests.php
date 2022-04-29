<?php

require_once("lib/http.php");
require_once("lib/villagenav.php");

function dagquests_getmoduleinfo(){
	$info = array(
		"name"=>"Dag's Quests",
		"version"=>"1.0",
		"author"=>"Sneakabout",
		"category"=>"Quest",
		"download"=>"core_module",
		"settings"=>array(
			"Minotaur Quest Settings,title",
			"qonerewardgold"=>"What is the gold reward for the Minotaur Quest?,int|1000",
			"qonerewardgems"=>"What is the gem reward for the Minotaur Quest?,int|2",
			"qoneexperience"=>"What is the quest experience multiplier for the Minotaur Quest?,floatrange,1.01,1.1,0.01|1.04",
			"qoneminlevel"=>"What is the minimum level for this quest?,range,1,15|5",
			"qonemaxlevel"=>"What is the maximum level for this quest?,range,1,15|9",
			"Manticore Quest Settings,title",
			"qtworewardgold"=>"What is the gold reward for the Manticore Quest?,int|2000",
			"qtworewardgems"=>"What is the gem reward for the Manticore Quest?,int|3",
			"qtwoexperience"=>"What is the quest experience multiplier for the Manticore Quest?,floatrange,1.01,1.2,0.01|1.1",
			"qtwominlevel"=>"What is the minimum level for this quest?,range,1,15|10",
			"qtwomaxlevel"=>"What is the maximum level for this quest?,range,1,15|14",
		),
		"prefs"=>array(
            "qonestatus"=>"How far in the Minotaur Quest has the player got?,int|0",
            "qtwostatus"=>"How far in the Manticore Quest has the player got?,int|0",
        ),
	);
	return $info;
}

function dagquests_install(){
	module_addhook("village");
	module_addhook("dragonkilltext");
	module_addhook("newday");
	module_addhook("footer-runmodule");
	return true;
}

function dagquests_uninstall(){
	return true;
}

function dagquests_dohook($hookname,$args){
	global $session;
	switch ($hookname) {
	case "village":
		if ($session['user']['location']==
				getsetting("villagename", LOCATION_FIELDS)) {
			tlschema($args['schemas']['gatenav']);
			addnav($args['gatenav']);
			tlschema();
			if (get_module_pref("qonestatus")==1 &&
					$session['user']['turns'] >= 1) {
				addnav("Search the Caves (1 turn)",
						"runmodule.php?module=dagquests&op=searchone");
			} elseif (get_module_pref("qtwostatus")==1 &&
					$session['user']['turns'] >= 1) {
				addnav("Search the Trails (1 turn)",
						"runmodule.php?module=dagquests&op=searchtwo");
			}
		}
		break;
	case "dragonkilltext":
		set_module_pref("qonestatus",0);
		set_module_pref("qtwostatus",0);
		break;
	case "newday":
		if (get_module_pref("qonestatus")==1 &&
				$session['user']['level']>get_module_setting("qonemaxlevel")) {
			set_module_pref("qonestatus",4);
			output("`n`6You hear that another adventurer defeated the minotaur plaguing the Caves.`0`n");
		}
		if (get_module_pref("qtwostatus")==1 &&
				$session['user']['level']>get_module_setting("qtwomaxlevel")) {
			set_module_pref("qtwostatus",4);
			output("`n`6You hear that another adventurer defeated the manticore which had slaughtered the travellers.`0`n");
		}
		break;
	case "footer-runmodule":
		$op=httpget("op");
		$module=httpget("module");
		if (!$op &&  $module=="dag" &&
				httpget("manage")!="true") {
			addnav("Ask About Special Bounties",
					"runmodule.php?module=dagquests&op=askquest");
		}
		break;
	}
	return $args;
}

function dagquests_runevent($type) {
}

function dagquests_run(){
	global $session;
	$op = httpget('op');
	
	switch($op){
	case "askquest":
		$iname = getsetting("innname", LOCATION_INN);
		page_header($iname);
		rawoutput("<span style='color: #9900FF'>");
		output_notl("`c`b");
		output($iname);
		output_notl("`b`c");
		$onemin=get_module_setting("qoneminlevel");
		$onemax=get_module_setting("qonemaxlevel");
		$twomin=get_module_setting("qtwominlevel");
		$twomax=get_module_setting("qtwomaxlevel");
		output("`3You lean over the table to Dag to inquire if there are any jobs for you to do.");
		if ($session['user']['level']<$onemin &&
				$session['user']['level']<$twomin) {
			output("He seems very busy, and when you ask him about work, he simply shakes his head and explains that his current problems are too tough for you.");
		} elseif ($onemin<=$session['user']['level'] &&
				$session['user']['level']<=$onemax &&
				!get_module_pref("qonestatus")) {
			output("He seems very busy, but when you ask him about work, he looks at you carefully and motions you closer.`n`n");
			output("\"Aye, there be something ye might be helpin' me wit'.... there be rumours of a half-man beast that be preyin' on adventurers. It be operatin' from a nearby cave. It seems t' be reasonably smart, and the normal guards ain't bein' the sort to take the thing on. Ye look like ye can handle yerself, and there be a bounty from one o' the relatives if'n yer interested.  Do ye be takin' the job?\"`n`n");
			output("It almost crosses your mind to wonder why Dag would be offering this to you, but the caves aren't that far away after all.");
			output("It shouldn't be any problem to search them.");
			addnav("Take the Job",
					"runmodule.php?module=dagquests&op=qonetake");
		} elseif ($twomin<=$session['user']['level'] &&
				$session['user']['level']<=$twomax &&
				!get_module_pref("qtwostatus")) {
			output("He seems very busy, but when you ask him about work, he nods and leans in closer.`n`n");
			output("\"Yer just the person I be needin'. There be a wagon found destroyed, the sides filled with spikes. The survivor be tellin' the tale o' the trouble. I figger one manticore, maybe more, t' be claimin' that trail fer their territory. They be very aggressive, so ye should be havin' no trouble findin' the beasts, and yer trained well enough that I be willin' t' trust this t' ye. There be no bounty though 'cause we ain't let the information get out, but the wagon owner be killed in th' attack, so whatever ye find at the scene ye can keep.  Do ye be wantin' t' take on the beast?\"");
			output("You almost take a full second to consider how mean this creature might be, but you're a hero after all.");
			output("How hard could killing some small monster blocking the trails be?");
			addnav("Take the Job",
					"runmodule.php?module=dagquests&op=qtwotake");
		} else {
			output("He seems very busy, and says he has no special bounties available at the moment.");
		}
		addnav("I?Return to the Inn","inn.php");
		rawoutput("</span>");
		break;
	case "qonetake":
		$iname = getsetting("innname", LOCATION_INN);
		page_header($iname);
		rawoutput("<span style='color: #9900FF'>");
		output_notl("`c`b");
		output($iname);
		output_notl("`b`c");
		output("`3Dag nods, and gives you directions to the rough area the beast has been seen in, as well as a description of a bull-headed humanoid, tough and strong.");
		output("You leave the table, ready to seek out the beast.");
		set_module_pref("qonestatus",1);
		addnav("I?Return to the Inn","inn.php");
		rawoutput("</span>");
		break;
	case "qtwotake":
		$iname = getsetting("innname", LOCATION_INN);
		page_header($iname);
		rawoutput("<span style='color: #9900FF'>");
		output_notl("`c`b");
		output($iname);
		output_notl("`b`c");
		output("`3Dag nods, and tells you which trail the caravan was lost down.");
		output("He advises you to prepare before you go for the monster.");
		set_module_pref("qtwostatus",1);
		addnav("I?Return to the Inn","inn.php");
		rawoutput("</span>");
		break;
	case "searchone":
		page_header("The Caves");
		output("`2You hike up to the area riddled with caves, and start to check them out individually for traces of the beast.`n`n");
		$session['user']['turns']--;
		$rand=e_rand(1,10);
		switch($rand){
		case 1:
		case 2:
			output("You search through the caves for a while, finding nothing but bleached bones and dust.");
			output("Dispirited after a few hours, you trudge back to the town and look for something else to do.");
			villagenav();
			break;
		case 3:
		case 4:
			output("You wander through the caves for a while, eventually hearing some cries for help from a distance.");
			output("You rush over, and find and injured traveller who had been attempting to travel across the countryside.");
			output("Spikes protude from his chest, and he is obviously mortally wounded - you do your best, but he dies after choking something about an attack from a powerful monster.");
			output("You hurry back to town, watching your back for whatever attacked the traveller.");
			villagenav();
			break;
		case 5:
			output("You wander through the caves for a while, finding that tracking something across rock is extremely difficult.");
			output("While looking in vain through an empty cave, you discover intricate patterns carved into the rock!");
			output("However, you're more interested in the gem embedded in the rock, and you pry it our as a souvenir before returning to town.");
			debuglog("gained a gem from an ancient cave");
			$session['user']['gems']++;
			villagenav();
			break;
		case 6:
			output("You wander through the caves for a while before hearing a roar from the top of a nearby outcropping!");
			output("A mountain lion has spotted you, and bounds towards you, snarling.");
			output("You have nowhere to run to, so you ready your %s`2 to fight!",$session['user']['weapon']);
			addnav("Fight the Lion","runmodule.php?module=dagquests&fight=lionfight");
			break;
		case 7:
		case 8:
		case 9:
		case 10:
			output("You wander through the caves for a while before finding a trail of blood from a dropped backpack.");
			output("You rush following the trail across the rocks to a sandy outcrop where you can see the minotaur, gorging on the body of the dead traveller in front of a small cave.");
			output("The beast sniffs the air, and you know you have been detected - you draw your %s`2 and charge down as the beast prepares with it's club, snarling all the while.",$session['user']['weapon']);
			addnav("Fight the Minotaur","runmodule.php?module=dagquests&fight=minotaurfight");
			break;
		}
		break;
	case "searchtwo":
		page_header("The Trails");
		output("`2You hitch a ride with a wagon, and they drop you off at the start of the trail, leaving you to walk the remaining distance on your own.");
		$session['user']['turns']--;
		output("You start down the trail, looking for the remains of the wagon.");
		$rand=e_rand(1,7);
		switch($rand){
		case 1:
		case 2:
			output("You walk down the trail, eyes peeled, strung tight for ages, until you spot a wagon travelling towards you.");
			output("Confused, as you thought this part of the trail was closed until the threat had been dealt with, you hail the driver, only to find that you were left at the wrong trail!`n`n");
			output("Fortunately he is kind enough to give you a lift back to town and you return, your time wasted.");
			villagenav();
			break;
		case 3:
		case 4:
			output("You walk down the trail, eyes peeled, strung tight for ages, until you spot what seems to be the caravan in the distance.");
			output("However, as you drop your guard to move closer you hear a howl to your left and you back away as a grey wolf stalks towards you!");
			output("You must ready your %s`2 to defend yourself!",$session['user']['weapon']);
			addnav("Fight the Wolf","runmodule.php?module=dagquests&fight=wolffight");
			break;
		case 5:
		case 6:
		case 7:
			output("You walk down the trail, eyes peeled, strung tight for ages, until you spot what seems to be the wagon in the distance.");
			output("Remaining careful, you circle round, and spot the hideous creature lying in wait by the side of the road, spiked tail waving as it awaits its prey.");
			output("You charge the monster, which whips around to face you and hisses before pouncing with its terrible claws!");
			addnav("Fight the Manticore","runmodule.php?module=dagquests&fight=manticorefight");
			break;
		}
		break;
	}
	$fight=httpget("fight");
	switch($fight){
	case "lionfight":
		$badguy = array(
			"creaturename"=>"Lion",
			"creaturelevel"=>$session['user']['level']-1,
			"creatureweapon"=>"Savage Claws",
			"creatureattack"=>$session['user']['attack'],
			"creaturedefense"=>round($session['user']['defense']*0.8, 0),
			"creaturehealth"=>round($session['user']['maxhitpoints']*0.9, 0), 
			"diddamage"=>0,
			"type"=>"quest"
		);
		$session['user']['badguy']=createstring($badguy);
		$battle=true;
		// Drop through
	case "lionfighting":
		page_header("The Caves");
		require_once("lib/fightnav.php");
		include("battle.php");
		if ($victory) {
			output("`2The lion collapses on the ground, bleeding from its wounds.");
			output("You quickly flee the scene, hoping that there are not more of them around.");
			$expgain=round($session['user']['experience']*(e_rand(1,2)*0.01));
			$session['user']['experience']+=$expgain;
			output("`n`n`&You gain %s experience from this fight!",$expgain);
			output("`2You return to town, shaken by your experience.");
			villagenav();
		} elseif ($defeat) {
			output("`2Your vision blacks out as the lion tears the throat out of your already badly injured body.");
			output("`n`n`%You have died! You lose 10% of your experience, and your gold is stolen by scavengers!");
			output("Your soul drifts to the shades.");
			debuglog("was killed by a lion and lost ".$session['user']['gold']." gold.");
			$session['user']['gold']=0;
			$session['user']['experience']*=0.9;
			$session['user']['alive'] = false;
			addnews("%s was slain by a Lion in the Caves!",$session['user']['name']);
			addnav("Return to the News","news.php");
		} else {
			fightnav(true,true,"runmodule.php?module=dagquests&fight=lionfighting");
		}
		break;
	case "minotaurfight":
		$badguy = array(
			"creaturename"=>"Minotaur",
			"creaturelevel"=>$session['user']['level']+1,
			"creatureweapon"=>"Bone Club",
			"creatureattack"=>round($session['user']['attack']*1.15, 0),
			"creaturedefense"=>round($session['user']['defense']*0.9, 0),
			"creaturehealth"=>round($session['user']['maxhitpoints']*1.2, 0), 
			"diddamage"=>0,
			"type"=>"quest"
		);
		$session['user']['badguy']=createstring($badguy);
		$battle=true;
		// drop through
	case "minotaurfighting":
		page_header("The Caves");
		require_once("lib/fightnav.php");
		include("battle.php");
		if ($victory) {
			output("`2The minotaur collapses to the ground with a thud, sending up a cloud of dust!");
			output("You have avenged the deaths of many travellers!");
			$expgain=round($session['user']['experience']*(get_module_setting("qoneexperience")-1), 0);
			$session['user']['experience']+=$expgain;
			output("`n`n`&You gain %s experience from this fight!",$expgain);
			$goldgain=get_module_setting("qonerewardgold");
			$gemgain=get_module_setting("qonerewardgems");
			$session['user']['gold']+=$goldgain;
			$session['user']['gems']+=$gemgain;
			debuglog("got a reward of $goldgain gold and $gemgain gems for slaying a minotaur.");
			if ($goldgain && $gemgain) {
				output("`2You return to the Inn carrying the beast's head, and Dag pays you the bounty of `^%s gold`2 and a pouch of `%%s %s`2!",$goldgain,$gemgain,translate_inline(($gemgain==1)?"gems":"gem"));
			} elseif ($gemgain) {
				output("`2You return to the Inn carrying the beast's head, and Dag pays you the bounty of a pouch of `%%s %s`2!",$gemgain,translate_inline(($gemgain==1)?"gems":"gem"));
			} elseif ($goldgain) {
				output("`2You return to the Inn carrying the beast's head, and Dag pays you the bounty of `^%s gold`2!",$goldgain);
			} else {
				output("`2You return to the Inn carrying the beast's head, but Dag cannot find the bounty to pay you!");
			}
			set_module_pref("qonestatus",2);
			addnews("%s defeated a Minotaur in the Caves! The deaths of many travellers have been avenged!",$session['user']['name']);
			addnav("I?Return to the Inn","inn.php");
		} elseif ($defeat) {
			output("`2Your vision blacks out as the minotaur clubs you to the ground.");
			output("You have failed your task to avenge the travellers!");
			output("`n`n`%You have died! You lose 10% of your experience, and your gold is stolen by the minotaur!");
			output("Your soul drifts to the shades.");
			debuglog("was killed by a minotaur in the Caves and lost ".$session['user']['gold']." gold.");
			$session['user']['gold']=0;
			$session['user']['experience']*=0.9;
			$session['user']['alive'] = false;
			set_module_pref("qonestatus",3);
			addnews("%s was slain by a Minotaur in the Caves!",
					$session['user']['name']);
			addnav("Return to the News","news.php");
		} else {
			fightnav(true,true,"runmodule.php?module=dagquests&fight=minotaurfighting");
		}
		break;
	case "wolffight":
		$badguy = array(
			"creaturename"=>"Grey Wolf",
			"creaturelevel"=>$session['user']['level']-1,
			"creatureweapon"=>"Hungry Jaws",
			"creatureattack"=>$session['user']['attack'],
			"creaturedefense"=>round($session['user']['defense']*0.75, 0),
			"creaturehealth"=>round($session['user']['maxhitpoints']*1.1, 0), 
			"diddamage"=>0,
			"type"=>"quest"
		);
		$session['user']['badguy']=createstring($badguy);
		$battle=true;
		// drop through
	case "wolffighting":
		page_header("The Trails");
		require_once("lib/fightnav.php");
		include("battle.php");
		if ($victory) {
			output("`2The wolf collapses on the ground, bleeding from its wounds.");
			output("You quickly flee the scene, hoping to avoid the rest of the pack.");
			$expgain=round($session['user']['experience']*(e_rand(1,2)*0.01));
			$session['user']['experience']+=$expgain;
			output("`n`n`&You gain %s experience from this fight!",$expgain);
			output("`2You return to town, shaken by the attack.");
			villagenav();
		} elseif ($defeat) {
			output("`6Your vision blacks out as the wolf tears the throat out of your already badly injured body.");
			output("`n`n`%You have died!");
			output("You lose 10% of your experience, and your gold is stolen by scavengers!");
			output("Your soul drifts to the shades.");
			$session['user']['gold']=0;
			$session['user']['experience']*=0.9;
			$session['user']['alive'] = false;
			debuglog("was killed by a wolf on a trail.");
			addnews("%s's body turned up, torn to shreds!",$session['user']['name']);
			addnav("Return to the News","news.php");
		} else {
			fightnav(true,true,"runmodule.php?module=dagquests&fight=wolffighting");
		}
		break;
	case "manticorefight":
		$badguy = array(
			"creaturename"=>"Manticore",
			"creaturelevel"=>$session['user']['level']+2,
			"creatureweapon"=>"Terrible Claws",
			"creatureattack"=>round($session['user']['attack']*1.15, 0),
			"creaturedefense"=>round($session['user']['defense']*1.1, 0),
			"creaturehealth"=>round($session['user']['maxhitpoints']*1.4, 0), 
			"diddamage"=>0,
			"type"=>"quest"
		);
		apply_buff('manticorespike',array(
			"name"=>"`\$Manticore Spikes",
			"roundmsg"=>"The manticore flicks its tail over its head and sends a volley of spikes at you!",
			"effectmsg"=>"You are hit by one of the spikes for `4{damage}`) points!",
			"effectnodmgmsg"=>"You dodge one of the spikes!",
			"rounds"=>20,
			"wearoff"=>"The monster runs out of spikes!",
			"minioncount"=>3,
			"maxgoodguydamage"=>$session['user']['level'],
			"schema"=>"dagquests"
		));
		$session['user']['badguy']=createstring($badguy);
		$battle=true;
		// drop through
	case "manticorefighting":
		page_header("The Trails");
		require_once("lib/fightnav.php");
		include("battle.php");
		if ($victory) {
			output("`2The manticore falls to the ground with a scream which resonates over the hills, mortally wounded by your blows!");
			output("You have avenged the deaths of the wagon!");
			$expgain=round($session['user']['experience']*(get_module_setting("qtwoexperience")-1), 0);
			$session['user']['experience']+=$expgain;
			output("`n`n`&You gain %s experience from this fight!",$expgain);
			$goldgain=get_module_setting("qtworewardgold");
			$gemgain=get_module_setting("qtworewardgems");
			$session['user']['gold']+=$goldgain;
			$session['user']['gems']+=$gemgain;
			debuglog("found $goldgain gold and $gemgain gems after slaying a manticore.");
			output("`n`n`2With the monster dead, you search through the wagon, but most of the goods are missing!");
			output("Someone else has already been here and looted it without killing the manticore!");
			if ($goldgain && $gemgain) {
				output("All you can find is `^%s gold`2 lying around and `%%s %s`2 which were hidden by the wagon master.",$goldgain,$gemgain,translate_inline(($gemgain==1)?"gems":"gem"));
			} elseif ($gemgain) {
				output("All you can find is `%%s %s`2 which were hidden by the wagon master.",$gemgain,translate_inline(($gemgain==1)?"gems":"gem"));
			} elseif ($goldgain) {
				output("All you can find is `^%s gold`2 lying around.",$goldgain);
			} else {
				output("You don't find anything!");
			}
			output("You make your way back to the fork in the trail, and tell the next wagon the news on the way back to town.");
			set_module_pref("qtwostatus",2);
			addnews("%s defeated a Manticore on the trails! The victims have been avenged!",$session['user']['name']);
			villagenav();
			strip_buff("manticorespike");
		} elseif ($defeat) {
			output("`2You fall backwards to the ground as the final volley of spikes from the manticore pierces your skull.");
			output("You have failed in your mission to kill this monster!");
			output("`n`n`%You have died!");
			output("You lose 10% of your experience, and your gold is stolen by scavengers!");
			output("Your soul drifts to the shades.");
			debuglog("was killed by a manticore on a trail and lost ".$session['user']['gold']." gold.");
			$session['user']['gold']=0;
			$session['user']['experience']*=0.9;
			$session['user']['alive'] = false;
			set_module_pref("qtwostatus",3);
			addnews("%s was slain by a Manticore on a trail!",$session['user']['name']);
			addnav("Return to the News","news.php");
			strip_buff("manticorespike");
		} else {
			fightnav(true,true,"runmodule.php?module=dagquests&fight=manticorefighting");
		}
		break;
	}
	page_footer();
}
?>

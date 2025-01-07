<?php
// addnews ready
// translator ready
// mail ready
require_once("common.php");
require_once("lib/fightnav.php");
require_once("lib/titles.php");
require_once("lib/http.php");
require_once("lib/buffs.php");
require_once("lib/taunt.php");
require_once("lib/names.php");
require_once 'lib/creatures.php';

tlschema("dragon");
$battle = false;
page_header("The Green Dragon!");
$op = httpget('op');
if ($op==""){
	if (!httpget('nointro')) {
		output("`\$Fighting down every urge to flee, you cautiously enter the cave entrance, intent on catching the great green dragon sleeping, so that you might slay it with a minimum of pain.");
		output("Sadly, this is not to be the case, for as you round a corner within the cave you discover the great beast sitting on its haunches on a huge pile of gold, picking its teeth with a rib.");
	}
	$badguy = array(
		"creaturename"=>translate_inline("`@The Green Dragon`0"),
		"creaturelevel"=>$settings->maxlevel + 3,
		"creatureweapon"=>translate_inline("Great Flaming Maw"),
		"creatureattack"=>creature_attack($settings->maxlevel + 3),
		"creaturedefense"=>creature_defense($settings->maxlevel + 3),
		"creaturehealth"=>creature_health($settings->maxlevel * 2),
		"diddamage"=>0, 
		"type"=>"dragon");

	//toughen up each consecutive dragon.
	// First, find out how each dragonpoint has been spent and count those
	// used on attack and defense.
	// Coded by JT, based on collaboration with MightyE
	$points = 0;
	restore_buff_fields();
	foreach ($user->dragonpoints as $key=>$val) {
		if ($val=="at" || $val == "de") $points++;
	}

	// Now, add points for hitpoint buffs that have been done by the dragon
	// or by potions!
	$points += (int)(($user->maxhitpoints - ($settings->maxlevel * 10))/5);

	$points = round($points*.75,0);

	$atkflux = e_rand(0, $points);
	$defflux = e_rand(0,$points-$atkflux);

	$hpflux = ($points - ($atkflux+$defflux)) * 5;
	debug("DEBUG: $points modification points total.`0`n");
	debug("DEBUG: +$atkflux allocated to attack.`n");
	debug("DEBUG: +$defflux allocated to defense.`n");
	debug("DEBUG: +". ($hpflux/5) . "*5 to hitpoints.`0`n");
	calculate_buff_fields();
	$badguy['creatureattack']+=$atkflux;
	$badguy['creaturedefense']+=$defflux;
	$badguy['creaturehealth']+=$hpflux;

	$badguy = modulehook("buffdragon", $badguy);

	$user->badguy=createstring($badguy);
	$battle=true;
}elseif($op=="prologue1"){
	output("`@Victory!`n`n");
	$flawless = (int)(httpget('flawless'));
  	if ($flawless) {
		output("`b`c`&~~ Flawless Fight ~~`0`c`b`n`n");
	}
	output("`2Before you, the great dragon lies immobile, its heavy breathing like acid to your lungs.");
	output("You are covered, head to toe, with the foul creature's thick black blood.");
	output("The great beast begins to move its mouth.  You spring back, angry at yourself for having been fooled by its ploy of death, and watch for its huge tail to come sweeping your way.");
	output("But it does not.");
	output("Instead the dragon begins to speak.`n`n");
	output("\"`^Why have you come here mortal?  What have I done to you?`2\" it says with obvious effort.");
	output("\"`^Always my kind are sought out to be destroyed.  Why?  Because of stories from distant lands that tell of dragons preying on the weak?  I tell you that these stories come only from misunderstanding of us, and not because we devour your children.`2\"");
	output("The beast pauses, breathing heavily before continuing, \"`^I will tell you a secret.  Behind me now are my eggs.  They will hatch, and the young will battle each other.  Only one will survive, but she will be the strongest.  She will quickly grow, and be as powerful as me.`2\"");
	output("Breath comes shorter and shallower for the great beast.`n`n");
	output("\"`#Why do you tell me this?  Don't you know that I will destroy your eggs?`2\" you ask.`n`n");
	output("\"`^No, you will not, for I know of one more secret that you do not.`2\"`n`n");
	output("\"`#Pray tell oh mighty beast!`2\"`n`n");
	output("The great beast pauses, gathering the last of its energy.  \"`^Your kind cannot tolerate the blood of my kind.  Even if you survive, you will be a feeble creature, barely able to hold a weapon, your mind blank of all that you have learned.  No, you are no threat to my children, for you are already dead!`2\"`n`n");
	output("Realizing that already the edges of your vision are a little dim, you flee from the cave, bound to reach the healer's hut before it is too late.");
	output("Somewhere along the way you lose your weapon, and finally you trip on a stone in a shallow stream, sight now limited to only a small circle that seems to float around your head.");
	output("As you lay, staring up through the trees, you think that nearby you can hear the sounds of the village.");
	output("Your final thought is that although you defeated the dragon, you reflect on the irony that it defeated you.`n`n");
	output("As your vision winks out, far away in the dragon's lair, an egg shuffles to its side, and a small crack appears in its thick leathery skin.");

	if ($flawless) {
		output("`n`nYou fall forward, and remember at the last moment that you at least managed to grab some of the dragon's treasure, so maybe it wasn't all a total loss.");
	}
	addnav("It is a new day","news.php");
	strip_all_buffs();
	$sql = "DESCRIBE " . DB::prefix("accounts");
	$result = DB::query($sql);

	$dkpoints = 0;
	foreach ($user->dragonpoints as $key=>$val) {
		if ($val=="hp") $dkpoints+=5;
	}

	restore_buff_fields();
	$hpgain = array(
			'total' => $user->maxhitpoints,
			'dkpoints' => $dkpoints,
			'extra' => $user->maxhitpoints - $dkpoints -
					($user->level*10),
			'base' => $dkpoints + ($user->level * 10),
			);
	$hpgain = modulehook("hprecalc", $hpgain);
	calculate_buff_fields();

	$nochange=array("acctid"=>1
				   ,"name"=>1
				   ,"sex"=>1
				   ,"password"=>1
				   ,"marriedto"=>1
				   ,"title"=>1
				   ,"login"=>1
				   ,"dragonkills"=>1
				   ,"locked"=>1
				   ,"loggedin"=>1
				   ,"superuser"=>1
				   ,"gems"=>1
				   ,"hashorse"=>1
				   ,"gentime"=>1
				   ,"gentimecount"=>1
				   ,"lastip"=>1
				   ,"uniqueid"=>1
				   ,"dragonpoints"=>1
				   ,"laston"=>1
				   ,"prefs"=>1
				   ,"lastmotd"=>1
				   ,"emailaddress"=>1
				   ,"emailvalidation"=>1
				   ,"gensize"=>1
				   ,"bestdragonage"=>1
				   ,"dragonage"=>1
				   ,"donation"=>1
				   ,"donationspent"=>1
				   ,"donationconfig"=>1
				   ,"bio"=>1
				   ,"charm"=>1
				   ,"banoverride"=>1
				   ,"referer"=>1
				   ,"refererawarded"=>1
				   ,"ctitle"=>1
				   ,"beta"=>1
				   ,"clanid"=>1
				   ,"clanrank"=>1
				   ,"clanjoindate"=>1
				   ,"regdate"=>1);

	$nochange = modulehook("dk-preserve", $nochange);

	$user->dragonage = $user->age;
	if ($user->dragonage <  $user->bestdragonage ||
			$user->bestdragonage == 0) {
		$user->bestdragonage = $user->dragonage;
	}
	$number=DB::num_rows($result);
	for ($i=0;$i<$number;$i++){
		$row = DB::fetch_assoc($result);
		if (isset($nochange[$row['Field']]) && $nochange[$row['Field']]){
		}elseif($row['Field'] == "location"){
			$user->$row['Field'] = getsetting("villagename", LOCATION_FIELDS);
		}else{
			$user->$row['Field'] = $row["Default"];
		}
	}
	$user->gold = getsetting("newplayerstartgold",50);

	$newtitle = get_dk_title($user->dragonkills, $user->sex);

	$restartgold = $user->gold +
		getsetting("newplayerstartgold", 50)*$user->dragonkills;
	$restartgems = 0;
	if ($restartgold > getsetting("maxrestartgold", 300)) {
		$restartgold = getsetting("maxrestartgold", 300);
		$restartgems = max(0,($user->dragonkills -
				(getsetting("maxrestartgold", 300)/
				 getsetting("newplayerstartgold", 50)) - 1));
		if ($restartgems > getsetting("maxrestartgems", 10)) {
			$restartgems = getsetting("maxrestartgems", 10);
		}
	}
	$user->gold = $restartgold;
	$user->gems += $restartgems;

	if ($flawless) {
		$user->gold += 3*getsetting("newplayerstartgold",50);
		$user->gems += 1;
	}

	$user->maxhitpoints = 10 + $hpgain['dkpoints'] +
		$hpgain['extra'];
	$user->hitpoints=$user->maxhitpoints;

	// Sanity check
	if ($user->maxhitpoints < 1) {
		// Yes, this is a freaking hack.
		die("ACK!! Somehow this user would end up perma-dead.. Not allowing DK to proceed!  Notify admin and figure out why this would happen so that it can be fixed before DK can continue.");
	}

	// Set the new title.
	$newname = change_player_title($newtitle);
	$user->title = $newtitle;
	$user->name = $newname;

	foreach ($user->dragonpoints as $key=>$val) {
		if ($val=="at"){
			$user->attack++;
		}
		if ($val=="de"){
			$user->defense++;
		}
	}
	$user->slaydragon = 1;
	$companions = array();
	$user->companions = array();

	output("`n`nYou wake up in the midst of some trees.  Nearby you hear the sounds of a village.");
	output("Dimly you remember that you are a new warrior, and something of a dangerous Green Dragon that is plaguing the area.  You decide you would like to earn a name for yourself by perhaps some day confronting this vile creature.");

	// allow explanative text as well.
	modulehook("dragonkilltext");

	$regname = get_player_basename();
	output("`n`n`^You are now known as `&%s`^!!",$user->name);
	if ($user->dragonkills == 1) {
		addnews("`#%s`# has earned the title `&%s`# for having slain the `@Green Dragon`& `^%s`# time!",$regname,$user->title,$user->dragonkills);
		output("`n`n`&Because you have slain the dragon %s time, you start with some extras.  You also keep additional permanent hitpoints you've earned.`n",$user->dragonkills);
	} else {
		addnews("`#%s`# has earned the title `&%s`# for having slain the `@Green Dragon`& `^%s`# times!",$regname,$user->title,$user->dragonkills);
		output("`n`n`&Because you have slain the dragon %s times, you start with some extras.  You also keep additional permanent hitpoints you've earned.`n",$user->dragonkills);
	}
	$user->charm+=5;
	output("`^You gain FIVE charm points for having defeated the dragon!`n");
	debuglog("slew the dragon and starts with {$user->gold} gold and {$user->gems} gems");

	// Moved this hear to make some things easier.
	modulehook("dragonkill", array());
	invalidatedatacache("list.php-warsonline");
}

if ($op=="run"){
	output("The creature's tail blocks the only exit to its lair!");
	$op="fight";
	httpset('op', 'fight');
}
if ($op=="fight" || $op=="run"){
	$battle=true;
}
if ($battle){
	require_once("battle.php");

	if ($victory){
		$flawless = 0;
		if ($badguy['diddamage'] != 1) $flawless = 1;
		$user->dragonkills++;
		output("`&With a mighty final blow, `@The Green Dragon`& lets out a tremendous bellow and falls at your feet, dead at last.");
		addnews("`&%s has slain the hideous creature known as `@The Green Dragon`&.  All across the land, people rejoice!",$user->name);
		tlschema("nav");
		addnav("Continue","dragon.php?op=prologue1&flawless=$flawless");
		tlschema();
	}else{
		if($defeat){
			tlschema("nav");
			addnav("Daily news","news.php");
			tlschema();
			$taunt = select_taunt_array();
			if ($user->sex){
				addnews("`%%s`5 has been slain when she encountered `@The Green Dragon`5!!!  Her bones now litter the cave entrance, just like the bones of those who came before.`n%s",$user->name,$taunt);
			}else{
				addnews("`%%s`5 has been slain when he encountered `@The Green Dragon`5!!!  His bones now litter the cave entrance, just like the bones of those who came before.`n%s",$user->name,$taunt);
			}
			$user->alive=false;
			debuglog("lost {$user->gold} gold when they were slain");
			$user->gold=0;
			$user->hitpoints=0;
			output("`b`&You have been slain by `@The Green Dragon`&!!!`n");
			output("`4All gold on hand has been lost!`n");
			output("You may begin fighting again tomorrow.");

			page_footer();
		}else{
		  fightnav(true,false);
		}
	}
}
page_footer();
?>

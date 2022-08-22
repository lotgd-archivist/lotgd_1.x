<?php
		$dmlevel = get_module_pref("dmlevel");
		$l = httpget('l'); //type of attack
		$m = httpget('m'); //level of attack
		$drunkpower = get_module_pref("drunkeness","drinks") - get_module_setting("maxdrunk","drinks");
		$maxpower = 200 + $dmlevel*10;
		switch($l) {
			case 1: //Drinking Powers
				switch($m) {
					case 1:
						$drinks = 30;
						break;
					case 2:
						$drinks = 60;
						break;
					case 3:
						$drinks = 120;
						break;
					case 4:
						$drinks = 180;
						break;
				}
				if ($drunkpower + $drinks > $maxpower)
					$drinks -= ($drunkpower + $drinks) - $maxpower;
				increment_module_pref("drunkeness",$drinks,"drinks");
				$percent = floor($drinks/$maxpower*100);
				apply_buff('dm1', array(
					"name"=>"",
					"startmsg"=>sprintf_translate("$ccode"."You pound down alcohol like there is no tomorrow, becoming `@%s"."%% $ccode"."more drunk!`0",$percent),
					"roundmsg"=>"$ccode"."You are busy drinking and don't attack as well!`0",
					"atkmod"=>.8,
					"rounds"=>1,
					"expireafterfight"=>1,
					"schema"=>"module-specialtydrunkenmaster"
				));
				set_module_pref("cooldown",3);
			break;

			case 2: //Drunken Movement
				switch($m) {
					case 1:
						$buffname = "Basic Drunken Movement";
						$defmod = 1.12;
						$rounds = 4;
						$cost = 75;
						break;
					case 2:
						$buffname = "Drunken Movement";
						$defmod = 1.25;
						$rounds = 6;
						$cost = 100;
						break;
					case 3:
						$buffname = "Improved Drunken Movement";
						$defmod = 1.37;
						$rounds = 8;
						$cost = 125;
						break;
					case 4:
						$buffname = "Perfect Drunken Movement";
						$defmod = 1.5;
						$rounds = 10;
						$cost = 150;
						break;
				}
				increment_module_pref("drunkeness",-$cost,"drinks");
				apply_buff('dm2', array(
					"name"=>"$ccode"."$buffname`0",
					"startmsg"=>"$ccode"."Your movements become more fluid and unpredictable.`0",
					"roundmsg"=>"$ccode"."Your strange movement patterns confuse the enemy.`0",
					"wearoff"=>"$ccode"."Your movements return to normal.`0",
					"defmod"=>$defmod,
					"rounds"=>$rounds,
					"schema"=>"module-specialtydrunkenmaster"
				));
				set_module_pref("cooldown",6);
			break;

			case 3: //Drunken Boxing
				switch($m) {
					case 1:
						$buffname = "Basic Drunken Boxing";
						$atkmod = 1.12;
						$rounds = 4;
						$cost = 75;
						break;
					case 2:
						$buffname = "Drunken Boxing";
						$atkmod = 1.25;
						$rounds = 6;
						$cost = 100;
						break;
					case 3:
						$buffname = "Improved Drunken Boxing";
						$atkmod = 1.37;
						$rounds = 8;
						$cost = 125;
						break;
					case 4:
						$buffname = "Perfect Drunken Boxing";
						$atkmod = 1.5;
						$rounds = 10;
						$cost = 150;
						break;
				}
				increment_module_pref("drunkeness",-$cost,"drinks");
				apply_buff('dm3', array(
					"name"=>"$ccode"."$buffname`0",
					"startmsg"=>"$ccode"."Your attack patterns become more fluid and unpredictable.`0",
					"roundmsg"=>"$ccode"."Your strange attack patterns confuse the enemy.`0",
					"wearoff"=>"$ccode"."Your attacks return to normal.`0",
					"atkmod"=>$atkmod,
					"rounds"=>$rounds,
					"schema"=>"module-specialtydrunkenmaster"
				));
				set_module_pref("cooldown",6);
			break;

			case 4: //Alcohol Healing
				switch($m) {
					case 1:
						$buffname = "Minor Alcohol Healing";
						$regen = .5;
						$rounds = 4;
						$cost = 35;
						break;
					case 2:
						$buffname = "Alcohol Healing";
						$regen = .75;
						$rounds = 6;
						$cost = 65;
						break;
					case 3:
						$buffname = "Major Alcohol Healing";
						$regen = 1;
						$rounds = 8;
						$cost = 95;
						break;
					case 4:
						$buffname = "Perfect Alcohol Healing";
						$regen = 1.5;
						$rounds = 10;
						$cost = 135;
						break;
				}
				increment_module_pref("drunkeness",-$cost,"drinks");
				apply_buff('dm4', array(
					"name"=>"$ccode"."$buffname`0",
					"startmsg"=>"$ccode"."Your trained body starts drawing healing qualities out of the alcohol in your blood.`0",
					"effectmsg"=>"$ccode"."The alcohol in your blood heals you for {damage} health.`0",
					"effectnodmgmsg"=>"$ccode"."You have no wounds to heal.`0",
					"wearoff"=>"$ccode"."You stop healing.`0",
					"regen"=>ceil($regen*$session['user']['level']), // added ceil for buff to do *something* at level 1
					"rounds"=>$rounds,
					"schema"=>"module-specialtydrunkenmaster"
				));
				set_module_pref("cooldown",4);
			break;

			case 5: //Fire Breathing
				switch($m) {
					case 1:
						$buffname = "Breathe Fire";
						$damage = 1;
						$minion = 1;
						$rounds = 4;
						$cost = 50;
						break;
					case 2:
						$buffname = "Breathe Giant Flame";
						$damage = 1.25;
						$minion = 2;
						$rounds = 6;
						$cost = 100;
						break;
					case 3:
						$buffname = "Breathe Inferno";
						$damage = 1.5;
						$minion = 3;
						$rounds = 8;
						$cost = 150;
						break;
					case 4:
						$buffname = "Breathe Dragonfire";
						$damage = 2;
						$minion = 4;
						$rounds = 10;
						$cost = 200;
						break;
				}
				increment_module_pref("drunkeness",-$cost,"drinks");
				apply_buff('dm5', array(
					"name"=>"$ccode"."$buffname`0",
					"startmsg"=>"$ccode"."You hold a torch in front of you and blow, covering {badguy} in flames.`0",
					"effectmsg"=>"`\${badguy} is burned for `^{damage}`\$ damage.`0",
					"effectnodmgmsg"=>"`\${badguy} is not harmed by the flames.`0",
					"wearoff"=>"`\$The fire engulfing {badguy} goes out.`0",
					"minioncount"=>$minion,
					"minbadguydamage"=>1,
					"maxbadguydamage"=>$session['user']['level']*$damage,
					"rounds"=>$rounds,
					"schema"=>"module-specialtydrunkenmaster"
				));
				set_module_pref("cooldown",5);
			break;

			case 6: //Drunken Fury
				switch($m) {
					case 1:
						$buffname = "Drunken Fury";
						$mod = 2;
						$rounds = 3;
						$cost = 300;
						break;
					case 2:
						$buffname = "Improved Drunken Fury";
						$mod = 2.5;
						$rounds = 4;
						$cost = 400;
						break;
					case 3:
						$buffname = "Perfect Drunken Fury";
						$mod = 3;
						$rounds = 5;
						$cost = 500;
						break;
				}
				increment_module_pref("drunkeness",-$cost,"drinks");
				apply_buff('dm6', array(
					"name"=>"$ccode"."$buffname`0",
					"startmsg"=>"$ccode"."You fly into an unstoppable drunken fury.`0",
					"roundmsg"=>"$ccode"."Your drunken fury overwhelms the enemy.`0",
					"wearoff"=>"$ccode"."Your fury ends.`0",
					"atkmod"=>$mod,
					"defmod"=>$mod,
					"rounds"=>$rounds,
					"schema"=>"module-specialtydrunkenmaster"
				));
				set_module_pref("cooldown",11);
			break;
		}
?>

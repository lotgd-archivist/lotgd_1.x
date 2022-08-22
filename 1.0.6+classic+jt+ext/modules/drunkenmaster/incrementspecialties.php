<?php
		increment_module_pref("dmlevelpoints",1);
		$goup = 0;

		if (get_module_setting("uselogscale")) {
			$pointsneeded = floor(log(get_module_pref("dmlevel")+2)*5);
			if ($pointsneeded <= get_module_pref("dmlevelpoints")) {
				$goup = 1;
			}
		} else {
			if (get_module_pref("dmlevelpoints") % get_module_setting("dmlevelincrement") == 0) {
				$goup = 1;
			}
		}

		if ($goup) {
			set_module_pref("dmlevelpoints", 0);
			increment_module_pref("dmlevel",1);
			$dmlevel = get_module_pref("dmlevel");
			output("`7Your `5Drunken Master `7Class Proficiency Rating has risen to `^%s`7!`0`n",$dmlevel);
			switch($dmlevel) {
				case 1:
					output("`7You have raised your skills to the level of a $cccode"."Drunken Initiate`7!`0`n");
					apply_buff('dmbuff', array(
						"name"=>"$ccode"."Drunken Initiate`0",
						"rounds"=>-1,
						"schema"=>"module-specialtydrunkenmaster"
					));
					break;
				case 2:
					output("`7You have learned the ability $ccode"."Basic Drunken Movement`7!`0`n");
					break;
				case 4:
					output("`7You have learned the ability $ccode"."Basic Drunken Boxing`7!`0`n");
					break;
				case 6:
					output("`7You have raised your skills to the level of a $ccode"."Drunken Novice`7!`0`n");
					apply_buff('dmbuff', array(
						"name"=>"$ccode"."Drunken Novice`0",
						"atkmod"=>1.03,
						"defmod"=>1.03,
						"rounds"=>-1,
						"schema"=>"module-specialtydrunkenmaster"
					));
					break;
				case 8:
					output("`7You have learned the ability $ccode"."Breathe Fire`7!`0`n");
					output("`7You will now stay $ccode"."partially drunk`7 all the time!`0`n");
					$drunkeness = get_module_pref("drunkeness","drinks");
					$maxpower = get_module_setting("maxdrunk","drinks");
					if ($drunkeness < floor($maxpower/3)) 
						set_module_pref("drunkeness",floor($maxpower/3),"drinks");
					break;
				case 10:
					output("`7You have fully mastered the ability $ccode"."Minor Alcohol Healing`7 and learned $ccode"."Alcohol Healing`7!`0`n");
					break;
				case 12:
					output("`7You have fully mastered the ability $ccode"."Basic Drunken Movement`7 and learned $ccode"."Drunken Movement`7!`0`n");
					break;
				case 14:
					output("`7You have fully mastered the ability $ccode"."Basic Drunken Boxing`7 and learned $ccode"."Drunken Boxing`7!`0`n");
					break;
				case 16:
					output("`7You have fully mastered the ability $ccode"."Drink`7 and learned $ccode"."Drink like a Demon`7!`0`n");
					break;
				case 18:
					output("`7You have fully mastered the ability $ccode"."Breathe Fire`7 and learned $ccode"."Breathe Giant Flame`7!`0`n");
					output("`7You have raised your skills to the level of an $ccode"."Drunken Adept`7!`0`n");
					apply_buff('dmbuff', array(
						"name"=>"$ccode"."Drunken Adept`0",
						"atkmod"=>1.05,
						"defmod"=>1.05,
						"rounds"=>-1,
						"schema"=>"module-specialtydrunkenmaster"
					));
					break;
				case 20:
					output("`7You have fully mastered the ability $ccode"."Drunken Movement`7 and learned $ccode"."Improved Drunken Movement`7!`0`n");
					output("`7You will now stay $ccode"."mostly drunk`7 all the time!`0`n");
					$drunkeness = get_module_pref("drunkeness","drinks");
					$maxpower = get_module_setting("maxdrunk","drinks");
					if ($drunkeness < floor($maxpower*2/3)) 
						set_module_pref("drunkeness",floor($maxpower*2/3),"drinks");
					break;
				case 22:
					output("`7You have fully mastered the ability $ccode"."Drunken Boxing`7 and learned $ccode"."Improved Drunken Boxing`7!`0`n");
					break;
				case 24:
					output("`7You have fully mastered the ability $ccode"."Alcohol Healing`7 and learned $ccode"."Major Alcohol Healing`7!`0`n");
					break;
				case 25: case 26: 
					output("`7You did not learn a new skill this level, you will gain a new skill in $ccode"."%s`7 proficiency levels.`0`n",28-$dmlevel);
					break;
				case 28:
					output("`7You have learned the ability $ccode"."Drunken Fury`7!`0`n");
					break;
				case 30:
					output("`7You have fully mastered the ability $ccode"."Breathe Giant Flame`7 and learned $ccode"."Breathe Inferno`7!`0`n");
					break;
				case 32:
					output("`7You have fully mastered the ability $ccode"."Drink like a Demon`7 and learned $ccode"."Drink like a Devil`7!`0`n");
					output("`7You have raised your skills to the level of a $ccode"."Drunken Master`7!`0`n");
					apply_buff('dmbuff', array(
						"name"=>"$ccode"."Drunken Master`0",
						"atkmod"=>1.08,
						"defmod"=>1.08,
						"rounds"=>-1,
						"schema"=>"module-specialtydrunkenmaster"
					));
					break;
				case 34:
					output("`7You have fully mastered the ability $ccode"."Improved Drunken Movement`7 and learned $ccode"."Perfect Drunken Movement`7!`0`n");
					break;
				case 36:
					output("`7You have fully mastered the ability $ccode"."Improved Drunken Boxing`7 and learned $ccode"."Perfect Drunken Boxing`7!`0`n");
					break;
				case 38:
					output("`7You will now stay $ccode"."totally drunk`7 all the time!`0`n");
					$drunkeness = get_module_pref("drunkeness","drinks");
					$maxpower = get_module_setting("maxdrunk","drinks");
					if ($drunkeness < $maxpower) 
						set_module_pref("drunkeness",$maxpower,"drinks");
					break;
				case 40:
					output("`7You have fully mastered the ability $ccode"."Major Alcohol Healing`7 and learned $ccode"."Perfect Alcohol Healing`7!`0`n");
					break;
				case 42:
					output("`7You have fully mastered the ability $ccode"."Breathe Inferno`7 and learned $ccode"."Breathe DragonFire`7!`0`n");
					break;
				case 44:
					output("`7You have fully mastered the ability $ccode"."Drunken Fury`7 and learned $ccode"."Improved Drunken Fury`7!`0`n");
					break;
				case 45: case 46: 
					output("`7You did not learn a new skill this level, you will gain a new skill in $ccode"."%s`7 proficiency levels.`0`n",48-$dmlevel);
					break;
				case 48:
					output("`7You have raised your skills to the level of a $ccode"."Drunken Grand Master`7!`0`n");
					apply_buff('dmbuff', array(
						"name"=>"$ccode"."Drunken Grand Master`0",
						"atkmod"=>1.1,
						"defmod"=>1.1,
						"rounds"=>-1,
						"schema"=>"module-specialtydrunkenmaster"
					));
					break;
				case 49: case 50: case 51: case 52: case 53: case 54:
					output("`7You did not learn a new skill this level, you will gain a new skill in $ccode"."%s`7 proficiency levels.`0`n",56-$dmlevel);
					break;
				case 56:
					output("`7You have fully mastered the ability $ccode"."Drink like a Devil`7 and learned $ccode"."Drink like the Gods`7!`0`n");
					output("`7You have fully mastered the ability $ccode"."Improved Drunken Fury`7 and learned $ccode"."Perfect Drunken Fury`7!`0`n");
					output("`n`&`iYou have now earned the ability to select this specialty at any time!`i`0`n");
					break;
				case 100:
					output("`7You have raised your skills to the level of a $ccode"."Drunken Master Guru`7!`0`n");
					apply_buff('dmbuff', array(
						"name"=>"$ccode"."Drunken Master Guru`0",
						"atkmod"=>1.25,
						"defmod"=>1.25,
						"rounds"=>-1,
						"schema"=>"module-specialtydrunkenmaster"
					));
					break;
				default:
					if ($dmlevel > 56)
						output("`7You are the ultimate drunken master; there is nothing more for you to learn!`0`n");
					else
						output("`7You did not learn a new skill this level, you will gain a new skill in $ccode"."1`7 proficiency level.`0`n");
					break;
			}
		}
		else {
			output("`7Your `5Drunken Master `7abilities are slowly improving.`0`n");
		}
?>

<?php
			$drunkpower = get_module_pref("drunkeness","drinks") - get_module_setting("maxdrunk","drinks");
			$dmlevel = get_module_pref("dmlevel");
			$maxpower = (200 + $dmlevel*10)/100;
			$percent = ($drunkpower>=0?floor($drunkpower/$maxpower):0);
			$colorarray = array(1=>"`@",2=>"`2",3=>"`^",4=>"`6",5=>"`q",6=>"`Q",7=>"`$",8=>"`4",9=>"`%",10=>"`5");

			if ($drunkpower >= 0)
				addnav(array("$ccode$name (`@%s%% Drunk"."$ccode)`0",$percent),"");
			else
				addnav("$ccode$name (`\$Sober"."$ccode)`0","");
			addnav(array("$ccode" . "Cooldown: %s%s $ccode"."Rounds`0",$colorarray[$cooldown],$cooldown),"");

//Drinking Powers; l=1
			if ($dmlevel < 16) {
				$percent2 = floor(30/$maxpower);
				addnav(array(" $ccode2&#149; Drink (`@%s%%$ccode2)`0",$percent2),"", true);
			}
			elseif ($dmlevel < 32) {
				$percent2 = floor(60/$maxpower);
				addnav(array(" $ccode2&#149; Drink like a Demon (`@%s%%$ccode2)`0",$percent2),"", true);
			}
			elseif ($dmlevel < 56) {
				$percent2 = floor(120/$maxpower);
				addnav(array(" $ccode2&#149; Drink like a Devil (`@%s%%$ccode2)`0",$percent2),"", true);
			}
			else {
				$percent2 = floor(180/$maxpower);
				addnav(array(" $ccode2&#149; Drink like the Gods (`@%s%%$ccode2)`0",$percent2),"", true);
			}

//Healing Powers; l=4
			if ($dmlevel < 10) {
				$percent2 = floor(35/$maxpower);
				addnav(array(" $ccode2&#149; Minor Alcohol Healing (`\$%s%%$ccode2)`0",$percent2),"", true);
			}
			elseif ($dmlevel < 24) {
				$percent2 = floor(65/$maxpower);
				addnav(array(" $ccode2&#149; Alcohol Healing (`\$%s%%$ccode2)`0",$percent2),"", true);
			}
			elseif ($dmlevel < 40) {
				$percent2 = floor(95/$maxpower);
				addnav(array(" $ccode2&#149; Major Alcohol Healing (`\$%s%%$ccode2)`0",$percent2),"", true);
			}
			else {
				$percent2 = floor(135/$maxpower);
				addnav(array(" $ccode2&#149; Perfect Alcohol Healing (`\$%s%%$ccode2)`0",$percent2),"", true);
			}

//Drunken Movement; l=2
			if ($dmlevel >= 2) {
				if ($dmlevel < 12) {
					$percent2 = floor(75/$maxpower);
					addnav(array(" $ccode2&#149; Basic Drunken Movement (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 20) {
					$percent2 = floor(100/$maxpower);
					addnav(array(" $ccode2&#149; Drunken Movement (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 34) {
					$percent2 = floor(125/$maxpower);
					addnav(array(" $ccode2&#149; Improved Drunken Movement (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				else {
					$percent2 = floor(150/$maxpower);
					addnav(array(" $ccode2&#149; Perfect Drunken Movement (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
			}

//Drunken Boxing; l=3
			if ($dmlevel >= 4) {
				if ($dmlevel < 14) {
					$percent2 = floor(75/$maxpower);
					addnav(array(" $ccode2&#149; Basic Drunken Boxing (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 22) {
					$percent2 = floor(100/$maxpower);
					addnav(array(" $ccode2&#149; Drunken Boxing (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 36) {
					$percent2 = floor(125/$maxpower);
					addnav(array(" $ccode2&#149; Improved Drunken Boxing (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				else {
					$percent2 = floor(150/$maxpower);
					addnav(array(" $ccode2&#149; Perfect Drunken Boxing (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
			}


//Fire Breathing; l=5
			if ($dmlevel >= 8) {
				if ($dmlevel < 18) {
					$percent2 = floor(50/$maxpower);
					addnav(array(" $ccode2&#149; Breathe Fire (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 30) {
					$percent2 = floor(100/$maxpower);
					addnav(array(" $ccode2&#149; Breathe Giant Flame (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 42) {
					$percent2 = floor(150/$maxpower);
					addnav(array(" $ccode2&#149; Breathe Inferno (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				else {
					$percent2 = floor(200/$maxpower);
					addnav(array(" $ccode2&#149; Breathe Dragonfire (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
			}

//Drunken Fury; l=6
			if ($dmlevel >= 28) {
				if ($dmlevel < 44) {
					$percent2 = floor(300/$maxpower);
					addnav(array(" $ccode2&#149; Drunken Fury (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 56) {
					$percent2 = floor(400/$maxpower);
					addnav(array(" $ccode2&#149; Improved Drunken Fury (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				else {
					$percent2 = floor(500/$maxpower);
					addnav(array(" $ccode2&#149; Perfect Drunken Fury (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
			}
?>

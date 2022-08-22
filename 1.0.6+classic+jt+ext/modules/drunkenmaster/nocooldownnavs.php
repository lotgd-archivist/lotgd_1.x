<?php
			$script = $args['script'];
			$drunkpower = get_module_pref("drunkeness","drinks") - get_module_setting("maxdrunk","drinks");
			$dmlevel = get_module_pref("dmlevel");
			$maxpower = (200 + $dmlevel*10)/100;
			$percent = ($drunkpower>=0?floor($drunkpower/$maxpower):0);
			if ($drunkpower >= 0)
				addnav(array("$ccode$name (`@%s%% Drunk"."$ccode)`0",$percent),"");
			else
				addnav("$ccode$name (`\$Sober"."$ccode)`0","");
			addnav("$ccode"."Cooldown: `#Ready to Act`0","");
//Drinking Powers; l=1
			if ($dmlevel < 16) {
				$percent2 = floor(30/$maxpower);
				addnav(array(" $ccode&#149; Drink (`@%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=1&m=1", true);
			}
			elseif ($dmlevel < 32) {
				$percent2 = floor(60/$maxpower);
				addnav(array(" $ccode&#149; Drink like a Demon (`@%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=1&m=2", true);
			}
			elseif ($dmlevel < 56) {
				$percent2 = floor(120/$maxpower);
				addnav(array(" $ccode&#149; Drink like a Devil (`@%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=1&m=3", true);
			}
			else {
				$percent2 = floor(180/$maxpower);
				addnav(array(" $ccode&#149; Drink like the Gods (`@%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=1&m=4", true);
			}

//Healing Powers; l=4
			if ($dmlevel < 10) {
			  $percent2 = floor(35/$maxpower);
			  if($drunkpower >= 35) 
				addnav(array(" $ccode&#149; Minor Alcohol Healing (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=4&m=1", true);
			  else
				addnav(array(" $ccode2&#149; Minor Alcohol Healing (`\$%s%%$ccode2)`0",$percent2),"", true);
			}
			elseif ($dmlevel < 24) {
			  $percent2 = floor(65/$maxpower);
			  if($drunkpower >= 65) 
				addnav(array(" $ccode&#149; Alcohol Healing (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=4&m=2", true);
			  else
				addnav(array(" $ccode2&#149; Alcohol Healing (`\$%s%%$ccode2)`0",$percent2),"", true);
			}
			elseif ($dmlevel < 40) {
			  $percent2 = floor(95/$maxpower);
			  if($drunkpower >= 65) 
				addnav(array(" $ccode&#149; Major Alcohol Healing (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=4&m=3", true);
			  else
				addnav(array(" $ccode2&#149; Major Alcohol Healing (`\$%s%%$ccode2)`0",$percent2),"", true);
			}
			else {
			  $percent2 = floor(135/$maxpower);
			  if($drunkpower >= 135) 
				addnav(array(" $ccode&#149; Perfect Alcohol Healing (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=4&m=4", true);
			  else
				addnav(array(" $ccode2&#149; Perfect Alcohol Healing (`\$%s%%$ccode2)`0",$percent2),"", true);
			}

//Drunken Movement; l=2
			if ($dmlevel >= 2) {
				if ($dmlevel < 12) {
				  $percent2 = floor(75/$maxpower);
				  if($drunkpower >= 75) 
					addnav(array(" $ccode&#149; Basic Drunken Movement (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=2&m=1", true);
				  else
					addnav(array(" $ccode2&#149; Basic Drunken Movement (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 20) {
				  $percent2 = floor(100/$maxpower);
				  if($drunkpower >= 100) 
					addnav(array(" $ccode&#149; Drunken Movement (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=2&m=2", true);
				  else
					addnav(array(" $ccode2&#149; Drunken Movement (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 34) {
				  $percent2 = floor(125/$maxpower);
				  if($drunkpower >= 125) 
					addnav(array(" $ccode&#149; Improved Drunken Movement (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=2&m=3", true);
				  else
					addnav(array(" $ccode2&#149; Improved Drunken Movement (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				else {
				  $percent2 = floor(150/$maxpower);
				  if($drunkpower >= 150) 
					addnav(array(" $ccode&#149; Perfect Drunken Movement (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=2&m=4", true);
				  else
					addnav(array(" $ccode2&#149; Perfect Drunken Movement (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
			}

//Drunken Boxing; l=3
			if ($dmlevel >= 4) {
				if ($dmlevel < 14) {
				  $percent2 = floor(75/$maxpower);
				  if($drunkpower >= 75) 
					addnav(array(" $ccode&#149; Basic Drunken Boxing (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=3&m=1", true);
				  else
					addnav(array(" $ccode2&#149; Basic Drunken Boxing (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 22) {
				  $percent2 = floor(100/$maxpower);
				  if($drunkpower >= 100) 
					addnav(array(" $ccode&#149; Drunken Boxing (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=3&m=2", true);
				  else
					addnav(array(" $ccode2&#149; Drunken Boxing (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 36) {
				  $percent2 = floor(125/$maxpower);
				  if($drunkpower >= 125) 
					addnav(array(" $ccode&#149; Improved Drunken Boxing (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=3&m=3", true);
				  else
					addnav(array(" $ccode2&#149; Improved Drunken Boxing (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				else {
				  $percent2 = floor(150/$maxpower);
				  if($drunkpower >= 150) 
					addnav(array(" $ccode&#149; Perfect Drunken Boxing (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=3&m=4", true);
				  else
					addnav(array(" $ccode2&#149; Perfect Drunken Boxing (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
			}

//Fire Breathing; l=5
			if ($dmlevel >= 8) {
				if ($dmlevel < 18) {
				  $percent2 = floor(50/$maxpower);
				  if($drunkpower >= 50) 
					addnav(array(" $ccode&#149; Breathe Fire (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=5&m=1", true);
				  else
					addnav(array(" $ccode2&#149; Breathe Fire (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 30) {
				  $percent2 = floor(100/$maxpower);
				  if($drunkpower >= 100) 
					addnav(array(" $ccode&#149; Breathe Giant Flame (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=5&m=2", true);
				  else
					addnav(array(" $ccode2&#149; Breathe Giant Flame (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 42) {
				  $percent2 = floor(150/$maxpower);
				  if($drunkpower >= 150) 
					addnav(array(" $ccode&#149; Breathe Inferno (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=5&m=3", true);
				  else
					addnav(array(" $ccode2&#149; Breathe Inferno (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				else {
				  $percent2 = floor(200/$maxpower);
				  if($drunkpower >= 200) 
					addnav(array(" $ccode&#149; Breathe Dragonfire (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=5&m=4", true);
				  else
					addnav(array(" $ccode2&#149; Breathe Dragonfire (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
			}

//Drunken Fury; l=6
			if ($dmlevel >= 28) {
				if ($dmlevel < 44) {
				  $percent2 = floor(300/$maxpower);
				  if($drunkpower >= 300) 
					addnav(array(" $ccode&#149; Drunken Fury (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=6&m=1", true);
				  else
					addnav(array(" $ccode2&#149; Drunken Fury (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				elseif ($dmlevel < 56) {
				  $percent2 = floor(400/$maxpower);
				  if($drunkpower >= 400) 
					addnav(array(" $ccode&#149; Improved Drunken Fury (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=6&m=2", true);
				  else
					addnav(array(" $ccode2&#149; Improved Drunken Fury (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
				else {
				  $percent2 = floor(500/$maxpower);
				  if($drunkpower >= 500) 
					addnav(array(" $ccode&#149; Perfect Drunken Fury (`\$%s%%$ccode)`0",$percent2),$script."op=fight&skill=$spec&l=6&m=3", true);
				  else
					addnav(array(" $ccode2&#149; Perfect Drunken Fury (`\$%s%%$ccode2)`0",$percent2),"", true);
				}
			}
?>
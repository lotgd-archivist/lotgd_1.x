<?php
		$dmlevel = get_module_pref("dmlevel");
		if ($dmlevel >= 1) {
			if ($dmlevel < 6) {
				$buffname = "Drunken Initiate";
				$mod = 1;
			}
			elseif ($dmlevel < 18) {
				$buffname = "Drunken Novice";
				$mod = 1.03;
			}
			elseif ($dmlevel < 32) {
				$buffname = "Drunken Adept";
				$mod = 1.05;
			}
			elseif ($dmlevel < 48) {
				$buffname = "Drunken Master";
				$mod = 1.08;
			}
			elseif ($dmlevel < 100) {
				$buffname = "Drunken Grand Master";
				$mod = 1.1;
			}
			else {
				$buffname = "Drunken Master Guru";
				$mod = 1.25;
			}
			apply_buff('dmbuff', array(
				"name"=>"$ccode"."$buffname`0",
				"atkmod"=>$mod,
				"defmod"=>$mod,
				"rounds"=>-1,
				"schema"=>"module-specialtydrunkenmaster"
			));
		}		
		if ($dmlevel >= 8) {
			if ($dmlevel < 20) {
				set_module_pref("drunkeness",floor(get_module_setting("maxdrunk","drinks")/3),"drinks");
			}
			elseif ($dmlevel < 38) {
				set_module_pref("drunkeness",floor(get_module_setting("maxdrunk","drinks")*2/3),"drinks");
			}
			else {
				set_module_pref("drunkeness",get_module_setting("maxdrunk","drinks"),"drinks");
			}
		}
?>
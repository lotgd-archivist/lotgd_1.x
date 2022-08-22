<?php
		$dmlevel = get_module_pref("dmlevel");
		if ($dmlevel >= 8) {
			$drunkeness = get_module_pref("drunkeness","drinks");
			$maxpower = get_module_setting("maxdrunk","drinks");
			if ($dmlevel < 20) {
				if ($drunkeness < floor($maxpower/3)) 
					set_module_pref("drunkeness",floor($maxpower/3),"drinks");
			}
			elseif ($dmlevel < 38) {
				if ($drunkeness < floor($maxpower*2/3)) 
					set_module_pref("drunkeness",floor($maxpower*2/3),"drinks");
			}
			else {
				if ($drunkeness < $maxpower) 
					set_module_pref("drunkeness",$maxpower,"drinks");
			}
		}
?>
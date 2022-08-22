<?php
		global $charstat_info;
		if (isset($charstat_info['Extra Info']['Drunkeness']))
			unset($charstat_info['Extra Info']['Drunkeness']);
		if (isset($charstat_info['Extra Info']['Sobriety']))
			unset($charstat_info['Extra Info']['Sobriety']);
		$drunkpower = get_module_pref("drunkeness","drinks") - get_module_setting("maxdrunk","drinks");
		$dmlevel = get_module_pref("dmlevel");
		$maxpower = 200 + $dmlevel*10;
		if ($drunkpower > $maxpower) {
			$drunkpower = $maxpower;
			set_module_pref("drunkeness",$maxpower,"drinks");
		}
		if ($drunkpower >= 0) {
			$pct = floor($drunkpower/$maxpower*100);
	                $nonpct = 100-$pct;
			if ($pct > 100) {
				$pct = 100;
				$nonpct = 0;
			} elseif ($pct < 0) {
				$pct = 0;
				$nonpct = 100;
			}
			$level = $pct . translate_inline("% Drunk");
			if ($pct > 90) {
				$color = "#0000ff";
			} elseif ($pct > 60) {
				$color = "#00ff00";
			} elseif ($pct > 25) {
				$color = "#ffff00";
			} else {
				$color = "#ff0000";
			}
		} else {
			$drunkpower = get_module_pref("drunkeness","drinks");
			$maxpower = get_module_setting("maxdrunk","drinks");
			$pct = floor($drunkpower/$maxpower*100);
	                $nonpct = 100-$pct;
			if ($pct > 100) {
				$pct = 100;
				$nonpct = 0;
			} elseif ($pct < 0) {
				$pct = 0;
				$nonpct = 100;
			}
				if ($drunkpower<=5){
					$level = "Sober";
					$color = "#FF1111";
				}
				elseif ($drunkpower <= 20){
					$level = "Buzzed";
					$color = "#D50374";
				}
				elseif ($drunkpower <= 40){
					$level = "Tipsy";
					$color = "#4803D5";
				}
				elseif ($drunkpower <= 60){
					 $level = "Sloshed";
					 $color = "#028073";
				}
				elseif ($drunkpower <= 80){
					 $level = "Stumbling";
					 $color = "#016108";
				}
				else {
					 $level = "Crap-Faced";
					 $color = "#5E4604";
				}
				$level = translate_inline($level);
		}
		$barbgcol = "#777777";
		$drunk = "";
		$drunk .= "`b$level`b";
		$drunk .= "<br />";
		$drunk .= "<table style='border: solid 1px #000000' bgcolor='$barbgcol' cellpadding='0' cellspacing='0' width='70' height='5'><tr><td width='$pct%' bgcolor='$color'></td><td width='$nonpct%'></td></tr></table>";
		setcharstat("Personal Info","Drunkeness",$drunk);


?>
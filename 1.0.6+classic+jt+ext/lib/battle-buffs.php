<?php
// translator ready
// addnews ready
// mail ready
require_once("lib/substitute.php");

function activate_buffs($tag) {
	global $session, $badguy;
	tlschema("buffs");
	reset($session['bufflist']);
	$result = array();
	$result['invulnerable'] = 0;
	$result['dmgmod'] = 1;
	$result['badguydmgmod'] = 1;
	$result['atkmod'] = 1;
	$result['badguyatkmod'] = 1;
	$result['defmod'] = 1;
	$result['badguydefmod'] = 1;
	$result['lifetap'] = array();
	$result['dmgshield'] = array();

	while(list($key,$buff) = each($session['bufflist'])) {
		if (array_key_exists('suspended',$buff) && $buff['suspended']) continue;
		if ($buff['schema']) tlschema($buff['schema']);
		if (isset($buff['startmsg'])) {
			if (is_array($buff['startmsg'])) {
				$buff['startmsg'] = str_replace("`%", "`%%", $buff['startmsg']);
				$msg = sprintf_translate($buff['startmsg']);
				$msg = substitute("`5".$msg."`0`n");
				output_notl($msg); //Here it's already translated
			}else{
				$msg = substitute_array("`5".$buff['startmsg']."`0`n");
				output($msg);
			}
			unset($session['bufflist'][$key]['startmsg']);
		}

		// Figure out activate based on buff features
		$activate = false;
		if ($tag == "roundstart") {
			if (isset($buff['regen'])) $activate = true;
			if (isset($buff['minioncount'])) $activate = true;
		} else if ($tag == "offense") {
			if (isset($buff['invulnerable']) && $buff['invulnerable'])
				$activate = true;
			if (isset($buff['atkmod'])) $activate = true;
			if (isset($buff['dmgmod'])) $activate = true;
			if (isset($buff['badguydefmod'])) $activate = true;
			if (isset($buff['lifetap'])) $activate = true;
			if (isset($buff['damageshield'])) $activate = true;
		} else if ($tag == "defense") {
			if (isset($buff['invulnerable']) && $buff['invulnerable'])
				$activate = true;
			if (isset($buff['defmod'])) $activate = true;
			if (isset($buff['badguyatkmod'])) $activate = true;
			if (isset($buff['badguydmgmod'])) $activate = true;
			if (isset($buff['lifetap'])) $activate = true;
			if (isset($buff['damageshield'])) $activate = true;
		}

		// If this should activate now and it hasn't already activated,
		// do the round message and mark it.
		if ($activate && (!array_key_exists('used',$buff) || !$buff['used'])) {
			// mark it used.
			$session['bufflist'][$key]['used'] = 1;
			// if it has a 'round message', run it.
			if (isset($buff['roundmsg'])) {
				if (is_array($buff['roundmsg'])) {
					$buff['roundmsg'] = str_replace("`%", "`%%", $buff['roundmsg']);
					$msg = sprintf_translate($buff['roundmsg']);
					$msg = substitute("`5".$msg."`0`n");
					output_notl($msg); //Here it's already translated
				}else{
					$msg = substitute_array("`5".$buff['roundmsg']."`0`n");
				output($msg);
				}
			}
		}

		// Now, calculate any effects and run them if needed.
		if (isset($buff['invulnerable']) && $buff['invulnerable']) {
			$result['invulnerable'] = 1;
		}
		if (isset($buff['atkmod'])) {
			$result['atkmod'] *= $buff['atkmod'];
		}
		if (isset($buff['badguyatkmod'])) {
			$result['badguyatkmod'] *= $buff['badguyatkmod'];
		}
		if (isset($buff['defmod'])) {
			$result['defmod'] *= $buff['defmod'];
		}
		if (isset($buff['badguydefmod'])) {
			$result['badguydefmod'] *= $buff['badguydefmod'];
		}
		if (isset($buff['dmgmod'])) {
			$result['dmgmod'] *= $buff['dmgmod'];
		}
		if (isset($buff['badguydmgmod'])) {
			$result['badguydmgmod'] *= $buff['badguydmgmod'];
		}
		if (isset($buff['lifetap'])) {
			array_push($result['lifetap'], $buff);
		}
		if (isset($buff['damageshield'])) {
			array_push($result['dmgshield'], $buff);
		}
		if (isset($buff['regen']) && $tag == "roundstart") {
			$hptoregen = (int)$buff['regen'];
			$hpdiff = $session['user']['maxhitpoints'] -
			$session['user']['hitpoints'];
			// Don't regen if we are above max hp
			if ($hpdiff < 0) $hpdiff = 0;
			if ($hpdiff < $hptoregen) $hptoregen = $hpdiff;
			$session['user']['hitpoints'] += $hptoregen;
			// Now, take abs value just incase this was a damaging buff
			$hptoregen = abs($hptoregen);

			if ($hptoregen == 0) $msg = $buff['effectnodmgmsg'];
			else $msg = $buff['effectmsg'];

			if (is_array($msg)) {
				$msg = sprintf_translate($msg);
				$msg = substitute("`)".$msg."`0`n", array("{damage}"), array($hptoregen));
				output_notl($msg); //Here it's already translated
			}elseif ($msg!="") {
				$msg = substitute_array("`)".$msg."`0`n", array("{damage}"), array($hptoregen));
				output($msg);
			}

		}
		if (isset($buff['minioncount']) && $tag == "roundstart") {
			$who = -1;
			if (isset($buff['maxbadguydamage']) &&
					$buff['maxbadguydamage']  <> 0) {
				$max = $buff['maxbadguydamage'];
				$min = $buff['minbadguydamage'];
				$who = 0;
			} else {
				$max = $buff['maxgoodguydamage'];
				$min = $buff['mingoodguydamage'];
				$who = 1;
			}
			for ($i = 0; $who >= 0 && $i < $buff['minioncount']; $i++) {
				$damage = e_rand($min, $max);
				if ($who == 0) {
					$badguy['creaturehealth'] -= $damage;

				} else if ($who == 1) {
					$session['user']['hitpoints'] -= $damage;
				}
				if ($damage < 0) {
					$msg = $buff['effectfailmsg'];
				} else if ($damage == 0) {
					$msg = $buff['effectnodmgmsg'];
				} else if ($damage > 0) {
					$msg = $buff['effectmsg'];
				}
				if (is_array($msg)) {
					$msg = sprintf_translate($msg);
					$msg = substitute("`)".$msg."`0`n", array("{damage}"), array(abs($damage)));
					output_notl($msg); //Here it's already translated
				}else if ($msg>"") {
					$msg = substitute_array("`)".$msg."`0`n", array("{damage}"), array(abs($damage)));
					output($msg);
				}
			}
		}
		if ($buff['schema']) tlschema();
	}
	tlschema();
	return $result;
}

function process_lifetaps($ltaps, $damage) {
	global $session, $badguy;
	tlschema("buffs");
	reset($ltaps);
	while(list($key,$buff) = each($ltaps)) {
		if (isset($buff['suspended']) && $buff['suspended']) continue;
		if ($buff['schema']) tlschema($buff['schema']);
		$healhp = $session['user']['maxhitpoints'] -
			$session['user']['hitpoints'];
		if ($healhp < 0) $healhp = 0;
		if ($healhp == 0) {
			$msg = $buff['effectnodmgmsg'];
		} else {
			if ($healhp > $damage * $buff['lifetap'])
				$healhp = round($damage * $buff['lifetap'], 0);
			if ($healhp < 0) $healhp = 0;
			if ($damage > 0) {
				$msg = $buff['effectmsg'];
			} else if ($damage == 0) {
				$msg = $buff['effectfailmsg'];
			} else if ($damage < 0) {
				$msg = $buff['effectfailmsg'];
			}
		}
		$session['user']['hitpoints'] += $healhp;
		if (is_array($msg)) {
			$msg = sprintf_translate($msg);
			$msg = substitute("`)".$msg."`0`n", array("{damage}"), array($healhp));
			output_notl($msg); //Here it's already translated
		}else if ($msg>"") {
			$msg = substitute_array("`)".$msg."`0`n", array("{damage}"), array($healhp));
			output($msg);
		}
		if ($buff['schema']) tlschema();
	}
	tlschema();
}

function process_dmgshield($dshield, $damage) {
	global $session, $badguy;
	tlschema("buffs");
	reset($dshield);
	while(list($key,$buff) = each($dshield)) {
		if (isset($buff['suspended']) && $buff['suspended']) continue;
		if ($buff['schema']) tlschema($buff['schema']);
		$realdamage = round($damage * $buff['damageshield'], 0);
		if ($realdamage < 0) $realdamage = 0;
		$msg = "";
		if ($realdamage > 0) {
			if (isset($buff['effectmsg'])) $msg = $buff['effectmsg'];
		} else if ($realdamage == 0) {
			if (isset($buf['effectnodmgmsg'])) $msg = $buff['effectnodmgmsg'];
		} else if ($realdamage < 0) {
			if (isset($buff['effectfailmsg'])) $msg = $buff['effectfailmsg'];
		}
		$badguy['creaturehealth'] -= $realdamage;
		if (is_array($msg)) {
			$msg = sprintf_translate($msg);
			$msg = substitute("`)".$msg."`0`n", array("{damage}"), array($realdamage));
			output_notl($msg); //Here it's already translated
		}else if ($msg>"") {
			$msg = substitute_array("`)".$msg."`0`n", array("{damage}"), array($realdamage));
			output($msg);
		}
		if ($buff['schema']) tlschema();
	}
	tlschema();
}

function expire_buffs() {
	global $session, $badguy;
	tlschema("buffs");
	reset($session['bufflist']);
	while (list($key, $buff) = each($session['bufflist'])) {
		if (array_key_exists('suspended',$buff) && $buff['suspended']) continue;
		if ($buff['schema']) tlschema($buff['schema']);
		if (array_key_exists('used',$buff) && $buff['used']) {
			$session['bufflist'][$key]['used'] = 0;
			if ($session['bufflist'][$key]['rounds']>0)
				$session['bufflist'][$key]['rounds']--;
			if ((int)$session['bufflist'][$key]['rounds'] == 0) {
				if (isset($buff['wearoff']) && $buff['wearoff']) {
					if (is_array($buff['wearoff'])) {
						$buff['wearoff'] = str_replace("`%", "`%%", $buff['wearoff']);
						$msg = sprintf_translate($buff['wearoff']);
						$msg = substitute("`5".$msg."`0`n");
						output_notl($msg); //Here it's already translated
					}else{
						$msg = substitute_array("`5".$buff['wearoff']."`0`n");
					output($msg);
					}
				}
				//unset($session['bufflist'][$key]);
				strip_buff($key);
			}
		}
		if ($buff['schema']) tlschema();
	}
	tlschema();
}

?>

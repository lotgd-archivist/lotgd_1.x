<?php
function cities_dangerscale($danger)
{
	global $session;
	$dlevel = ($danger ?
			get_module_setting("dangerchance"):
			get_module_setting("safechance"));
	if ($session['user']['dragonkills'] <= 1) $dlevel = round(.50*$dlevel, 0);
	elseif ($session['user']['dragonkills'] <= 30) {
		$scalef = 50/29;
		$scale = (($session['user']['dragonkills']-1)*$scalef + 50)/100;
		$dlevel = round($scale*$dlevel, 0);
	} // otherwise, dlevel is unscaled.
	return $dlevel;
}
?>
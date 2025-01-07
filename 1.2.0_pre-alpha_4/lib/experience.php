<?php
// translator ready
// addnews ready
// mail ready
// phpDocumentor ready

/**
 * Returns the experience needed to advance to the next level.
 *
 * @param int $curlevel The current level of the player.
 * @param int $label The current number of dragonkills.
 * @return int The amount of experience needed to advance to the next level.
 */
function exp_for_next_level($curlevel, $curdk)
{
	$exparray = explode(',', Settings::getsetting('experiance', '100,400,1002,1912,3140,4707,6641,8985,11795,15143,19121,23840,29437,36071,43930'));
	array_unshift($exparray, 0);

	if ($curlevel < 1) return 0;
	
	if (!isset($exparray[$curlevel])) {
		$exparray[count($exparray)-1] += $curlevel;
		$curlevel = count($exparray)-1;
	}

	$exparray[$curlevel] += round(($curdk/4) * $curlevel * 100, 0);

	$exprequired = $exparray[$curlevel];
	return $exprequired;
}

?>

<?php
// translator ready
// addnews ready
// mail ready
function exp_for_next_level($curlevel, $curdk)
{
	$exparray = array(1=>100,2=>400,3=>1002,4=>1912,5=>3140,6=>4707,
			7=>6641,8=>8985, 9=>11795,10=>15143,11=>19121,12=>23840,
			13=>29437,14=>36071,15=>43930);

	if ($curlevel < 1) return 0;

	while(list($key,$val) = each($exparray)) {
		$exparray[$key] = round($val + ($curdk/4) * $key * 100, 0);
	}
	if ($curlevel > 15) $curlevel = 15;
	$exprequired = $exparray[$curlevel];
	return $exprequired;
}

?>

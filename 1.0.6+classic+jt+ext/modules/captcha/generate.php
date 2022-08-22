<?php
function captcha_pickletter()
{
	$allcaps = get_module_setting("allcaps");
	$str = "ABCDEFGHJKMNPQRSTUVWXYZ!@#$%&23456789?";
	$n = mt_rand(0, strlen($str)-1);
	$l = $str{$n};
	if ($l >= "a" && $l <= "z") {
		if (!$allcaps && mt_rand(0, 1) == 1) $l = strtolower($l);
	}
	return $l;
}

function captcha_generate()
{
	global $session;
	$max = get_module_setting("length");
	$allowdupes = get_module_setting("allowdupes");
	$text = "";
	for ($i = 0; $i < $max; $i++) {
		$letter = captcha_pickletter();
		while(!$allowdupes && stristr($text, $letter)) {
			$letter = captcha_pickletter();
		}
		$text .= $letter;
	}
	return $text;
}
?>

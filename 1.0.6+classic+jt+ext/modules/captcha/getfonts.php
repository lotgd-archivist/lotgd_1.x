<?php
function captcha_getfonts($fontdir)
{
	$fonts = array();
	$d = dir($fontdir);
	while ($entry = $d->read()) {
		if ($entry[0] == '.') continue;
		if (!preg_match("/\.ttf$/i", $entry)) continue;
		$fonts[] = $entry;
	}
	sort($fonts);
	return $fonts;
}
?>

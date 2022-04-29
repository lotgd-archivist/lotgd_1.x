<?php
function get_partner()
{
	global $session;
	$partner = getsetting("barmaid", "`%Violet");
	if ($session['user']['sex'] != SEX_MALE) {
		$partner = getsetting("bard", "`^Seth");
	}
	tlschema("partner");
	$t = translate_inline($partner);
	tlschema();
	return $t;
}

?>

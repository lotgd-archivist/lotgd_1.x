<?php
// addnews ready
// translator ready
// mail ready
require_once("lib/modules.php");

function holidayize($text,$type='unknown'){
	global $user;
	if (!array_key_exists('ihavenocheer', $user->prefs)) {
		if(!is_array($user->prefs)) $user->prefs = array();
		$user->prefs = $user->prefs + array('ihavenocheer' => 0);
	}
	if ($user->prefs['ihavenocheer']) {
		return $text;
	}

	$args = array('text'=>$text,'type'=>$type);
	$args = modulehook("holiday", $args);
	$text = $args['text'];

	return $text;
}

?>

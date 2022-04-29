<?php
// translator ready
// addnews ready
// mail ready
require_once("lib/dbwrapper.php");

function get_player_title($old=false) {
	global $session;
	$title = "";
	if ($old === false) {
		$title = $session['user']['title'];
		if ($session['user']['ctitle']) $title = $session['user']['ctitle'];
	} else {
		$title = $old['title'];
		if ($old['ctitle']) $title = $old['ctitle'];
	}
	return $title;
}

function get_player_basename($old=false) {
	global $session;
	$name = "";
	$title = get_player_title($old);
	if ($old===false) {
		$name = $session['user']['name'];
	} else {
		$name = $old['name'];
	}
	$x = strpos($name, $title);
	if ($x !== false)
		$name = trim(substr($name,$x+strlen($title)));
	return str_replace("`0", "", $name);
}

function change_player_name($newname, $old=false) {
	$title = get_player_title($old);

	$newname = str_replace("`0", "", $newname);
	$x = strpos($newname, $title);
	if ($x !== false)
		$newname = trim(substr($newname, $x+strlen($title)));
	$newname =  $title . " " . $newname . "`0";
	return $newname;
}

function change_player_ctitle($nctitle,$old=false) {
	global $session;
	if ($nctitle == "") {
		if ($old == false) {
			$nctitle = $session['user']['title'];
		} else {
			$nctitle = $old['title'];
		}
	}
	$newname = $nctitle." ".get_player_basename($old)."`0";
	return $newname;
}

function change_player_title($ntitle, $old=false) {
	global $session;
	if ($old===false) {
		$newname = $session['user']['name'];
		$ctitle = $session['user']['ctitle'];
	} else {
		$newname = $old['name'];
		$ctitle = $old['ctitle'];
	}
	if ($ctitle == "") {
		$newname = $ntitle." ".get_player_basename($old)."`0";
	}
	return $newname;
}

?>

<?php
// translator ready
// addnews ready
// mail ready
function httpget($var){
	$res = isset($_GET[$var]) ? $_GET[$var] : false;
	return $res;
}

function httpallget() {
	return $_GET;
}

function httpset($var, $val,$force=false){
	if (isset($_GET[$var]) || $force) $_GET[$var] = $val;
}

function httppost($var){
	$res = isset($_POST[$var]) ? $_POST[$var] : false;
	return $res;
}

function httppostisset($var) {
	$res = isset($_POST[$var]) ? 1 : 0;
	return $res;
}

function httppostset($var, $val, $sub=false){
	if ($sub === false) {
		if (isset($_POST[$var])) $_POST[$var] = $val;
	} else {
		if (isset($_POST[$var]) && isset($_POST[$var][$sub]))
			$_POST[$var][$sub]=$val;
	}
}

function httpallpost(){
	return $_POST;
}

function postparse($verify=false, $subval=false){
	if ($subval) $var = $_POST[$subval];
	else $var = $_POST;

	reset($var);
	$sql = "";
	$keys = "";
	$vals = "";
	$i = 0;
	while(list($key, $val) = each($var)) {
		if ($verify === false || isset($verify[$key])) {
			if (is_array($val)) $val = addslashes(serialize($val));
			$sql .= (($i > 0) ? "," : "") . "$key='$val'";
			$keys .= (($i > 0) ? "," : "") . "$key";
			$vals .= (($i > 0) ? "," : "") . "'$val'";
			$i++;
		}
	}
	return array($sql, $keys, $vals);
}
?>

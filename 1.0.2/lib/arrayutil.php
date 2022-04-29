<?php
// translator ready
// addnews ready
// mail ready
function createstring($array){
	if (is_array($array)){
		$out = serialize($array);
	}
	return $out;
}

function createarray($string){
	$out = unserialize($string);
	if (!is_array($out)){
		//legacy support.  Need to leave in 0.9.8, but can remove for 0.9.9
		$arr1 = split("\"",$string);
		$out = array();
		while (list($key,$val)=each($arr1)){
			$arr2=split("\"",rawurldecode($val));
			$out[rawurldecode($arr2[0])] = rawurldecode($arr2[1]);
		}
	}
	return $out;
}

?>

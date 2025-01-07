<?php
// translator ready
// addnews ready
// mail ready

function createstring($array){
	if (is_array($array) || is_object($array)){
		$out = serialize($array);
	} else {
		$out = (string)$array;
	}
	return $out;
}

?>

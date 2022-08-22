<?php
// addnews ready
// translator ready
// mail ready
// Set error reporting to all but notice (for now)
error_reporting (E_ALL ^ E_NOTICE);
#error_reporting (E_ALL);

function set_magic_quotes(&$vars) {
	if (is_array($vars)) {
		reset($vars);
		foreach($vars as $key => $val)
			set_magic_quotes($vars[$key]);
	}else{
		$vars = addslashes($vars);
	}
}


//do some cleanup here to make sure magic_quotes_gpc is ON
set_magic_quotes($_GET);
set_magic_quotes($_POST);
set_magic_quotes($_SESSION);
set_magic_quotes($_COOKIE);

// magic_quotes_runtime is OFF
//set_magic_quotes_runtime(0);
?>

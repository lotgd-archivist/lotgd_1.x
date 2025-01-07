<?php
// addnews ready
// translator ready
// mail ready
// Set error reporting to all but notice (for now)
error_reporting (E_ALL ^ E_NOTICE);
#error_reporting (E_ALL | E_STRICT);

function set_magic_quotes(&$vars) {
	if (is_array($vars)) {
		foreach (array_keys($vars) as $key) {
			set_magic_quotes($vars[$key]);
		}
	}else{
		$vars = addslashes($vars);
	}
}

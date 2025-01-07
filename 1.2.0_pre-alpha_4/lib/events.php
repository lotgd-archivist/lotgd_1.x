<?php
// addnews ready
// translator ready
// mail ready
require_once("lib/constants.php");
require_once("lib/http.php");

// This file encapsulates all the special event handling for most locations

// Returns whether or not the description should be skipped
function handle_event($location, $baseLink=false, $needHeader=false)
{
	if ($baseLink === false){
		global $PHP_SELF;
		$baseLink = substr($PHP_SELF,strrpos($PHP_SELF,"/")+1)."?";
	}else{
		//debug("Base link was specified as $baseLink");
		//debug(debug_backtrace());
	}
	global $user;
	$skipdesc = false;

	tlschema("events");
	$allowinactive = false;
	$eventhandler = httpget('eventhandler');
	if (($user->superuser & SU_DEVELOPER) && $eventhandler!=""){
		$allowinactive = true;
		$array = preg_split("/[:-]/", $eventhandler);
		if ($array[0] == "module") {
			$user->specialinc = "module:" . $array[1];
		} else {
			$user->specialinc = "";
		}
	}

	$_POST['i_am_a_hack'] = 'true';

	if ($user->specialinc!=""){
		$specialinc = $user->specialinc;
		$user->specialinc = "";
		if ($needHeader !== false) {
			page_header($needHeader);
		}

		output("`^`c`bSomething Special!`c`b`0");
		if (strchr($specialinc, ":")) {
			$array = explode(':', $specialinc);
			$starttime = getmicrotime();
			module_do_event($location, $array[1], $allowinactive,$baseLink);
			$endtime = getmicrotime();
			if (($endtime - $starttime >= 1.00 && ($user->superuser & SU_DEBUG_OUTPUT))){
				debug("Slow Event (".round($endtime-$starttime,2)."s): $location`n");
			}
		}
		if (checknavs()) {
			// The page rendered some linkage, so we just want to exit.
			page_footer();
		} else {
			$skipdesc=true;
			$user->specialinc = "";
			$user->specialmisc = "";
			httpset("op", "");
		}
	}
	tlschema();
	return $skipdesc;
}
?>

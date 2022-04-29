<?php
// translator ready
// addnews ready
// mail ready
define("OVERRIDE_FORCED_NAV",true);
require_once("common.php");
require_once("lib/villagenav.php");

tlschema("badnav");

if ($session['user']['loggedin'] && $session['loggedin']){
	if (strpos($session['output'],"<!--CheckNewDay()-->")){
		checkday();
	}
	while (list($key,$val)=each($session['allowednavs'])){
		//hack-tastic.
		if (
			trim($key)=="" ||
			$key===0 ||
			substr($key,0,8)=="motd.php" ||
			substr($key,0,8)=="mail.php"
		) unset($session['allowednavs'][$key]);
	}
	if (!is_array($session['allowednavs']) ||
			count($session['allowednavs'])==0 || $session['output']=="") {
		$session['allowednavs']=array();
		page_header("Your Navs Are Corrupted");
		if ($session['user']['alive']) {
			villagenav();
			output("Your navs are corrupted, please return to %s.",
					$session['user']['location']);
		} else {
			addnav("Return to Shades", "shades.php");
			output("Your navs are corrupted, please return to the Shades.");
		}
		page_footer();
	}
	echo $session['output'];
	$session['debug']="";
	$session['user']['allowednavs']=$session['allowednavs'];
	saveuser();
}else{
	$session=array();
	redirect("index.php");
}

?>

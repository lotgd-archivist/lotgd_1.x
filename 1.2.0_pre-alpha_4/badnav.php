<?php
// translator ready
// addnews ready
// mail ready
define("OVERRIDE_FORCED_NAV",true);
require_once("common.php");
require_once("lib/villagenav.php");

tlschema("badnav");
if ($user->loggedin && Session::get('loggedin')){
	if (strpos(Session::get('output'),"<!--CheckNewDay()-->")){
		checkday();
	}
	$navs = Session::get('allowednavs');
	if(!is_array($navs)) {
		$navs = @unserialize($navs);
	}
	foreach ($navs as $key=>$val){
		//hack-tastic.
		if (
			trim($key)=="" ||
			$key===0 ||
			substr($key,0,8)=="motd.php" ||
			substr($key,0,8)=="mail.php"
		) unset($navs[$key]);
	}
	$sql="SELECT output FROM ".DB::prefix("accounts_output")." WHERE acctid={$user->acctid};";
	$result=DB::query($sql);
	$row=DB::fetch_assoc($result);
	if (!is_array($navs) ||
			count($navs)==0 || !$row['output'] || !($row['output'] = @gzuncompress($row['output']))) {
		$navs=array();
		page_header("Your Navs Are Corrupted");
		if ($user->alive) {
			villagenav();
			output("Your navs are corrupted, please return to %s.",
					$user->location);
		} else {
			addnav("Return to Shades", "shades.php");
			output("Your navs are corrupted, please return to the Shades.");
		}
		page_footer();
	}
	echo $row['output'];
	Session::delete('debug');
	$user->allowednavs=$navs;
	Session::set('allowednavs', $navs);
	saveuser();
}else{
	Session::clean();
	translator_setup();
	redirect("index.php");
}

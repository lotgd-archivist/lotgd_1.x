<?php
// PRIVATE_CODE
/*
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-76543-1";
urchinTracker();
</script>
*/
function google_analytics_getmoduleinfo(){
	$info = array(
		"name"=>"Google Analytics Plugin",
		"version"=>"1.0",
		"author"=>"Eric Stevens",
		"category"=>"General",
		"download"=>"core_module",
		"allowanonymous"=>true,
		"override_forced_nav"=>true,
		"settings"=>array(
			"Google Analytics,title",
			"uacct"=>"User account (the value inside the quotes after '_uacct = ')|",
		),
	);
	return $info;
}

function google_analytics_install(){
	module_addhook("everyfooter");
	return true;
}

function google_analytics_uninstall(){
	return true;
}

function google_analytics_dohook($hookname,$args){
	
	rawoutput('<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "'.get_module_setting('uacct').'";
urchinTracker();
</script>');
	return $args;
}
?>

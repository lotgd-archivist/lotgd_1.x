<?php

function domain_move_getmoduleinfo(){
	$info = array(
		"name"=>"Domain Has Moved Notifier",
		"version"=>"1.0",
		"author"=>"Eric Stevens",
		"category"=>"Administrative",
		"download"=>"core_module",
		"settings"=>array(
			"Domain Has Moved Notifier Settings,title",
			"valid_list"=>"RegExp of valid domain names|http:\\/\\/logd\\.dragoncat\\.net",
			"If left blank all domains are considered invalid except those specifically matching the invalid expression.,note",
			"invalid_list"=>"RegExp of invalid domain names|http:\\/\\/new-logd\\.dragoncat\\.net",
			"dest_url"=>"Actual value for live server.|http://new-logd.dragoncat.net/",
			"message"=>"Message to display,textarea|This website is in the process of moving.  It may take up to a day for the DNS to propogate and have the name logd.dragoncat.net point to the new server.  Until then, you can access the game at 'http://new-logd.dragoncat.net'."
		),
	);
	return $info;
}

function domain_move_install(){
	module_addhook("header-home");
	module_addhook("validatesettings");
	return true;
}

function domain_move_uninstall(){
	return true;
}

function domain_move_dohook($hookname,$args){
	$valid = get_module_setting("valid_list");
	$invalid = get_module_setting("invalid_list");
	$dest = get_module_setting("dest_url");
	$message = get_module_setting("message");
	
	switch($hookname){
	case "validatesettings":
		$reset = false;
		if ($args['valid_list'] > ''){
			$reg = "/".stripslashes($args['valid_list'])."/";
			if (!preg_match($reg,$args['dest_url'])){
				$args['valid_list'] = $valid;
				$args['invalid_list'] = $invalid;
				$args['dest_url'] = $dest;
				$args['message'] = $message;
				$reset = true;
				output("`\$The destination URL you provided does not match against the regular expression provided for valid addresses, or it is an invalid expression.  Your settings have been restored.`0`n");
			}
		}
		if ($args['invalid_list'] > '' && !$reset){
			$reg = "/".stripslashes($args['invalid_list'])."/";
			if (preg_match($reg,$args['dest_url'])){
				$args['valid_list'] = $valid;
				$args['invalid_list'] = $invalid;
				$args['dest_url'] = $dest;
				$args['message'] = $message;
				output("`\$The destination URL you provided matches against the regular expression provided for invalid addresses, or it is an invalid expression.  Your settings have been restored.`0`n");
			}
		}
		break;
	case "header-home":
		$fail = true;
		global $REQUEST_URI;
		$url = "http://".$_SERVER['HTTP_HOST']."/".$REQUEST_URI;
		//if ($valid > ""){
		//	if (!preg_match("/$valid/",$url)){
		//		$fail = true;
		//	}
		//}
		//if ($invalid > ""){
		//	if (preg_match("/$invalid/",$url)){
		//		$fail = true;
		//	}
		//}
		if ($fail){
			popup_header("This website has moved");
			clearnav();
			output("`\$This website has moved.`n");
			rawoutput("<blockquote>");
			rawoutput($message);
			rawoutput("</blockquote>");
			output("`n<a href=\"%s\">Please click here to continue.</a>",$dest,true);
			popup_footer();
		}
		break;
	}
	return $args;
}
?>

<?php
// translator ready
// addnews ready
// mail ready

function faqmute_getmoduleinfo(){
	$info = array(
		"name"=>"Newbie Mute",
		"version"=>"1.0",
		"author"=>"Booger",
		"category"=>"Administrative",
		"download"=>"core_module",
		"prefs"=>array(
			"Newbie Mute User Prefs, title",
			"seenfaq"=>"Has the player seen the FAQ,bool|0",
		)
	);
	return $info;
}

function faqmute_install(){
	module_addhook("insertcomment");
	module_addhook("faq-posttoc");
	return true;
}

function faqmute_uninstall(){
	return true;
}

function faqmute_dohook($hookname,$args){
	global $session;
	$seen=get_module_pref("seenfaq");
	switch ($hookname) {
	case "insertcomment":
		if (!$seen && !$session['user']['dragonkills']) {
			$args['mute']=1;
			$mutemsg="`n`\$You have to read the FAQ before you can post comments. You can find it in any town.`0`n`n";
			$mutemsg=translate_inline($mutemsg);
			$args['mutemsg']=$mutemsg;
		}
		break;
	case "faq-posttoc":
		if (!$seen) set_module_pref("seenfaq",true);
		break;
	}
	return $args;
}

function faqmute_run(){
}
?>
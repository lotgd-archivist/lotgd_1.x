<?php

function chatcolor_getmoduleinfo() {
	$info = array(
		"name"=>"User-settable Chat Colors",
		"version"=>"1.0",
		"author"=>"JT Traub",
		"category"=>"General",
		"download"=>"",
		"prefs"=>array(
			"User-settable Chat Color Preferences,title",
			"user_speech"=>"Color code to use for speaking|",
			"user_emote"=>"Color code to use for emotes|",
		),
	);
	return $info;
}

function chatcolor_install() {
	module_addhook("commentary");
	module_addhook("commentary-js");
	return(true);
}

function chatcolor_uninstall() {
	return(true);
}

function chatcolor_dohook($hookname, $args) {
	switch($hookname) {
	case "commentary-js":
		$speechC = sanitize_html(get_module_pref("user_speech"));
		$emoteC = sanitize_html(get_module_pref("user_emote"));
		if ($emoteC{0} != '`') $emoteC = "";
		if ($speechC{0} != '`') $speechC = "";
		$args['speech'] = $speechC;
		$args['emote'] = $emoteC;
		break;
	case "commentary":
		$comment = $args['commentline'];
		$fchar = $comment{0};
		$speechC = sanitize_html(get_module_pref("user_speech"));
		$emoteC = sanitize_html(get_module_pref("user_emote"));
		// Break early if the first char in the comment is a '`' (ie, they
		// have already colorized it.
		if ($fchar == "`") break;

		// Is this a possible emote?
		if ($fchar == ":" || $fchar == "/") {
			if (!$emoteC || $emoteC{0} != '`') break;
			if ($fchar == "/" && substr($comment,0,3)!="/me") break;
			// Okay, it  is now either :, :: or /me
			if ($fchar == "/") {
				$comment = trim(substr($comment,3));
				if ($comment{0} == "`") break;
			} else {
				if ($comment{1} == ":") {
					$comment = trim(substr($comment,2));
				} else {
					$comment = trim(substr($comment,1));
				}
			}
			if ($comment{0} == ":") $comment = " " . $comment;
			$comment = ":" . $emoteC . $comment;
		} else {
			if (!$speechC || $speechC{0} != '`') break;
			$comment = $speechC . $comment;
		}
		$args['commentline'] = $comment;
		break;
	}
	return $args;
}

function chatcolor_run() {
}

?>

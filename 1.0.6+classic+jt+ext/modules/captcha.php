<?php
// PRIVATE_CODE
function captcha_getmoduleinfo(){
	$info = array(
		"name"=>"Captcha",
		"version"=>"1.0",
		"author"=>"JT Traub",
		"category"=>"Administrative",
		"download"=>"http://www.dragoncat.net/~jtraub/captcha.html",
		"settings"=>array(
			"Captcha Settings,title",
			"allcaps"=>"Should captchas be all uppercase,bool|1",
			"allowdupes"=>"Should captchas allow duplicates of the same letter,bool|0",
			"reward"=>"Should players be rewarded with a gem (max 1 per day) for the inconvenience of answering?,bool|1",
			"pagehits"=>"How many page hits (on average) should happen between captchas,int|75",
			"mistakes"=>"Allow how many mistakes in the captcha to still be treated as success?,range,0,2,1|0",
			"length"=>"How many characters to use for the captcha?,range,4,8,1|6",
			"lockouttime"=>"How many hours should captcha failure lock out for?,range,1,5,1|2",
			"maxfailures"=>"How many failures must occur in a row before locking out the user?,range,1,5,1|3",
			"Captcha Story Settings,title",
			"`#The story introduction has 4 substitutions (%s tokens) that must be provided in the string.`nThe four substitutions in order are:`n1) The number of failures before lockout occurs.`n2) The word time or times. (based on the number of failures)`n3) The number of hours for which the player will be locked out.`n4) The word hour or hours. (based on the amount),note",
			"storysetup"=>"Introduction of the story,textarea|In order to detect and prevent cheating by robots and other forms of auto-players, we have instituted a 'human detection' system on this server.  Please read the instructions below and follow them.`n`nView the image below and then enter the letters, numbers or symbols into the provided entry box.`n`nIf you fail to enter the correct string %s %s in a row, you will be locked out of the game for %s %s.`n`n",
			"storyreward"=>"Story reward explanation,textarea|`n`nAs a consolation for being asked to do this, any real day on which you answer at least one captcha correctly you will be rewarded with one gem.`n`n",
			"`#If the success text is empty player will just be automatically redirected,note",
			"successheader"=>"Header for success page,string|",
			"storysuccess"=>"Text for successful input,textarea|",
			"continuenav"=>"Continuation nav text,string|",
			"failureheader"=>"Header for failure page,string|",
			"`#The lockout message has two substitutions (%s tokens) which must be provide in the string.`nThe two substitutions in order are:`n1) The number of hours for which the player will be locked out.`n2) the word hour or hours. (based on the amount),note",

			"lockoutmsg"=>"Message when player is locked out,textarea|You have been locked out for %s %s.",
			"retrymsg"=>"Message when player needs to retry,textarea|Please try again.",
		),
		"prefs"=>array(
			"Captcha User Preferences,title",
			"lastcaptcha"=>"Captcha which was last displayed,viewonly",
			"curfail"=>"How many captchas in a row have been failed?,viewonly",
			"hitsleft"=>"How many hits until the person gets their next captcha?,int|0",
			"nocaptcha"=>"Should this user be exempt from getting captchas?,bool|0",
			"lastrewarded"=>"Last date this player was rewarded,|0000-00-00",
			"lastlockout"=>"Last date this player was locked out (GMT),|0000-00-00 00:00:00",
			"interceptpage"=>"Where was the person going when we took over,viewonly|",
			"inprogress"=>"Is a captcha in progress? (make sure the players cannot just close down when they hit a captcha),bool|0",
		),
	);
	return $info;
}

function captcha_install(){
	// Check for the font directory
	$fontdir = "modules/captcha/fonts";
	if (!is_dir($fontdir)) {
		output("You must create the directory '$fontdir'.`n");
		return false;
	}
	require_once("modules/captcha/getfonts.php");
	$fonts = captcha_getfonts($fontdir);
	if (count($fonts) == 0) {
		output("You must install some truetype fonts into the captcha font directory '$fontdir'.`n");
		return false;
	}
	module_addhook("check-login");
	module_addhook_priority("everyheader", 10);
	return true;
}

function captcha_uninstall()
{
	return true;
}

function captcha_dohook($hookname,$args){
	require_once("modules/captcha/hooks.php");
	return captcha_dohookinternal($hookname, $args);
}

function captcha_runevent($type,$link)
{
}

function captcha_run(){
	require_once("modules/captcha/execute.php");
	captcha_run_internal();
}
?>

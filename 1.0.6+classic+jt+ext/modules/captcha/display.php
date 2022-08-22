<?php
function captcha_display()
{
	$fails = get_module_setting("maxfailures");
	$time = get_module_setting("lockouttime");

	require_once("lib/nltoappon.php");
	$intro = nltoappon(get_module_setting("storysetup"));
	if ($intro) {
		output($intro, $fails, translate_inline($fails == 1?"time":"times"), $time, translate_inline($time==1?"hour":"hours"));
	} else {
		output("In order to detect and prevent cheating by robots and other forms of auto-players, we have instituted a 'human detection' system on this server.");
		output("Please read the instructions below and follow them.`n`n");
		output("View the image below and then enter the letters, numbers or symbols into the provided entry box.`n`n");
		output("If you fail to enter the correct string %s %s in a row, you will be locked out of the game for %s %s.`n`n", $fails, translate_inline($fails == 1?"time":"times"), $time, translate_inline($time==1?"hour":"hours"));
	}

	require_once("modules/captcha/generate.php");
	$captcha = captcha_generate();
	set_module_pref("lastcaptcha", $captcha);
	$link = "runmodule.php?module=captcha&op=display";
	$link = appendcount($link);
	rawoutput("<center>");
	rawoutput("<img src='$link' noborder>");
	addnav("", $link);
	$link2 = "runmodule.php?module=captcha";
	$link2 = appendcount($link2);
	rawoutput("<form action='$link2' method='post'>");
	rawoutput("<input name='answer' id='answer'>");
	$sub = translate_inline("Submit");
	rawoutput("<input type='submit' name='submit' value='$sub' class='button'>");
	rawoutput("</form>");
	addnav("", $link2);
	rawoutput("<script language='JavaScript'>document.getElementById('answer').focus();</script>");
	rawoutput("</center>");
	output("`n`nPlease note:");
	if (get_module_setting("allcaps")) {
		output("The valid characters are: ABCDEFGHJKMNPQRSTUVWXYZ!@#$%&?23456789`n");
		output("The characters I, L, 1, O, and 0 will not appear in the image.`n`n");
	} else {
		output("The valid characters are: ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz!@#$%&?23456789`n");
		output("The characters I, i, L, l, 1, O, o, and 0 will not appear in the image.`n`n");
	}
	if (!get_module_setting("allowdupes")) {
		output("No character will appear more than once in the image.`n");
	}
	if (get_module_setting("reward")) {
		$reward = nltoappon(get_module_setting("storyreward"));
		if ($reward) {
			output($reward);
		} else {
			output("`n`nAs a consolation for being asked to do this, any real day on which you answer at least one captcha correctly you will be rewarded with one gem.`n`n");
		}
	}
}

?>

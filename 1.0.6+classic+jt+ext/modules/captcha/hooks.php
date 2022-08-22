<?php
function captcha_nexthit()
{
	$l = get_module_setting("pagehits");
	$m = round($l/4, 0);
	$l += mt_rand(-$m, $m);
	set_module_pref("hitsleft", $l);
}

function captcha_dohookinternal($hookname, $args)
{
	global $session;
	switch ($hookname) {
	case "check-login":
		// check the players login status
		$now = strtotime(gmdate("Y-m-d H:i:s") . " GMT");
		$then = strtotime(get_module_pref("lastlockout"));
		if ($now - $then < (3600*get_module_setting("lockouttime"))) {
			page_header("Lockout");
			output("You are currently locked out of the game for having failed the image test too many times in a row.`n");
			addnav("Return to Login", "index.php");
			page_footer();
		}
		break;
	case "everyheader":
		// We don't want captchas when they aren't logged in
		if (!$session['loggedin'] || !$session['user']['loggedin']) {
			break;
		}
		$hleft = get_module_pref("hitsleft");
		if ($hleft <= 0) {
			// This user has no hits left, that means we need to reset his
			// captcha time!
			captcha_nexthit();
			$hleft = get_module_pref("hitsleft");
		}
		if (count(httpallpost()) != 0) {
			// Someone is submitting a form.  Don't break it.
			break;
		}
		// Okay, check if this person is exempt
		$nocap = get_module_pref("nocaptcha");
		if ($nocap) break;
		// Okay.. they are not exempt.  Decrement the counter
		if (!get_module_pref("inprogress")) {
			$hleft--;
			set_module_pref("hitsleft", $hleft);
			// Okay.. they are not up for a new captcha yet.
			if ($hleft) break;
			// Captcha INTERCEPT
			$page = substr($_SERVER['REQUEST_URI'], 1);
			set_module_pref("interceptpage", $page);
			set_block_new_navs(true);
			set_block_new_output(true);
			page_header("Bot detection");
			set_block_new_navs(false);
			set_block_new_output(false);
			require_once("modules/captcha/display.php");
			captcha_display();
			captcha_nexthit();
			// And end the page early.
			set_block_new_navs(true);
			set_block_new_output(true);
			page_footer();
			set_block_new_navs(false);
			set_block_new_output(false);
		}
		break;
	}
	return $args;
}

?>

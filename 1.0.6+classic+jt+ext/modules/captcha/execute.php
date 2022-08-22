<?php
function captcha_drawletter(&$im, $sz, $rot, $xcent, $ycent, $color, $font, $letter)
{
	$bbox = imagettfbbox($sz, 0, $font, $letter);
	$x0 = $bbox[6];
	$y0 = -$bbox[7];
	$bbox[5] = -$bbox[5] + $bbox[1];
	$bbox[7] = -$bbox[7] + $bbox[3];
	$bbox[1] = 0;
	$bbox[3] = 0;
	$xc = ($bbox[0] + $bbox[2])/2.0;
	$yc = ($bbox[1] + $bbox[7])/2.0;
	$rad = $rot * pi() / 180.0;
	$sa = sin($rad);
	$ca = cos($rad);
	$x1 = $x0 - $xc;
	$y1 = $y0 - $yc;
	$px = $xcent;
	$py = $ycent;
	$x2 = intval($x1 * $ca + $y1 * $sa + $px + 0.5);
	$y2 = intval(-$x1 * $sa + $y1 * $ca + $py + 0.5);
	imagettftext($im, $sz, $rot, $x2, $y2, $color, $font, $letter);
}

function captcha_run_internal()
{
	global $session;
	$op = httpget('op');
	if ($op == "display") {
		$text = get_module_pref("lastcaptcha");
		$im = ImageCreateTrueColor(325, 75);
		ImageAntialias($im, true);
		$black = ImageColorAllocate($im, 0, 0, 0);
		$fontdir = "modules/captcha/fonts";
		require_once("modules/captcha/getfonts.php");
		$fonts = captcha_getfonts($fontdir);
		$lastf = "";
		$x = 25;
		for($i = 0; $i < strlen($text); $i++) {
			$letter = $text{$i};
			$sz = mt_rand(35, 45);
			$rot = mt_rand(5, 20);
			if (mt_rand(0, 1)) $rot = -$rot;
			$y = mt_rand(35,45);
			$f = $fontdir . "/" . $fonts[mt_rand(0, count($fonts)-1)];
			while ($f == $lastf) {
				$f = $fontdir . "/" . $fonts[mt_rand(0, count($fonts)-1)];
			}
			$lastf = $f;
			$red = mt_rand(100, 255);
			$blue = mt_rand(100, 255);
			$green = mt_rand(100, 255);
			$c1 = ImageColorAllocate($im, $red, $blue, $green);
			$c2 = ImageColorAllocate($im, $red^0xff, $blue^0xff, $green^0xff);
			captcha_drawletter($im, $sz, $rot, $x+mt_rand(-5,5),
					$y+mt_rand(-5,5), $c2, $f, $letter);
			captcha_drawletter($im, $sz, $rot, $x, $y, $c1, $f, $letter);
			$x += (50 + mt_rand(-5, 10));
			$letter = "";
		}
		header("Content-type: image/png");
		ImagePng($im);
		ImageDestroy($im);
		exit();
	} else {
		require_once("lib/nltoappon.php");
		// Okay, they are trying to answer a captcha!
		$answer = strtolower(httppost('answer'));
		$real = strtolower(get_module_pref("lastcaptcha"));
		$p = levenshtein($answer, $real, 1000, 1, 1000);
		if ($p <= get_module_setting("mistakes")) {
			// This is a good enough answer
			if (get_module_setting("reward")) {
				$d = date("Y-m-d");
				if ($d != get_module_pref("lastrewarded")) {
					$session['user']['gems']++;
					debuglog("`&'s total number of gems increased to {$session['user']['gems']} for answering a captcha satisfactorily.");
					set_module_pref("lastrewarded", $d);
				}
			}
			set_module_pref("inprogress", 0);
			set_module_pref("curfail", 0);
			set_module_pref("lastcaptcha", "");
			set_module_pref("lastlockout", "0000-00-00 00:00:00 GMT");

			$success = nltoappon(get_module_setting("storysuccess"));
			if ($success) {
				output($success);
				$head = get_module_setting("successheader");
				if (!$head) $head = "Success";
				page_header($head);
				$cont = get_module_setting("continuenav");
				if (!$cont) $cont = "Continue";
				addnav($cont, get_module_pref("interceptpage"));
				page_footer();
			} else {
				addnav("", get_module_pref("interceptpage"));
				redirect(get_module_pref("interceptpage"));
			}
		} else {
			$head = get_module_setting("failureheader");
			if (!$head) $head = "Failure";
			page_header($head);
			output("`n`n`c`\$The answer you entered was not correct!`0`c`n");
			$fails = get_module_pref("curfail");
			$fails++;
			if ($fails >= get_module_setting("maxfailures")) {
				// we failed, set up the lockout
				set_module_pref("lastlockout", gmdate("Y-m-d H:i:s")." GMT");
				output("You have failed too many times in a row.`n");
				$h = get_module_setting("lockouttime");
				$lock = nltoappon(get_module_setting("lockoutmsg"));
				if ($lock) {
					output($lock, $h,
							translate_inline($h == 1?"hour":"hours"));
				} else {
					output("You have been locked out for %s %s.", $h,
						translate_inline($h == 1?"hour":"hours"));
				}
				addnav("Logout", "login.php?op=logout");
				set_module_pref("inprogress", 0);
				set_module_pref("curfail", 0);
			} else {
				set_module_pref("curfail", $fails);
				set_module_pref("inprogress", 1);
				$retry = nltoappon(get_module_setting("retrymsg"));
				if ($retry) {
					output($retry);
				} else {
					output("Please try again.");
				}
				require_once("modules/captcha/display.php");
				captcha_display();
			}
			// We failed.
			page_footer();
		}
	}
}
?>

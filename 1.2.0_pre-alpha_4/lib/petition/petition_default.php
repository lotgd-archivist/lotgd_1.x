<?php
require_once 'lib/recaptcha/recaptchalib.php';
tlschema("petition");
popup_header("Petition for Help");
$post = httpallpost();
$captchaFail = false;
if(count($post) > 0) {
	if(!(Settings::getsetting('reCAPTCHAlogeedin', true) && $user->loggedin) && Settings::getsetting('reCAPTCHAkeyPublic', '') && Settings::getsetting('reCAPTCHAkeyPrivate', '')) {
		// reCaptcha check
		$captchaFail = recaptcha_check_answer(Settings::getsetting('reCAPTCHAkeyPrivate', ''), $_SERVER['REMOTE_ADDR'], $post['recaptcha_challenge_field'], $post['recaptcha_response_field']);
		if($captchaFail->is_valid) {
			$captchaFail = false;
			unset($post['recaptcha_challenge_field'], $post['recaptcha_response_field']);
		}
	}
	if(!$captchaFail) {
		$ip = explode(".", $_SERVER['REMOTE_ADDR']);
		array_pop($ip);
		$ip = join($ip, ".") . ".";
		$sql = "SELECT count(petitionid) AS c FROM " . DB::prefix("petitions") . " WHERE (ip LIKE '$ip%' OR id = '" . addslashes($_COOKIE['lgi']) . "') AND date > '" . date("Y-m-d H:i:s", strtotime("-1 day")) . "'";
		$result = DB::query($sql);
		$row = DB::fetch_assoc($result);
		if($row['c'] < 5 || (isset($user->superuser) && $user->superuser & ~SU_DOESNT_GIVE_GROTTO)) {
			if(!isset($user->acctid))
				$user->acctid = 0;
			if(!isset($user->password))
				$user->password = "";
			$p = $user->password;
			unset($user->password);
			$date = date("Y-m-d H:i:s");
			$post['cancelpetition'] = false;
			$post['cancelreason'] = 'The admins here decided they didn\'t like something about how you submitted your petition.  They were also too lazy to give a real reason.';
			$post = modulehook("addpetition", $post);
			if(!$post['cancelpetition']) {
				unset($post['cancelpetition'], $post['cancelreason']);
				$sql = "INSERT INTO " . DB::prefix("petitions") . " (author,date,body,pageinfo,ip,id) VALUES (" . (int)$user->acctid . ",'$date',\"" . addslashes(output_array($post)) . "\",\"" . addslashes(output_array(Session::record(), "Session:")) . "\",'{$_SERVER['REMOTE_ADDR']}','" . addslashes($_COOKIE['lgi']) . "')";
				DB::query($sql);
				// Fix the counter
				invalidatedatacache("petitioncounts");
				// If the admin wants it, email the petitions to them.
				if(getsetting("emailpetitions", 0)) {
					// Yeah, the format of this is ugly.
					require_once ("lib/sanitize.php");
					$name = color_sanitize($user->name);
					$url = getsetting("serverurl", "http://" . $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] == 80 ? "" : ":" . $_SERVER['SERVER_PORT']) . dirname($_SERVER['REQUEST_URI']));
					if(!preg_match("/\\/$/", $url)) {
						$url = $url . "/";
						savesetting("serverurl", $url);
					}
					$tl_server = translate_inline("Server");
					$tl_author = translate_inline("Author");
					$tl_date = translate_inline("Date");
					$tl_body = translate_inline("Body");
					$tl_subject = sprintf_translate("New LoGD Petition at %s", $url);
					
					$msg = "$tl_server: $url\n";
					$msg .= "$tl_author: $name\n";
					$msg .= "$tl_date : $date\n";
					$msg .= "$tl_body :\n" . output_array($post) . "\n";
					mail(getsetting("gameadminemail", "postmaster@localhost.com"), $tl_subject, $msg);
				}
				$user->password = $p;
				output("Your petition has been sent to the server admin.");
				output("Please be patient, most server admins have jobs and obligations beyond their game, so sometimes responses will take a while to be received.");
			} else {
				output("`\$There was a problem with your petition!`n");
				output("`@Please read the information below carefully; there was a problem with your petition, and it was not submitted.\n");
				rawoutput("<blockquote>");
				output($post['cancelreason']);
				rawoutput("</blockquote>");
			}
		} else {
			output("`\$`bError:`b There have already been %s petitions filed from your network in the last day; to prevent abuse of the petition system, you must wait until there have been 5 or fewer within the last 24 hours.", $row['c']);
			output("If you have multiple issues to bring up with the staff of this server, you might think about consolidating those issues to reduce the overall number of petitions you file.");
		}
	}
}
if(count($post) <= 0 || $captchaFail) {
	output("`c`b`\$Before sending a petition, please make sure you have read the motd.`n");
	output("Petitions about problems we already know about just take up time we could be using to fix those problems.`b`c`n");
	rawoutput("<form action='petition.php?op=submit' method='POST'>");
	if($user->loggedin) {
		output("Your Character's Name: ");
		output_notl("%s", $user->name);
		rawoutput("<input type='hidden' name='charname' value=\"" . htmlentities($user->name, ENT_COMPAT, getsetting("charset", "ISO-8859-1")) . "\">");
		output("`nYour email address: ");
		output_notl("%s", htmlentities($user->emailaddress));
		rawoutput("<input type='hidden' name='email' value=\"" . htmlentities($user->emailaddress, ENT_COMPAT, getsetting("charset", "ISO-8859-1")) . "\">");
	} else {
		output("Your Character's Name: ");
		rawoutput("<input name='charname' value=\"" . htmlentities((isset($post['charname'])) ? $post['charname'] : $user->name, ENT_COMPAT, getsetting("charset", "ISO-8859-1")) . "\" size='46'>");
		output("`nYour email address: ");
		rawoutput("<input name='email' value=\"" . htmlentities((isset($post['email'])) ? $post['email'] : $user->emailaddress, ENT_COMPAT, getsetting("charset", "ISO-8859-1")) . "\" size='50'>");
		$nolog = translate_inline("Character is not logged in!!");
		rawoutput("<input name='unverified' type='hidden' value='$nolog'>");
	}
	output("`nDescription of the problem:`n");
	$abuse = httpget("abuse");
	if($abuse == "yes") {
		rawoutput("<textarea name='description' cols='55' rows='7' class='input'></textarea>");
		rawoutput("<input type='hidden' name='abuse' value=\"" . stripslashes_deep(htmlentities(httpget("problem"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))) . "\"><br><hr><pre>" . stripslashes(htmlentities(httpget("problem"))) . "</pre><hr><br>");
	} elseif(httppost('abuse')) {
		rawoutput("<textarea name='description' cols='55' rows='7' class='input'>" . stripslashes_deep(htmlentities(httppost("description"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))) . "</textarea>");
		rawoutput("<input type='hidden' name='abuse' value=\"" . stripslashes_deep(htmlentities(httppost("abuse"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))) . "\"><br><hr><pre>" . stripslashes(htmlentities(httppost("abuse"))) . "</pre><hr><br>");
	} elseif(httppost('description')) {
		rawoutput("<textarea name='description' cols='55' rows='7' class='input'>" . stripslashes_deep(htmlentities(httppost("description"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))) . "</textarea>");
	} else {
		rawoutput("<textarea name='description' cols='55' rows='7' class='input'>" . stripslashes_deep(htmlentities(httpget("problem"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))) . "</textarea>");
	}
	if(!(Settings::getsetting('reCAPTCHAlogeedin', true) && $user->loggedin) && Settings::getsetting('reCAPTCHAkeyPublic', '') && Settings::getsetting('reCAPTCHAkeyPrivate', '')) {
		$html = recaptcha_get_html(Settings::getsetting('reCAPTCHAkeyPublic', ''), ($captchaFail) ? $captchaFail->error : null);
		output('`nPlease enter the two words into the box below.`n');
		if($captchaFail) {
			output('`b`$Plesase insert the words exactly as they appear.`0`b`n');
		}
		rawoutput(<<<reCAPTCHA
<script>
var RecaptchaOptions = {
	theme : 'clean'
};
</script>
$html
reCAPTCHA
);
	}
	modulehook("petitionform", array());
	$submit = translate_inline("Submit");
	rawoutput("<br/><input type='submit' class='button' value='$submit'><br/>");
	output("Please be as descriptive as possible in your petition.");
	output("If you have questions about how the game works, please check out the <a href='petition.php?op=faq'>FAQ</a>.", true);
	output("Petitions about game mechanics will more than likely not be answered unless they have something to do with a bug.");
	output("Remember, if you are not signed in, and do not provide an email address, we have no way to contact you.");
	rawoutput("</form>");
}
?>

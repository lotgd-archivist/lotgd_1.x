<?php
/*
1.01 added a validation function + you can now enter a URL for an avatar. Okay, this won't help that much if player want to cheat, but it's a security issue

*/
function avatar_getmoduleinfo() {
	$info = array(
		"name" => "Bio Avatars",
		"version" => "1.01",
		"author" => "JT Traub`n`&modified by `2Oliver Brendel",
		"category" => "Lodge",
		"download" => "core_module",
		"settings" => array(
			"Bio Avatar Settings,title",
			"cost"=>"What is the cost of having an avatar?,int|500",
			"changecost"=>"What is the cost of changing your avatar?,int|25",
			"allowpersonal"=>"Allow personal avatars entered via URL?,bool|1",
			"allowsets"=>"Allow sets to be selected by the player?,bool|1",
			"restrictsize"=>"Is the size restricted?,bool|1",
			"maxwidth"=>"Max. width of personal avatars (Pixel),range,20,400,20|200",
			"maxheight"=>"Max. height of personal avatars (Pixel),range,20,400,20|200",
			"Note: If you do not show the avatar in the nav then it will be displayed in the bio itself,note",
			"navdisplay"=>"Display the avatar in the nav,bool|0",
			"Note: if you have it in the bio then do you want to have it at the top? If not it will be a simple line more,note",
			"bioheaddisplay"=>"Display the avatar at the top of the bioinfo text?,bool|1",
		),
		"prefs"=>array(
			"Bio Avatar User Preferences,title",
			"bought"=>"Has the player bought an avatar yet?,bool,0",
			"setname"=>"Which set is the player using?|vixy1",
			"avatar"=>"URL of personal your avatar|",
			"user_seeavatar"=>"Show your avatar in your user bio?,bool|1",
			"user_seeotheravatars"=>"Show other avatars to their bio?,bool|1",
			"validated"=>"Is this avatar validated?,bool|0",
		),
	);
	return $info;
}

function avatar_install() {
	module_addhook("lodge");
	module_addhook("pointsdesc");
	module_addhook("superuser");
	// Let's get our hook at the top.
	module_addhook_priority("biotop", 5);
	//for normal display
	module_addhook_priority("bioinfo",50);
	return true;
}

function avatar_uninstall() {
	return true;
}

function avatar_dohook($hookname, $args) {
	global $session;
	$cost = get_module_setting("cost");
	$changecost = get_module_setting("changecost");
	switch($hookname) {
	case "pointsdesc":
		$args['count']++;
		$format = $args['format'];
		$str = translate("For %s points, you will get an avatar picture to display on your bio page.  You can change it to a different avatar at a later time for %s additional points.");
		$str = sprintf($str, $cost, $changecost);
		output($format, $str, true);
		break;
	case "lodge":
		if (!get_module_pref("bought")) {
			addnav(array("Bio Avatar Picture (%s %s)", $cost,
					translate_inline($cost == 1 ? "point" : "points")),
				"runmodule.php?module=avatar&op=purchase&cost=$cost");
		} else {
			addnav(array("Change Avatar Picture (%s %s)", $changecost,
					translate_inline($changecost == 1 ? "point" : "points")),
				"runmodule.php?module=avatar&op=purchase&cost=$changecost");
		}
		if (get_module_setting("allowsets")) {
			addnav("Gallery");
			addnav("View Bio Avatar Gallery", "runmodule.php?module=avatar&op=view");
		}
		break;
	case "bioinfo":
		if (!get_module_setting("navdisplay")&& !get_module_setting("bioheaddisplay"))
		if (get_module_pref("user_seeavatar") &&
				get_module_pref("bought", "avatar", $args['acctid']) && get_module_pref("user_seeotheravatars")) {
			$set = get_module_pref("setname", "avatar", $args['acctid']);
			$race = strtolower($args['race']);
			if ($args['sex'] == SEX_MALE) {
				$gender = "male";
			} else {
				$gender = "female";
			}
			$image = avatar_getimage($race, $gender, $set,true,$args['acctid']);
			rawoutput("<table><tr><td valign='top'>");
			output("`^Avatar:`0`n");			
			rawoutput("</td><td valign='top'>$image</td></tr></table>");
			//rawoutput($image);
		}
		break;
	case "biotop":
		if (get_module_setting("navdisplay")) {
			if (get_module_pref("user_seeavatar") &&
					get_module_pref("bought", "avatar", $args['acctid']) && get_module_pref("user_seeotheravatars")) {
				$set = get_module_pref("setname", "avatar", $args['acctid']);
				$race = strtolower($args['race']);
				if ($args['sex'] == SEX_MALE) {
					$gender = "male";
				} else {
					$gender = "female";
				}
				$image = avatar_getimage($race, $gender, $set,true,$args['acctid']);

				// Make the avatar image collapsible away.  Some people view the
				// game from work and having the avatar image makes it VERY
				// obviously a non-work site even in work-friendly skins
				// addnavheader("Avatar", false);
				addnavheader("Avatar");
				global $templatename;
				if ($templatename == "Classic.htm") {
					$image = "<tr><td>$image</td></tr>";
				}
				addnav("$image","!!!addraw!!!",true);
			}
		} elseif (get_module_setting("bioheaddisplay")) {
			if (get_module_pref("user_seeavatar") &&
					get_module_pref("bought", "avatar", $args['acctid']) && get_module_pref("user_seeotheravatars")) {
				$set = get_module_pref("setname", "avatar", $args['acctid']);
				$race = strtolower($args['race']);
				if ($args['sex'] == SEX_MALE) {
					$gender = "male";
				} else {
					$gender = "female";
				}
				$image = avatar_getimage($race, $gender, $set,true,$args['acctid']);
				rawoutput($image);
			}		
		}
		break;
	case "superuser":
		if ($session['user']['superuser'] & SU_AUDIT_MODERATION) {
			addnav("Mechanics");
			addnav("Validate Avatars","runmodule.php?module=avatar&op=validate");
			$sql="SELECT count(u.userid) AS counter FROM ".db_prefix("module_userprefs")." AS u INNER JOIN ".db_prefix("module_userprefs")." AS t ON u.userid=t.userid AND u.modulename='avatar' AND u.setting='avatar' AND u.value!='' AND t.modulename='avatar' AND t.setting='validated' AND t.value!='1';";
			$result=db_query($sql);
			$num=db_fetch_assoc($result);
			output_notl("`n`n");
			if ($num['counter']>0) output("`^Currently there are `\$%s`^ avatars waiting for validation.`0`n`n",$num['counter']);
		}
	}
	return $args;
}


function avatar_run() {
	global $session;
	$op = httpget("op");
	if ($op=='superuser') {
		page_header("Avatar Validation");
	} else {
		page_header("Hunter's Lodge");
	}
	switch ($op) {
	case "purchase":
		$cost = httpget("cost");
		$allowsets=get_module_setting("allowsets");
		$pointsavail = $session['user']['donation'] -
			$session['user']['donationspent'];
		output("`7J. C. Petersen leads you into a back room filled with portraits, and guides you over to a group of images for your race.`n`n");
		output("`n`7J. C. Petersen smiles, \"`&Of course, each race has its own set of images, but these are the possible ones that you can choose right now.  Your image will always reflect your current race.`7\"`n`n");
		output("If you would like to see all of the images, feel free to look around the gallery.`n");
		output("You might also `iadd a portrait on your own`i if you tell him where he has to look for it.`n`n");
		if ($allowsets) addnav("Gallery", "runmodule.php?module=avatar&op=view");

		if ($pointsavail < $cost) {
			if (!get_module_pref("bought")) {
				output("`7He glances at his ledger, \"`&Unfortunately, purchasing a portrait will cost you %s points, and you currently only have %s %s available to spend.`7\"", $cost, $pointsavail, translate_inline($pointsavail==1?"point":"points"));
			} else {
				output("`7He glances at his ledger, \"`&Unfortunately, changing your portrait will cost you %s points, and you currently only have %s %s available to spend.`7\"", $cost, $pointsavail, translate_inline($pointsavail==1?"point":"points"));
			}
		} else {
			if ($allowsets) output("`7He steps back to let you admire the pictures for a moment, \"`&So, does one of these suit you?`7\"");
		}
		$race = strtolower($session['user']['race']);
		if ($session['user']['sex'] == SEX_MALE) {
			$gender = "male";
		} else {
			$gender = "female";
		}
		$button = false;
		if ($pointsavail >= $cost) {
			$button = "Purchase";
			if (get_module_pref("bought")) $button= "Change";
			$button = translate_inline($button);
			if (get_module_setting("allowpersonal")) {
				addnav("Personal Avatar","runmodule.php?module=avatar&op=personal&cost=$cost");
			}
		}

		$set = get_module_pref("setname");
		if ($allowsets) {
			rawoutput("<form method='POST' action='runmodule.php?module=avatar&op=yes&cost=$cost'>");
			$image = avatar_get_all_images($race, $gender, $set, $button);
			rawoutput($image);
			rawoutput("</form>");
			addnav("", "runmodule.php?module=avatar&op=yes&cost=$cost");
		} else {
			output("\"`&Oh... I forgot... the local gods don't allow these sets to be used in public, sorry... choose a personal one if you like.`7\", states J.C. Petersen.");
		}
		break;
	case "personal":
		$cost=httpget("cost");
		$send=translate_inline("Preview");
		$value=get_module_pref("avatar");
		output("`7J.C.P. takes a good look at you.`n");
		output("\"`&So you want a personal picture. In this case, please tell me where I have to look.`7\"`n`n");
		output("URL to your avatar:`n");
		rawoutput("<form method='POST' action='runmodule.php?module=avatar&op=preview&cost=$cost'>");
		rawoutput("<input name='url' type='text' size='40' value=$value>");
		rawoutput("<input type='submit' class='button' value=$send>");
		rawoutput("</form>");
		addnav("","runmodule.php?module=avatar&op=preview&cost=$cost");
		if (get_module_setting("restrictsize")) {
			$maxwidth = get_module_setting("maxwidth");
			$maxheight = get_module_setting("maxheight");			
			output("`n`nPlease note that there are regulations concerning the size.`n");
			output("You may not have an avatar that has a width of more than %s pixels or a height of more than %s pixels.",$maxwidth,$maxheight);
			output("`n`nAny larger picture will be scaled to a smaller size.");
		}
		addnav("Go back to purchasing","runmodule.php?module=avatar&op=purchase&cost=$cost");
		break;
	case "preview":
		$cost=httpget("cost");
		$url=httppost('url');
		output("`7J.C.P. takes a good look at you.`n");
		output("So this is how you want to look like?`n`n");
		$image="<img align='left' src='".$url."' ";
		if (get_module_setting("restrictsize")) {
			//stripped lines from Anpera's avatar module =)
			$maxwidth = get_module_setting("maxwidth");
			$maxheight = get_module_setting("maxheight");
			$pic_size = @getimagesize(get_module_pref("avatar","avatar",$user)); // GD2 required here - else size always is recognized as 0
			$pic_width = $pic_size[0];
			$pic_height = $pic_size[1];
			if ($pic_width > $maxwidth) $image.=" width=\"$maxwidth\" ";
			if ($pic_height > $maxheight) $image.=" height=\"$maxheight\" ";
		}
		$image.=">";
		rawoutput("<table><tr><td valign='top'>");
		output("`^Avatar:`0`n");			
		rawoutput("</td><td valign='top'>$image</td></tr><td></td><td>$url</td></table>");
		addnav("Yes","runmodule.php?module=avatar&op=yes&cost=$cost&url=".rawurlencode($url));
		addnav("No","runmodule.php?module=avatar&op=personal&cost=$cost");
		break;
	case "view":
		$set = httpget("set");
		$cost = get_module_setting("cost");
		$ccost = get_module_setting("changecost");
		addnav("Purchase");
		if (!get_module_pref("bought")) {
			addnav(array("Purchase Avatar (%s %s)", $cost,
					translate_inline($cost == 1 ? "point" : "points")),
				"runmodule.php?module=avatar&op=purchase&cost=$cost");
		} else {
			addnav(array("Change Avatar (%s %s)", $ccost,
					translate_inline($ccost == 1 ?  "point" : "points")),
					"runmodule.php?module=avatar&op=purchase&cost=$ccost");
		}
		if (!$set) {
			output("As you look around the room, you see different groups of images.");
			output("Which one would you like to look at?`n`n");
			avatar_showsets();
		} else {
			output("You step over to view the set of images which caught your eye.`n`n");
			avatar_showimages($set);
			avatar_showsets();
		}
		break;
	case "yes":
		$cost = httpget("cost");
		$set = httppost("set");
		$url = httpget("url");
		output("`7J. C. Petersen grins broadly, \"`&Excellent.  I'll take care of that for you right now.`7\"");
		if ($url!='') output("`n`nA moderator has to validate your avatar. Please be patient.");
		$session['user']['donationspent'] += $cost;
		set_module_pref("bought", 1);
		if ($set) set_module_pref("setname", $set);
		set_module_pref("avatar",$url);
		set_module_pref("validated",0);
		break;
	case "validate":
		$mode=httpget("mode");
		require_once("lib/superusernav.php");
		superusernav();
		switch($mode) {
			case "invalidate":
				$search=httppost('search');
				$who=httpget('who');
				if ($who=='') {
					$send=translate_inline("Search");
					output("Whose avatar do you want to invalidate?`n`n");
					rawoutput("<form method='POST' action='runmodule.php?module=avatar&op=validate&mode=invalidate'>");
					rawoutput("<input name='search' type='text' size='40' value=$search>");
					rawoutput("<input type='submit' class='button' value=$send>");
					rawoutput("</form>");
					addnav("","runmodule.php?module=avatar&op=validate&mode=invalidate");
					output_notl("`n`n");
					if ($search) {
						$name="%";
						for ($i=0;$i<strlen($search);$i++) {
							$name.=substr($search,$i,1)."%";
						}
						$sql="SELECT u.userid AS acctid,k.name AS username FROM ".db_prefix("module_userprefs")." AS u INNER JOIN ".db_prefix("module_userprefs")." AS t RIGHT JOIN ".db_prefix("accounts")." as k ON u.userid=t.userid AND k.acctid=u.userid WHERE u.modulename='avatar' AND u.setting='avatar' AND u.value!='' AND t.modulename='avatar' AND t.setting='validated' AND t.value='1' AND k.name LIKE '$name';";
						$result=db_query($sql);
						if (db_num_rows($result)>100) {
							output("There are more than 100 matches. Please specify the user a bit more.");
							addnav("runmodule.php?module=avatar&op=validate&mode=invalidate");
							break;
						}
						if (db_num_rows($result)==0) {
							output("No user with a valid personal avatar found matching this criteria.");
						}
						while ($row=db_fetch_assoc($result)) {
							rawoutput("<a href='runmodule.php?module=avatar&op=validate&mode=invalidate&who={$row['acctid']}'>");
							output_notl("`^".$row['username']."`0`n");
							rawoutput("</a>");
							addnav("","runmodule.php?module=avatar&op=validate&mode=invalidate&who={$row['acctid']}");
						}
					}
						
				} else {
					set_module_pref("validated",0,"avatar",$who);
					output("Avatar invalidated and user notified!");
					require_once("./lib/systemmail.php");
					systemmail($who,array("Your avatar has been invalidated!"),array("`^As of now, your avatar has been invalidated because it did meet the requirements of this server.`nYou won't be refunded points for a violation.`n`n If you replace the picture at this URL, petition about it and we will reevaluate your avatar.`nIf you decide to use another URL, the normal validation process will take place automatically.`n`nRegards`n%s`^`n `&Moderator",$session['user']['name']));
					
				}
				addnav("Avatars");
				addnav("Return to the main menu","runmodule.php?module=avatar&op=validate");
				
				break;
			case "validate":
				if (httpget('giveok')) {
					output("Avatar has been validated!`n`n");
					set_module_pref("validated",1,"avatar",httpget('user'));
					require_once("./lib/systemmail.php");
					systemmail(httpget('user'),array("Your avatar has been validated!"),array("`^As of now, your avatar will be visible.`n`nRegards`n%s`^`n `&Moderator",$session['user']['name']));					
				} else {
					output_notl("`n`n");
				}
				$sql="SELECT a.login as login, u.userid as acctid ,a.name as name,a.emailaddress as email FROM ".db_prefix("module_userprefs")." AS u INNER JOIN ".db_prefix("module_userprefs")." AS t RIGHT JOIN ".db_prefix("accounts")." AS a ON u.userid=t.userid AND a.acctid=u.userid WHERE u.modulename='avatar' AND u.setting='avatar' AND u.value!='' AND t.modulename='avatar' AND t.setting='validated' AND t.value!='1' ORDER BY rand(".e_rand().") LIMIT 1;";
				$result=db_query($sql);
				$num=db_num_rows($result);
				if ($num==0) {
					output("No avatars left! You validated the last one (for now).");
					break;
				}
				$row=db_fetch_assoc($result);
				$url=get_module_pref("avatar","avatar",$row['acctid']);
				$image="<img align='left' src='".$url."' ";
				if (get_module_setting("restrictsize")) {
					//stripped lines from Anpera's avatar module =)
					$maxwidth = get_module_setting("maxwidth");
					$maxheight = get_module_setting("maxheight");
					$pic_size = @getimagesize(get_module_pref("avatar","avatar",$user)); // GD2 required here - else size always is recognized as 0
					$pic_width = $pic_size[0];
					$pic_height = $pic_size[1];
					if ($pic_width > $maxwidth) $image.=" width=\"$maxwidth\" ";
					if ($pic_height > $maxheight) $image.=" height=\"$maxheight\" ";
				}
				$image.=">";
				rawoutput("<table><tr><td valign='top'>");
				output("`^Avatar:`0`n");			
				rawoutput("</td><td valign='top'>$image</td></tr><td></td><td>$url</td></table>");				addnav("Actions");
				output("Username: ");
				rawoutput("<a href='mail.php?op=write&to={$row['login']}' class='colLtGreen' target='_blank' onClick=\"".popup("mail.php?op=write&to={$row['login']}").";return false;\">".sanitize($row['name'])."</a>");
				output_notl("`n");
				output("Email: ");
				rawoutput("<a href='mailto:{$row['email']}'>{$row['email']}</a>");
				output_notl("`n`n");
				output("Click on the name to YOM and click the email to email this user.");
				addnav("Avatars");
				addnav("Return to the main menu","runmodule.php?module=avatar&op=validate");
				addnav("Validate this Avatar","runmodule.php?module=avatar&op=validate&mode=validate&giveok=1&user={$row['acctid']}");
				addnav("Ignore this Avatar","runmodule.php?module=avatar&op=validate&mode=validate");

				break;
			default:
				$sql="SELECT count(u.userid) AS counter FROM ".db_prefix("module_userprefs")." AS u INNER JOIN ".db_prefix("module_userprefs")." AS t ON u.userid=t.userid AND u.modulename='avatar' AND u.setting='avatar' AND u.value!='' AND t.modulename='avatar' AND t.setting='validated' AND t.value!='1';";
				$result=db_query($sql);
				$num=db_fetch_assoc($result);
				output("Currently there are %s avatars waiting for validation.`n`n",$num['counter']);
				addnav("Actions");
				if ($num['counter']>0) {
					addnav("Validate Avatars","runmodule.php?module=avatar&op=validate&mode=validate");
				}
				addnav("Invalidate Avatars","runmodule.php?module=avatar&op=validate&mode=invalidate");
			break;
		}
	}
	if ($op!='validate') {
		addnav("Return");
		addnav("L?Return to Lodge", "lodge.php");
	}
	page_footer();
}

function avatar_getimage($race, $gender, $set,$bio=false,$user=0) {
	$usedefault = 0;
	$file = "modules/avatar/$set/$race-$gender.gif";
	if (!file_exists($file)) {
		$usedefault = 1;
		$file = "modules/avatar/default.gif";
	}
	if (get_module_pref("avatar","avatar",$user)!='' && $bio)  {
		if (get_module_pref("validated","avatar",$user)) {
			$image="<img align='left' src='".get_module_pref("avatar","avatar",$user)."' ";
			if (get_module_setting("restrictsize")) {
				//stripped lines from Anpera's avatar module =)
				$maxwidth = get_module_setting("maxwidth");
				$maxheight = get_module_setting("maxheight");
				$pic_size = @getimagesize(get_module_pref("avatar","avatar",$user)); // GD2 required here - else size always is recognized as 0
				$pic_width = $pic_size[0];
				$pic_height = $pic_size[1];
				if ($pic_width > $maxwidth) $image.=" width=\"$maxwidth\" ";
				if ($pic_height > $maxheight) $image.=" height=\"$maxheight\" ";
			}
			$image.=">";
			} else {
			$image=translate_inline("Avatar not validated yet");
		}
	} else {
		$l = translate_inline("Licensed for use in LoTGD");
		$image = "<center><img align='center' src='$file'>$l</img>";
		if (!$usedefault) {
			require("modules/avatar/$set/setinfo.php");
			$image .= "<br><center>$setcopy<br>$l</center>";
		}
	}
	return $image;
}

function avatar_showimages($set) {
	$races = modulehook("racenames");
	rawoutput("<table cellpadding='0' cellspacing='0' border='0' bgcolor='#999999'>");
	$r = translate_inline("Race");
	$m = translate_inline("Male Image");
	$f = translate_inline("Female Image");
	rawoutput("<tr class='trhead'><th>$r</th><th>$m</th><th>$f</th></tr>");
	$i = 0;
	foreach ($races as $key=>$race) {
		$r = strtolower($race);
		$imm = avatar_getimage($r, "male", $set);
		$imf = avatar_getimage($r, "female", $set);
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
		rawoutput("<th>");
		output_notl('`^');
		output($race);
		output_notl("`0");
		rawoutput("</th><td>");
		rawoutput($imm);
		rawoutput("</td><td>");
		rawoutput($imf);
		rawoutput("</td>");
		rawoutput("</tr>");
		$i++;
	}
	rawoutput("</table>");
}

function avatar_showsets() {
	$setnames = array();
	$setdirs = array();

	addnav("Image sets");
	$dir = "modules/avatar";
	$d = opendir($dir);
	while (($file = readdir($d)) !== false) {
		if ($file[0] == '.') continue;
		if (is_dir($dir . "/" . $file)) {
			// okay, this is a possible set
			$f = $dir . "/" . $file . "/" . "setinfo.php";
			if (file_exists($f)) {
				require($f);
				$setnames[$setindex] = $setname;
				$setdirs[$setindex] = $file;
			}
		}
	}
	closedir($d);

	// Now display the sets in order.
	ksort($setnames);
	ksort($setdirs);
	reset($setdirs);
	while(list($key, $val) = each($setdirs)) {
		addnav($setnames[$key],
				"runmodule.php?module=avatar&op=view&set=$val");
	}
}

function avatar_get_all_images($race, $gender, $selset, $button) {
	$setnames = array();
	$setdirs = array();

	$dir = "modules/avatar";
	$d = opendir($dir);
	while (($file = readdir($d)) !== false) {
		if ($file[0] == '.') continue;
		if (is_dir($dir . "/" . $file)) {
			// okay, this is a possible set
			$f = $dir . "/" . $file . "/" . "setinfo.php";
			if (file_exists($f)) {
				require($f);
				$setnames[$setindex] = $setname;
				$setdirs[$setindex] = $file;
			}
		}
	}
	closedir($d);

	// Now display the sets in order.
	ksort($setnames);
	ksort($setdirs);
	reset($setdirs);
	$str = "<table border=0>";
	while(list($key, $val) = each($setdirs)) {
		$str .= "<tr>";
		// We are going to do three per row here
		$str .= "<td>" . $setnames[$key] .
			"<br /><input type='radio' name='set' value='" .
			$setdirs[$key] . "'";
		if ($setdirs[$key] == $selset) $str .= " checked";
		$str .= "></td><td>";
		$str .= avatar_getimage($race, $gender, $setdirs[$key]);
		$str .= "</td>";

		// second
		if(list($key, $val) = each($setdirs)) {
			$str .= "<td>" . $setnames[$key] .
				"<br /><input type='radio' name='set' value='" .
				$setdirs[$key]."'";
			if ($setdirs[$key] == $selset) $str .= " checked";
			$str .= "></td><td>";
			$str .= avatar_getimage($race, $gender, $setdirs[$key]);
			$str .= "</td>";
		} else {
			$str .= "<td>&nbsp;</td><td>&nbsp;</td>";
		}

		// third 
		if(list($key, $val) = each($setdirs)) {
			$str .= "<td>" . $setnames[$key] .
				"<br /><input type='radio' name='set' value='" .
				$setdirs[$key]."'";
			if ($setdirs[$key] == $selset) $str .= " checked";
			$str .= "></td><td>";
			$str .= avatar_getimage($race, $gender, $setdirs[$key]);
			$str .= "</td>";
		} else {
			$str .= "<td>&nbsp;</td><td>&nbsp;</td>";
		}

		$str .= "</tr>";
	}

	if ($button !== false) {
		$str .= "<tr><td colspan=6 align=center>";
		$str .= "<input type='submit' class='button' value='$button'>";
		$str .= "</td></tr>";
	}
	$str .= "</table>";
	return $str;
}

?>
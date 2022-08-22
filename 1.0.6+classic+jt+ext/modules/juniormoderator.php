<?php

// PRIVATE_CODE
// this module allows for players to become junior moderators, who
// are allowed to delete comments from all public commentary areas

// note that much of the code here is taken from the code for the
// deputy moderator module, so if there is a bug here, there's a
// decent chance that modules/deputymoderator.php will also have
// the same bug, and vice versa.

// in order to make a player a junior moderator, someone with user
// editor access should set the module pref named "isjrmod" to true.

// junior moderators are given a deletestick that can be used from the 
// junior moderator's moderation screen, which is accessible from the 
// villages under the header "jr. moderator duties".  this screen allows 
// junior moderators to view the last 20 comments in all the public 
// commentary areas.  note that for the purposes of this module, a
// public commentary area is defined as a commentary area whose section
// name does not begin with either "clan-", "pet-", or "special-".

// the junior moderator screen also allows junior moderators to add
// comments to these same public commentary areas, so that when they
// must exercise their powers, they don't have to continually flip back
// and forth between the moderation screen and the actual commentary area.
// they are also allowed to break post limits from the moderation screen.

// the ability to review even earlier comments is deliberately left out,
// since, in theory, junior moderators are given their powers so that they
// can delete comments in case other staff members don't get there as
// quickly, and it is hopefully unlikely that a moderator has not had the
// chance to review an inappropriate comment by the time it scrolls off the
// junior moderator's moderation screen.  nevertheless, the number of
// comments that the junior moderator can review at once can be changed,
// as it is a module setting, going by the name "numcomments".

// all comments deleted by junior moderators can be reviewed by any
// staff member with the SU_AUDIT_MODERATION flag set.  the screen from
// which these comments can be reviewed is accessible from the superuser
// grotto, under the header "actions".  the nav is named "review jr.
// moderator actions".  from this screen, auditors can either validate
// or restore deleted comments.  validated comments are entered into
// the moderatedcomments table.  restored comments are put back into the
// commentary areas.  in either case, reviewed comments are kept in
// the junior moderator review screen for five days after they have
// been marked as either validated or restored.

// note that under the moderatedcomments table, the staff member who
// validated the comment is listed under the "moderator" column,
// but the junior moderator who deleted the comment is still recorded
// under the junior moderator review screen.

// note that junior moderators are only given a deletestick that works
// in the public commentary areas, and that they are deliberately not
// given a mutestick nor a banstick.  this way, the junior moderator
// has the powers to do the job required of them, but if the junior
// moderator abuses the granted powers, the worst that could happen
// is that all the public commentary areas get wiped out, which any
// auditor will have the ability to reverse at any time.

// junior moderators are also not given access to the grotto, the
// commentary in the character biographies, the ability to speak
// without getting one's comments parsed through the drunk filter,
// or anything else aside from the deletestick and the ability to
// bypass post limits from the junior moderator's moderation screen.

// credit goes to saucy for the original modification ideas
//                          to modules/deputymoderator.php

function juniormoderator_getmoduleinfo() {
	$info = array(
		"name"=>"Junior Moderator",
		"version"=>"0.1",
		"author"=>"dying",
		"category"=>"Administrative",
		"download"=>"core_module",
		"settings"=>array(
			"Junior Moderator Settings, title",
			"numcomments"=>"Limit to number of recent comments retrieved for junior moderators,range,0,500,5|20",
			"toreview"=>"Comments deleted by junior moderators that have not been reviewed yet,viewonly",
		),
		"prefs"=>array(
			"Junior Moderator Preferences, title",
			"isjrmod"=>"Is this player a junior moderator?,bool|0"
		),
	);
	return $info;
}

function juniormoderator_install() {
	module_addhook("village");     // for junior moderators
	module_addhook("shades");
	module_addhook("superuser");   // need SU_AUDIT_MODERATION set
	module_addhook("moderate");
	return true;
}

function juniormoderator_uninstall() {
	return true;
}

function juniormoderator_dohook($hookname, $args) {
	global $session;
	switch($hookname){
	case "moderate":
		tlschema("commentary");
		$args['helperarea'] = translate_inline("Helper Private Chat");
		tlschema();
		break;
	case "shades":
	case "village":
		$isjrmod = get_module_pref("isjrmod");
		if (!$isjrmod) break;

		$navjrmodheader = translate_inline("Jr. Moderator Duties");
		$navjrmod = translate_inline("Moderate Public Areas");
		addnav($navjrmodheader);
		addnav("@?" . $navjrmod, "runmodule.php?module=juniormoderator");
		unblocknav($navjrmodheader);
		unblocknav("runmodule.php?module=juniormoderator");
		break;
	case "superuser":
		if ($session['user']['superuser'] & SU_AUDIT_MODERATION){
			$navreview = translate_inline("Review Jr. Moderator Actions");
			addnav("Actions");
			addnav($navreview, "runmodule.php?module=juniormoderator&op=review&admin=true");
		}
		break;
	}
	return $args;
}

function juniormoderator_run()
{
	global $session;

	$getop = httpget("op");

	if ($getop=="delete") {
		$postcommentid = httppost("commentid");
		$postarea = httppost("area");

		$sql = "SELECT * FROM " . db_prefix("commentary") . " WHERE commentid='". $postcommentid . "'";
		$res = db_query($sql);
		if (db_num_rows($res)!=0) {
			$row = db_fetch_assoc($res);
			$toreview = @unserialize(get_module_setting("toreview"));
			if (!is_array($toreview)) $toreview = array();

			$asql = "SELECT " . db_prefix("accounts") . ".name, "
					. db_prefix("accounts") . ".login, "
					. db_prefix("accounts") . ".clanrank, "
					. db_prefix("clans") . ".clanshort"
					. " FROM " . db_prefix("accounts")
					. " LEFT JOIN " . db_prefix("clans")
					. " ON " . db_prefix("accounts") . ".clanid"
					. " = " . db_prefix("clans") . ".clanid"
					. " WHERE " . db_prefix("accounts") . ".acctid"
					. " = '" . $row['author'] . "'";
			$ares = db_query($asql);
			$arow = db_fetch_assoc($ares);

				// 'jrmodid' isn't currently used, but is stored in case it will be
			array_push($toreview, array('jrmod'=>$session['user']['name'],
								'jrmodid'=>$session['user']['acctid'],
								'deletedate'=>time(),
								'comment'=>$row,
								'author'=>$arow));

			set_module_setting("toreview", serialize($toreview));

			$sql = "DELETE FROM " . db_prefix("commentary") . " WHERE commentid='". $postcommentid . "'";
			db_query($sql);
			invalidatedatacache("comments-".$postarea);
		}
	}

	if ($getop=="validate"||$getop=="restore") {
		$postcommentid = httppost("commentid");

		$toreview = @unserialize(get_module_setting("toreview"));
		if (!is_array($toreview)) $toreview = array();

		$changedtoreview = false;

		$keys = array_keys($toreview);

		for($i=0; $i<count($toreview); $i++) {
			if ($toreview[$keys[$i]]['comment']['commentid']==$postcommentid) {

				if ($getop=="validate") {

					$comment = array_merge($toreview[$keys[$i]]['comment'], $toreview[$keys[$i]]['author']);

					$sql = "INSERT LOW_PRIORITY INTO " . db_prefix("moderatedcomments")
							. " (comment, moderator, moddate)"
							. " VALUES ('" . addslashes(serialize($comment)) . "', "
							. "'" . $session['user']['acctid'] . "', "
							. "'" . date("Y-m-d H:i:s") . "')";
					db_query($sql);

					$toreview[$keys[$i]]['validatedby'] = $session['user']['name'];
					$toreview[$keys[$i]]['validatedate'] = time();

				} else {

					$sql = "INSERT LOW_PRIORITY INTO " . db_prefix("commentary")
							. " (commentid, section, author, comment, postdate)"
							. " VALUES ('" . $toreview[$keys[$i]]['comment']['commentid'] . "', "
							. "'" . addslashes($toreview[$keys[$i]]['comment']['section']) . "', "
							. "'" . addslashes($toreview[$keys[$i]]['comment']['author']) . "', "
							. "'" . addslashes($toreview[$keys[$i]]['comment']['comment']) . "', "
							. "'" . $toreview[$keys[$i]]['comment']['postdate'] . "')";
					db_query($sql);
					invalidatedatacache("comments-".$toreview[$keys[$i]]['comment']['section']);

					$toreview[$keys[$i]]['restoredby'] = $session['user']['name'];
					$toreview[$keys[$i]]['restoredate'] = time();
				}

				$changedtoreview = true;

				break;
			}
		}

		if ($changedtoreview) set_module_setting("toreview", serialize($toreview));
	}

	if (($getop=="")||($getop=="delete")) {
		$isjrmod = get_module_pref("isjrmod");
		if (!$isjrmod) juniormoderator_die("Unauthorized Access");

		page_header("Moderate Public Commentary Areas");
		villagenav();

		$navrefresh = translate_inline("Refresh Comments");
		addnav($navrefresh, "runmodule.php?module=juniormoderator");

		output("`c`b`^Moderate Public Commentary Areas`0`b`c`n");

		if ($getop=="delete") output ("`b`\$Comment deleted.`0`b`n`n");

		require_once("lib/commentary.php");
		addcommentary();

		$numcomments = get_module_setting("numcomments");

		// Let's use the API, shall we???
		/*
		$areas = array();
		$sql = "SELECT DISTINCT section FROM " . db_prefix("commentary") . " ORDER BY section DESC";
		$res = db_query($sql);
		while ($row = db_fetch_assoc($res)) {
			if ( (strstr($row['section'], "clan-") != $row['section'])
				&& (strstr($row['section'], "pet-") != $row['section'])
				&& (strstr($row['section'], "special-") != $row['section']) ) {
				array_push($areas, $row['section']);
			}
		}
		*/
		$areas = commentarylocs();
		$areas = array_keys($areas);

		$areanum = 0;

		while (++$areanum && $area = array_pop($areas))
		{
			$sql = "SELECT * FROM " . db_prefix("commentary") . " WHERE section='" . $area . "' ORDER BY postdate DESC LIMIT " . $numcomments;
			$res = db_query($sql);
			
			if ($areanum != 1) rawoutput("<hr>");
			output_notl("`b`^$area`0`b");

			if (db_num_rows($res)==0) {
				output("This commentary area currently contains no comments.");
			} else {
				addnav("", "runmodule.php?module=juniormoderator&op=delete");
				$buttondelete = translate_inline("Delete");


				$stack = array();

				while ($row=db_fetch_assoc($res)) array_push($stack, $row);

				rawoutput("<table>");

				while ($row=array_pop($stack)) {
					rawoutput("<tr><td valign='top'>");
					rawoutput("<form action='runmodule.php?module=juniormoderator&op=delete' method='post'>");
					rawoutput("<input type='hidden' name='commentid' value='" . $row['commentid'] . "'>");
					rawoutput("<input type='hidden' name='area' value='" . $area . "'>");
					rawoutput("<input type='submit' class='button' value='" . $buttondelete . "'>");
					rawoutput("</form>");
					rawoutput("</td><td>");
					juniormoderator_displaycomment($row);
					rawoutput("</td></tr>");
				}

				rawoutput("</table>");

					// note:  the usage of "says" here may clash with what
					//        may be typically used in the section in question,
					//        but retrieving the appropriate string to use
					//        here instead does not appear to be feasible.
					//        also, the usage of "500" here allows the junior
					//        moderator to basically avoid post limits without
					//        actually having to get access to the grotto.
			}
			talkform($area, "says", 500);
		}

		page_footer();
	} elseif ($getop=="review"||$getop=="validate"||$getop=="restore") {
		juniormoderator_checkforsuperuser();

		page_header("Review Junior Moderator Actions");
		require_once("lib/superusernav.php");
		superusernav();

		$navrefresh = translate_inline("Refresh Deleted Comments");
		addnav($navrefresh, "runmodule.php?module=juniormoderator&op=review");

		juniormoderator_pruneoldentries();

		output("`c`b`^Review Junior Moderator Actions`0`b`c`n");

		if ($getop=="validate") output ("`b`\$Deleted comment validated.`0`b`n`n");
		if ($getop=="restore") output ("`b`\$Deleted comment restored.`0`b`n`n");

		$toreview = @unserialize(get_module_setting("toreview"));
		if (!is_array($toreview)) set_module_setting("toreview", serialize($toreview = array()));

		output("`b`#Comments Deleted By Junior Moderators`0`b");
		if (count($toreview)==0) {
			rawoutput("<blockquote>");
			output("There are no deleted comments to review.");
			rawoutput("</blockquote>");
		} else {
			addnav("", "runmodule.php?module=juniormoderator&op=validate");
			$buttonvalidate = translate_inline("Validate");

			addnav("", "runmodule.php?module=juniormoderator&op=restore");
			$buttonrestore = translate_inline("Restore");

			rawoutput("<blockquote>");
			output("`&When you `b`@validate`&`b a comment, the comment will be marked as validated.`0`n");
			output("`&When you `b`%restore`&`b a comment, the comment will be marked as restored and inserted back into the commentary.`0`n");
			output("`&Comments that have been reviewed will remain in this list for five days.`0");
			rawoutput("</blockquote>");

			rawoutput("<table>");

			$toreviewreverse = array_reverse($toreview);

			foreach($toreviewreverse as $element) {
				rawoutput("<tr>");
				if (!isset($element['validatedby'])&&!isset($element['restoredby'])) {
					rawoutput("<td>");
					rawoutput("<form action='runmodule.php?module=juniormoderator&op=validate' method='post'>");
					rawoutput("<input type='hidden' name='commentid' value='" . $element['comment']['commentid'] . "'>");
					rawoutput("<input type='submit' class='button' value='" . $buttonvalidate . "'>");
					rawoutput("</form>");
					rawoutput("</td><td>");
					rawoutput("<form action='runmodule.php?module=juniormoderator&op=restore' method='post'>");
					rawoutput("<input type='hidden' name='commentid' value='" . $element['comment']['commentid'] . "'>");
					rawoutput("<input type='submit' class='button' value='" . $buttonrestore . "'>");
					rawoutput("</form>");
				} else {
					rawoutput("<td colspan='2' align='center'>");
					if (isset($element['validatedby'])) {
						rawoutput("<table cellspacing=0><tr><td>&nbsp;&nbsp;</td><td>");
						output("`c`b`@Validated! `b`c");
						rawoutput("</td><td>&nbsp;&nbsp;</td></tr></table>");
						rawoutput("</td><td>");
						output("`#Validated`& on %s at %s by %s`0.", date("Y.m.d", $element['validatedate']), date("H:i:s", $element['validatedate']), $element['validatedby']);
					} else {
						rawoutput("<table cellspacing=0><tr><td>&nbsp;&nbsp;</td><td>");
						output("`c`b`%Restored! `b`c");
						rawoutput("</td><td>&nbsp;&nbsp;</td></tr></table>");
						rawoutput("</td><td>");
						output("`#Restored`& on %s at %s by %s`0.", date("Y.m.d", $element['restoredate']), date("H:i:s", $element['restoredate']), $element['restoredby']);
					}
					rawoutput("</td></tr><tr><td></td><td>");
				}

				rawoutput("</td><td>");
				output("`#Deleted`& on %s at %s by %s`0.", date("Y.m.d", $element['deletedate']), date("H:i:s", $element['deletedate']), $element['jrmod']);
				rawoutput("</td></tr><tr><td></td><td></td><td>");
				juniormoderator_displaycomment($element['comment']);
				output_notl("`n");
				rawoutput("</td></tr>");
			}

			rawoutput("</table>");
		}

		page_footer();
	} else {
		juniormoderator_die("Operation Not Defined");
	}
}

function juniormoderator_displaycomment($ccomment)
{
	$section = translate_inline($ccomment['section']);
	$time = juniormoderator_formattedtime(strtotime($ccomment['postdate']));
	$author = juniormoderator_formattedauthor($ccomment['author']);
	$comment = juniormoderator_formattedcomment($ccomment['comment']);

	output("(%s) %s %s %s`0`n", $section, $time, $author, $comment, true);
}

	// the code here was mostly taken from the lib/commentary.php file,
	// although it may be more useful if the code was placed into one of
	// the files in the lib directory
function juniormoderator_formattedtime($timestamp)
{
	global $session;

	if ($session['user']['prefs']['timestamp']==1) {
		if (!isset($session['user']['prefs']['timeformat'])) $session['user']['prefs']['timeformat'] = "[m/d h:ia]";
		$time = strtotime("+{$session['user']['prefs']['timeoffset']} hours",$timestamp);
		return (date("`7" . $session['user']['prefs']['timeformat'] . "`0",$time));
	} elseif ($session['user']['prefs']['timestamp']==2) {
		return ("`7(" . reltime($timestamp) . ")`0");
	} else {
		return "";
	}
}

function juniormoderator_formattedauthor($acctid)
{
	$sql = "SELECT name, login, clanid, clanrank FROM " . db_prefix("accounts") . " WHERE acctid=" . $acctid;
	$res = db_query($sql);

	if (db_num_rows($res)>0) {
		$row = db_fetch_assoc($res);
		$tag = juniormoderator_formattedclantag($row['clanid'], $row['clanrank']);
		if ($tag != "") $tag .= " ";
		$link = "bio.php?char=" . rawurlencode($row['login']) . "&ret=" . URLEncode($_SERVER['REQUEST_URI']);
		addnav("",$link);
		return ($tag . "`0<a href='$link' style='text-decoration: none'>`&{$row['name']}`0</a>`&");
	}

	return "";
}

function juniormoderator_formattedclantag($clanid, $clanrank)
{
	if ($clanrank==0) return "";

	$sql = "SELECT clanid, clanshort FROM " . db_prefix("clans") . " WHERE clanid=" . $clanid;
	$res = db_query($sql);
	if ($row=db_fetch_assoc($res)) {
		$clanshort = $row['clanshort'];

		if ($clanrank==1) return ("`#<`2$clanshort`#>`0");
		if ($clanrank==2) return ("`^<`2$clanshort`^>`0");
		if ($clanrank==3) return ("`&<`2$clanshort`&>`0");

			// should be hit only if more clan rank values are added
		return ("`%<`2$clanshort`%>`0");

	} else return "";
}

function juniormoderator_formattedcomment($comment)
{
	$emote = preg_replace("|^::|", "`&", $comment);
	if ($emote != $comment) return $emote;
	$emote = preg_replace("|^:|", "`&", $comment);
	if ($emote != $comment) return $emote;
	$emote = preg_replace("|^/me|", "`&", $comment);
	if ($emote != $comment) return $emote;

	return (sprintf("`3says, \"`#%s`3\"", $comment));
}

	// this function should primarily be used by other code
	//    to determine if the player in question is a junior moderator
function juniormoderator_deputized($acctid) {
	return (get_module_pref("area", false, $acctid)!="");
}

function juniormoderator_checkforsuperuser() {
	global $session;

	if (!($session['user']['superuser'] & SU_EDIT_COMMENTS)) {
		juniormoderator_die("Unauthorized Access");
	}
}

function juniormoderator_pruneoldentries() {

	$toreview = @unserialize(get_module_setting("toreview"));
	if (!is_array($toreview)) $toreview = array();

	$changedtoreview = false;

	$toreviewkeys = array_keys($toreview);

	for($j=0; $j<count($toreview); $j++) {

		$i = $toreviewkeys[$j];

		unset($date);

		if (isset($toreview[$i]['validatedate'])) $date = $toreview[$i]['validatedate'];
		if (isset($toreview[$i]['restoredate'])) $date = $toreview[$i]['restoredate'];

		if (isset($date)) {
			if ($date < strtotime("-5 days")) {
				unset($toreview[$i]);
				$changedtoreview = true;
			}
		}
	}

	if ($changedtoreview) set_module_setting("toreview", serialize($toreview));
}

function juniormoderator_die($header, $text="`&Oops.  You're not supposed to be here.") {
	page_header($header);
	output($text);
	villagenav();
	page_footer();
	die();
}

?>

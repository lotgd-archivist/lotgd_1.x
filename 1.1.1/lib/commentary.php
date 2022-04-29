<?php
// translator ready
// addnews ready
// mail ready
require_once("lib/datetime.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");

$comsecs = array();
function commentarylocs() {
	global $comsecs, $session;
	if (is_array($comsecs) && count($comsecs)) return $comsecs;

	$vname = getsetting("villagename", LOCATION_FIELDS);
	$iname = getsetting("innname", LOCATION_INN);
	tlschema("commentary");
	$comsecs['village'] = sprintf_translate("%s Square", $vname);
	if ($session['user']['superuser'] & ~SU_DOESNT_GIVE_GROTTO) {
		$comsecs['superuser']=translate_inline("Grotto");
	}
	$comsecs['shade']=translate_inline("Land of the Shades");
	$comsecs['grassyfield']=translate_inline("Grassy Field");
	$comsecs['inn']="$iname";
	$comsecs['motd']=translate_inline("MotD");
	$comsecs['veterans']=translate_inline("Veterans Club");
	$comsecs['hunterlodge']=translate_inline("Hunter's Lodge");
	$comsecs['gardens']=translate_inline("Gardens");
	$comsecs['waiting']=translate_inline("Clan Hall Waiting Area");
	if (getsetting("betaperplayer", 1) == 1 && @file_exists("pavilion.php")) {
		$comsecs['beta']=translate_inline("Pavilion");
	}
	tlschema();
	// All of the ones after this will be translated in the modules.
	$comsecs = modulehook("moderate", $comsecs);
	rawoutput(tlbutton_clear());
	return $comsecs;
}

function addcommentary() {
	global $session, $emptypost;
	$section = httppost('section');
	$talkline = httppost('talkline');
	$schema = httppost('schema');
	$comment = trim(httppost('insertcommentary'));
	$counter = httppost('counter');
	if (array_key_exists('commentcounter',$session) &&
			$session['commentcounter']==$counter) {
		if ($section || $talkline || $comment) {
			$tcom = color_sanitize($comment);
			if ($tcom == "" || $tcom == ":" || $tcom == "::" || $tcom == "/me")
				$emptypost = 1;
			else injectcommentary($section, $talkline, $comment, $schema);
		}
	}
}

function injectrawcomment($section, $author, $comment)
{
	$sql = "INSERT INTO " . db_prefix("commentary") . " (postdate,section,author,comment) VALUES ('".date("Y-m-d H:i:s")."','$section',$author,\"$comment\")";
	db_query($sql);
	invalidatedatacache("comments-{$section}");
	// invalidate moderation screen also.
	invalidatedatacache("comments-or11");
}

function injectcommentary($section, $talkline, $comment, $schema=false) {
	global $session,$doublepost, $translation_namespace;
	if ($schema===false) $schema=$translation_namespace;
	// Make the comment pristine so that we match on it correctly.
	$comment = stripslashes($comment);
	tlschema("commentary");
	$doublepost=0;
	$emptypost = 0;
	$colorcount = 0;
	if ($comment !="") {
		$commentary = str_replace("`n","",soap($comment));
		$y = strlen($commentary);
		for ($x=0;$x<$y;$x++){
			if (substr($commentary,$x,1)=="`"){
				$colorcount++;
				if ($colorcount>=getsetting("maxcolors",10)){
					$commentary = substr($commentary,0,$x).color_sanitize(substr($commentary,$x));
					$x=$y;
				}
				$x++;
			}
		}

		$args = array('commentline'=>$commentary, 'commenttalk'=>$talkline);
		$args = modulehook("commentary", $args);
		$commentary = $args['commentline'];
		$talkline = $args['commenttalk'];
		tlschema($schema);
		$talkline = translate_inline($talkline);
		tlschema();

		$commentary = preg_replace("'([^[:space:]]{45,45})([^[:space:]])'","\\1 \\2",$commentary);
		$commentary = addslashes($commentary);
		// do an emote if the area has a custom talkline and the user
		// isn't trying to emote already.
		if ($talkline!="says" && substr($commentary,0,1)!=":" &&
				substr($commentary,0,2)!="::" &&
				substr($commentary,0,3)!="/me") {
			$commentary = ":`3$talkline, \\\"`#$commentary`3\\\"";
		}
		$sql = "SELECT comment,author FROM " . db_prefix("commentary") . " WHERE section='$section' ORDER BY commentid DESC LIMIT 1";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		db_free_result($result);
		if ($row['comment']!=stripslashes($commentary) ||
				$row['author']!=$session['user']['acctid']){
			injectrawcomment($section, $session['user']['acctid'],
					$commentary);
			$session['user']['laston']=date("Y-m-d H:i:s");
		} else {
			$doublepost = 1;
		}
	}
	tlschema();
}

function commentdisplay($intro, $section, $message="Interject your own commentary?",$limit=10,$talkline="says",$schema=false) {
	// Let's add a hook for modules to block commentary sections
	$args = modulehook("blockcommentarea", array("section"=>$section));
	if (isset($args['block']) && ($args['block'] == "yes"))
		return;

	if ($intro) output($intro);
	viewcommentary($section, $message, $limit, $talkline, $schema);
}

function viewcommentary($section,$message="Interject your own commentary?",$limit=10,$talkline="says",$schema=false) {
 	global $session,$REQUEST_URI,$doublepost, $translation_namespace;
	global $emptypost;

	// Let's add a hook for modules to block commentary sections
	$args = modulehook("blockcommentarea", array("section"=>$section));
	if (isset($args['block']) && ($args['block'] == "yes"))
		return;

	if ($schema === false)
		$schema=$translation_namespace;
	tlschema("commentary");

	$nobios = array("motd.php"=>true);
	if (!array_key_exists(basename($_SERVER['SCRIPT_NAME']),$nobios)) $nobios[basename($_SERVER['SCRIPT_NAME'])] = false;
	if ($nobios[basename($_SERVER['SCRIPT_NAME'])])
		$linkbios=false;
	else
		$linkbios=true;

	if ($message=="X") $linkbios=true;

	if ($doublepost) output("`\$`bDouble post?`b`0`n");
	if ($emptypost) output("`\$`bWell, they say silence is a virtue.`b`0`n");

	$clanrankcolors=array("`!","`#","`^","`&");

	$com=(int)httpget("comscroll");
	$cc = false;
	if (httpget("comscroll") !==false && (int)$session['lastcom']==$com+1)
		$cid = (int)$session['lastcommentid'];
	else
		$cid = 0;

	$session['lastcom'] = $com;

	if ($com > 0 || $cid > 0) {
		// Find newly added comments.
		$sql = "SELECT COUNT(commentid) AS newadded FROM " .
			db_prefix("commentary") . " LEFT JOIN " .
			db_prefix("accounts") . " ON " .
			db_prefix("accounts") . ".acctid = " .
			db_prefix("commentary"). ".author WHERE section='$section' AND " .
			db_prefix("accounts").".locked=0 AND commentid > '$cid'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$newadded = $row['newadded'];
	} else {
		$newadded = 0;
	}

	$commentbuffer = array();
	if ($cid == 0) {
		$sql = "SELECT ". db_prefix("commentary") . ".*, " .
			db_prefix("accounts").".name, " .
			db_prefix("accounts").".login, " .
			db_prefix("accounts").".clanrank, " .
			db_prefix("clans") .  ".clanshort FROM " .
			db_prefix("commentary") . " INNER JOIN " .
			db_prefix("accounts") . " ON " .
			db_prefix("accounts") .  ".acctid = " .
			db_prefix("commentary"). ".author LEFT JOIN " .
			db_prefix("clans") . " ON " .
			db_prefix("clans") . ".clanid=" .
			db_prefix("accounts") .
			".clanid WHERE section = '$section' AND " .
			db_prefix("accounts") .
			".locked=0 ORDER BY commentid DESC LIMIT " .
			($com*$limit).",$limit";
		if ($com==0 && strstr( $_SERVER['REQUEST_URI'], "/moderate.php" ) !== $_SERVER['REQUEST_URI'] )
			$result = db_query_cached($sql,"comments-{$section}");
		else
			$result = db_query($sql);
		while($row = db_fetch_assoc($result)) $commentbuffer[] = $row;
	} else {
		$sql = "SELECT " . db_prefix("commentary") . ".*, " .
			db_prefix("accounts").".name, " .
			db_prefix("accounts").".login, " .
			db_prefix("accounts").".clanrank, " .
			db_prefix("clans").".clanshort FROM " .
			db_prefix("commentary") . " INNER JOIN " .
			db_prefix("accounts") . " ON " .
			db_prefix("accounts") . ".acctid = " .
			db_prefix("commentary"). ".author LEFT JOIN " .
			db_prefix("clans") . " ON " . db_prefix("clans") . ".clanid=" .
			db_prefix("accounts") .
			".clanid WHERE section = '$section' AND " .
			db_prefix("accounts") .
			".locked=0 AND commentid > '$cid' " .
			"ORDER BY commentid ASC LIMIT $limit";
		$result = db_query($sql);
		while ($row = db_fetch_assoc($result)) $commentbuffer[] = $row;
		$commentbuffer = array_reverse($commentbuffer);
	}

	$rowcount = count($commentbuffer);
	if ($rowcount > 0)
		$session['lastcommentid'] = $commentbuffer[0]['commentid'];

	$counttoday=0;
	for ($i=0; $i < $rowcount; $i++){
		$row = $commentbuffer[$i];
		$row['comment'] = comment_sanitize($row['comment']);
		$commentids[$i] = $row['commentid'];
		if (date("Y-m-d",strtotime($row['postdate']))==date("Y-m-d")){
			if ($row['name']==$session['user']['name']) $counttoday++;
		}
		$x=0;
		$ft="";
		for ($x=0;strlen($ft)<3 && $x<strlen($row['comment']);$x++){
			if (substr($row['comment'],$x,1)=="`" && strlen($ft)==0) {
				$x++;
			}else{
				$ft.=substr($row['comment'],$x,1);
			}
		}

		$link = "bio.php?char=" . rawurlencode($row['login']) .
			"&ret=".URLEncode($_SERVER['REQUEST_URI']);

		if (substr($ft,0,2)=="::")
			$ft = substr($ft,0,2);
		elseif (substr($ft,0,1)==":")
			$ft = substr($ft,0,1);

		$row['comment'] = holidayize($row['comment'],'comment');
		$row['name'] = holidayize($row['name'],'comment');
		if ($row['clanrank'])
			$row['name'] = ($row['clanshort']>""?"{$clanrankcolors[ceil($row['clanrank']/10)]}&lt;`2{$row['clanshort']}{$clanrankcolors[ceil($row['clanrank']/10)]}&gt; `&":"").$row['name'];
		if ($ft=="::" || $ft=="/me" || $ft==":"){
			$x = strpos($row['comment'],$ft);
			if ($x!==false){
				if ($linkbios)
					$op[$i] = str_replace("&amp;","&",HTMLEntities(substr($row['comment'],0,$x), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0<a href='$link' style='text-decoration: none'>\n`&{$row['name']}`0</a>\n`& ".str_replace("&amp;","&",HTMLEntities(substr($row['comment'],$x+strlen($ft)), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`n";
				else
					$op[$i] = str_replace("&amp;","&",HTMLEntities(substr($row['comment'],0,$x), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`&{$row['name']}`0`& ".str_replace("&amp;","&",HTMLEntities(substr($row['comment'],$x+strlen($ft)), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`n";
				$rawc[$i] = str_replace("&amp;","&",HTMLEntities(substr($row['comment'],0,$x), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`&{$row['name']}`0`& ".str_replace("&amp;","&",HTMLEntities(substr($row['comment'],$x+strlen($ft)), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`n";
			}
		}
		if (!isset($op) || !is_array($op)) $op = array();
		if (!array_key_exists($i,$op) || $op[$i] == "")  {
			if ($linkbios)
				$op[$i] = "`0<a href='$link' style='text-decoration: none'>`&{$row['name']}`0</a>`3 says, \"`#".str_replace("&amp;","&",HTMLEntities($row['comment'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`3\"`0`n";
			else
				$op[$i] = "`&{$row['name']}`3 says, \"`#".str_replace("&amp;","&",HTMLEntities($row['comment'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`3\"`0`n";
			$rawc[$i] = "`&{$row['name']}`3 says, \"`#".str_replace("&amp;","&",HTMLEntities($row['comment'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`3\"`0`n";
		}
		$session['user']['prefs']['timeoffset'] = round($session['user']['prefs']['timeoffset'],1);

		if (!array_key_exists('timestamp', $session['user']['prefs']))
			$session['user']['prefs']['timestamp'] = 0;

		if ($session['user']['prefs']['timestamp']==1) {
			if (!isset($session['user']['prefs']['timeformat'])) $session['user']['prefs']['timeformat'] = "[m/d h:ia]";
			$time = strtotime($row['postdate']) + ($session['user']['prefs']['timeoffset'] * 60 * 60);
			$s=date("`7" . $session['user']['prefs']['timeformat'] . "`0 ",$time);
			$op[$i] = $s.$op[$i];
		}elseif ($session['user']['prefs']['timestamp']==2) {
			$s=reltime(strtotime($row['postdate']));
			$op[$i] = "`7($s)`0 ".$op[$i];
		}
		if ($message=="X")
			$op[$i]="`0({$row['section']}) ".$op[$i];
		if ($row['postdate']>=$session['user']['recentcomments'])
			$op[$i]="<img src='images/new.gif' alt='&gt;' width='3' height='5' align='absmiddle'> ".$op[$i];
		addnav("",$link);
		$auth[$i] = $row['author'];
		$rawc[$i] = full_sanitize($rawc[$i]);
		$rawc[$i] = htmlentities($rawc[$i], ENT_QUOTES, getsetting("charset", "ISO-8859-1"));
	}
	$i--;
	$outputcomments=array();
	$sect="x";

	$moderating=false;
	if (($session['user']['superuser'] & SU_EDIT_COMMENTS) && $message=="X")
		$moderating=true;

	for (;$i>=0;$i--){
		$out="";
		if ($moderating){
			if ($session['user']['superuser'] & SU_EDIT_USERS){
				$out.="`0[ <input type='checkbox' name='comment[{$commentids[$i]}]'> | <a href='user.php?op=setupban&userid=".$auth[$i]."&reason=".rawurlencode($rawc[$i])."'>Ban</a> ]&nbsp;";
				addnav("","user.php?op=setupban&userid=$auth[$i]&reason=".rawurlencode($rawc[$i]));
			}else{
				$out.="`0[ <input type='checkbox' name='comment[{$commentids[$i]}]'> ]&nbsp;";
			}
			$matches=array();
			preg_match("/[(]([^)]*)[)]/",$op[$i],$matches);
			$sect=trim($matches[1]);
			if (substr($sect,0,5)!="clan-" || $sect==$section){
				if (substr($sect,0,4)!="pet-"){
					$out.=$op[$i];
					if (!isset($outputcomments[$sect]) ||
							!is_array($outputcomments[$sect]))
						$outputcomments[$sect]=array();
					array_push($outputcomments[$sect],$out);
				}
			}
		}else{
			$out.=$op[$i];
			if (!array_key_exists($sect,$outputcomments) || !is_array($outputcomments[$sect]))
				$outputcomments[$sect]=array();
			array_push($outputcomments[$sect],$out);
		}
	}

	if ($moderating){
		$scriptname=substr($_SERVER['SCRIPT_NAME'],strrpos($_SERVER['SCRIPT_NAME'],"/")+1);
		addnav("","$scriptname?op=commentdelete&return=".URLEncode($_SERVER['REQUEST_URI']));
		$mod_Del1 = htmlentities(translate_inline("Delete Checked Comments"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
		$mod_Del2 = htmlentities(translate_inline("Delete Checked & Ban (3 days)"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
		$mod_Del_confirm = addslashes(htmlentities(translate_inline("Are you sure you wish to ban this user and have you specified the exact reason for the ban, i.e. cut/pasted their offensive comments?"), ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
		$mod_reason = translate_inline("Reason:");
		$mod_reason_desc = htmlentities(translate_inline("Banned for comments you posted."), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));

		output_notl("<form action='$scriptname?op=commentdelete&return=".URLEncode($_SERVER['REQUEST_URI'])."' method='POST'>",true);
		output_notl("<input type='submit' class='button' value=\"$mod_Del1\">",true);
		output_notl("<input type='submit' class='button' name='delnban' value=\"$mod_Del2\" onClick=\"return confirm('$mod_Del_confirm');\">",true);
		output_notl("`n$mod_reason <input name='reason0' size='40' value=\"$mod_reason_desc\" onChange=\"document.getElementById('reason').value=this.value;\">",true);
	}


	//output the comments
	ksort($outputcomments);
	reset($outputcomments);
	$sections = commentarylocs();
	$needclose = 0;

	while (list($sec,$v)=each($outputcomments)){
		if ($sec!="x") {
			if($needclose) modulehook("}collapse");
			output_notl("`n<hr><a href='moderate.php?area=%s'>`b`^%s`0`b</a>`n",
				$sec, isset($sections[$sec]) ? $sections[$sec] : "($sec)", true);
			addnav("", "moderate.php?area=$sec");
			modulehook("collapse{",array("name"=>"com-".$sec));
			$needclose = 1;
		} else {
			modulehook("collapse{",array("name"=>"com-".$section));
			$needclose = 1;
		}
		reset($v);
		while (list($key,$val)=each($v)){
			$args = array('commentline'=>$val);
			$args = modulehook("viewcommentary", $args);
			$val = $args['commentline'];
			output_notl($val, true);
		}
	}

	if ($moderating && $needclose) {
		modulehook("}collapse");
		$needclose = 0;
	}

	if ($moderating){
		output_notl("`n");
		rawoutput("<input type='submit' class='button' value=\"$mod_Del1\">");
		rawoutput("<input type='submit' class='button' name='delnban' value=\"$mod_Del2\" onClick=\"return confirm('$mod_Del_confirm');\">");
		output_notl("`n%s ", $mod_reason);
		rawoutput("<input name='reason' size='40' id='reason' value=\"$mod_reason_desc\">");
		rawoutput("</form>");
		output_notl("`n");
	}

	if ($session['user']['loggedin']) {
		$args = modulehook("insertcomment", array("section"=>$section));
		if (array_key_exists("mute",$args) && $args['mute'] &&
				!($session['user']['superuser'] & SU_EDIT_COMMENTS)) {
			output_notl("%s", $args['mutemsg']);
		} elseif ($counttoday<($limit/2) ||
				($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO)
				|| !getsetting('postinglimit',1)){
			if ($message!="X"){
				$message="`n`@$message`n";
				output($message);
				talkform($section,$talkline,$limit,$schema);
			}
		}else{
			$message="`n`@$message`n";
			output($message);
			output("Sorry, you've exhausted your posts in this section for now.`0`n");
		}
	}

	$firstu = translate_inline("&lt;&lt; First Unseen");
	$prev = translate_inline("&lt; Previous");
	$ref = translate_inline("Refresh");
	$next = translate_inline("Next &gt;");
	$lastu = translate_inline("Last Page &gt;&gt;");
	if ($rowcount>=$limit || $cid>0){
		$sql = "SELECT count(*) AS c FROM " . db_prefix("commentary") . " WHERE section='$section' AND postdate > '{$session['user']['recentcomments']}'";
		$r = db_query($sql);
		$val = db_fetch_assoc($r);
		$val = round($val['c'] / $limit + 0.5,0) - 1;
		if ($val>0){
			$first = comscroll_sanitize($REQUEST_URI)."&comscroll=".($val);
			$first = str_replace("?&","?",$first);
			if (!strpos($first,"?")) $first = str_replace("&","?",$first);
			$first .= "&refresh=1";
			output_notl("<a href=\"$first\">$firstu</a>",true);
			addnav("",$first);
		}else{
			output_notl($firstu,true);
		}
		$req = comscroll_sanitize($REQUEST_URI)."&comscroll=".($com+1);
		$req = str_replace("?&","?",$req);
		if (!strpos($req,"?")) $req = str_replace("&","?",$req);
		$req .= "&refresh=1";
		output_notl("<a href=\"$req\">$prev</a>",true);
		addnav("",$req);
	}else{
		output_notl("$firstu $prev",true);
	}
	$last = appendlink(comscroll_sanitize($REQUEST_URI),"refresh=1");

	// Okay.. we have some smart-ass (or stupidass, you guess) players
	// who think that the auto-reload firefox plugin is a good way to
	// avoid our timeouts.  Won't they be surprised when I take that little
	// hack away.
	$last = appendcount($last);

	$last = str_replace("?&","?",$last);
	//if (!strpos($last,"?")) $last = str_replace("&","?",$last);
	//debug($last);
	output_notl("&nbsp;<a href=\"$last\">$ref</a>&nbsp;",true);
	addnav("",$last);
	if ($com>0 || ($cid > 0 && $newadded > $limit)){
		$req = comscroll_sanitize($REQUEST_URI)."&comscroll=".($com-1);
		$req = str_replace("?&","?",$req);
		if (!strpos($req,"?")) $req = str_replace("&","?",$req);
		$req .= "&refresh=1";
		output_notl(" <a href=\"$req\">$next</a>",true);
		addnav("",$req);
		output_notl(" <a href=\"$last\">$lastu</a>",true);
	}else{
		output_notl("$next $lastu",true);
	}
	if (!$cc) db_free_result($result);
	tlschema();
	if ($needclose) modulehook("}collapse");
}

function talkform($section,$talkline,$limit=10,$schema=false){
	global $REQUEST_URI,$session,$translation_namespace;
	if ($schema===false) $schema=$translation_namespace;
	tlschema("commentary");

	$counttoday=0;
	if (substr($section,0,5)!="clan-"){
		$sql = "SELECT author FROM " . db_prefix("commentary") . " WHERE section='$section' AND postdate>'".date("Y-m-d 00:00:00")."' ORDER BY commentid DESC LIMIT $limit";
		$result = db_query($sql);
		while ($row=db_fetch_assoc($result)){
			if ($row['author']==$session['user']['acctid']) $counttoday++;
		}
		if (round($limit/2,0)-$counttoday <= 0 && getsetting('postinglimit',1)){
			if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO){
				output("`n`)(You'd be out of posts if you weren't a superuser or moderator.)`n");
			}else{
				output("`n`)(You are out of posts for the time being.  Once some of your existing posts have moved out of the comment area, you'll be allowed to post again.)`n");
				return false;
			}
		}
	}
	rawoutput("<script language='JavaScript'>
	function previewtext(t){
		var out = \"<span class=\'colLtWhite\'>".addslashes(appoencode($session['user']['name']))." \";
		var end = '</span>';
		var x=0;
		var y='';
		var z='';
		if (t.substr(0,2)=='::'){
			x=2;
			out += '</span><span class=\'colLtWhite\'>';
		}else if (t.substr(0,1)==':'){
			x=1;
			out += '</span><span class=\'colLtWhite\'>';
		}else if (t.substr(0,3)=='/me'){
			x=3;
			out += '</span><span class=\'colLtWhite\'>';
		}else{
			out += '</span><span class=\'colDkCyan\'>".addslashes(appoencode(translate_inline($talkline))).", \"</span><span class=\'colLtCyan\'>';
			end += '</span><span class=\'colDkCyan\'>\"';
		}
		for (; x < t.length; x++){
			y = t.substr(x,1);
			if (y=='<'){
				out += '&lt;';
				continue;
			}else if(y=='>'){
				out += '&gt;';
				continue;
			}else if (y=='`'){
				if (x < t.length-1){
					z = t.substr(x+1,1);
					if (z=='0'){
						out += '</span>';
					}else if (z=='1'){
						out += '</span><span class=\'colDkBlue\'>';
					}else if (z=='2'){
						out += '</span><span class=\'colDkGreen\'>';
					}else if (z=='3'){
						out += '</span><span class=\'colDkCyan\'>';
					}else if (z=='4'){
						out += '</span><span class=\'colDkRed\'>';
					}else if (z=='5'){
						out += '</span><span class=\'colDkMagenta\'>';
					}else if (z=='6'){
						out += '</span><span class=\'colDkYellow\'>';
					}else if (z=='7'){
						out += '</span><span class=\'colDkWhite\'>';
					}else if (z=='q'){
						out += '</span><span class=\'colDkOrange\'>';
					}else if (z=='!'){
						out += '</span><span class=\'colLtBlue\'>';
					}else if (z=='@'){
						out += '</span><span class=\'colLtGreen\'>';
					}else if (z=='#'){
						out += '</span><span class=\'colLtCyan\'>';
					}else if (z=='$'){
						out += '</span><span class=\'colLtRed\'>';
					}else if (z=='%'){
						out += '</span><span class=\'colLtMagenta\'>';
					}else if (z=='^'){
						out += '</span><span class=\'colLtYellow\'>';
					}else if (z=='&'){
						out += '</span><span class=\'colLtWhite\'>';
					}else if (z=='Q'){
						out += '</span><span class=\'colLtOrange\'>';
					}else if (z==')'){
						out += '</span><span class=\'colLtBlack\'>';
					}else if (z=='r'){
						out += '</span><span class=\'colRose\'>';
					}else if (z=='R'){
						out += '</span><span class=\'colRose\'>';
					}else if (z=='v'){
						out += '</span><span class=\'coliceviolet\'>';
					}else if (z=='V'){
						out += '</span><span class=\'colBlueViolet\'>';
					}else if (z=='g'){
						out += '</span><span class=\'colXLtGreen\'>';
					}else if (z=='G'){
						out += '</span><span class=\'colXLtGreen\'>';
					}else if (z=='T'){
						out += '</span><span class=\'colDkBrown\'>';
					}else if (z=='t'){
						out += '</span><span class=\'colLtBrown\'>';
					}else if (z=='~'){
						out += '</span><span class=\'colBlack\'>';
					}else if (z=='j'){
						out += '</span><span class=\'colMdGrey\'>';
					}else if (z=='J'){
						out += '</span><span class=\'colMdBlue\'>';
					}else if (z=='e'){
						out += '</span><span class=\'colDkRust\'>';
					}else if (z=='E'){
						out += '</span><span class=\'colLtRust\'>';
					}else if (z=='l'){
						out += '</span><span class=\'colDkLinkBlue\'>';
					}else if (z=='L'){
						out += '</span><span class=\'colLtLinkBlue\'>';
					}
					x++;
				}
			}else{
				out += y;
			}
		}
		document.getElementById(\"previewtext\").innerHTML=out+end+'<br/>';
	}
	</script>
	");
	if ($talkline!="says") $tll = strlen($talkline)+11; else $tll=0;
	$req = comscroll_sanitize($REQUEST_URI)."&comment=1";
	$req = str_replace("?&","?",$req);
	if (!strpos($req,"?")) $req = str_replace("&","?",$req);
	addnav("",$req);
	output_notl("<form action=\"$req\" method='POST' autocomplete='false'>",true);
	output_notl("<input name='insertcommentary' id='commentary' onKeyUp='previewtext(document.getElementById(\"commentary\").value);'; size='40' maxlength='".(200-$tll)."'>",true);
	rawoutput("<input type='hidden' name='talkline' value='$talkline'>");
	rawoutput("<input type='hidden' name='schema' value='$schema'>");
	rawoutput("<input type='hidden' name='counter' value='{$session['counter']}'>");
	$session['commentcounter'] = $session['counter'];
	if ($section=="X"){
		$vname = getsetting("villagename", LOCATION_FIELDS);
		$iname = getsetting("innname", LOCATION_INN);
		$sections = commentarylocs();
		reset ($sections);
		output_notl("<select name='section'>",true);
		while (list($key,$val)=each($sections)){
			output_notl("<option value='$key'>$val</option>",true);
		}
		output_notl("</select>",true);
	}else{
		output_notl("<input type='hidden' name='section' value='$section'>",true);
	}
	$add = translate_inline("Add");
	output_notl("<input type='submit' class='button' value='$add'>`n",true);
	if (round($limit/2,0)-$counttoday < 3 && getsetting('postinglimit',1)){
		output("`)(You have %s posts left today)`n`0",(round($limit/2,0)-$counttoday));
	}
	output_notl("<div id='previewtext'></div></form>",true);
	tlschema();
}
?>
<?php
// PRIVATE_CODE
// addnews ready
// mail ready
function blog_getmoduleinfo(){
	$info = array(
		"name"=>"MightyE Blog",
		"version"=>"1.0",
		"author"=>"Eric Stevens",
		"allowanonymous"=>true,
		"category"=>"General",
		"settings"=>array(
			"Blog Settings,title",
			"lastblog"=>"Last time any blog was posted|0000-00-00 00:00:00",
			"remote"=>"Address of a remote server where blogs are hosted.|",
			"words"=>"Path to words dictionary file|/usr/share/dict/words"
		),
		"prefs"=>array(
			"Blog User Preferences,title",
			"lastblog"=>"Last time user read blog|0000-00-00 00:00:00",
			"canblog"=>"User can blog,bool|0",
			"blogsig"=>"Blog Signature|"
		)
	);
	return $info;
}

function blog_install(){
	if (!db_table_exists(db_prefix("mod_blog"))){
		$sql = "CREATE TABLE " . db_prefix("mod_blog") . " (
			blogid int(11) unsigned not null auto_increment,
			author int(11) unsigned not null,
			date datetime not null,
			subject varchar(255),
			body text,
			
			PRIMARY KEY(blogid),
			INDEX(date),
			INDEX(author)
		)";
		db_query($sql);
		output("Created Blog Table`n");
	}
	$sql = "DESCRIBE " . db_prefix("mod_blog");
	$result = db_query($sql);
	$fields = array();
	while ($row = db_fetch_assoc($result)){
		$fields[$row['Field']] = $row['Field'];
	}
	if (!isset($fields['hits'])){
		$sql = "ALTER TABLE " . db_prefix("mod_blog") . " ADD hits int(11) unsigned not null default 0";
		db_query($sql);
		output("Adding column 'hits' to mod_blog`n");
	}
	module_addhook("village");
	module_addhook("footer-shades");
	module_addhook("index");
	return true;
}

function blog_uninstall(){
	$sql = "DROP TABLE " . db_prefix("mod_blog");
	db_query($sql);
	output("Dropped the mod_blog table, all blogs are lost.  Woe is them.");
	return true;
}

function blog_dohook($hookname,$args){
	switch($hookname){
	case "village":
	case "footer-shades":
	case "index":
		// $args only has the othernav stuff from the village.
		if ($hookname == "village") {
			tlschema($args['schemas']['othernav']);
			addnav($args['othernav']);
			tlschema();
		} else {
			addnav("Other");
		}
		$remote = get_module_setting("remote");
		if ($remote > "") {
			//this is a remote server, we'll have to fetch the most
			// recent blog date.
			// Note: if the remote server has a different set of prefixes
			// (or no prefixes) you'll need to use the special handling
			// mechanism in db_prefix()
			$result = db_query_remote("SELECT MAX(date) AS d FROM " . db_prefix("mod_blog"),$remote);
			if (db_num_rows($result)>0){
				$row = db_fetch_assoc($result);
				set_module_setting("lastblog",$row['d']);
			}
		}
		if (get_module_pref("lastblog") < get_module_setting("lastblog")){
			addnav("!?`bMightyBlogs`b","runmodule.php?module=blog&op=view");
		}else{
			addnav("!?MightyBlogs","runmodule.php?module=blog&op=view");
		}
		break;
	}
	return $args;
}

function blog_run(){
	global $session;
	require_once("lib/datetime.php");
	require_once("lib/villagenav.php");
	$op = httpget("op");
	$id = httpget("id");
	$author = httpget("author");
	$day = httpget("day");
	$month = httpget("month");
	$remote = get_module_setting("remote");
	if ($op=="keepalive"){
		$sql = "UPDATE " . db_prefix("accounts") . " SET laston='".date("Y-m-d H:i:s")."' WHERE acctid='{$session['user']['acctid']}'";
		db_query($sql);
		global $REQUEST_URI;
		echo '<html><meta http-equiv="Refresh" content="30;url='.$REQUEST_URI.'"></html><body>'.date("Y-m-d H:i:s")."</body></html>";
		exit();
	}

	page_header("Blogs");
	rawoutput("<script language='JavaScript'>
	<!--
	function showHide(id){
		var item = document.getElementById(id);
		if (item.style.visibility=='hidden'){
			item.style.visibility='visible';
			item.style.display='inline';
		}else{
			item.style.visibility='hidden';
			item.style.display='none';
		}
	}
	//-->
	</script>");
	rawoutput("<style type='text/css'>
		span.tangent {
			visibility: hidden;
			display: none;
			border: 1px dotted #0000FF;
		}
		table.calendar {
			border-left: 1px solid #000000;
			border-right: 0px solid #000000;
			border-bottom: 1px solid #000000;
			border-top: 0px solid #000000;
		}
		table.calendar tr {
			
		}
		table.calendar td {
			border-left: 0px solid #000000;
			border-right: 1px solid #000000;
			border-bottom: 0px solid #000000;
			border-top: 1px solid #000000;
			font-size: 10px;
			background-color: #003366;
			color: #FFFFFF;
			text-align: center;
		}
		table.calendar td.new {
			background-color: #006699;
		}
		table.calendar td.new a {
			color: #FFFF66;
			text-decoration: none;
		}
		table.calendar td.offmonth {
			background-color: #006633;
		}
	</style>");
	if ($op == "del") {
		$sql = "DELETE FROM " . db_prefix("mod_blog") . " WHERE blogid='".httpget("id")."'";
		db_query($sql);
		output(db_affected_rows()." blogs deleted.`n");
		$op = "view";
		$id = "";
	}

	// If the remote server has a different prefix setup, you'll need
	// to use the special handling setup in db_prefix()
	if ($id>"") {
		$where = "WHERE blogid='$id'";
		addnav("Calendar");
	}elseif ($author>""){
		$where = "WHERE " . db_prefix("accounts") . ".login='$author'";
		if ($day > ""){
			$where .= " AND date>='$day 00:00:00' AND date<='$day 23:59:59'";
		}
		addnav("$author's Calendar");
	}else{
		if ($day > ""){
			$where = " AND date>='$day 00:00:00' AND date<='$day 23:59:59'";
		} else {
			$where = "WHERE date>'".date("Y-m-d H",strtotime("-7 days"))."'";
		}
		addnav("Calendar");
	}
	$calendar = blog_calendar($month,$day,$author,$remote);
	global $templatename;
	if ($templatename == "Classic.htm") {
		$calendar = "<tr><td>$calendar</td></tr>";
	}
	addnav("$calendar","!!!addraw!!!",true);

	$sql = "SELECT " . db_prefix("accounts") . ".name, " . db_prefix("mod_blog") . ".* FROM " . db_prefix("mod_blog") . " INNER JOIN " . db_prefix("accounts") . " ON ". db_prefix("accounts") . ".acctid = ". db_prefix("mod_blog") . ".author $where ORDER BY date DESC LIMIT 15";
	
	/*debug($sql); */
	
	if ($op == "view") {
		if ($remote > "")
			$result = db_query_remote($sql,$remote);
		else
			$result = db_query($sql);
		while ($row = db_fetch_assoc($result)){
			//user ID's won't line up for a remote server, we *don't* want
			//to be able to edit this here.
			if ($remote > "") $row['author']=-1;
			blog_show($row);
		}
	}elseif ($op == "edit"){
		$result = db_query($sql);
		if (db_num_rows($result)==0){
			$row = array("name"=>$session['user']['name'],"blogid"=>"","author"=>"","date"=>date("Y-m-d H:i:s"),"subject"=>"","body"=>"","hits"=>0);
		}else{
			$row = db_fetch_assoc($result);
		}
		blog_form($row);
		if ($row['subject']>"" || $row['body']>"")
			blog_show($row);
	}elseif ($op == "save"){
		$post = httpallpost();
		if (isset($post['save'])){
			if ($post['blogid']>""){
				$sql = "UPDATE " . db_prefix("mod_blog") . " SET body='{$post['body']}', subject='{$post['subject']}' WHERE blogid='{$post['blogid']}'";
				db_query($sql);
				output(db_affected_rows()." rows updated.`n");
			}else{
				$blogsig = get_module_pref("blogsig");
				if ($blogsig > "")
					$post['body'] .= "`0`n".addslashes(get_module_pref("blogsig"))."`0";
				$date = date("Y-m-d H:i:s");
				$sql = "INSERT INTO " . db_prefix("mod_blog") . " (body, subject, author, date) VALUES ('{$post['body']}','{$post['subject']}',{$session['user']['acctid']},'$date')";
				db_query($sql);
				output(db_affected_rows()." rows inserted.`n");
				set_module_setting("lastblog", $date);
			}
			$post['body'] = stripslashes($post['body']);
			$post['subject'] = stripslashes($post['subject']);
			$post['author'] = $session['user']['acctid'];
		}else{
			//we're previewing the blog
			$post['body'] = stripslashes($post['body']);
			$post['subject'] = stripslashes($post['subject']);
			blog_form($post);
		}
		$post['body'] = blog_spell($post['body']);
		blog_show($post);
	}
	addnav("Options");
	addnav("Blog Homepage","runmodule.php?module=blog&op=view");
	if (!$session['user']['loggedin']){
		addnav("L?Return to Login","index.php");
	}elseif ($session['user']['alive']){
		villagenav();
	}else{
		addnav("S?Return to the Shades","shades.php");
	}if (get_module_pref("canblog")){
		addnav("Add a blog","runmodule.php?module=blog&op=edit&id=-1");
	}

	addnav("Browse by Author");
	// If the remote server has a different prefix setup, you'll need
	// to use the special handling setup in db_prefix()
	$sql1 = "SELECT name,max(login) AS login, max(date) AS date FROM " . db_prefix("mod_blog") . " INNER JOIN " . db_prefix("accounts") . " ON acctid = author GROUP BY name";
	if ($remote>"")
		$result = db_query_remote($sql1,$remote,600);
	else
		$result = db_query($sql1);
	while ($row = db_fetch_assoc($result)){
		addnav($row['name']." (".reltime(strtotime($row['date'])).")","runmodule.php?module=blog&op=view&author=".rawurlencode($row['login']));
	}

	global $seenblogs;
	if (count($seenblogs)>0){
		// If the remote server has a different prefix setup, you'll need
		// to use the special handling setup in db_prefix()
		$sql = "UPDATE " . db_prefix("mod_blog") . " SET hits=hits+1 WHERE blogid IN (".join(",",$seenblogs).")";
		if ($remote>"")
			db_query_remote($sql,$remote,0);
		else
			db_query($sql);
	}
	page_footer();
}

$lastblogdate = "";
$seenblogs = array();
function blog_show($blog){
	require_once("lib/nltoappon.php");
	global $lastblogdate, $session, $seenblogs;
	if (!is_array($seenblogs)) $seenblogs = array();
	if ($blog['blogid']>"" && $session['user']['acctid']!=$blog['author']) array_push($seenblogs,$blog['blogid']);
	$d = strtotime($blog['date']);
	$thisblogdate = substr($blog['date'],0,10);
	if ($thisblogdate != $lastblogdate){
		$lastblogdate = $thisblogdate;
		output("`^<font size=+1>".date("l, F d".(date("Y",$d)!=date("Y")?", Y":" "),$d)."</font>`0`n",true);
	}
	if ($blog['date'] > get_module_pref("lastblog")) set_module_pref("lastblog",$blog['date']);
	output("`^".date("h:i a T",$d)."`0 — ");
	output("`@{$blog['name']}`0");
	if ($blog['subject']>"") output(" — `%{$blog['subject']}`0");
	output("`n");
	if ($session['user']['acctid']==$blog['author']){
		output("[ <a href='runmodule.php?module=blog&op=edit&id={$blog['blogid']}'>Edit</a>",true);
		addnav("","runmodule.php?module=blog&op=edit&id={$blog['blogid']}");
		output("| <a href='runmodule.php?module=blog&op=del&id={$blog['blogid']}' onClick=\"return(confirm('Are you sure you want to delete this blog?'));\">Delete</a>",true);
		addnav("","runmodule.php?module=blog&op=del&id={$blog['blogid']}");
		output(" ]");
	}
	output("Hits: {$blog['hits']}`n");
	//add in raw links
	//$urlcodes = "[!-;=?-~]"; //all keyboard chars sans space, < and >
	$bodyparts = preg_split("/([<>])/",$blog['body'],-1,PREG_SPLIT_DELIM_CAPTURE);
	$body = "";
	$intag = false;
	while (list($key,$val)=each($bodyparts)){
		//$body .= "`n--------------`n".htmlentities($val);
		if ($val == "<") {
			$intag = true;
		}elseif ($val == ">") {
			$intag = false;
		}elseif (!$intag){
			//we're not within any HTML tags, we are safe to add links here.
			$val = htmlentities($val); //get quotes and such encoded.
			$val = str_replace("`&amp;", "`&", $val);
			$val = preg_replace("/([[:alpha:]]+:\\/\\/)([!-~]+)/","<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>",$val);
		}elseif ($intag){
			$tag = preg_split("/[ \t\n]/",$val);
			if (strtolower($tag[0])=="a"){
				$targetfound = false;
				while (list($k,$v)=each($tag)){
					if (substr(strtolower($v),0,6)=="target") {
						$targetfound = true;
						break;
					}
				}
				if (!$targetfound) $val.=" target=\"_blank\"";
			}
		}
		$body .= $val;
	}
	$body = str_replace("<tangent>","<a href='#' onClick='return false;' onClick='showHide(\"tangent{$blog['blogid']}\")'>Tangent here</a>.<br><span class='tangent' id='tangent{$blog['blogid']}'>",$body);
	$body = str_replace("</tangent>","</span>",$body);
	//$body = preg_replace("/(>?)([[:alpha:]]+:\\/\\/)($urlcodes+)[[:punct:]]?/","\\1<a href=\"\\2\\3\" target=\"_blank\">\\2\\3</a>\\4",$blog['body']);
	//$body = preg_replace("/([[:alpha:]]+:\\/\\/)([!-~]+)[[:punct:]]*/","<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>",$blog['body']);
	//yeah, we want to allow HTML, blogs are only being given to trusted users.
	output("`@".nltoappon($body)."`0`n",true);
}

function blog_form($blog){
	rawoutput("<form action='runmodule.php?module=blog&op=save' method='POST'>");
	addnav("","runmodule.php?module=blog&op=save");
	output("`bAdd / Edit a Blog:`b`n");
	rawoutput("<input type='hidden' name='blogid' value=\"".htmlentities($blog['blogid'])."\">");
	rawoutput("<input type='hidden' name='name' value=\"".htmlentities($blog['name'])."\">");
	rawoutput("<input type='hidden' name='date' value=\"".htmlentities($blog['date'])."\">");
	rawoutput("Subject: <input name='subject' value=\"".htmlentities($blog['subject'])."\" size='50' maxlength='255'><br/>");
	output("Body:`n");
	rawoutput("<textarea name='body' cols='70' rows='15' class='input'>".htmlentities($blog['body'])."</textarea><br/>");
	rawoutput("<input type='submit' value='Preview' name='preview' class='button'>");
	rawoutput("<input type='submit' value='Save' name='save' class='button'>");
	rawoutput("</form>");
	rawoutput("<iframe src='runmodule.php?module=blog&op=keepalive' width='1' height='1' border='0'></iframe>");
	addnav("","runmodule.php?module=blog&op=keepalive");
}

function blog_spell($input,$prefix="<span style='border: 1px dotted #FF0000;'>",$postfix="</span>"){
	$words = get_module_setting("words");
	require_once("lib/spell.php");
	return spell($input,$words,$prefix,$postfix);
}

function blog_calendar($month,$day,$author,$remote){
	if ($month>""){
		$month = strtotime($month."-01");
	}else{
		$month = strtotime(date("Y-m-01"));
	}
	//start should be the Sunday before on on the first of the month.
	$start = strtotime("-".date("w",$month)." days",$month); 
	$end = strtotime("+5 weeks",$start);

	$calrange = "
	SELECT DISTINCT 
		MID(date,1,10) AS d 
	FROM ".db_prefix("mod_blog")." 
	INNER JOIN ".db_prefix("accounts")." 
		ON acctid=author 
	WHERE ".($author>""?"login='$author' ":"1=1")."
		AND date>='".date("Y-m-d",$start)."'
		AND date<='".date("Y-m-d",$end)."'
	ORDER BY date";
	if ($remote>"")
		$result = db_query_remote($calrange,$remote,600);
	else
		$result = db_query($calrange);
	$blogdays = array();
	while ($row = db_fetch_assoc($result)){
		$blogdays[$row['d']]=true;
	}
	
	$calendar = "<table class='calendar' cellpadding='1' cellspacing='0'>";
	$calendar.= "<tr>";
	$link = "runmodule.php?module=blog&op=view&author=$author&month=".date("Y-m",strtotime("-1 month",$month))."&day=$day";
	addnav("",$link);
	$calendar.= "<td class='new'><a href='$link'>&lt;</a></td>";
	$calendar.= "<td colspan='5' class='new'>".date("F y",$month)."</td>";
	$link = "runmodule.php?module=blog&op=view&author=$author&month=".date("Y-m",strtotime("+1 month",$month))."&day=$day";
	addnav("",$link);
	$calendar.= "<td class='new'><a href='$link'>&gt;</a></td></tr>";
	for ($d=$start; $d<$end; $d=strtotime("+1 day",$d)){
		if (date("w",$d)==0) $calendar .= "<tr>";
		if (isset($blogdays[date("Y-m-d",$d)])){
			$link = "runmodule.php?module=blog&op=view&author=$author&month=".date("Y-m",$month)."&day=".date("Y-m-d",$d)."";
			addnav("",$link);
			$calendar.="<td class='new'><a href='$link'>".date("d",$d)."</a></td>";
		}else{
			if (date("m",$d)==date("m",$month)){
				$calendar.="<td>".date("d",$d)."</td>";
			}else{
				$calendar.="<td class='offmonth'>".date("d",$d)."</td>";
			}
		}
		if (date("w",$d)==6) $calendar .= "</tr>";
	}
	$calendar.="</table>";
	return $calendar;
}
?>

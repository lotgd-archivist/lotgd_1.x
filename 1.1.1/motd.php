<?php
// addnews ready
// translator ready
// mail ready
define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);
require_once("common.php");
require_once("lib/commentary.php");
require_once("lib/nltoappon.php");
require_once("lib/http.php");

tlschema("motd");

$op = httpget('op');
$id = httpget('id');

addcommentary();
popup_header("LoGD Message of the Day (MoTD)");

if ($session['user']['superuser'] & SU_POST_MOTD) {
	$addm = translate_inline("Add MoTD");
	$addp = translate_inline("Add Poll");
	rawoutput(" [ <a href='motd.php?op=add'>$addm</a> | <a href='motd.php?op=addpoll'>$addp</a> ]<br\><br\>");
}

function motd_admin($id, $poll=false) {
	global $session;
	if ($session['user']['superuser'] & SU_POST_MOTD) {
		$ed = translate_inline("Edit");
		$del = translate_inline("Del");
		$confirm = translate_inline("Are you sure you want to delete this item?");
		output_notl("[ ");
		if (!$poll) {
			rawoutput("<a href='motd.php?op=add".($poll?"poll":"")."&id=$id'>$ed</a> | ");
		}
		rawoutput("<a href='motd.php?op=del&id=$id' onClick=\"return confirm('$confirm');\">$del</a> ]");
	}
}

function motditem($subject,$body,$author,$date,$id){
	if ($date)
		rawoutput("<a name='motd".date("YmdHis",strtotime($date))."'>");
	output_notl("`b`^%s`0`b", $subject);
	if ($id > "") {
		motd_admin($id);
	}
	if ($date || $author) output_notl("`n");
	if ($author > "") {
		output_notl("`3%s`0", $author);
	}
	if ($date>"")
		output_notl("`0 &#150; `#%s`0", $date, true);
	if ($date || $author) output_notl("`n");

	output_notl("`2%s`0", nltoappon($body), true);
	if ($date) rawoutput("</a>");
	rawoutput("<hr>");
}

function pollitem($id,$subject,$body,$author,$date){
	global $session;
	$sql = "SELECT count(resultid) AS c, MAX(choice) AS choice FROM " . db_prefix("pollresults") . " WHERE motditem='$id' AND account='{$session['user']['acctid']}'";
	$result = db_query($sql);
	$row = db_fetch_assoc($result);
	$choice = $row['choice'];
	$body = unserialize($body);

	$poll = translate_inline("Poll:");
	if ($session['user']['loggedin']) {
		rawoutput("<form action='motd.php?op=vote' method='POST'>");
		rawoutput("<input type='hidden' name='motditem' value='$id'>",true);
	}
	output_notl("`b`&%s `^%s`0`b", $poll, $subject);
	motd_admin($id, true);
	output_notl("`n`3%s`0 &#150; `#%s`0`n", $author, $date, true);
	output_notl("`2%s`0`n", stripslashes($body['body']));
	$sql = "SELECT count(resultid) AS c, choice FROM " . db_prefix("pollresults") . " WHERE motditem='$id' GROUP BY choice ORDER BY choice";
	$result = db_query_cached($sql,"poll-$id");
	$choices=array();
	$totalanswers=0;
	$maxitem = 0;
	$number=db_num_rows($result);
	for ($i=0;$i<$number;$i++){
		$row = db_fetch_assoc($result);
		$choices[$row['choice']]=$row['c'];
		$totalanswers+=$row['c'];
		if ($row['c']>$maxitem) $maxitem = $row['c'];
	}
	while (list($key,$val)=each($body['opt'])){
		if (trim($val)!=""){
			if ($totalanswers<=0) $totalanswers=1;
			$percent = 0;
			if(isset($choices[$key])) {
				$percent = round($choices[$key] / $totalanswers * 100,1);
			}
			if ($session['user']['loggedin']) {
				rawoutput("<input type='radio' name='choice' value='$key'".($choice==$key?" checked":"").">");
			}
			output_notl("%s (%s - %s%%)`n", stripslashes($val),
					(isset($choices[$key])?(int)$choices[$key]:0), $percent);
			if ($maxitem==0){
				$width=1;
			} else {
				$width = round(($choices[$key]/$maxitem) * 400,0);
			}
			$width = max($width,1);
			rawoutput("<img src='images/rule.gif' width='$width' height='2' alt='$percent'><br>");
		}
	}
	if ($session['user']['loggedin']) {
		$vote = translate_inline("Vote");
		rawoutput("<input type='submit' class='button' value='$vote'></form>");
	}
	rawoutput("<hr>",true);
}


if ($op=="vote"){
	$motditem = httppost('motditem');
	$choice = httppost('choice');
	$sql = "DELETE FROM " . db_prefix("pollresults") . " WHERE motditem='$motditem' AND account='{$session['user']['acctid']}'";
	db_query($sql);
	$sql = "INSERT INTO " . db_prefix("pollresults") . " (choice,account,motditem) VALUES ('$choice','{$session['user']['acctid']}','$motditem')";
	db_query($sql);
	invalidatedatacache("poll-$motditem");
	header("Location: motd.php");
	exit();
}

function motd_form($id) {
	global $session;
	$subject = httppost('subject');
	$body = httppost('body');
	$preview = httppost('preview');
	if ($subject=="" || $body=="" || $preview>""){
		$edit = translate_inline("Edit a MoTD");
		$add = translate_inline("Add a MoTD");
		$ret = translate_inline("Return");

		$row = array(
			"motditem"=>0,
			"motdauthorname"=>"",
			"motdtitle"=>"",
			"motdbody"=>"",
		);
		if ($id>""){
			$sql = "SELECT " . db_prefix("motd") . ".*,name AS motdauthorname FROM " . db_prefix("motd") . " LEFT JOIN " . db_prefix("accounts") . " ON " . db_prefix("accounts") . ".acctid = " . db_prefix("motd") . ".motdauthor WHERE motditem='$id'";
			$result = db_query($sql);
			if (db_num_rows($result)>0){
				$row = db_fetch_assoc($result);
				$msg = $edit;
			}else{
				$msg = $add;
			}
		}else{
			$msg = $add;
		}
		output_notl("`b%s`b", $msg);
		rawoutput("[ <a href='motd.php'>$ret</a> ]<br>");

		rawoutput("<form action='motd.php?op=add&id={$row['motditem']}' method='POST'>");
		addnav("","motd.php?op=add&id={$row['motditem']}");
		if ($row['motdauthorname']>"")
			output("Originally by `@%s`0 on %s`n", $row['motdauthorname'],
					$row['motddate']);
		if ($subject>"") $row['motdtitle'] = stripslashes($subject);
		if ($body>"") $row['motdbody'] = stripslashes($body);
		if ($preview>""){
			if (httppost('changeauthor') || $row['motdauthorname']=="")
				$row['motdauthorname']=$session['user']['name'];
			if (httppost('changedate') || $row['motddate']=="")
				$row['motddate']=date("Y-m-d H:i:s");
			motditem($row['motdtitle'], $row['motdbody'],
					$row['motdauthorname'],$row['motddate'], "");
		}
		output("Subject: ");
		rawoutput("<input type='text' size='50' name='subject' value=\"".HTMLEntities(stripslashes($row['motdtitle']), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\"><br/>");
		output("Body:`n");
		rawoutput("<textarea align='right' class='input' name='body' cols='37' rows='5'>".HTMLEntities(stripslashes($row['motdbody']), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</textarea><br/>");
		if ($row['motditem']>0){
			output("Options:`n");
			rawoutput("<input type='checkbox' value='1' name='changeauthor'".(httppost('changeauthor')?" checked":"").">");
			output("Change Author`n");
			rawoutput("<input type='checkbox' value='1' name='changedate'".(httppost('changedate')?" checked":"").">");
			output("Change Date (force popup again)`n");
		}
		$prev = translate_inline("Preview");
		$sub = translate_inline("Submit");
		rawoutput("<input type='submit' class='button' name='preview' value='$prev'> <input type='submit' class='button' value='$sub'></form>");
	}else{
		if ($id>""){
			$sql = " SET motdtitle='$subject', motdbody='$body'";
			if (httppost('changeauthor'))
				$sql.=", motdauthor={$session['user']['acctid']}";
			if (httppost('changedate'))
				$sql.=", motddate='".date("Y-m-d H:i:s")."'";
			$sql = "UPDATE " . db_prefix("motd") . $sql . " WHERE motditem='$id'";
			db_query($sql);
			invalidatedatacache("motd");
			invalidatedatacache("motddate");
		}
		if ($id=="" || db_affected_rows()==0){
			if ($id>""){
				$sql = "SELECT * FROM " . db_prefix("motd") . " WHERE motditem='$id'";
				$result = db_query($sql);
				if (db_num_rows($result)>0) $doinsert = false;
				else $doinsert=true;
			}else{
				$doinsert=true;
			}
			if ($doinsert){
				$sql = "INSERT INTO " . db_prefix("motd") . " (motdtitle,motdbody,motddate,motdauthor) VALUES (\"$subject\",\"$body\",'".date("Y-m-d H:i:s")."','{$session['user']['acctid']}')";
				db_query($sql);
				invalidatedatacache("motd");
				invalidatedatacache("motddate");
			}
		}
		header("Location: motd.php");
		exit();
	}
}

function motd_poll_form() {
	global $session;
	$subject = httppost('subject');
	$body = httppost('body');
	if ($subject=="" || $body==""){
		output("`\$NOTE:`^ Polls cannot be edited after they are begun in order to ensure fairness and accuracy of results.`0`n`n");
		rawoutput("<form action='motd.php?op=addpoll' method='POST'>");
		addnav("","motd.php?op=add");
		output("Subject: ");
		rawoutput("<input type='text' size='50' name='subject' value=\"".HTMLEntities(stripslashes($subject), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\"><br/>");
		output("Body:`n");
		rawoutput("<textarea class='input' name='body' cols='37' rows='5'>".HTMLEntities(stripslashes($body), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</textarea><br/>");
		$option = translate_inline("Option");
		output("Choices:`n");
		$pollitem = "$option <input name='opt[]'><br/>";
		rawoutput($pollitem);
		rawoutput($pollitem);
		rawoutput($pollitem);
		rawoutput($pollitem);
		rawoutput($pollitem);
		rawoutput("<div id='hidepolls'>");
		rawoutput("</div>");
		rawoutput("<script language='JavaScript'>document.getElementById('hidepolls').innerHTML = '';</script>",true);
		$addi = translate_inline("Add Poll Item");
		$add = translate_inline("Add");
		rawoutput("<a href=\"#\" onClick=\"javascript:document.getElementById('hidepolls').innerHTML += '".addslashes($pollitem)."'; return false;\">$addi</a><br>");
		rawoutput("<input type='submit' class='button' value='$add'></form>");
	}else{
		$opt = httppost("opt");
		$body = array("body"=>$body,"opt"=>$opt);
		$sql = "INSERT INTO " . db_prefix("motd") . " (motdtitle,motdbody,motddate,motdtype,motdauthor) VALUES (\"$subject\",\"".addslashes(serialize($body))."\",'".date("Y-m-d H:i:s")."',1,'{$session['user']['acctid']}')";
		db_query($sql);
		invalidatedatacache("motd");
		invalidatedatacache("motddate");
		header("Location: motd.php");
		exit();
	}
}

function motd_del($id) {
	$sql = "DELETE FROM " . db_prefix("motd") . " WHERE motditem=\"$id\"";
	db_query($sql);
	invalidatedatacache("motd");
	invalidatedatacache("motddate");
	header("Location: motd.php");
	exit();
}

if ($op == "add" || $op == "addpoll" || $op == "del")  {
	if ($session['user']['superuser'] & SU_POST_MOTD) {
		if ($op == "add") motd_form($id);
		elseif ($op == "addpoll") motd_poll_form($id);
		elseif ($op == "del") motd_del($id);
	} else {
		if ($session['user']['loggedin']){
			$session['user']['experience'] =
				round($session['user']['experience']*0.9,0);
			addnews("%s was penalized for attempting to defile the gods.",
					$session['user']['name']);
			output("You've attempted to defile the gods.  You are struck with a wand of forgetfulness.  Some of what you knew, you no longer know.");
			saveuser();
		}
	}
}


if ($op=="") {
	$count = getsetting("motditems", 5);
	/*
	motditem("Beta!","Please see the beta message below.","","", "");
	*/
	$m = httpget("month");
	if ($m > ""){
		$sql = "SELECT " . db_prefix("motd") . ".*,name AS motdauthorname FROM " . db_prefix("motd") . " LEFT JOIN " . db_prefix("accounts") . " ON " . db_prefix("accounts") . ".acctid = " . db_prefix("motd") . ".motdauthor WHERE motddate >= '{$m}-01' AND motddate <= '{$m}-31' ORDER BY motddate DESC";
		$result = db_query($sql);
	}else{
		$sql = "SELECT " . db_prefix("motd") . ".*,name AS motdauthorname FROM " . db_prefix("motd") . " LEFT JOIN " . db_prefix("accounts") . " ON " . db_prefix("accounts") . ".acctid = " . db_prefix("motd") . ".motdauthor ORDER BY motddate DESC limit $count";
		$result = db_query_cached($sql,"motd");
	}
	$number=db_num_rows($result);
	for ($i=0;$i<$number;$i++){
		$row = db_fetch_assoc($result);
		if (!isset($session['user']['lastmotd']))
			$session['user']['lastmotd']=0;
		if ($row['motdauthorname']=="")
			$row['motdauthorname']="`@Green Dragon Staff`0";
		if ($row['motdtype']==0){
			motditem($row['motdtitle'], $row['motdbody'],
					$row['motdauthorname'], $row['motddate'],
					$row['motditem']);
		}else{
			pollitem($row['motditem'], $row['motdtitle'], $row['motdbody'],
					$row['motdauthorname'],$row['motddate'],
					$row['motditem']);
		}
	}
	/*
	motditem("Beta!","For those who might be unaware, this website is still in beta mode.  I'm working on it when I have time, which generally means a couple of changes a week.  Feel free to drop suggestions, I'm open to anything :-)","","", "");
	*/

	$result = db_query("SELECT mid(motddate,1,7) AS d, count(*) AS c FROM ".db_prefix("motd")." GROUP BY d ORDER BY d DESC");
	$row = db_fetch_assoc($result);
	rawoutput("<form action='motd.php' method='GET'>");
	output("MoTD Archives:");
	rawoutput("<select name='month' onChange='this.form.submit();' >");
	rawoutput("<option value=''>--Current--</option>");
	while ($row = db_fetch_assoc($result)){
		$time = strtotime("{$row['d']}-01");
		$m = translate_inline(date("M",$time));
		rawoutput ("<option value='{$row['d']}'".(httpget("month")==$row['d']?" selected":"").">$m".date(", Y",$time)." ({$row['c']})</option>");
	}
	rawoutput("</select>".tlbutton_clear());
	rawoutput("<input type='submit' value='&gt;' class='button'>");
	rawoutput("</form>");

	commentdisplay("`n`@Commentary:`0`n", "motd");
}

$session['needtoviewmotd']=false;

$sql = "SELECT motddate FROM " . db_prefix("motd") ." ORDER BY motditem DESC LIMIT 1";
$result = db_query_cached($sql, "motddate");
$row = db_fetch_assoc($result);
$session['user']['lastmotd']=$row['motddate'];

popup_footer();
?>

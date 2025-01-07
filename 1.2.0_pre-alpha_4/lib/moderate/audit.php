<?php
	$moddb = DB::prefix("moderatedcomments");
	$comdb = DB::prefix("commentary");
	
	$subop = httpget("subop");
	if ($subop=="undelete") {
		$unkeys = httppost("mod");
		if ($unkeys && is_array($unkeys)) {
			$sql = "SELECT * FROM $moddb WHERE modid IN ('".join("','",array_keys($unkeys))."')";
			$result = DB::query($sql);
			while ($row = DB::fetch_assoc($result)){
				$comment = unserialize($row['comment']);
				$id = addslashes($comment['commentid']);
				$postdate = addslashes($comment['postdate']);
				$section = addslashes($comment['section']);
				$author = addslashes($comment['author']);
				$comment = addslashes($comment['comment']);
				$sql = "INSERT LOW_PRIORITY INTO $comdb (commentid,postdate,section,author,comment) VALUES ('$id','$postdate','$section','$author','$comment')";
				DB::query($sql);
				invalidatedatacache("comments-$section");
			}
			$sql = "DELETE FROM $moddb WHERE modid IN ('".join("','",array_keys($unkeys))."')";
			DB::query($sql);
		} else {
			output("No items selected to undelete -- Please try again`n`n");
		}
	}
	
	//fetch and list all moderators
	$sql = "SELECT DISTINCT acctid, name FROM ".DB::prefix("accounts").
		" INNER JOIN $moddb ON acctid=moderator ORDER BY name";
	$result = DB::query($sql);
	addnav("Review by Moderator");
	tlschema("notranslate");
	while ($row = DB::fetch_assoc($result)){
		addnav(" ?".$row['name'],"moderate.php?op=audit&moderator={$row['acctid']}");
	}
	
	tlschema();
	output("`c`bComment Auditing`b`c");
	$ops = translate_inline("Ops");
	$mod = translate_inline("Moderator");
	$when = translate_inline("When");
	$com = translate_inline("Comment");
	$unmod = translate_inline("Unmoderate");
	rawoutput("<form action='moderate.php?op=audit&subop=undelete' method='POST'>");
	addnav("","moderate.php?op=audit&subop=undelete");
	rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
	rawoutput("<tr class='trhead'><td>$ops</td><td>$mod</td><td>$when</td><td>$com</td></tr>");
	$limit = "75";
	$where = "1=1 ";
	$moderator = httpget("moderator");
	if ($moderator>"") $where.="AND moderator=$moderator ";
	$sql = "SELECT name, $moddb.* FROM $moddb LEFT JOIN " . DB::prefix("accounts") . " ON acctid=moderator WHERE $where ORDER BY moddate DESC LIMIT $limit";
	$result = DB::query($sql);
	$i=0;
	$clanrankcolors=array("`!","`#","`^","`&");
	while ($row = DB::fetch_assoc($result)){
		$i++;
		rawoutput("<tr class='".($i%2?'trlight':'trdark')."'>");
		rawoutput("<td><input type='checkbox' name='mod[{$row['modid']}]' value='1'></td>");
		rawoutput("<td>");
		output_notl("%s", $row['name']);
		rawoutput("</td>");
		rawoutput("<td>");
		output_notl("%s", $row['moddate']);
		rawoutput("</td>");
		rawoutput("<td>");
		$comment = unserialize($row['comment']);
		output_notl("`0(%s)", $comment['section']);

		if ($comment['clanrank']>0) output_notl("%s<%s%s>`0", 
			$clanrankcolors[ceil($comment['clanrank']/10)], 
			$comment['clanshort'], 
			$clanrankcolors[ceil($comment['clanrank']/10)]);
		output_notl("%s", $comment['name']);
		output_notl("-");
		output_notl("%s", comment_sanitize($comment['comment']));
		rawoutput("</td>");
		rawoutput("</tr>");
	}
	rawoutput("</table>");
	rawoutput("<input type='submit' class='button' value='$unmod'>");
	rawoutput("</form>");
?>

<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/nltoappon.php");
require_once("lib/commentary.php");
require_once("lib/systemmail.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");
require_once("lib/villagenav.php");

tlschema("clans");

addnav("Clan Options");
addnav("Village");

addcommentary();
$gold = getsetting("goldtostartclan",10000);
$gems = getsetting("gemstostartclan",15);
$ranks = array(CLAN_APPLICANT=>"`!Applicant`0",CLAN_MEMBER=>"`#Member`0",CLAN_OFFICER=>"`^Officer`0",CLAN_LEADER=>"`&Leader`0");
$ranks = translate_inline($ranks);

$apply_short = "`@Clan App: `&%s`0";
$apply_subj = array($apply_short, $session['user']['name']);

$op = httpget('op');

$detail = httpget('detail');
if ($detail>0){
	if ($session['user']['superuser'] & SU_EDIT_COMMENTS){
		$clanname = httppost('clanname');
		if ($clanname) $clanname = full_sanitize($clanname);
		$clanshort = httppost('clanshort');
		if ($clanshort) $clanshort = full_sanitize($clanshort);
		if ($clanname>"" && $clanshort>""){
			$sql = "UPDATE " . db_prefix("clans") . " SET clanname='$clanname',clanshort='$clanshort' WHERE clanid='$detail'";
			output("Updating clan names`n");
			db_query($sql);
			invalidatedatacache("clandata-$detail");
		}
		if (httppost('block')>""){
			$blockdesc = translate_inline("Description blocked for inappropriate usage.");
			$sql = "UPDATE " . db_prefix("clans") . " SET descauthor=4294967295, clandesc='$blockdesc' where clanid='$detail'";
			output("Blocking public description`n");
			db_query($sql);
			invalidatedatacache("clandata-$detail");
		}elseif (httppost('unblock')>""){
			$sql = "UPDATE " . db_prefix("clans") . " SET descauthor=0, clandesc='' where clanid='$detail'";
			output("UNblocking public description`n");
			db_query($sql);
			invalidatedatacache("clandata-$detail");
		}
	}
	$sql = "SELECT * FROM " . db_prefix("clans") . " WHERE clanid='$detail'";
	$result1 = db_query_cached($sql, "clandata-$detail", 3600);
	$row1 = db_fetch_assoc($result1);
	if ($session['user']['superuser'] & SU_EDIT_COMMENTS){
		rawoutput("<div id='hidearea'>");
		rawoutput("<form action='clan.php?detail=$detail' method='POST'>");
		addnav("","clan.php?detail=$detail");
		output("Superuser / Moderator renaming:`n");
		output("Long Name: ");
		rawoutput("<input name='clanname' value=\"".htmlentities($row1['clanname'])."\" maxlength=50 size=50>");
		output("`nShort Name: ");
		rawoutput("<input name='clanshort' value=\"".htmlentities($row1['clanshort'])."\" maxlength=5 size=5>");
		output_notl("`n");
		$save = translate_inline("Save");
		rawoutput("<input type='submit' class='button' value=\"$save\">");
		$snu = htmlentities(translate_inline("Save & UNblock public description"));
		$snb = htmlentities(translate_inline("Save & Block public description"));
		if ($row1['descauthor']=="4294967295")
			rawoutput("<input type='submit' name='unblock' value=\"$snu\" class='button'>");
		else
			rawoutput("<input type='submit' name='block' value=\"$snb\" class='button'>");
		rawoutput("</form>");
		rawoutput("</div>");
		rawoutput("<script language='JavaScript'>var hidearea = document.getElementById('hidearea');hidearea.style.visibility='hidden';hidearea.style.display='none';</script>",true);
		$e = translate_inline("Edit Clan Info");
		rawoutput("<a href='#' onClick='hidearea.style.visibility=\"visible\"; hidearea.style.display=\"inline\"; return false;'>$e</a>",true);
		output_notl("`n");
	}

	output("This is the current clan membership of %s <%s>:`n",$row1['clanname'],$row1['clanshort']);
	output_notl(nltoappon($row1['clandesc'])."`n");
	page_header("Clan Membership for %s &lt;%s&gt;", full_sanitize($row1['clanname']), full_sanitize($row1['clanshort']));
	addnav("Clan Options");
	$rank = translate_inline("Rank");
	$name = translate_inline("Name");
	$dk = translate_inline("Dragon Kills");
	$jd = translate_inline("Join Date");
	rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
	rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$dk</td><td>$jd</td></tr>");
	$i=0;
	$sql = "SELECT name,login,clanrank,clanjoindate,dragonkills FROM " . db_prefix("accounts") . " WHERE clanid=$detail ORDER BY clanrank DESC,clanjoindate";
	$result = db_query($sql);
	$tot = 0;
	while ($row=db_fetch_assoc($result)){
		$i++;
		$tot += $row['dragonkills'];
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
		rawoutput("<td>");
		output_notl($ranks[$row['clanrank']]); //translated earlier
		rawoutput("</td><td>");
		$link = "bio.php?char=".rawurlencode($row['login'])."&ret=".urlencode($_SERVER['REQUEST_URI']);
		rawoutput("<a href='$link'>");
		addnav("", $link);
		output_notl("`&%s`0", $row['name']);
		rawoutput("</a>");
		rawoutput("</td><td align='center'>");
		output_notl("`\$%s`0", $row['dragonkills']);
		rawoutput("</td><td>");
		output_notl("`3%s`0", $row['clanjoindate']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	output("`n`n`^This clan has a total of `\$%s`^ dragon kills.",$tot);
}elseif ($op=="list"){
	page_header("Clan Listing");
	addnav("Clan Options");
	$sql = "SELECT MAX(" . db_prefix("clans") . ".clanid) AS clanid, MAX(clanshort) AS clanshort, MAX(clanname) AS clanname,count(" . db_prefix("accounts") . ".acctid) AS c FROM " . db_prefix("clans") . " LEFT JOIN " . db_prefix("accounts") . " ON " . db_prefix("clans") . ".clanid=" . db_prefix("accounts") . ".clanid AND clanrank>".CLAN_APPLICANT." GROUP BY " . db_prefix("clans") . ".clanid ORDER BY c DESC";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		output("`7You ask `%Karissa`7 for the clan listings.  She points you toward a marquee board near the entrance of the lobby that lists the clans.`0`n`n");
		$v = 0;
		$memb_n = translate_inline("(%s members)");
		$memb_1 = translate_inline("(%s member)");
		rawoutput('<table cellspacing="0" cellpadding="2" align="left">');
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			if ($row['c']==0){
				$sql = "DELETE FROM " . db_prefix("clans") . " WHERE clanid={$row['clanid']}";
				db_query($sql);
			}else{
				rawoutput('<tr class="' . ($v%2?"trlight":"trdark").'"><td>', true);
				if ($row['c'] == 1) {
					$memb = sprintf($memb_1, $row['c']);
				} else {
					$memb = sprintf($memb_n, $row['c']);
				}
				output_notl("&#149; &#60;%s&#62; <a href='clan.php?detail=%s'>%s</a> %s`n",
						$row['clanshort'],
						$row['clanid'],
						htmlentities(full_sanitize($row['clanname'])),
						$memb, true);
				rawoutput('</td></tr>');
				addnav("","clan.php?detail={$row['clanid']}");
				$v++;
			}
		}
		rawoutput("</table>", true);
		addnav("Return to the Lobby","clan.php");
	}else{
		output("`7You ask `%Karissa`7 for the clan listings.  She stares at you blankly for a few moments, then says, \"`5Sorry pal, no one has had enough gumption to start up a clan yet.  Maybe that should be you, eh?`7\"");
		addnav("Apply for a New Clan","clan.php?op=new");
		addnav("Return to the Lobby","clan.php");
	}
	
	page_footer();
} elseif ($op == "waiting") {
	page_header("Clan Halls");
	addnav("Clan Options");
	output("`b`c`&Clan Halls`c`b");
	output("You stroll off to the side where there are some plush leather chairs, and take a seat.");
	output("There are several other warriors sitting here talking amongst themselves.");
	output("Some Ye Olde Muzak is coming from a fake rock sitting at the base of a potted bush.`n`n");
	viewcommentary("waiting","Speak",25);
	if ($session['user']['clanrank']==CLAN_APPLICANT) {
		addnav("Return to the Lobby","clan.php");
	} else {
		addnav("Return to your Clan Rooms","clan.php");
	}
}elseif ($session['user']['clanrank']==CLAN_APPLICANT){
	page_header("Clan Halls");
	addnav("Clan Options");
	output("`b`c`&Clan Halls`c`b");
	if ($op=="apply"){
		$to = (int)httpget('to');
		if ($to>0){
			output("`%Karissa`7 accepts your application, files it in her out box, and folds her hands on the desk, staring at you.");
			output("You stand there staring blankly back at her for a few minutes before she suggests that perhaps you'd like to take a seat in the waiting area.");
			addnav("Return to the Lobby","clan.php");
			addnav("Waiting Area","clan.php?op=waiting");
			$session['user']['clanid']=$to;
			$session['user']['clanrank']=CLAN_APPLICANT;
			$session['user']['clanjoindate']=date("Y-m-d H:i:s");
			$sql = "SELECT acctid FROM " . db_prefix("accounts") . " WHERE clanid='{$session['user']['clanid']}' AND clanrank>=".CLAN_OFFICER;
			$result = db_query($sql);
			$sql = "DELETE FROM . ".  db_prefix("mail") . " WHERE msgfrom=0 AND seen=0 AND subject='".serialize($apply_subj)."'";
			db_query($sql);
			while ($row = db_fetch_assoc($result)){
				$msg = array("`^You have a new clan applicant!  `&%s`^ has completed a membership application for your clan!",$session['user']['name']);
				systemmail($row['acctid'],$apply_subj,$msg);
			}
		}else{
			$sql = "SELECT MAX(" . db_prefix("clans") . ".clanid) AS clanid,MAX(clanname) AS clanname,count(" . db_prefix("accounts") . ".acctid) AS c FROM " . db_prefix("clans") . " INNER JOIN " . db_prefix("accounts") . " ON " . db_prefix("clans") . ".clanid=" . db_prefix("accounts") . ".clanid WHERE " . db_prefix("accounts") . ".clanrank > ".CLAN_APPLICANT." GROUP BY " . db_prefix("clans") . ".clanid ORDER BY c DESC";
			$result = db_query($sql);
			if (db_num_rows($result)>0){
				output("`7You ask `%Karissa`7 for a clan membership application form.");
				output("She opens a drawer in her desk and pulls out a form.  It contains only two lines: Name and Clan Name.");
				output("You furrow your brow, not sure if you really like having to deal with all this red tape, and get set to concentrate really hard in order to complete the form.");
				output("Noticing your attempt to write on the form with your %s, `%Karissa`7 claims the form back from you, writes %s`7 on the first line, and asks you the name of the clan that you'd like to join:`n`n",$session['user']['weapon'],$session['user']['name']);
				for ($i=0;$i<db_num_rows($result);$i++){
					$row = db_fetch_assoc($result);
					if ($row['c']==0){
						$sql = "DELETE FROM " . db_prefix("clans") . " WHERE clanid={$row['clanid']}";
						db_query($sql);
					}else{
						$memb_n = translate_inline("(%s members)");
						$memb_1 = translate_inline("(%s member)");
						if ($row['c'] == 1) {
							$memb = sprintf($memb_1, $row['c']);
						} else {
							$memb = sprintf($memb_n, $row['c']);
						}
						output_notl("&#149; <a href='clan.php?op=apply&to=%s'>%s</a> %s`n",
								$row['clanid'],
								htmlentities(full_sanitize($row['clanname'])),
								$memb, true);
						addnav("","clan.php?op=apply&to={$row['clanid']}");
					}
				}
				addnav("Return to the Lobby","clan.php");
			}else{
				output("`7You ask `%Karissa`7 for a clan membership application form.");
				output("She stares at you blankly for a few moments, then says, \"`5Sorry pal, no one has had enough gumption to start up a clan yet.  Maybe that should be you, eh?`7\"");
				addnav("Apply for a New Clan","clan.php?op=new");
				addnav("Return to the Lobby","clan.php");
			}
		}
	}elseif ($op=="new"){
		$apply = httpget('apply');
		if ($apply==1){
			$ocn = httppost('clanname');
			$ocs = httppost('clanshort');
			$clanname = stripslashes($ocn);
			$clanname = full_sanitize($clanname);
			$clanname = preg_replace("'[^[:alpha:] \\'-]'","",$clanname);
			$clanname = addslashes($clanname);
			httppostset('clanname', $clanname);
			$clanshort = full_sanitize($ocs);
			$clanshort = preg_replace("'[^[:alpha:]]'","",$clanshort);
			httppostset('clanshort', $clanshort);
			$sql = "SELECT * FROM " . db_prefix("clans") . " WHERE clanname='$clanname'";
			$result = db_query($sql);
			$e1 = translate_inline("`%Karissa`7 looks over your form but informs you that your clan name must consist only of letters, spaces, apostrophes, or dashes.  Also, your short name can consist only of letters. She hands you a blank form.");
			$e2 = translate_inline("`%Karissa`7 looks over your form but informs you that you must have at least 5 and no more than 50 characters in your clan's name (and they must consist only of letters, spaces, apostrophes, or dashes), then hands you a blank form.");
			$e3 = translate_inline("`%Karissa`7 looks over your form but informs you that you must have at least 2 and no more than 5 characters in your clan's short name (and they must all be letters), then hands you a blank form.");
			$e4 = translate_inline("`%Karissa`7 looks over your form but informs you that the clan name %s is already taken, and hands you a blank form.");
			$e5 = translate_inline("`%Karissa`7 looks over your form but informs you that the short name %s is already taken, and hands you a blank form.");
			$e6 = translate_inline("`%Karissa`7 asks for the %s gold to start the clan, but you seem to be unable to produce the fees.");
			$e7 = translate_inline("`%Karissa`7 asks for the %s gold and %s gems to start the clan, but you seem to be unable to produce the fees.");
			$e8 = translate_inline("`%Karissa`7 asks for the %s gems to start the clan, but you seem to be unable to produce the fees.");
			$e9 = translate_inline("She takes your application, and stamps it \"`\$DENIED`7\".");
			if ($clanname!=$ocn || $clanshort!=$ocs){
				output_notl($e1);
				clanform();
				addnav("Return to the Lobby","clan.php");
			}elseif (strlen($clanname)<5 || strlen($clanname)>50){
				output_notl($e2);
				clanform();
				addnav("Return to the Lobby","clan.php");
			}elseif (strlen($clanshort)<2 || strlen($clanshort)>5){
				output_notl($e3);
				clanform();
				addnav("Return to the Lobby","clan.php");
			}elseif (db_num_rows($result)>0){
				output_notl($e4,stripslashes($clanname));
				clanform();
				addnav("Return to the Lobby","clan.php");
			}else{
				$sql = "SELECT * FROM " . db_prefix("clans") . " WHERE clanshort='$clanshort'";
				$result = db_query($sql);
				if (db_num_rows($result)>0){
					output_notl($e5,stripslashes($clanshort));
					clanform();
					addnav("Return to the Lobby","clan.php");
				}else{
					if ($session['user']['gold']<$gold || $session['user']['gems']<$gems){
						if ($gold>0 && $gems<=0) output_notl($e6,$gold);
						elseif ($gems>0 && $gold<=0) output_notl($e8,$gems);
						else output_notl($e7,$gold,$gems);
						output_notl($e9);
						addnav("Return to the Lobby","clan.php");
					}else{
						$sql = "INSERT INTO " . db_prefix("clans") . " (clanname,clanshort) VALUES ('$clanname','$clanshort')";
						db_query($sql);
						$id = db_insert_id();
						$session['user']['clanid'] = $id;
						$session['user']['clanrank']=CLAN_LEADER;
						$session['user']['clanjoindate']=date("Y-m-d H:i:s");
						$session['user']['gold']-=$gold;
						$session['user']['gems']-=$gems;
						output("`%Karissa`7 looks over your form, and finding that everything seems to be in order, she takes your fees, stamps the form \"`\$APPROVED`7\" and files it in a drawer.`n`n");
						output("Congratulations, you've created a new clan named %s!",stripslashes($clanname));
						addnav("Enter your clan hall","clan.php");
					}
				}
			}
		}else{
			output("`7You approach `%Karissa`7 and inquire about starting a new clan.");
			output("She tells you that there are three requirements to starting a clan.");
			output("First, you have to decide on a full name for your clan.");
			output("Second, you have to decide on an abbreviation for your clan.");
			output("Third you have to decide on whether or not you're willing to give up the fees that are required to start the clan.");
			output("This fee is used to tailor the locks on your clan door to you and your members.`n");
			output("The fees are as follows:`nGold: `^%s`7`nGems: `%%s`7",$gold,$gems);
			addnav("Return to the Lobby","clan.php");
			$e1 = translate_inline("`n`n\"`5Since you do not have enough gold with you, I cannot allow you to apply for a clan,`7\" she says.");
			$e2 = translate_inline("`n`n\"`5Since you do not have enough gems with you, I cannot allow you to apply for a clan,`7\" she says.");
			$e3 = translate_inline("`n`n\"`5If you're ok with these three requirements, please fill out the following form,`7\" she says, handing you a sheet of paper.");
			if ($session['user']['gold']<$gold){
				output_notl($e1);
			}else{
				if ($session['user']['gems']<$gems){
					output_notl($e2);
				}else{
					output_notl($e3);
					clanform();
				}
			}		
		}
	}else{
		output("`7You stand in the center of a great marble lobby filled with pillars.");
		output("All around the walls of the lobby are various doors which lead to various clan halls.");
		output("The doors each possess a variety of intricate mechanisms which are obviously elaborate locks designed to be opened only by those who have been educated on how to operate them.");
		output("Nearby, you watch another warrior glance about nervously to make sure no one is watching before touching various levers and knobs on the door.");
		output("With a large metallic \"Chunk\" the lock on the door disengages, and the door swings silently open, admitting the warrior before slamming shut.`n`n");
		output("In the center of the lobby sits a highly polished desk, behind which sits `%Karissa`7, the clan registrar.");
		output("She can take your filing for a new clan, or accept your application to an existing clan.`n`n");
		
		if ($op=="withdraw"){
			$session['user']['clanid']=0;
			$session['user']['clanrank']=CLAN_APPLICANT;
			$session['user']['clanjoindate']='0000-00-00 00:00:00';
			output("`7You tell `%Karissa`7 that you're no longer interested in joining %s.", $claninfo['clanname']);
			output("She reaches into her desk, withdraws your application, and tears it up.  \"`5You wouldn't have been happy there anyhow, I don't think,`7\" as she tosses the shreds in her trash can.");
			$claninfo = array();
			$sql = "DELETE FROM " . db_prefix("mail") . " WHERE msgfrom=0 AND seen=0 AND subject='".serialize($apply_subj)."'";
			db_query($sql);
			output("You are not a member of any clan.");
			addnav("Apply for Membership to a Clan","clan.php?op=apply");
			addnav("Apply for a New Clan","clan.php?op=new");
		}else{
			if ($claninfo["clanid"]>0){
				//applied for membership to a clan
				output("`7You approach `%Karissa`7 who smiles at you, but lets you know that your application to %s hasn't yet been accepted.",$claninfo['clanname']);
				output("Perhaps you'd like to take a seat in the waiting area, she suggests.");
				addnav("Waiting Area","clan.php?op=waiting");
				addnav("Withdraw Application","clan.php?op=withdraw");
			}else{
				//hasn't applied for membership to any clan.
				output("You are not a member of any clan.");
				addnav("Apply for Membership to a Clan","clan.php?op=apply");
				addnav("Apply for a New Clan","clan.php?op=new");
			}
		}
	}
}else{
	page_header("Clan Hall for %s",  full_sanitize($claninfo['clanname']));
	addnav("Clan Options");
	if ($op==""){
		modulehook("collapse{", array("name"=>"clanentry"));
		output("Having pressed the secret levers and turned the secret knobs on the lock of the door to your clan's hall, you gain entrance, and chat with your clan mates.`n`n");
		modulehook("}collapse");

		$sql = "SELECT name FROM " . db_prefix("accounts")  . " WHERE acctid={$claninfo['motdauthor']}";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$motdauthname = $row['name'];
		
		$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid={$claninfo['descauthor']}";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$descauthname = $row['name'];


		if ($claninfo['clanmotd'] != '') {
			rawoutput("<div style='margin-left: 15px; padding-left: 15px;'>");
			output("`&`bCurrent MoTD:`b `#by %s`2`n",$motdauthname);
			output_notl(nltoappon($claninfo['clanmotd'])."`n");
			rawoutput("</div>");
			output_notl("`n");
		}
		
		viewcommentary("clan-{$claninfo['clanid']}","Speak",25,($claninfo['customsay']>''?$claninfo['customsay']:"says"));

		if ($claninfo['clandesc'] != '') {
			modulehook("collapse{", array("name"=>"collapsedesc"));
			output("`n`n`&`bCurrent Description:`b `#by %s`2`n",$descauthname);
			output_notl(nltoappon($claninfo['clandesc']));
			modulehook("}collapse");
		}
		$sql = "SELECT count(*) AS c, clanrank FROM " . db_prefix("accounts") . " WHERE clanid={$claninfo['clanid']} GROUP BY clanrank DESC";
		$result = db_query($sql);
		// begin collapse
		modulehook("collapse{", array("name"=>"clanmemberdet"));
		output("`n`n`bMembership Details:`b`n");
		$leaders = 0;
		while ($row = db_fetch_assoc($result)){
			output_notl($ranks[$row['clanrank']].": ".$row['c']."`n");
			if ($row['clanrank']==CLAN_LEADER) $leaders = $row['c'];
		}
		output("`n");
		$noleader = translate_inline("`^There is currently no leader!  Promoting %s`^ to leader as they are the highest ranking member (or oldest member in the event of a tie).`n`n");
		if ($leaders==0){
			//There's no leader here, probably because the leader's account
			//expired.
			$sql = "SELECT name,acctid,clanrank FROM " . db_prefix("accounts") . " WHERE clanid={$session['user']['clanid']} AND acctid<>{$session['user']['acctid']} ORDER BY clanrank DESC, clanjoindate";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$sql = "UPDATE " . db_prefix("accounts") . " SET clanrank=".CLAN_LEADER." WHERE acctid={$row['acctid']}";
			db_query($sql);
			output_notl($noleader,$row['name']);
			if ($row['acctid']==$session['user']['acctid']){
				//if it's the current user, we'll need to update their session in order for the db write to take effect.
				$session['user']['clanrank']=CLAN_LEADER;
			}
		}
		// end collapse
		modulehook("}collapse");

		if ($session['user']['clanrank']>=CLAN_OFFICER){
			addnav("Update MoTD / Clan Desc","clan.php?op=motd");
		}
		addnav("M?View Membership","clan.php?op=membership");
		addnav("Online Members","list.php?op=clan");
		addnav("Your Clan's Waiting Area","clan.php?op=waiting");
		addnav("Withdraw From Your Clan","clan.php?op=withdrawconfirm");
	}elseif ($op=="motd"){
		page_header("Update Clan Description / MoTD");
		addnav("Clan Options");
		if ($session['user']['clanrank']>=CLAN_OFFICER){
			$clanmotd = substr(httppost('clanmotd'),0,4096);
			if (httppostisset('clanmotd') &&
					stripslashes($clanmotd)!=$claninfo['clanmotd']){
				$sql = "UPDATE " . db_prefix("clans") . " SET clanmotd='$clanmotd',motdauthor={$session['user']['acctid']} WHERE clanid={$claninfo['clanid']}";
				db_query($sql);
				invalidatedatacache("clandata-{$claninfo['clanid']}");
				$claninfo['clanmotd']=stripslashes($clanmotd);
				output("Updating MoTD`n");
				$claninfo['motdauthor']=$session['user']['acctid'];
			}
			$clandesc = httppost('clandesc');
			if (httppostisset('clandesc') &&
					stripslashes($clandesc)!=$claninfo['clandesc'] &&
					$claninfo['descauthor']!=4294967295){
				$sql = "UPDATE " . db_prefix("clans") . " SET clandesc='".addslashes(substr(stripslashes($clandesc),0,4096))."',descauthor={$session['user']['acctid']} WHERE clanid={$claninfo['clanid']}";
				db_query($sql);
				invalidatedatacache("clandata-{$claninfo['clanid']}");
				output("Updating description`n");
				$claninfo['clandesc']=stripslashes($clandesc);
				$claninfo['descauthor']=$session['user']['acctid'];
			}
			$customsay = httppost('customsay');
			if (httppostisset('customsay') && $customsay!=$claninfo['customsay'] && $session['user']['clanrank']>=CLAN_LEADER){
				$sql = "UPDATE " . db_prefix("clans") . " SET customsay='$customsay' WHERE clanid={$claninfo['clanid']}";
				db_query($sql);
				invalidatedatacache("clandata-{$claninfo['clanid']}");
				output("Updating custom say line`n");
				$claninfo['customsay']=stripslashes($customsay);
			}
			$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid={$claninfo['motdauthor']}";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$motdauthname = $row['name'];
			
			$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE acctid={$claninfo['descauthor']}";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$descauthname = $row['name'];
			
			output("`&`bCurrent MoTD:`b `#by %s`2`n",$motdauthname);
			output_notl(nltoappon($claninfo['clanmotd'])."`n");
			output("`&`bCurrent Description:`b `#by %s`2`n",$descauthname);
			output_notl(nltoappon($claninfo['clandesc'])."`n");
			
			rawoutput("<form action='clan.php?op=motd' method='POST'>");
			addnav("","clan.php?op=motd");
			output("`&`bMoTD:`b `7(4096 chars)`n");
			rawoutput("<textarea name='clanmotd' cols='50' rows='10' style='width: 100%'>".htmlentities($claninfo['clanmotd'])."</textarea><br>");
			output("`n`&`bDescription:`b `7(4096 chars)`n");
			$blocked = translate_inline("Your clan has been blocked from posting a description.`n");
			if ($claninfo['descauthor']==4294967295){
				output_notl($blocked);
			}else{
				rawoutput("<textarea name='clandesc' cols='50' rows='10' style='width: 100%'>".htmlentities($claninfo['clandesc'])."</textarea><br>");
			}
			if ($session['user']['clanrank']>=CLAN_LEADER){
				output("`n`&`bCustom Talk Line`b `7(blank means \"says\" -- 15 chars max)`n");
				rawoutput("<input name='customsay' value=\"".htmlentities($claninfo['customsay'])."\" maxlength=\"15\"><br/>");
			}
			$save = translate_inline("Save");
			rawoutput("<input type='submit' class='button' value='$save'>");
			rawoutput("</form>");
		}else{
			output("You do not have authority to change your clan's motd or description.");
		}
		addnav("Return to your clan hall","clan.php");
	}elseif ($op=="membership"){
		addnav("Clan Hall","clan.php");
		addnav("Clan Options");
		output("This is your current clan membership:`n");
		$setrank = httpget('setrank');
		$who = httpget('who');
		if ($setrank>""){
			$sql = "UPDATE " . db_prefix("accounts") . " SET clanrank=greatest(0,least({$session['user']['clanrank']},$setrank)) WHERE login='$who'";
			db_query($sql);
		}
		$remove = httpget('remove');
		if ($remove>""){
			$sql = "UPDATE " . db_prefix("accounts") . " SET clanrank=".CLAN_APPLICANT.",clanid=0,clanjoindate='0000-00-00 00:00:00' WHERE login='$remove' AND clanrank<={$session['user']['clanrank']}";
			db_query($sql);
			//delete unread application emails from this user.
			//breaks if the applicant has had their name changed via
			//dragon kill, superuser edit, or lodge color change
			$sql = "SELECT name FROM " . db_prefix("accounts") . " WHERE login='$remove'";
			$row = db_fetch_assoc(db_query($sql));
			require_once("lib/safeescape.php");
			$subj = safeescape(serialize(array($apply_short, $row['name'])));
			$sql = "DELETE FROM " . db_prefix("mail") . " WHERE msgfrom=0 AND seen=0 AND subject='$subj'";
			db_query($sql);
		}
		$sql = "SELECT name,login,clanrank,laston,clanjoindate,dragonkills,level FROM " . db_prefix("accounts") . " WHERE clanid={$claninfo['clanid']} ORDER BY clanrank DESC,clanjoindate";
		$result = db_query($sql);
		rawoutput("<table border='0' cellpadding='2' cellspacing='0'>");
		$rank = translate_inline("Rank");
		$name = translate_inline("Name");
		$lev = translate_inline("Level");
		$dk = translate_inline("Dragon Kills");
		$jd = translate_inline("Join Date");
		$lo = translate_inline("Last On");
		$ops = translate_inline("Operations");
		$promote = translate_inline("Promote");
		$demote = translate_inline("Demote");
		$remove = translate_inline("Remove From Clan");
		$confirm = translate_inline("Are you sure you wish to remove this member from your clan?");
		rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$lev</td><td>$dk</td><td>$jd</td><td>$lo</td>".($session['user']['clanrank']>CLAN_MEMBER?"<td>$ops</td>":"")."</tr>",true);
		$i=0;
		$tot = 0;
		while ($row=db_fetch_assoc($result)){
			$i++;
			$tot += $row['dragonkills'];
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
			rawoutput("<td>");
			output_notl($ranks[$row['clanrank']]);
			rawoutput("</td><td>");
			$link = "bio.php?char=".rawurlencode($row['login'])."&ret=".urlencode($_SERVER['REQUEST_URI']);
			rawoutput("<a href='$link'>", true);
			addnav("", $link);
			output_notl("`&%s`0", $row['name']);
			rawoutput("</a>");
			rawoutput("</td><td align='center'>");
			output_notl("`^%s`0",$row['level']);
			rawoutput("</td><td align='center'>");
			output_notl("`\$%s`0",$row['dragonkills']);
			rawoutput("</td><td>");
			output_notl("`3%s`0",$row['clanjoindate']);
			rawoutput("</td><td>");
			output_notl("`#%s`0",reltime(strtotime($row['laston'])));
			rawoutput("</td>");
			if ($session['user']['clanrank']>CLAN_MEMBER){
				rawoutput("<td>");
				if ($row['clanrank']<$session['user']['clanrank']){
					rawoutput("[ <a href='clan.php?op=membership&setrank=".($row['clanrank']+1)."&who=".rawurlencode($row['login'])."'>$promote</a> | ");
					addnav("","clan.php?op=membership&setrank=".($row['clanrank']+1)."&who=".rawurlencode($row['login']));
				}else{
					output_notl("[ %s | ", $promote);
				}
				if ($row['clanrank']<=$session['user']['clanrank'] && $row['clanrank']>CLAN_APPLICANT && $row['login']!=$session['user']['login']){
					rawoutput("<a href='clan.php?op=membership&setrank=".($row['clanrank']-1)."&who=".rawurlencode($row['login'])."'>$demote</a> | ");
					addnav("","clan.php?op=membership&setrank=".($row['clanrank']-1)."&who=".rawurlencode($row['login']));
				}else{
					output_notl("%s | ", $demote);
				}
				if ($row['clanrank'] <= $session['user']['clanrank'] && $row['login']!=$session['user']['login']){
					rawoutput("<a href='clan.php?op=membership&remove=".rawurlencode($row['login'])."' onClick=\"return confirm('$confirm');\">$remove</a> ]");
					addnav("","clan.php?op=membership&remove=".rawurlencode($row['login']));
				}else{
					output_notl("%s ]", $remove);
				}
				rawoutput("</td>");
			}
			rawoutput("</tr>");
		}
		rawoutput("</table>");
		output("`n`n`^This clan has a total of `\$%s`^ dragon kills.",$tot);
	}elseif ($op=="withdrawconfirm"){
		output("Are you sure you want to withdraw from your clan?");
		addnav("Withdraw?");
		addnav("No","clan.php");
		addnav("!?Yes","clan.php?op=withdraw");
	}elseif ($op=="withdraw"){
		if ($session['user']['clanrank']>=CLAN_LEADER){
			//first test to see if we were the leader.
			$sql = "SELECT count(*) AS c FROM " . db_prefix("accounts") . " WHERE clanid={$session['user']['clanid']} AND clanrank>=".CLAN_LEADER." AND acctid<>{$session['user']['acctid']}";
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			if ($row['c']==0){
				//we were the solitary leader.
				$sql = "SELECT name,acctid,clanrank FROM " . db_prefix("accounts") . " WHERE clanid={$session['user']['clanid']} AND acctid<>{$session['user']['acctid']} ORDER BY clanrank DESC, clanjoindate";
				$result = db_query($sql);
				if (db_num_rows($result)>0){
					//There are other members of this clan, let's test to see
					//if there is an alternate leader.
					$row = db_fetch_assoc($result);
					if ($row['clanrank']==CLAN_LEADER){
						//whew, there's an alternate leader, we do nothing.
					}else{
						//there is no alternate leader, let's promote the
						//highest ranking member (or oldest member in the
						//event of a tie).  This will capture even people
						//who applied for membership.
						$sql = "UPDATE " . db_prefix("accounts") . " SET clanrank=".CLAN_LEADER." WHERE acctid={$row['acctid']}";
						db_query($sql);
						output("`^Promoting %s`^ to leader as they are the highest ranking member (or oldest member in the event of a tie).`n`n",$row['name']);
					}
				}else{
					//There are no other members, we need to delete the clan.
					$sql = "DELETE FROM " . db_prefix("clans") . " WHERE clanid={$session['user']['clanid']}";
					db_query($sql);
					//just in case we goofed, we don't want to have to worry
					//about people being associated with a deleted clan.
					$sql = "UPDATE " . db_prefix("accounts") . " SET clanid=0,clanrank=".CLAN_APPLICANT.",clanjoindate='0000-00-00 00:00:00' WHERE clanid={$session['user']['clanid']}";
					db_query($sql);
					output("`^As you were the last member of this clan, it has been deleted.");
				}
			}else{
				//we don't have to do anything special with this clan as
				//although we were leader, there is another leader already
				//to take our place.
			}
		}else{
			//we don't have to do anything special with this clan as we were
			//not the leader, and so there should still be other members.
		}
		$sql = "SELECT acctid FROM " . db_prefix("accounts") . " WHERE clanid='{$session['user']['clanid']}' AND clanrank>=".CLAN_OFFICER." AND acctid<>'{$session['user']['acctid']}'";
		$result = db_query($sql);
		$withdraw_subj = array("`\$Clan Withdraw: `&%s`0",$session['user']['name']);
		$msg = array("`^One of your clan members has resigned their membership.  `&%s`^ has surrendered their position within your clan!",$session['user']['name']);
		$sql = "DELETE FROM " . db_prefix("mail") . " WHERE msgfrom=0 AND seen=0 AND subject='".serialize($withdraw_subj)."'";
		db_query($sql);
		while ($row = db_fetch_assoc($result)){
			systemmail($row['acctid'],$withdraw_subj,$msg);
		}
		
		$session['user']['clanid']=0;
		$session['user']['clanrank']=CLAN_APPLICANT;
		$session['user']['clanjoindate']="0000-00-00 00:00:00";
		output("`&You have withdrawn from your clan.");
		addnav("Clan Options");
		addnav("Return to the Lobby","clan.php");
	}	
}
addnav("Clan Options");
addnav("C?List Clans","clan.php?op=list");
addnav("Village");
villagenav();
page_footer();

function clanform(){
	rawoutput("<form action='clan.php?op=new&apply=1' method='POST'>");
	addnav("","clan.php?op=new&apply=1");
	output("`b`cNew Clan Application Form`c`b");
	output("Clan Name: ");
	rawoutput("<input name='clanname' maxlength='50' value=\"".htmlentities(stripslashes(httppost('clanname')))."\">");
	output("`nShort Name: ");
	rawoutput("<input name='clanshort' maxlength='5' size='5' value=\"".htmlentities(stripslashes(httppost('clanshort')))."\">");
	output("`nNote, color codes are permitted in neither clan names nor short names.");
	output("The clan name is shown on player bios and on clan overview pages while the short name is displayed next to players' names in comment areas and such.`n");
	$apply = translate_inline("Apply");
	rawoutput("<input type='submit' class='button' value='$apply'></form>");
}
?>

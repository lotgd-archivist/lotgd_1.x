<?php
		addnav("Clan Hall","clan.php");
		addnav("Clan Options");
		output("This is your current clan membership:`n");
		$setrank = httpget('setrank');
		$whoacctid = (int)httpget('whoacctid');
		if ($setrank>"") {
			$sql="SELECT name,login,clanrank FROM ".DB::prefix("accounts")." WHERE acctid=$whoacctid LIMIT 1";
			$result=DB::query($sql);
			$row=DB::fetch_assoc($result);
			$who = $row['login'];
			$whoname = $row['name'];
			if ($setrank>""){
				$args = modulehook("clan-setrank", array("setrank"=>$setrank, "login"=>$who, "name"=>$whoname, "acctid"=>$whoacctid, "clanid"=>$user->clanid, "oldrank"=>$row['clanrank']));
				if (!(isset($args['handled']) && $args['handled'])) {
					$sql = "UPDATE " . DB::prefix("accounts") . " SET clanrank=GREATEST(0,least({$user->clanrank},$setrank)) WHERE login='$who'";
					DB::query($sql);
					debuglog("Player {$user->name} changed rank of {$whoname} to {$setrank}.", $whoacctid);
				}
			}
		}
		$remove = httpget('remove');
		if ($remove>""){
			$sql = "SELECT name,login,clanrank FROM " . DB::prefix("accounts") . " WHERE acctid='$remove'";
			$row = DB::fetch_assoc(DB::query($sql));
			$args = modulehook("clan-setrank", array("setrank"=>0, "login"=>$row['login'], "name"=>$row['name'], "acctid"=>$remove, "clanid"=>$user->clanid, "oldrank"=>$row['clanrank']));
			$sql = "UPDATE " . DB::prefix("accounts") . " SET clanrank=".CLAN_APPLICANT.",clanid=0,clanjoindate='0000-00-00 00:00:00' WHERE acctid='$remove' AND clanrank<={$user->clanrank}";
			DB::query($sql);
			debuglog("Player {$user->name} removed player {$row['login']} from {$claninfo['clanname']}.", $remove);
			//delete unread application emails from this user.
			//breaks if the applicant has had their name changed via
			//dragon kill, superuser edit, or lodge color change
			require_once("lib/safeescape.php");
			$subj = safeescape(serialize(array($apply_short, $row['name'])));
			$sql = "DELETE FROM " . DB::prefix("mail") . " WHERE msgfrom=0 AND seen=0 AND subject='$subj'";
			DB::query($sql);
		}
		$sql = "SELECT name,login,acctid,clanrank,laston,clanjoindate,dragonkills,level FROM " . DB::prefix("accounts") . " WHERE clanid={$claninfo['clanid']} ORDER BY clanrank DESC ,dragonkills DESC,level DESC,clanjoindate";
		$result = DB::query($sql);
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
		$stepdown = translate_inline("`\$Step down as founder");
		$remove = translate_inline("Remove From Clan");
		$confirm = translate_inline("Are you sure you wish to remove this member from your clan?");
		rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$lev</td><td>$dk</td><td>$jd</td><td>$lo</td>".($user->clanrank>CLAN_MEMBER?"<td>$ops</td>":"")."</tr>",true);
		$i=0;
		$tot = 0;
		require_once("lib/clan/func.php");
		while ($row=DB::fetch_assoc($result)){
			$i++;
			$tot += $row['dragonkills'];
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
			rawoutput("<td>");
			output_notl($ranks[$row['clanrank']]);
			rawoutput("</td><td>");
			$link = "bio.php?char=".$row['acctid']."&ret=".urlencode($_SERVER['REQUEST_URI']);
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
			if ($user->clanrank>CLAN_MEMBER){
				rawoutput("<td>");
				if ($row['clanrank']<$user->clanrank && $row['clanrank']<CLAN_FOUNDER){
					rawoutput("[ <a href='clan.php?op=membership&setrank=".clan_nextrank($ranks,$row['clanrank'])."&who=".rawurlencode($row['login'])."&whoname=".rawurlencode($row['name'])."&whoacctid=".$row['acctid']."'>$promote</a> | ");
					addnav("","clan.php?op=membership&setrank=".clan_nextrank($ranks,$row['clanrank'])."&who=".rawurlencode($row['login'])."&whoname=".rawurlencode($row['name'])."&whoacctid=".$row['acctid']);
				}else{
					output_notl("[ `)%s`0 | ", $promote);
				}
				if ($row['clanrank']<=$user->clanrank && $row['clanrank']>CLAN_APPLICANT && $row['login']!=$user->login && clan_previousrank($ranks,$row['clanrank']) > 0){
					rawoutput("<a href='clan.php?op=membership&setrank=".clan_previousrank($ranks,$row['clanrank'])."&whoacctid=".$row['acctid']."'>$demote</a> | ");
					addnav("","clan.php?op=membership&setrank=".clan_previousrank($ranks,$row['clanrank'])."&whoacctid=".$row['acctid']);
				}elseif ($row['clanrank']==CLAN_FOUNDER && $row['clanrank']>CLAN_APPLICANT && $row['login']==$user->login){
					output_notl("<a href='clan.php?op=membership&setrank=".clan_previousrank($ranks,$row['clanrank'])."&whoacctid=".$row['acctid']."'>$stepdown</a> | ",true);
					addnav("","clan.php?op=membership&setrank=".clan_previousrank($ranks,$row['clanrank'])."&whoacctid=".$row['acctid']);
				} else {
					output_notl("`)%s`0 | ", $demote);
				}
				if ($row['clanrank'] <= $user->clanrank && $row['login']!=$user->login){
					rawoutput("<a href='clan.php?op=membership&remove=".$row['acctid']."' onClick=\"return confirm('$confirm');\">$remove</a> ]");
					addnav("","clan.php?op=membership&remove=".$row['acctid']);
				}else{
					output_notl("`)%s`0 ]", $remove);
				}
				rawoutput("</td>");
			}
			rawoutput("</tr>");
		}
		rawoutput("</table>");
		output("`n`n`^This clan has a total of `\$%s`^ dragon kills.",$tot);
?>

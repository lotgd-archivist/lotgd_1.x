<?php
		modulehook("collapse{", array("name"=>"clanentry"));
		output("Having pressed the secret levers and turned the secret knobs on the lock of the door to your clan's hall, you gain entrance and chat with your clan mates.`n`n");
		modulehook("}collapse");

		$sql = "SELECT name FROM " . DB::prefix("accounts")  . " WHERE acctid={$claninfo['motdauthor']}";
		$result = DB::query($sql);
		$row = DB::fetch_assoc($result);
		$motdauthname = $row['name'];

		$sql = "SELECT name FROM " . DB::prefix("accounts") . " WHERE acctid={$claninfo['descauthor']}";
		$result = DB::query($sql);
		$row = DB::fetch_assoc($result);
		$descauthname = $row['name'];

		if ($claninfo['clanmotd'] != '') {
			rawoutput("<div style='margin-left: 15px; padding-left: 15px;'>");
			output("`&`bCurrent MoTD:`b `#by %s`2`n",$motdauthname);
			output_notl(nltoappon($claninfo['clanmotd'])."`n");
			rawoutput("</div>");
			output_notl("`n");
		}

		commentdisplay("", "clan-{$claninfo['clanid']}","Speak",25,($claninfo['customsay']>''?$claninfo['customsay']:"says"));

		modulehook("clanhall");

		if ($claninfo['clandesc'] != '') {
			modulehook("collapse{", array("name"=>"collapsedesc"));
			output("`n`n`&`bCurrent Description:`b `#by %s`2`n",$descauthname);
			output_notl(nltoappon($claninfo['clandesc']));
			modulehook("}collapse");
		}
		$sql = "SELECT count(*) AS c, clanrank FROM " . DB::prefix("accounts") . " WHERE clanid={$claninfo['clanid']} GROUP BY clanrank DESC";
		$result = DB::query($sql);
		// begin collapse
		modulehook("collapse{", array("name"=>"clanmemberdet"));
		output("`n`n`bMembership Details:`b`n");
		$leaders = 0;
		while ($row = DB::fetch_assoc($result)){
			output_notl($ranks[$row['clanrank']].": `0".$row['c']."`n");
			if ($row['clanrank']>CLAN_OFFICER) $leaders += $row['c'];
		}
		output("`n");
		$noleader = translate_inline("`^There is currently no leader!  Promoting %s`^ to leader as they are the highest ranking member (or oldest member in the event of a tie).`n`n");
		if ($leaders==0){
			//There's no leader here, probably because the leader's account
			//expired.
			$sql = "SELECT name,acctid,clanrank FROM " . DB::prefix("accounts") . " WHERE clanid={$user->clanid} AND clanrank > " . CLAN_APPLICANT . " ORDER BY clanrank DESC, clanjoindate";
			$result = DB::query($sql);
			if (DB::num_rows($result)) {
				$row = DB::fetch_assoc($result);
				$sql = "UPDATE " . DB::prefix("accounts") . " SET clanrank=".CLAN_LEADER." WHERE acctid={$row['acctid']}";
				DB::query($sql);
				output_notl($noleader,$row['name']);
				if ($row['acctid']==$user->acctid){
					//if it's the current user, we'll need to update their
					//session in order for the db write to take effect.
					$user->clanrank=CLAN_LEADER;
				}
			} else {
				// There are no viable leaders.  But we cannot disband the clan
				// here.
			}
		}
		// end collapse
		modulehook("}collapse");

		if ($user->clanrank>CLAN_MEMBER){
			addnav("Update MoTD / Clan Desc","clan.php?op=motd");
		}
		addnav("M?View Membership","clan.php?op=membership");
		addnav("Online Members","list.php?op=clan");
		addnav("Your Clan's Waiting Area","clan.php?op=waiting");
		addnav("Withdraw From Your Clan","clan.php?op=withdrawconfirm");
?>

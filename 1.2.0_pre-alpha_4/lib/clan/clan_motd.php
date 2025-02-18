<?php
		page_header("Update Clan Description / MoTD");
		addnav("Clan Options");
		if ($user->clanrank>=CLAN_OFFICER){
			$clanmotd = substr(httppost('clanmotd'),0,4096);
			if (httppostisset('clanmotd') &&
					stripslashes($clanmotd)!=$claninfo['clanmotd']){
				$sql = "UPDATE " . DB::prefix("clans") . " SET clanmotd='$clanmotd',motdauthor={$user->acctid} WHERE clanid={$claninfo['clanid']}";
				DB::query($sql);
				invalidatedatacache("clandata-{$claninfo['clanid']}");
				$claninfo['clanmotd']=stripslashes($clanmotd);
				output("Updating MoTD`n");
				$claninfo['motdauthor']=$user->acctid;
			}
			$clandesc = httppost('clandesc');
			if (httppostisset('clandesc') &&
					stripslashes($clandesc)!=$claninfo['clandesc'] &&
					$claninfo['descauthor']!=4294967295){
				$sql = "UPDATE " . DB::prefix("clans") . " SET clandesc='".addslashes(substr(stripslashes($clandesc),0,4096))."',descauthor={$user->acctid} WHERE clanid={$claninfo['clanid']}";
				DB::query($sql);
				invalidatedatacache("clandata-{$claninfo['clanid']}");
				output("Updating description`n");
				$claninfo['clandesc']=stripslashes($clandesc);
				$claninfo['descauthor']=$user->acctid;
			}
			$customsay = httppost('customsay');
			if (httppostisset('customsay') && $customsay!=$claninfo['customsay'] && $user->clanrank>CLAN_OFFICER){
				$sql = "UPDATE " . DB::prefix("clans") . " SET customsay='$customsay' WHERE clanid={$claninfo['clanid']}";
				DB::query($sql);
				invalidatedatacache("clandata-{$claninfo['clanid']}");
				output("Updating custom say line`n");
				$claninfo['customsay']=stripslashes($customsay);
			}
			$sql = "SELECT name FROM " . DB::prefix("accounts") . " WHERE acctid={$claninfo['motdauthor']}";
			$result = DB::query($sql);
			$row = DB::fetch_assoc($result);
			$motdauthname = $row['name'];

			$sql = "SELECT name FROM " . DB::prefix("accounts") . " WHERE acctid={$claninfo['descauthor']}";
			$result = DB::query($sql);
			$row = DB::fetch_assoc($result);
			$descauthname = $row['name'];

			output("`&`bCurrent MoTD:`b `#by %s`2`n",$motdauthname);
			output_notl(nltoappon($claninfo['clanmotd'])."`n");
			output("`&`bCurrent Description:`b `#by %s`2`n",$descauthname);
			output_notl(nltoappon($claninfo['clandesc'])."`n");

			rawoutput("<form action='clan.php?op=motd' method='POST'>");
			addnav("","clan.php?op=motd");
			output("`&`bMoTD:`b `7(4096 chars)`n");
			rawoutput("<textarea name='clanmotd' cols='50' rows='10' class='input' style='width: 66%'>".htmlentities($claninfo['clanmotd'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</textarea><br>");
			output("`n`&`bDescription:`b `7(4096 chars)`n");
			$blocked = translate_inline("Your clan has been blocked from posting a description.`n");
			if ($claninfo['descauthor']==INT_MAX){
				output_notl($blocked);
			}else{
				rawoutput("<textarea name='clandesc' cols='50' rows='10' class='input' style='width: 66%'>".htmlentities($claninfo['clandesc'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</textarea><br>");
			}
			if ($user->clanrank>CLAN_OFFICER){
				output("`n`&`bCustom Talk Line`b `7(blank means \"says\" -- 15 chars max)`n");
				rawoutput("<input name='customsay' value=\"".htmlentities($claninfo['customsay'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\" class='input' maxlength=\"15\"><br/>");
			}
			$save = translate_inline("Save");
			rawoutput("<input type='submit' class='button' value='$save'>");
			rawoutput("</form>");
		}else{
			output("You do not have authority to change your clan's motd or description.");
		}
		addnav("Return to your clan hall","clan.php");
?>

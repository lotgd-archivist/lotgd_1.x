<?php
		modulehook("clan-withdraw", array('clanid'=>$user->clanid, 'clanrank'=>$user->clanrank, 'acctid'=>$user->acctid));
		if ($user->clanrank>=CLAN_LEADER){
			//first test to see if we were the leader.
			$sql = "SELECT count(*) AS c FROM " . DB::prefix("accounts") . " WHERE clanid={$user->clanid} AND clanrank>=".CLAN_LEADER." AND acctid<>{$user->acctid}";
			$result = DB::query($sql);
			$row = DB::fetch_assoc($result);
			if ($row['c']==0){
				//we were the solitary leader.
				$sql = "SELECT name,acctid,clanrank FROM " . DB::prefix("accounts") . " WHERE clanid={$user->clanid} AND clanrank > " . CLAN_APPLICANT . " AND acctid<>{$user->acctid} ORDER BY clanrank DESC, clanjoindate LIMIT 1";
				$result = DB::query($sql);
				if ($row = DB::fetch_assoc($result)){
					//there is no alternate leader, let's promote the
					//highest ranking member (or oldest member in the
					//event of a tie).  This will capture even people
					//who applied for membership.
					$sql = "UPDATE " . DB::prefix("accounts") . " SET clanrank=".CLAN_LEADER." WHERE acctid={$row['acctid']}";
					DB::query($sql);
					output("`^Promoting %s`^ to leader as they are the highest ranking member (or oldest member in the event of a tie).`n`n",$row['name']);
				}else{
					//There are no other members, we need to delete the clan.
					modulehook("clan-delete", array("clanid"=>$user->clanid));
					$sql = "DELETE FROM " . DB::prefix("clans") . " WHERE clanid={$user->clanid}";
					DB::query($sql);
					//just in case we goofed, we don't want to have to worry
					//about people being associated with a deleted clan.
					$sql = "UPDATE " . DB::prefix("accounts") . " SET clanid=0,clanrank=".CLAN_APPLICANT.",clanjoindate='0000-00-00 00:00:00' WHERE clanid={$user->clanid}";
					DB::query($sql);
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
		$sql = "SELECT acctid FROM " . DB::prefix("accounts") . " WHERE clanid='{$user->clanid}' AND clanrank>=".CLAN_OFFICER." AND acctid<>'{$user->acctid}'";
		$result = DB::query($sql);
		$withdraw_subj = array("`\$Clan Withdraw: `&%s`0",$user->name);
		$msg = array("`^One of your clan members has resigned their membership.  `&%s`^ has surrendered their position within your clan!",$user->name);
		$sql = "DELETE FROM " . DB::prefix("mail") . " WHERE msgfrom=0 AND seen=0 AND subject='".serialize($withdraw_subj)."'";
		DB::query($sql);
		while ($row = DB::fetch_assoc($result)){
			systemmail($row['acctid'],$withdraw_subj,$msg);
		}

		$user->clanid=0;
		$user->clanrank=CLAN_APPLICANT;
		$user->clanjoindate="0000-00-00 00:00:00";
		output("`&You have withdrawn from your clan.");
		addnav("Clan Options");
		addnav("Return to the Lobby","clan.php");
?>

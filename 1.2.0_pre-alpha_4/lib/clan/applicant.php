<?php
page_header("Clan Halls");
$registrar=getsetting('clanregistrar','`%Karissa');
$gender = getsetting('clanregistrargender', SEX_FEMALE);
addnav("Clan Options");
output("`b`c`&Clan Halls`c`b");
if ($op=="apply"){
	require_once("lib/clan/applicant_apply.php");
}elseif ($op=="new"){
	require_once("lib/clan/applicant_new.php");
}else{
	output("`7You stand in the center of a great marble lobby filled with pillars.");
	output("All around the walls of the lobby are various doors which lead to various clan halls.");
	output("The doors each possess a variety of intricate mechanisms which are obviously elaborate locks designed to be opened only by those who have been educated on how to operate them.");
	output("Nearby, you watch another warrior glance about nervously to make sure no one is watching before touching various levers and knobs on the door.");
	output("With a large metallic \"Chunk\" the lock on the door disengages, and the door swings silently open, admitting the warrior before slamming shut.`n`n");
	output("In the center of the lobby sits a highly polished desk, behind which sits `%%s`7, the clan registrar.",$registrar);
	switch ($gender) {
		case SEX_MALE:
			output("He can take your filing for a new clan, or accept your application to an existing clan.`n`n");
			break;
		case SEX_FEMALE:
			output("She can take your filing for a new clan, or accept your application to an existing clan.`n`n");
			break;
	}
	modulehook("clan-enter");
	if ($op=="withdraw"){
		$user->clanid=0;
		$user->clanrank=CLAN_APPLICANT;
		$user->clanjoindate='0000-00-00 00:00:00';
		output("`7You tell `%%s`7 that you're no longer interested in joining %s.",$registrar, $claninfo['clanname']);
		switch ($gender) {
			case SEX_MALE:
				output("He reaches into his desk, withdraws your application, and tears it up.  \"`5You wouldn't have been happy there anyhow, I don't think,`7\" as he tosses the shreds in his trash can.");
				break;
			case SEX_FEMALE:
				output("She reaches into her desk, withdraws your application, and tears it up.  \"`5You wouldn't have been happy there anyhow, I don't think,`7\" as she tosses the shreds in her trash can.");
				break;
		}
		$claninfo = array();
		$sql = "DELETE FROM " . DB::prefix("mail") . " WHERE msgfrom=0 AND seen=0 AND subject='".serialize($apply_subj)."'";
		DB::query($sql);
		output("You are not a member of any clan.");
		addnav("Apply for Membership to a Clan","clan.php?op=apply");
		addnav("Apply for a New Clan","clan.php?op=new");
	}else{
		if (isset($claninfo['clanid']) && $claninfo["clanid"]>0){
			//applied for membership to a clan
			output("`7You approach `%%s`7 who smiles at you, but lets you know that your application to %s hasn't yet been accepted.",$registrar,$claninfo['clanname']);
			switch ($gender) {
				case SEX_MALE:
					output("Perhaps you'd like to take a seat in the waiting area, he suggests.");
					break;
				case SEX_FEMALE:
					output("Perhaps you'd like to take a seat in the waiting area, she suggests.");
					break;
			}
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
?>

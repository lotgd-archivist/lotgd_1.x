<?php
$apply = httpget('apply');
$gender = getsetting('clanregistrargender', SEX_FEMALE);
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
	$sql = "SELECT * FROM " . DB::prefix("clans") . " WHERE clanname='$clanname'";
	$result = DB::query($sql);
	switch ($gender) {
		case SEX_MALE:
			$e = array (translate_inline("%s`7 looks over your form but informs you that your clan name must consist only of letters, spaces, apostrophes, or dashes.  Also, your short name can consist only of letters. He hands you a blank form."),
				translate_inline("%s`7 looks over your form but informs you that you must have at least 5 and no more than 50 characters in your clan's name (and they must consist only of letters, spaces, apostrophes, or dashes), then hands you a blank form."),
				translate_inline("%s`7 looks over your form but informs you that you must have at least 2 and no more than 5 characters in your clan's short name (and they must all be letters), then hands you a blank form."),
				translate_inline("%s`7 looks over your form but informs you that the clan name %s is already taken, and hands you a blank form."),
				translate_inline("%s`7 looks over your form but informs you that the short name %s is already taken, and hands you a blank form."),
				translate_inline("%s`7 asks for the %s gold to start the clan, but you seem to be unable to produce the fees."),
				translate_inline("%s`7 asks for the %s gold and %s gems to start the clan, but you seem to be unable to produce the fees."),
				translate_inline("%s`7 asks for the %s gems to start the clan, but you seem to be unable to produce the fees."),
				translate_inline("He takes your application, and stamps it \"`\$DENIED`7\"."),
			);
			break;
		case SEX_FEMALE:
			$e = array (translate_inline("%s`7 looks over your form but informs you that your clan name must consist only of letters, spaces, apostrophes, or dashes.  Also, your short name can consist only of letters. She hands you a blank form."),
				translate_inline("%s`7 looks over your form but informs you that you must have at least 5 and no more than 50 characters in your clan's name (and they must consist only of letters, spaces, apostrophes, or dashes), then hands you a blank form."),
				translate_inline("%s`7 looks over your form but informs you that you must have at least 2 and no more than 5 characters in your clan's short name (and they must all be letters), then hands you a blank form."),
				translate_inline("%s`7 looks over your form but informs you that the clan name %s is already taken, and hands you a blank form."),
				translate_inline("%s`7 looks over your form but informs you that the short name %s is already taken, and hands you a blank form."),
				translate_inline("%s`7 asks for the %s gold to start the clan, but you seem to be unable to produce the fees."),
				translate_inline("%s`7 asks for the %s gold and %s gems to start the clan, but you seem to be unable to produce the fees."),
				translate_inline("%s`7 asks for the %s gems to start the clan, but you seem to be unable to produce the fees."),
				translate_inline("She takes your application, and stamps it \"`\$DENIED`7\"."),
			);
			break;
	}
	if ($clanname!=$ocn || $clanshort!=$ocs){
		output_notl($e[0],$registrar);
		clanform();
		addnav("Return to the Lobby","clan.php");
	}elseif (strlen($clanname)<5 || strlen($clanname)>50){
		output_notl($e[1],$registrar);
		clanform();
		addnav("Return to the Lobby","clan.php");
	}elseif (strlen($clanshort)<2 || strlen($clanshort)>5){
		output_notl($e[2],$registrar);
		clanform();
		addnav("Return to the Lobby","clan.php");
	}elseif (DB::num_rows($result)>0){
		output_notl($e[3],$registrar,stripslashes($clanname));
		clanform();
		addnav("Return to the Lobby","clan.php");
	}else{
		$sql = "SELECT * FROM " . DB::prefix("clans") . " WHERE clanshort='$clanshort'";
		$result = DB::query($sql);
		if (DB::num_rows($result)>0){
			output_notl($e[4],$registrar,stripslashes($clanshort));
			clanform();
			addnav("Return to the Lobby","clan.php");
		}else{
			if ($user->gold<$gold || $user->gems<$gems){
				if ($gold>0 && $gems<=0) output_notl($e[5],$registrar,$gold);
				elseif ($gems>0 && $gold<=0) output_notl($e[7],$registrar,$gems);
				else output_notl($e[6],$registrar,$gold,$gems);
				output_notl($e[8],$registrar);
				addnav("Return to the Lobby","clan.php");
			}else{
				$args = array("ocn"=>$ocn, "ocs"=>$ocs, "clanname"=>$clanname, "clanshort"=>$clanshort);
				$args = modulehook("process-createclan", $args);
				if (isset($args['blocked']) && $args['blocked']){
					output_notl(sprintf_translate($args['blockmsg']));
					clanform();
					addnav("Return to the Lobby","clan.php");
				} else {
					$sql = "INSERT INTO " . DB::prefix("clans") . " (clanname,clanshort) VALUES ('$clanname','$clanshort')";
					DB::query($sql);
					$id = DB::insert_id();
					$user->clanid = $id;
					$user->clanrank=CLAN_LEADER+1; //+1 because he is the founder
					$user->clanjoindate=date("Y-m-d H:i:s");
					$user->gold-=$gold;
					$user->gems-=$gems;
					debuglog("has started a new clan (<$clanshort> $clanname) for $gold gold and $gems gems.");
					switch ($gender) {
						case SEX_MALE:
							output("%s`7 looks over your form, and finding that everything seems to be in order, he takes your fees, stamps the form \"`\$APPROVED`7\" and files it in a drawer.`n`n",$registrar);
							break;
						case SEX_FEMALE:
							output("%s`7 looks over your form, and finding that everything seems to be in order, she takes your fees, stamps the form \"`\$APPROVED`7\" and files it in a drawer.`n`n",$registrar);
							break;
					}
					output("Congratulations, you've created a new clan named %s!",stripslashes($clanname));
					addnav("Enter your clan hall","clan.php");
				}
			}
		}
	}
}else{
	output("`7You approach %s`7 and inquire about starting a new clan.",$registrar);
	switch ($gender) {
		case SEX_MALE:
			output("He tells you that there are three requirements to starting a clan.");
			break;
		case SEX_FEMALE:
			output("She tells you that there are three requirements to starting a clan.");
			break;
	}
	output("First, you have to decide on a full name for your clan.");
	output("Second, you have to decide on an abbreviation for your clan.");
	output("Third you have to decide on whether or not you're willing to give up the fees that are required to start the clan.");
	output("This fee is used to tailor the locks on your clan door to you and your members.`n");
	output("The fees are as follows:`nGold: `^%s`7`nGems: `%%s`7",$gold,$gems);
	addnav("Return to the Lobby","clan.php");
	$e1 = translate_inline("`n`n\"`5Since you do not have enough gold with you, I cannot allow you to apply for a clan,`7\" she says.");
	$e2 = translate_inline("`n`n\"`5Since you do not have enough gems with you, I cannot allow you to apply for a clan,`7\" she says.");
	switch ($gender) {
		case SEX_MALE:
			$e3 = translate_inline("`n`n\"`5If you're ok with these three requirements, please fill out the following form,`7\" he says, handing you a sheet of paper.");
			break;
		case SEX_FEMALE:
			$e3 = translate_inline("`n`n\"`5If you're ok with these three requirements, please fill out the following form,`7\" she says, handing you a sheet of paper.");
			break;
	}
	if ($user->gold<$gold){
		output_notl($e1);
	}elseif ($user->gems<$gems){
		output_notl($e2,$registrar);
	}else{
		output_notl($e3,$registrar);
		clanform();
	}
}
?>

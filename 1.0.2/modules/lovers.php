<?php
//addnews ready
// translator ready
// mail ready
require_once("lib/buffs.php");
//should we move charm here?
//should we move marriedto here?

function lovers_getmoduleinfo(){
	$info = array(
		"name"=>"Violet and Seth Lovers",
		"author"=>"Eric Stevens",
		"version"=>"1.0",
		"category"=>"Inn",
		"download"=>"core_module",
		"prefs"=>array(
			"Lover Module User Preferences,title",
			"seenlover"=>"Visited Lover Today?,bool|0"
		)
	);
	return $info;
}

function lovers_install(){
	module_addhook("newday");
	module_addhook("inn");
	
	$sql = "DESCRIBE " . db_prefix("accounts");
	$result = db_query($sql);
	while ($row = db_fetch_assoc($result)){
		if ($row['Field']=="seenlover"){
			$sql = "SELECT seenlover,acctid FROM " . db_prefix("accounts") . " WHERE seenlover>0";
			$result1 = db_query($sql);
			debug("Migrating seenlover.`n");
			while ($row1 = db_fetch_assoc($result1)){
				$sql = "INSERT INTO " . db_prefix("module_userprefs") . " (modulename,setting,userid,value) VALUES ('lovers','seenlover',{$row1['acctid']},{$row1['seenlover']})";
				db_query($sql);
			}//end while
			debug("Dropping seenlover column from the user table.`n");
			$sql = "ALTER TABLE " . db_prefix("accounts") . " DROP seenlover";
			db_query($sql);
			//drop it from the user's session too.
			unset($session['user']['seenlover']);
		}//end if
	}//end while
	return true;
}

function lovers_uninstall(){
	return true;
}

function lovers_dohook($hookname, $args){
	global $session;
	switch($hookname){
	case "newday":
		set_module_pref("seenlover",0);
		if ($session['user']['marriedto'] == 4294967295){
			output("`n`%You're  married,  so there's no reason to keep up that perfect image, and you let yourself go a little today.`n");
			$dk = $session['user']['dragonkills'];
			
			
			// 0.7 seemed to be a perfect balance of no loss of charm.
			// 1.0 was too much.
			$dk = max(1, round(.85 * sqrt($dk), 0));
			$session['user']['charm'] -= e_rand(1,$dk);
			if ($session['user']['charm']<=0){
				output("`bWhen  you  wake  up, you find a note next to you, reading`n`5Dear %s`5,`n",$session['user']['name']);
				output("Despite  many  great  kisses, I find that I'm simply no longer attracted to you the way I used to be.`n`n");
				output("Call  me fickle, call me flakey, but I need to move on.");
				output("There are other warriors in the land, and I think some of them are really hot.");
				output("So it's not you, it's me, etcetera etcetera.`n`n");
				output("No hard feelings, Love,`n%s`b`n",$session['user']['sex']?"Seth":"Violet");
				addnews("`\$%s`\$ has left %s`\$ to pursue \"other interests.\"`0",($session['user']['sex']?"`^Seth":"`%Violet"), $session['user']['name']);
				$session['user']['marriedto']=0;
				$session['user']['charm']=0;
			}
		}
		break;
	case "inn":
		addnav("Things to do");
		if ($session['user']['sex']==SEX_MALE){
			addnav("F?Flirt with Violet","runmodule.php?module=lovers&op=flirt");
			addnav("Chat with Seth","runmodule.php?module=lovers&op=chat");
		}else{
			addnav("F?Flirt with Seth","runmodule.php?module=lovers&op=flirt");
			addnav("Gossip with Violet","runmodule.php?module=lovers&op=chat");
		}
		break;
	}
	return $args;
}

function lovers_run(){
	global $session;
	require_once("lib/villagenav.php");
	$iname = getsetting("innname", LOCATION_INN);
	page_header($iname);
	rawoutput("<span style='color: #9900FF'>");
	output_notl("`c`b");
	output($iname);
	output_notl("`b`c");
	switch(httpget('op')){
	case "flirt":
		if ($session['user']['sex']==SEX_MALE) 
			lovers_violet();
		else
			lovers_seth();
		break;
	case "chat":
		if ($session['user']['sex']==SEX_MALE)
			lovers_chat_seth();
		else
			lovers_chat_violet();
		break;
	}
	addnav("Return");
	addnav("I?Return to the Inn","inn.php");
	villagenav();
	rawoutput("</span>");
	page_footer();
}

function lovers_chat_seth(){
	global $session;
	if (httpget("act")==""){
		output("You make your way over to where Seth is sitting, ale in hand.");
		output("Sitting down, and waiting for Seth to finish a song, you light your pipe.");
		addnav("Ask about your manliness","runmodule.php?module=lovers&op=chat&act=armor");
		addnav("Discuss Sports","runmodule.php?module=lovers&op=chat&act=sports");
	}elseif(httpget("act")=="sports"){
		output("You and Seth spend some time talking about the recent dwarf tossing competition.");
		output("Not wanting to linger around another man for too long, so no one \"wonders,\" you decide you should find something else to do.");
	}else{
		$charm = $session['user']['charm']+e_rand(-1,1);
		output("Seth looks you up and down very seriously.");
		output("Only a friend can be truly honest, and that is why you asked him.");
		switch($charm){
			case -3: case -2: case -1: case 0:
				$msg = translate_inline("You make me glad I'm not gay!");
				break;
			case 1: case 2: case 3:
				$msg = translate_inline("I've seen some handsome men in my day, but I'm afraid you aren't one of them.");
				break;
			case 4: case 5: case 6:
				$msg = translate_inline("I've seen worse my friend, but only trailing a horse.");
				break;
			case 7: case 8: case 9:
				$msg = translate_inline("You're of fairly average appearance my friend.");
				break;
			case 10: case 11: case 12:
				$msg = translate_inline("You certainly are something to look at, just don't get too big of a head about it, eh?");
				break;
			case 13: case 14: case 15:
				$msg = translate_inline("You're quite a bit better than average!");
				break;
			case 16: case 17: case 18:
				$msg = translate_inline("Few women would be able to resist you!");
				break;
			default:
				$msg = translate_inline("I hate you, why, you are simply the most handsome man ever!");
		}
		output("Finally he reaches a conclusion and states, \"`%%s`0\"", $msg);
	}
}

function lovers_chat_violet(){
	global $session;
	if (httpget('act')==""){
		addnav("Gossip","runmodule.php?module=lovers&op=chat&act=gossip");
		addnav(array("Ask if your %s makes you look fat", $session['user']['armor']),"runmodule.php?module=lovers&op=chat&act=fat");
		output("You go over to `5Violet`0 and help her with the drinks she is carrying.");
		output("Once they are passed out, she takes a cloth and wipes the sweat off of her brow, thanking you much.");
		output("Of course you didn't mind, as she is one of your oldest and truest friends!");
	}else if(httpget('act')=="gossip"){
		output("You and `5Violet`0 gossip quietly for a few minutes about not much at all.");
		output("She offers you a pickle.");
		output("You accept, knowing that it's in her nature to do so as a former pickle wench.");
		output("After a few minutes, Cedrik begins to cast burning looks your way, and you decide you had best let Violet get back to work.");
	}else if(httpget('act')=="fat"){
		$charm = $session['user']['charm']+e_rand(-1,1);
		output("Violet looks you up and down very seriously.");
		output("Only a friend can be truly honest, and that is why you asked her.");
		switch($charm){
			case -3: case -2: case -1: case 0:
				$msg = translate_inline("Your outfit doesn't leave much to the imagination, but some things are best not thought about at all!  Get some less revealing clothes as a public service!");
				break;
			case 1: case 2: case 3:
				$msg = translate_inline("I've seen some lovely ladies in my day, but I'm afraid you aren't one of them.");
				break;
			case 4: case 5: case 6:
				$msg = translate_inline("I've seen worse my friend, but only trailing a horse.");
				break;
			case 7: case 8: case 9:
				$msg = translate_inline("You're of fairly average appearance my friend.");
				break;
			case 10: case 11: case 12:
				$msg = translate_inline("You certainly are something to look at, just don't get too big of a head about it, eh?");
				break;
			case 13: case 14: case 15:
				$msg = translate_inline("You're quite a bit better than average!");
				break;
			case 16: case 17: case 18:
				$msg = translate_inline("Few women could count themselves to be in competition with you!");
				break;
			default:
				$msg = translate_inline("I hate you, why, you are simply the most beautiful woman ever!");
		}
		output("Finally she reaches a conclusion and states, \"`%%s`0\"", $msg);
	}
}

function lovers_seth(){
	global $session;
	$seenlover = get_module_pref("seenlover");
	
	if ($seenlover==0){
		//haven't seen lover
		if ($session['user']['marriedto']==INT_MAX){
			//married
			$seenlover=1;
			if (e_rand(1,4)==1){
				switch(e_rand(1,4)){
				case 1:
					$msg = translate_inline("being too busy tuning his lute,");
					break;
				case 2:
					$msg = translate_inline("\"that time of month,\"");
					break;
				case 3:
					$msg = translate_inline("\"a little cold...  *cough cough* see?\"");
					break;
				case 4:
					$msg = translate_inline("wanting you to fetch him a beer,");
					break;
				}
				output("You head over to snuggle up to Seth and kiss him about the face and neck, but he grumbles something about %s and with a comment like that, you storm away from him!", $msg);
				$session['user']['charm']--;
				output("`n`n`^You LOSE a charm point!");
			}else{
				output("You and Seth take some time to yourselves, and you leave the inn, positively glowing!");
				apply_buff('lover',lovers_getbuff());
				$session['user']['charm']++;
				output("`n`n`^You gain a charm point!");
			}
		}else{
			//not married.
			if (httpget("flirt")==""){
				//haven't flirted yet
				addnav("Flirt");
				addnav("Wink","runmodule.php?module=lovers&op=flirt&flirt=1");
				addnav("Flutter Eyelashes","runmodule.php?module=lovers&op=flirt&flirt=2");
				addnav("Drop Hanky","runmodule.php?module=lovers&op=flirt&flirt=3");
				addnav("Ask him to buy you a drink","runmodule.php?module=lovers&op=flirt&flirt=4");
				addnav("Kiss him soundly","runmodule.php?module=lovers&op=flirt&flirt=5");
				addnav("Completely seduce the bard","runmodule.php?module=lovers&op=flirt&flirt=6");
				addnav("Marry him","runmodule.php?module=lovers&op=flirt&flirt=7");
			}else{
				//flirting now
				$c = $session['user']['charm'];
				$seenlover=1;
				switch(httpget('flirt')){
				case 1:
					if (e_rand($c,2)>=2){
						output("Seth grins a big toothy grin.");
						output("My, isn't the dimple in his chin cute??");
						if ($c<4) $c++;
					}else{
						output("Seth raises an eyebrow at you, and asks if you have something in your eye.");
					}
					break;
				case 2:
					if (e_rand($c,4)>=4){
						output("Seth smiles at you and says, \"`^My, what pretty eyes you have`0\"");
						if ($c<7) $c++;
					}else{
						output("Seth smiles, and waves... to the person standing behind you.");
					}
					break;
				case 3:
					if (e_rand($c,7)>=7){
						output("Seth bends over and retrieves your hanky, while you admire his firm posterior.");
						if ($c<11) $c++;
					}else{
						output("Seth bends over and retrieves your hanky, wipes his nose with it, and gives it back.");
					}
					break;
				case 4:
					if (e_rand($c,11)>=11){
						output("Seth places his arm around your waist, and escorts you to the bar where he buys you one of the Inn's fine swills.");
						if ($c<14) $c++;
					}else{
						output("Seth apologizes, \"`^I'm sorry m'lady, I have no money to spare,`0\" as he turns out his moth-riddled pocket.");
						if ($c>0 && $c<10) $c--;
					}
					break;
				case 5:
					if (e_rand($c,14)>=14){
						output("You walk up to Seth, grab him by the shirt, pull him to his feet, and plant a firm, long kiss right on his handsome lips.");
						output("He collapses after, hair a bit disheveled, and short on breath.");
						if ($c<18) $c++;
					}else{
						output("You duck down to kiss Seth on the lips, but just as you do so, he bends over to tie his shoe.");
						if ($c>0 && $c<13) $c--;
					} 
					break;
				case 6:
					if (e_rand($c,18)>=18){
						output("Standing at the base of the stairs, you make a come-hither gesture at Seth.");
						output("He follows you like a puppydog.");
						if ($session['user']['turns']>0){
							output("You feel exhausted!");
							$session['user']['turns']-=2;
							if ($session['user']['turns']<0)
								$session['user']['turns']=0;
						}
						addnews("`@%s`@ and `^Seth`@ were seen heading up the stairs in the inn together.`0",$session['user']['name']);
						if ($c<25) $c++;
					}else{
						output("\"`^I'm sorry m'lady, but I have a show in 5 minutes`0\"");
						if ($c>0) $c--;
					}
					break;
				case 7:
					output("Walking up to Seth, you simply demand that he marry you.`n`n");
					output("He looks at you for a few seconds.`n`n");
					if ($c>=22){
						output("\"`^Of course my love!`0\" he says.");
						output("The next weeks are a blur as you plan the most wonderous wedding, paid for entirely by Seth, and head on off to the deep forest for your honeymoon.");
						addnews("`&%s and `^Seth`& are joined today in joyous matrimony!!!",$session['user']['name']);
						$session['user']['marriedto']=INT_MAX;
						apply_buff('lover',lovers_getbuff());
					}else{
						output("Seth says, \"`^I'm sorry, apparently I've given you the wrong impression, I think we should just be friends.`0\"");
						output("Depressed, you have no more desire to fight in the forest today.");
						$session['user']['turns']=0;
						debuglog("lost all turns after being rejected for marriage.");
					}
					break;
				}//end switch
				if ($c > $session['user']['charm'])
					output("`n`n`^You gain a charm point!");
				if ($c < $session['user']['charm'])
					output("`n`n`\$You LOSE a charm point!");
				$session['user']['charm']=$c;
			}//end if
		}//end if
	}else{
		//have seen lover
		output("You think you had better not push your luck with Seth today.");
	}
	set_module_pref("seenlover",$seenlover);
}

function lovers_violet(){
	global $session;
	$seenlover = get_module_pref("seenlover");
	
	if ($seenlover==0){
		if ($session['user']['marriedto']==INT_MAX){
			if (e_rand(1, 4)==1){
				switch(e_rand(1,4)){
				case 1:
					$msg = translate_inline("being too busy serving these pigs,");
					break;
				case 2:
					$msg = translate_inline("\"that time of month,\"");
					break;
				case 3:
					$msg = translate_inline("\"a little cold...  *cough cough* see?\"");
					break;
				case 4:
					$msg = translate_inline("men all being pigs,");
					break;
				}
				output("You head over to cuddle Violet and kiss her about the face and neck, but she grumbles something about %s and with a comment like that, you storm away from her!`n`n", $msg);
				$session['user']['charm']--;
				output("`^You LOSE a charm point!");
			}else{
				output("You and `5Violet`0 take some time to yourselves, and you leave the inn, positively glowing!");
				apply_buff('lover',lovers_getbuff());
				$session['user']['charm']++;
				output("`n`n`^You gain a charm point!");
			}
			$seenlover = 1;
		}elseif (httpget('flirt')==""){
			output("You stare dreamily across the room at `5Violet`0, who leans across a table to serve a patron a drink.");
			output("In doing so, she shows perhaps a bit more skin than is necessary, but you don't feel the need to object.");
			addnav("Flirt");
			addnav("Wink","runmodule.php?module=lovers&op=flirt&flirt=1");
			addnav("Kiss her hand","runmodule.php?module=lovers&op=flirt&flirt=2");
			addnav("Peck her on the lips","runmodule.php?module=lovers&op=flirt&flirt=3");
			addnav("Sit her on your lap","runmodule.php?module=lovers&op=flirt&flirt=4");
			addnav("Grab her backside","runmodule.php?module=lovers&op=flirt&flirt=5");
			addnav("Carry her upstairs","runmodule.php?module=lovers&op=flirt&flirt=6");
			addnav("Marry her","runmodule.php?module=lovers&op=flirt&flirt=7");
		}else{
			$c = $session['user']['charm'];
			$seenlover = 1;
			switch(httpget('flirt')){
				case 1:
					if (e_rand($c,2)>=2){
						output("You wink at `5Violet`0, and she gives you a warm smile in return.");
						if ($c<4) $c++;
					}else{
						output("You wink at `5Violet`0, but she pretends not to notice.");
					}
					break;
				case 2:
					output("You stroll confidently across the room toward `5Violet`0.");
					if (e_rand($c,4)>=4){
						output("Taking hold of her hand, you kiss it gently, your lips remaining for only a few seconds.");
						output("`5Violet`0 blushes and tucks a strand of hair behind her ear as you walk away, then presses the back side of her hand longingly against her cheek while watching your retreat.");
						if ($c<7) $c++;
					}else{
						output("You reach out to grab her hand, but `5Violet`0 takes her hand back and asks if perhaps you'd like a drink.");
					}
					break;
				case 3:
					output("Standing with your back against a wooden column, you wait for `5Violet`0 to wander your way when you call her name.");
					if (e_rand($c,7)>=7){
						output("She approaches, a hint of a smile on her face.");
						output("You grab her chin, lift it slightly, and place a firm but quick kiss on her plump lips.");
						if ($c<11) $c++;
					}else{
						output("She smiles and apologizes, insisting that she is simply too busy to take a moment from her work.");
					}
					break;
				case 4:
					output("Sitting at a table, you wait for `5Violet`0 to come your way.");
					if (e_rand($c,11)>=11){
						output("When she does so, you reach up and grab her firmly by the waist, pulling her down on to your lap.");
						output("She laughs and throws her arms around your neck in a warm hug before thumping you on the chest, standing up, and insisting that she really must get back to work.");
						if ($c<14) $c++;
					}else{
						output("When she does so, you reach up to grab her by the waist, but she deftly dodges, careful not to spill the drink that she's carrying.");
						if ($c>0 && $c<10) $c--;
					}
					break;
				case 5:
					output("Waiting for `5Violet`0 to brush by you, you firmly palm her backside.");
					if (e_rand($c,14)>=14){
						output("She turns and gives you a warm, knowing smile.");
						if ($c<18) $c++;
					}else{
						output("She turns and slaps you across the face. Hard.");
						output("Perhaps you should go a little slower.");
						if ($c>0 && $c<13) $c--;
					} 
					break;
				case 6:
					if (e_rand($c,18)>=18){
						output("Like a whirlwind, you sweep through the inn, grabbing `5Violet`0, who throws her arms around your neck, and whisk her upstairs to her room there.");
						output("Not more than 10 minutes later you stroll down the stairs, smoking a pipe, and grinning from ear to ear.");
						if ($session['user']['turns']>0){
							output("You feel exhausted!  ");
							$session['user']['turns']-=2;
							if ($session['user']['turns']<0) $session['user']['turns']=0;
						}
						addnews("`@%s`@ and `5Violet`@ were seen heading up the stairs in the inn together.`0",$session['user']['name']);
						if ($c<25) $c++;
					}else{
						output("Like a whirlwind, you sweep through the inn, and grab for `5Violet`0.");
						output("She turns and slaps your face!");
						output("\"`%What sort of girl do you think I am, anyhow?`0\" she demands! ");
						if ($c>0) $c--;
					}
					break;
				case 7:
					output("`5Violet`0 is working feverishly to serve patrons of the inn.");
					output("You stroll up to her and take the mugs out of her hand, placing them on a nearby table.");
					output("Amidst her protests you kneel down on one knee, taking her hand in yours.");
					output("She quiets as you stare up at her and utter the question that you never thought you'd utter.");
					output("She stares at you and you immediately know the answer by the look on her face.`n`n");
					if ($c>=22){
						output("It is a look of exceeding happiness.");
						output("\"`%Yes!`0\" she says, \"`%Yes, yes yes!!!`0\"");
						output("Her final confirmations are buried in a flurry of kisses about your face and neck.`n`n");
						output("The next days are a blur; you and `5Violet`0 are married in the abbey down the street, in a gorgeous ceremony with many frilly girly things.");
						addnews("`&%s and `%Violet`& are joined today in joyous matrimony!!!",$session['user']['name']);
						$session['user']['marriedto']=INT_MAX;
						apply_buff('lover',lovers_getbuff());
					}else{
						output("It is a look of sadness.");
						output("\"`%No`0,\" she says, \"`%I'm not yet ready to settle down`0.\"`n`n");
						output("Disheartened, you no longer possess the will to pursue any more forest adventures today.");
						$session['user']['turns']=0;
						debuglog("lost all turns after being rejected for marriage.");
					}
			}
			if ($c > $session['user']['charm'])
				output("`n`n`^You gain a charm point!");
			if ($c < $session['user']['charm'])
				output("`n`n`\$You LOSE a charm point!");
			$session['user']['charm']=$c;
		}
	}else{
		output("You think you had better not push your luck with `5Violet`0 today.");
	}
	set_module_pref("seenlover",$seenlover);
}

function lovers_getbuff(){
	global $session;
	$buff = array(
		"name"=>"`!Lover's Protection",
		"rounds"=>60,
		"wearoff"=>
		"`!You miss ".($session['user']['sex']==SEX_FEMALE?"Seth":"`5Violet`!").".`0",
		"defmod"=>1.2,
		"roundmsg"=>"Your lover inspires you to keep safe!",
		"schema"=>"module-lovers",
	);
	return $buff;
}
?>

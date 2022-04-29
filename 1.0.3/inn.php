<?php
// addnews ready
// translator ready
// mail ready
require_once("common.php");
require_once("lib/commentary.php");
require_once("lib/pvpwarning.php");
require_once("lib/sanitize.php");
require_once("lib/pvplist.php");
require_once("lib/http.php");
require_once("lib/buffs.php");
require_once("lib/events.php");
require_once("lib/villagenav.php");

tlschema("inn");

addcommentary();
$iname = getsetting("innname", LOCATION_INN);
$vname = getsetting("villagename", LOCATION_FIELDS);

$op = httpget('op');
// Correctly reset the location if they fleeing the dragon
// This needs to be done up here because a special could alter your op.
if ($op == "fleedragon") {
	$session['user']['location'] = $vname;
}

page_header($iname);
$skipinndesc = handle_event("inn");

if (!$skipinndesc) {
	checkday();
	rawoutput("<span style='color: #9900FF'>");
	output_notl("`c`b");
	output($iname);
	output_notl("`b`c");
}

$subop = httpget('subop');

$com = httpget('comscroll');
$comment = httppost('insertcommentary');

if ($op=="" || $op=="strolldown" || $op=="fleedragon"){
	if ($com=="" && !$comment && $op!="fleedragon") {
		if (module_events("inn", getsetting("innchance", 0)) != 0) {
			if (checknavs()) {
				page_footer();
			} else {
				$skipinndesc = true;
				$session['user']['specialinc'] = "";
				$session['user']['specialmisc'] = "";
				$op = "";
				httpset("op", "");
			}
		}
	}

	addnav("Things to do");
	addnav("Converse with patrons","inn.php?op=converse");
	addnav("B?Talk to Cedrik the Barkeep","inn.php?op=bartender");

	addnav("Other");
	addnav("Get a room (log out)","inn.php?op=room");
	villagenav();

	if (!$skipinndesc) {
		if ($op=="strolldown"){
			output("You stroll down the stairs of the inn, once again ready for adventure!`n");
		} elseif ($op=="fleedragon") {
			output("You pelt into the inn as if the Devil himself is at your heels.  Slowly you catch your breath and look around.`n");
			if ($session['user']['sex']) {
				output("`^Seth`0 catches your eye and then looks away in disgust at your cowardice!`n`n");
			} else {
				output("`%Violet`0 catches your eye and then looks away in disgust at your cowardice!`n`n");
			}
			output("You `\$lose`0 a charm point.`n`n");
			if ($session['user']['charm'] > 0) $session['user']['charm']--;
		} else {
			output("You duck into a dim tavern that you know well.");
			output("The pungent aroma of pipe tobacco fills the air.`n");
		}

		output("You wave to several patrons that you know.");
		if ($session['user']['sex']) {
			output("You give a special wave and wink to `^Seth`0 who is tuning his harp by the fire.");
		} else {
			output("You give a special wave and wink to `%Violet `0 who is serving drinks to some locals.");
		}
		output("Cedrik the innkeep stands behind his counter, chatting with someone.");

		$chats = array(
			translate_inline("dragons"),
			translate_inline("Seth"),
			translate_inline("Violet"),
			translate_inline("MightyE"),
			translate_inline("fine drinks"),
		);
		$chats = modulehook("innchatter", $chats);
		$talk = $chats[e_rand(0, count($chats)-1)];
		output("You can't quite make out what he is saying, but it's something about %s`0.`n`n", $talk);
		output("The clock on the mantle reads `6%s`0.`n", getgametime());
		modulehook("inn-desc", array());
	}
	modulehook("inn", array());
	module_display_events("inn", "inn.php");
}else{
	switch($op){
	case "converse":
		commentdisplay("You stroll over to a table, place your foot up on the bench and listen in on the conversation:`n", "inn","Add to the conversation?",20);
		break;
	case "bartender":
		$act = httpget('act');
		if ($act==""){
			output("Cedrik looks at you sort-of sideways like.");
			output("He never was the sort who would trust a man any farther than he could throw them, which gave dwarves a decided advantage, except in provinces where dwarf tossing was made illegal.");
			output("Cedrik polishes a glass, holds it up to the light of the door as another patron opens it to stagger out into the street.");
			output("He then makes a face, spits on the glass and goes back to polishing it.");
			output("\"`%What d'ya want?`0\" he asks gruffly.");
			addnav("Cedrik");
			addnav("Bribe","inn.php?op=bartender&act=bribe");
			addnav("Drinks");
			modulehook("ale", array());
		}elseif ($act=="bribe"){
			$g1 = $session['user']['level']*10;
			$g2 = $session['user']['level']*50;
			$g3 = $session['user']['level']*100;
			$type = httpget('type');
			if ($type==""){
				output("While you know that you won't always get what you want, sometimes the way to a man's information is through your purse.");
				output("It's also always been said that more is better.`n`n");

				output("How much would you like to offer him?");
				addnav("1 gem","inn.php?op=bartender&act=bribe&type=gem&amt=1");
				addnav("2 gems","inn.php?op=bartender&act=bribe&type=gem&amt=2");
				addnav("3 gems","inn.php?op=bartender&act=bribe&type=gem&amt=3");
				addnav(array("%s gold", $g1),"inn.php?op=bartender&act=bribe&type=gold&amt=$g1");
				addnav(array("%s gold", $g2),"inn.php?op=bartender&act=bribe&type=gold&amt=$g2");
				addnav(array("%s gold", $g3),"inn.php?op=bartender&act=bribe&type=gold&amt=$g3");
			}else{
				$amt = httpget('amt');
				if ($type=="gem"){
					if ($session['user']['gems']<$amt){
						$try=false;
						output("You don't have %s gems!", $amt);
					}else{
						$chance = $amt*30;
						$session['user']['gems']-=$amt;
						debuglog("spent $amt gems on bribing Cedrik");
						$try=true;
					}
				}else{
					if ($session['user']['gold']<$amt){
						output("You don't have %s gold!", $amt);
						$try=false;
					}else{
						$try=true;
						$sfactor = 50/90;
						$fact = $amt/$session['user']['level'];
						$chance = ($fact - 10)*$sfactor + 25;

						$session['user']['gold']-=$amt;
						debuglog("spent $amt gold bribing Cedrik");
					}
				}
				if ($try){
					if (e_rand(0,100)<$chance){
						output("Cedrik leans over the counter toward you.  \"`%What can I do for you, kid?`0\" he asks.");
						if (getsetting("pvp",1)) {
							addnav("Who's upstairs?","inn.php?op=bartender&act=listupstairs");
						}
						addnav("Tell me about colors","inn.php?op=bartender&act=colors");
						addnav("Switch specialty","inn.php?op=bartender&act=specialty");
					}else{
						output("Cedrik begins to wipe down the counter top, an act that really needed doing a long time ago.");
						if ($type == "gem") {
							if ($amt == 1) {
								output("When he's finished, your gem is gone.");
							} else{
								output("When he's finished, your gems are gone.");
							}
						} else {
							output("When he's finished, your gold is gone.");
						}
						output("You inquire about the loss, and he stares blankly back at you.");
					}
				}else{
					output("`n`nCedrik stands there staring at you blankly.");
					addnav("B?Talk to Cedrik the Barkeep","inn.php?op=bartender");
				}
			}
		}else if ($act=="listupstairs"){
			addnav("Refresh the list","inn.php?op=bartender&act=listupstairs");
			output("Cedrik lays out a set of keys on the counter top, and tells you which key opens whose room.  The choice is yours, you may sneak in and attack any one of them.");
			pvplist($iname,"pvp.php", "?act=attack&inn=1");
		}else if($act=="colors"){
			output("Cedrik leans on the bar.  \"`%So you want to know about colors, do you?`0\" he asks.");
			output("You are about to answer when you realize the question was posed in the rhetoric.");
			output("Cedrik continues, \"`%To do colors, here's what you need to do.  First, you use a &#0096; mark (found right above the tab key) followed by 1, 2, 3, 4, 5, 6, 7, !, @, #, $, %, ^, &.", true);
			output("Each of those corresponds with a color to look like this:");
			output_notl("`n`1&#0096;1 `2&#0096;2 `3&#0096;3 `4&#0096;4 `5&#0096;5 `6&#0096;6 `7&#0096;7 ",true);
			output_notl("`n`!&#0096;! `@&#0096;@ `#&#0096;# `\$&#0096;\$ `%&#0096;% `^&#0096;^ `&&#0096;& `n",true);
			output("`% Got it?`0\"  You can practice below:");
			rawoutput("<form action=\"$REQUEST_URI\" method='POST'>",true);
			$testtext = httppost('testtext');
			output("You entered %s`n", prevent_colors(HTMLEntities($testtext)), true);
			output("It looks like %s`n", $testtext);
			$try = translate_inline("Try");
			rawoutput("<input name='testtext' id='input'>");
			rawoutput("<input type='submit' class='button' value='$try'>");
			rawoutput("</form>");
			rawoutput("<script language='javascript'>document.getElementById('input').focus();</script>");

			output("`0`n`nThese colors can be used in your name, and in any conversations you have.");
			addnav("",$REQUEST_URI);
		}else if($act=="specialty"){
			$specialty = httpget('specialty');
			if ($specialty==""){
				output("\"`2I want to change my specialty,`0\" you announce to Cedrik.`n`n");
				output("With out a word, Cedrik grabs you by the shirt, pulls you over the counter, and behind the barrels behind him.");
				output("There, he rotates the tap on a small keg labeled \"Fine Swill XXX\"`n`n");
				output("You look around for the secret door that you know must be opening nearby when Cedrik rotates the tap back, and lifts up a freshly filled foamy mug of what is apparently his fine swill, blue-green tint and all.`n`n");
				output("\"`3What?  Were you expecting a secret room?`0\" he asks.  \"`3Now then, you must be more careful about how loudly you say that you want to change your specialty, not everyone looks favorably on that sort of thing.`n`n");
				output("`0\"`3What new specialty did you have in mind?`0\"");
				$specialities = modulehook("specialtynames");
				foreach($specialities as $key=>$name) {
					addnav($name,cmd_sanitize($REQUEST_URI)."&specialty=$key");
				}
			}else{
				output("\"`3Ok then,`0\" Cedrik says, \"`3You're all set.`0\"`n`n\"`2That's it?`0\" you ask him.`n`n");
				output("\"`3Yep.  What'd you expect, some sort of fancy arcane ritual???`0\"  Cedrik begins laughing loudly.");
				output("\"`3You're all right, kid... just don't ever play poker, eh?`0`n`n");
				output("\"`3Oh, one more thing.  Your old use points and skill level still apply to that skill, you'll have to build up some points in this one to be very good at it.`0\"");
				$session['user']['specialty']=$specialty;
			}
		}
		break;
	case "room":
		$config = unserialize($session['user']['donationconfig']);
		$expense = round(($session['user']['level']*(10+log($session['user']['level']))),0);
		$pay = httpget('pay');
		if ($pay){
			if ($pay == 2 || $session['user']['gold']>=$expense ||
					$session['user']['boughtroomtoday']){
				if ($session['user']['loggedin']){
					if (!$session['user']['boughtroomtoday']) {
						if ($pay == 2) {
							$fee = getsetting("innfee", "5%");
							if (strpos($fee, "%"))
								$expense += round($expense * $fee / 100,0);
							else
								$expense += $fee;
							$session['user']['goldinbank']-=$expense;
						} else {
							$session['user']['gold']-=$expense;
						}
						$session['user']['boughtroomtoday']=1;
						debuglog("spent $expense gold on an inn room");
					}
					$session['user']['location']=$iname;
					$session['user']['loggedin']=0;
					$session['user']['restorepage']="inn.php?op=strolldown";
					saveuser();
				}
				$session=array();
				redirect("index.php");
			}else{
				output("\"Aah, so that's how it is,\" Cedrik says as he puts the key he had retrieved back on to its hook behind his counter.");
				output("Perhaps you'd like to get sufficient funds before you attempt to engage in local commerce.");
			}
		}else{
			if ($session['user']['boughtroomtoday']){
				output("You already paid for a room for the day.");
				addnav("Go to room","inn.php?op=room&pay=1");
			}else{
				modulehook("innrooms");
				output("You stroll over to the bartender and request a room.");
				output("He eyes you up and says, \"It will cost `\$%s`0 gold for the night in a standard room.", $expense);
				$fee = getsetting("innfee", "5%");
				if (strpos($fee, "%")) {
					$bankexpense = $expense + round($expense * $fee / 100,0);
				} else {
					$bankexpense = $expense + $fee;
				}
				if ($session['user']['goldinbank'] >= $bankexpense && $bankexpense != $expense) {
					output("And since you are such a fine person, I'll even offer you a rate of `\$%s`0 gold if you pay direct from the bank.", $bankexpense);
					if (strpos($fee, "%")) {
						output("That includes a %s transaction fee.", $fee);
					} else {
						output("That includes a transaction fee of %s gold.",
								$fee);
					}
				}
				$bodyguards = array("Butch","Bruce","Alfonozo","Guido","Bruno","Bubba","Al","Chuck","Brutus","Nunzio","Terrance","Mitch","Rocco","Spike","Gregor","Sven","Draco");
				output("`n`n\"Also, let me tell you about our new 'Bodyguard Assistance Program' &#151; BAP.  You see, you hire one of my guards here, and they'll protect you should anyone happen to, er, pick the locks of your room,\" he says as he gestures to a series of men sitting at one of the inn's tables drinking ale.", true);
				output("They range in size from a skinny shifty-eyed fellow who appears barely able to lift his stein to a great bear of a fellow.");
				output("This bruiser has a tattoo of a heart with \"Mom\" written across it on his huge bicep, and goes to take a sip from his ale, but instead crushes his stein, squirting it all over the skinny fellow who doesn't voice any objection for obvious reasons.");
				output("\"We call it the BAP program because when someone tries to sneak into your room, BAP BAP BAP, our guys go to work.");
				output("There's only two conditions: you pay your fee up front, and the guard you choose gets to keep a portion of the rewards from any fights.\"");
				output("`n`nNot wanting to part with your money when the fields offer a place to sleep, you debate the issue.");
				output("You realize, however, that the inn is a considerably safer place to sleep.");
				output("It is far harder for vagabonds to get you in your room while you sleep.");
				output("Also, those bodyguards sound pretty safe to you.");
				//output("`n`bNote, bodyguard levels not yet implemented`b`n");
				addnav(array("Give him %s gold", $expense),"inn.php?op=room&pay=1");
				if ($session['user']['goldinbank'] >= $bankexpense) {
					addnav(array("Pay %s gold from bank", $bankexpense),"inn.php?op=room&pay=2");
				}
			}
		}
		break;
	}
	addnav("Other");
	addnav("I?Return to the Inn","inn.php");
	villagenav();
}

if (!$skipinndesc) rawoutput("</span>");

page_footer();
?>

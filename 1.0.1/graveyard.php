<?php
// addnews ready.
// translator ready
// mail ready
require_once("common.php");
require_once("lib/systemmail.php");
require_once("lib/http.php");
require_once("lib/buffs.php");
require_once("lib/taunt.php");
require_once("lib/events.php");

if ($session['user']['alive']) {
	redirect("village.php");
}

tlschema("graveyard");

checkday();

page_header("The Graveyard");
$skipgraveyardtext = handle_event("graveyard");

strip_all_buffs();
$max = $session['user']['level'] * 5 + 50;
$favortoheal = round(10 * ($max-$session['user']['soulpoints'])/$max);

$op = httpget('op');
if ($op=="search"){
	if ($session['user']['gravefights']<=0){
		output("`\$`bYour soul can bear no more torment in this afterlife.`b`0");
		$op="";
		httpset('op', "");
	}else{
		if (module_events("graveyard", getsetting("gravechance", 0)) != 0) {
			if (!checknavs()) {
				// If we're going back to the graveyard, make sure to reset
				// the special and the specialmisc
				$session['user']['specialinc'] = "";
				$session['user']['specialmisc'] = "";
				$skipgraveyardtext=true;
				$op = "";
				httpset("op", "");
			} else {
				page_footer();
			}
		} else {
			$session['user']['gravefights']--;
  			$battle=true;
  			$sql = "SELECT * FROM " . db_prefix("creatures") . " WHERE graveyard=1 ORDER BY rand(".e_rand().") LIMIT 1";
			$result = db_query($sql);
			$badguy = db_fetch_assoc($result);
			$level = $session['user']['level'];
			$shift = 0;
			if ($level < 5) $shift = -1;
			$badguy['creatureattack'] = 9 + $shift + (int)(($level-1) * 1.5);
			// Make graveyard creatures easier.
			$badguy['creaturedefense'] = (int)((9 + $shift + (($level-1) * 1.5)));
			$badguy['creaturedefense'] *= .7;
			$badguy['creaturehealth'] = $level * 5 + 50;
			$badguy['creatureexp'] = e_rand(10 + round($level/3),20 + round($level/3));
			$badguy['creaturelevel'] = $level;
			$badguy['type'] = 'graveyard';
			$session['user']['badguy']=createstring($badguy);
		}
	}
}
if ($op == "fight" || $op == "run"){
	if ($op=="run"){
		if (e_rand(0,2)==1) {
			output("`\$Ramius`) curses you for your cowardice.`n`n");
			$favor = 5 + e_rand(0, $session['user']['level']);
			if ($favor > $session['user']['deathpower'])
				$favor = $session['user']['deathpower'];
			if ($favor > 0) {
				output("`)You have `\$LOST `^%s`) favor with `\$Ramius`).",$favor);
				$session['user']['deathpower']-=$favor;
			}
			tlschema("nav");
			addnav("G?Return to the Graveyard","graveyard.php");
			tlschema();
		} else {
			output("`)As you try to flee, you are summoned back to the fight!`n`n");
			$battle=true;
		}
	} else {
		$battle = true;
	}
}

if ($battle){
	//make some adjustments to the user to put them on mostly even ground
	//with the undead guy.
	$originalhitpoints = $session['user']['hitpoints'];
	$session['user']['hitpoints'] = $session['user']['soulpoints'];
	$originalattack = $session['user']['attack'];
	$originaldefense = $session['user']['defense'];
	$session['user']['attack'] =
		10 + round(($session['user']['level'] - 1) * 1.5);
	$session['user']['defense'] =
		10 + round(($session['user']['level'] - 1) * 1.5);

	require_once("battle.php");

	//reverse those adjustments, battle calculations are over.
	$session['user']['attack'] = $originalattack;
	$session['user']['defense'] = $originaldefense;
	$session['user']['soulpoints'] = $session['user']['hitpoints'];
	$session['user']['hitpoints'] = $originalhitpoints;
	if ($victory) {
		tlschema("battle");
		$msg = translate_inline($badguy['creaturelose']);
		tlschema();
		output_notl("`b`&%s`0`b`n", $msg); 
		output("`b`\$You have tormented %s!`0`b`n", $badguy['creaturename']);
		output("`#You receive `^%s`# favor with `\$Ramius`#!`n`0", $badguy['creatureexp']);
		$session['user']['deathpower']+=$badguy['creatureexp'];
		$badguy=array();
		$op = 0;
		httpset('op', "");
		$skipgraveyardtext=true;
	}else{
		if ($defeat){
			$taunt = select_taunt_array();
			addnews("`)%s`) has been defeated in the graveyard by %s.`n%s",$session['user']['name'],$badguy['creaturename'],$taunt);
			output("`b`&You have been defeated by `%%s`&!!!`n", $badguy['creaturename']);
			output("You may not torment any more souls today.");
			$session['user']['gravefights']=0;
			tlschema("nav");
			addnav("G?Return to the Graveyard","graveyard.php");
			tlschema();
		}else{
			tlschema("fightnav");
			addnav("F?Torment","graveyard.php?op=fight");
			addnav("R?Flee","graveyard.php?op=run");
			if (getsetting("autofight",0)) {
				addnav("Automatic Fighting");
				addnav("5?For 5 Rounds","graveyard.php?op=fight&auto=five");
				addnav("1?For 10 Rounds","graveyard.php?op=fight&auto=ten");
				if (getsetting("autofightfull", 0)) {
					addnav("U?Until End","graveyard.php?op=fight&auto=full");
				}
			} 
			tlschema();
		}
	}
}

if ($op==""){
	if (!$skipgraveyardtext) {
		output("`)`c`bThe Graveyard`b`c");
		output("Your spirit wanders into a lonely graveyard, overgrown with sickly weeds which seem to grab at your spirit as you float past them.");
		output("Around you are the remains of many broken tombstones, some lying on their faces, some shattered to pieces.");
		output("You can almost hear the wails of the souls trapped within each plot lamenting their fates.`n`n");
		output("In the center of the graveyard is an ancient looking mausoleum which has been worn by the effects of untold years.");
		output("A sinister looking gargoyle adorns the apex of its roof; its eyes seem to follow  you, and its mouth gapes with sharp stone teeth.");
		output("The plaque above the door reads `\$Ramius, Overlord of Death`).");
		modulehook("graveyard-desc");
	}
	modulehook("graveyard");

	if ($session['user']['gravefights']) {
		addnav("Look for something to torment","graveyard.php?op=search");
	}
	addnav("Places");
	addnav("W?List Warriors","list.php");
	addnav("S?Return to the Shades","shades.php");
	addnav("M?Enter the Mausoleum","graveyard.php?op=enter");
	module_display_events("graveyard", "graveyard.php");
}elseif ($op=="enter"){
	output("`)`b`cThe Mausoleum`c`b");
	output("You enter the mausoleum and find yourself in a cold, stark marble chamber.");
	output("The air around you carries the chill of death itself.");
	output("From the darkness, two black eyes stare into your soul.");
	output("A clammy grasp seems to clutch your mind, and fill it with the words of the Overlord of Death, `\$Ramius`) himself.`n`n");
	output("\"`7Your mortal coil has forsaken you.  Now you turn to me.  There are those within this land that have eluded my grasp and possess a life beyond life.  To prove your worth to me and earn my favor, go out and torment their souls.  Should you gain enough of my favor, I will reward you.`)\"");

	addnav("Question `\$Ramius`0 about the worth of your soul","graveyard.php?op=question");
	addnav(array("Restore Your Soul (%s favor)", $favortoheal),"graveyard.php?op=restore");
	addnav("Places");
	addnav("S?Land of the Shades","shades.php");
	addnav("G?Return to the Graveyard","graveyard.php");
	modulehook("mausoleum");
}elseif ($op=="restore"){
	output("`)`b`cThe Mausoleum`c`b");
	if ($session['user']['soulpoints']<$max){
		if ($session['user']['deathpower']>=$favortoheal){
			output("`\$Ramius`) calls you weak for needing restoration, but as you have enough favor with him, he grants your request at the cost of `4%s`) favor.", $favortoheal);
			$session['user']['deathpower']-=$favortoheal;
			$session['user']['soulpoints']=$max;
		}else{
			output("`\$Ramius`) curses you and throws you from the Mausoleum, you must gain more favor with him before he will grant restoration.");
		}
	}else{
		output("`\$Ramius`) sighs and mumbles something about, \"`7just 'cause they're dead, does that mean they don't have to think?`)\"`n`n");
		output("Perhaps you'd like to actually `ineed`i restoration before you ask for it.");
	}

	addnav("Question `\$Ramius`0 about the worth of your soul","graveyard.php?op=question");
	addnav("Places");
	addnav("S?Land of the Shades","shades.php");
	addnav("G?Return to the Graveyard","graveyard.php");
}elseif ($op=="resurrection"){
	output("`\$Ramius waves his skeletal arms as he begins to command the very fabric of life.`n`n");
	// Note to translators.  The text spoken by Ramius here is backwards
	// English.  You might choose to maintain it in tact, or you might choose
	// to translate it to your language, and reverse the letters that way.  A
	// few words have been replaced with latin counterparts to make it a
	// little harder to identify that they are just backwards english.
	// tu shall have resurrection
	// rise from the dead o servant
	// power over death is mine
	// your life ego grant tu again
	// for ego know tu shall return to me again
	output("\"`)Noitcerruser evah llahs ut...`\$\"  The air begins to crackle around you.`n`n");
	output("\"`)Tnavres o htaed eht morf esir.`\$\" Your soul begins to burn with the pain of a thousand frosty fires.`n`n");
	output("\"`)Enim si htaed revo rewop.`\$\" Gradually you begin to become aware that the fires are dimming and are replaced by the blinding pain last known by your body before it fell.`n`n");
	output("\"`)Niaga ut tnarg oge efil ruoy.`\$\" You begin to look around you, and you watch as your muscles knit themselves back together.`n`n");
	output("\"`)Niaga em ot nruter llahs ut wonk oge rof.`\$\" With a gasp, you laboriously again draw your first breath.");

	addnav("Continue","newday.php?resurrection=true");
}elseif ($op=="question"){
	if ($session['user']['deathpower']>=100) {
		output("`\$Ramius`) speaks, \"`7You have impressed me indeed.  I shall grant you the ability to visit your foes in the mortal world.`)\"");
		addnav("Ramius Favors");
		addnav("H?Haunt a foe (25 favor)","graveyard.php?op=haunt");
		addnav("e?Resurrection (100 favor)","graveyard.php?op=resurrection");
		addnav("Other");
	}elseif ($session['user']['deathpower'] >= 25){
		output("`\$Ramius`) speaks, \"`7I am moderately impressed with your efforts.  A minor favor I now grant to you, but continue my work, and I may yet have more power to bestow.`)\""); 
		addnav("Ramius Favors");
		addnav("H?Haunt a foe (25 favor)","graveyard.php?op=haunt");
		addnav("Other");
	}else{
		output("`\$Ramius`) speaks, \"`7I am not yet impressed with your efforts.  Continue my work, and we may speak further.`)\"");
	}
	output("`n`nYou have `6%s`) favor with `\$Ramius`).", $session['user']['deathpower']);
	addnav("Question `\$Ramius`0 about the worth of your soul","graveyard.php?op=question");
	addnav(array("Restore Your Soul (%s favor)",$favortoheal),"graveyard.php?op=restore");
	
	addnav("Places");
	addnav("S?Land of the Shades","shades.php");
	addnav("G?Return to the Graveyard","graveyard.php");
	modulehook("ramiusfavors");
}elseif ($op=="haunt"){
	output("`\$Ramius`) is impressed with your actions, and grants you the power to haunt a foe.`n`n");
	$search = translate_inline("Search");
	rawoutput("<form action='graveyard.php?op=haunt2' method='POST'>");
	addnav("","graveyard.php?op=haunt2");
	output("Who would you like to haunt? ");
	rawoutput("<input name='name' id='name'>");
	rawoutput("<input type='submit' class='button' value='$search'>");
	rawoutput("</form>");
	rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>");

	addnav("Places");
	addnav("S?Land of the Shades","shades.php");
	addnav("G?The Graveyard","graveyard.php");
	addnav("M?Return to the Mausoleum","graveyard.php?op=enter");
}elseif ($op=="haunt2"){
	$string="%";
	$name = httppost('name');
	for ($x=0;$x<strlen($name);$x++){
		$string .= substr($name,$x,1)."%";
	}
	$sql = "SELECT login,name,level FROM " . db_prefix("accounts") . " WHERE name LIKE '".addslashes($string)."' AND locked=0 ORDER BY level,login";
	$result = db_query($sql);
	if (db_num_rows($result)<=0){
		output("`\$Ramius`) could find no one who matched the name you gave him.");
	}elseif(db_num_rows($result)>100){
		output("`\$Ramius`) thinks you should narrow down the number of people you wish to haunt.");
		$search = translate_inline("Search");
		rawoutput("<form action='graveyard.php?op=haunt2' method='POST'>");
		addnav("","graveyard.php?op=haunt2");
		output("Who would you like to haunt? ");
		rawoutput("<input name='name' id='name'>");
		rawoutput("<input type='submit' class='button' value='$search'>");
		rawoutput("</form>");
		rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>",true);
	}else{
		output("`\$Ramius`) will allow you to try to haunt these people:`n");
		$name = translate_inline("Name");
		$lev = translate_inline("Level");
		rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
		rawoutput("<tr class='trhead'><td>$name</td><td>$lev</td></tr>");
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='graveyard.php?op=haunt3&name=".HTMLEntities($row['login'])."'>");
			output_notl("%s", $row['name']);
			rawoutput("</a></td><td>");
			output_notl("%s", $row['level']);
			rawoutput("</td></tr>",true);
			addnav("","graveyard.php?op=haunt3&name=".HTMLEntities($row['login']));
		}
		rawoutput("</table>",true);
	}

	addnav("Question `\$Ramius`0 about the worth of your soul","graveyard.php?op=question");
	addnav(array("Restore Your Soul (%s favor)",$favortoheal),"graveyard.php?op=restore");
	addnav("Places");
	addnav("S?Land of the Shades","shades.php");
	addnav("G?The Graveyard","graveyard.php");
	addnav("M?Return to the Mausoleum","graveyard.php?op=enter");
}elseif ($op=="haunt3"){
	output("`)`c`bThe Mausoleum`b`c");
	$name = httpget('name');
	$sql = "SELECT name,level,hauntedby,acctid FROM " . db_prefix("accounts") . " WHERE login='$name'";
	$result = db_query($sql);
	if (db_num_rows($result)>0){
		$row = db_fetch_assoc($result);
		if ($row['hauntedby']!=""){
			output("That person has already been haunted, please select another target");
		}else{
			$session['user']['deathpower']-=25;
			$roll1 = e_rand(0,$row['level']);
			$roll2 = e_rand(0,$session['user']['level']);
			if ($roll2>$roll1){
				output("You have successfully haunted `7%s`)!", $row['name']);
				$sql = "UPDATE " . db_prefix("accounts") . " SET hauntedby='".addslashes($session['user']['name'])."' WHERE login='$name'";
				db_query($sql);
				addnews("`7%s`) haunted `7%s`)!",$session['user']['name'],$row['name']);
				$subj = array("`)You have been haunted");
				$body = array("`)You have been haunted by `&%s`).",$session['user']['name']);
		 		systemmail($row['acctid'], $subj, $body);
			}else{
				addnews("`7%s`) unsuccessfully haunted `7%s`)!",$session['user']['name'],$row['name']);
				switch (e_rand(0,5)){
				case 0:
					$msg = translate_inline("Just as you were about to haunt `7%s`) good, they sneezed, and missed it completely.");
					break;
				case 1:
					$msg = translate_inline("You haunt `7%s`) real good like, but unfortunately they're sleeping and are completely unaware of your presence.");
					break;
				case 2:
					$msg = translate_inline("You're about to haunt `7%s`), but trip over your ghostly tail and land flat on your, um... face.");
					break;
				case 3:
					$msg = translate_inline("You go to haunt `7%s`) in their sleep, but they look up at you, and roll over mumbling something about eating sausage just before going to bed.");
					break;
				case 4:
					$msg = translate_inline("You wake `7%s`) up, who looks at you for a moment before declaring, \"Neat!\" and trying to catch you.");
					break;
				case 5:
					$msg = translate_inline("You go to scare `7%s`), but catch a glimpse of yourself in the mirror and panic at the sight of a ghost!");
					break;
				}
				output($msg, $row['name']);
			}
		}
	}else{
		output("`\$Ramius`) has lost their concentration on this person, you cannot haunt them now.");
	}

	addnav("Question `\$Ramius`0 about the worth of your soul","graveyard.php?op=question");
	addnav(array("Restore Your Soul (%s favor)", $favortoheal),"graveyard.php?op=restore");
	addnav("Places");
	addnav("S?Land of the Shades","shades.php");
	addnav("G?The Graveyard","graveyard.php");
	addnav("M?Return to the Mausoleum","graveyard.php?op=enter");
}

page_footer();
?>

<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/http.php");
require_once("lib/villagenav.php");

tlschema("weapon");

checkday();

page_header("MightyE's Weapons");
output("`c`b`&MightyE's Weapons`0`b`c");
$tradeinvalue = round(($session['user']['weaponvalue']*.75),0);
$op = httpget("op");

if ($op==""){
	$women=translate_inline("women");
	$men=translate_inline("men");
  	output("`!MightyE `7stands behind a counter and appears to pay little attention to you as you enter, but you know from experience that he has his eye on every move you make. ");
	output("He may be a humble weapons merchant, but he still carries himself with the grace of a man who has used his weapons to kill mightier %s than you.`n`n",($session['user']['sex']?$women:$men));
	output("The massive hilt of a claymore protrudes above his shoulder; its gleam in the torch light not much brighter than the gleam off of `!MightyE's`7 bald forehead, kept shaved mostly as a strategic advantage, but in no small part because nature insisted that some level of baldness was necessary.`n`n");
	output("`!MightyE`7 finally nods to you, stroking his goatee and looking like he wished he could have an opportunity to use one of these weapons.");
	$sql = "SELECT max(level) AS level FROM " .  db_prefix("weapons") . " WHERE level<=".(int)$session['user']['dragonkills'];
	$result = db_query($sql);
	$row = db_fetch_assoc($result);

	$sql = "SELECT * FROM " . db_prefix("weapons") . " WHERE level = ".(int)$row['level']." ORDER BY damage ASC";
	$result = db_query($sql);
	output("`7You stroll up the counter and try your best to look like you know what most of these contraptions do. ");
	output("`!MightyE`7 looks at you and says, \"`#I'll give you `^%s`# trade-in value for your `5%s`#. ",$tradeinvalue,$session['user']['weapon']);
	output("Just click on the weapon you wish to buy, what ever 'click' means`7,\" and looks utterly confused. ");
	output("He stands there a few seconds, snapping his fingers and wondering if that is what is meant by \"click,\" before returning to his work: standing there and looking good.`n`n");
	$wname=translate_inline("`bName`b");
	$wdam=translate_inline("`bDamage`b");
	$wcost=translate_inline("`bCost`b");
	rawoutput("<table border='0' cellpadding='0'>");
	rawoutput("<tr class='trhead'><td>");
	output_notl($wname);
	rawoutput("</td><td align='center'>");
	output_notl($wdam);
	rawoutput("</td><td align='right'>");
	output_notl($wcost);
	rawoutput("</td></tr>");
	$number=db_num_rows($result);
	for ($i=0;$i<$number;$i++){
		$row = db_fetch_assoc($result);
		$row = modulehook("modify-weapon", $row);
		rawoutput("<tr class='".($i%2==1?"trlight":"trdark")."'><td>");
		$color = "`)";
		if ($row['value']<=($session['user']['gold']+$tradeinvalue)){
			$color = "`&";
			rawoutput("<a href='weapons.php?op=buy&id={$row['weaponid']}'>");
			output_notl("%s%s`0",$color,$row['weaponname']);
			rawoutput("</a>");
			addnav("","weapons.php?op=buy&id={$row['weaponid']}");
		}else{
			output_notl("%s%s`0",$color,$row['weaponname']);
			addnav("","weapons.php?op=buy&id={$row['weaponid']}");
		}
		rawoutput("</td><td align='center'>");
		output_notl("%s%s`0",$color,$row['damage']);
		rawoutput("</td><td align='right'>");
		output_notl("%s%s`0",$color,$row['value']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
	villagenav();
}else if ($op=="buy"){
	$id = httpget("id");
	$sql = "SELECT * FROM " . db_prefix("weapons") . " WHERE weaponid='$id'";
	$result = db_query($sql);
	if (db_num_rows($result)==0){
		output("`!MightyE`7 looks at you, confused for a second, then realizes that you've apparently taken one too many bonks on the head, and nods and smiles.");
		addnav("Try again?","weapons.php");
		villagenav();
	}else{
		$row = db_fetch_assoc($result);
		$row = modulehook("modify-weapon", $row);
		if ($row['value']>($session['user']['gold']+$tradeinvalue)){
			output("Waiting until `!MightyE`7 looks away, you reach carefully for the `5%s`7, which you silently remove from the rack upon which it sits. ",$row['weaponname']);
			output("Secure in your theft, you turn around and head for the door, swiftly, quietly, like a ninja, only to discover that upon reaching the door, the ominous `!MightyE`7 stands, blocking your exit. ");
			output("You execute a flying kick. ");
			output("Mid flight, you hear the \"SHING\" of a sword leaving its sheath.... your foot is gone. ");
			output("You land on your stump, and `!MightyE`7 stands in the doorway, claymore once again in its back holster, with no sign that it had been used, his arms folded menacingly across his burly chest.  \"`#Perhaps you'd like to pay for that?`7\" is all he has to say as you collapse at his feet, lifeblood staining the planks under your remaining foot.");
			output("`n`nYou wake up some time later, having been tossed unconscious into the street.");
			villagenav();
		}else{
			output("`!MightyE`7 takes your `5%s`7 and promptly puts a price on it, setting it out for display with the rest of his weapons.`n`n",$session['user']['weapon']);
			output("In return, he hands you a shiny new `5%s`7 which you swoosh around the room, nearly taking off `!MightyE`7's head, which he deftly ducks; you're not the first person to exuberantly try out a new weapon.",$row['weaponname']);
			debuglog("spent " . ($row['value']-$tradeinvalue) . " gold on the " . $row['weaponname'] . " weapon");
			$session['user']['gold']-=$row['value'];
			$session['user']['weapon'] = $row['weaponname'];
			$session['user']['gold']+=$tradeinvalue;
			$session['user']['attack']-=$session['user']['weapondmg'];
			$session['user']['weapondmg'] = $row['damage'];
			$session['user']['attack']+=$session['user']['weapondmg'];
			$session['user']['weaponvalue'] = $row['value'];
			villagenav();
		}
	}
}

page_footer();
?>

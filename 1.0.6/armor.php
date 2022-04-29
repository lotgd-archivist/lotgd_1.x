<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/http.php");
require_once("lib/villagenav.php");

tlschema("armor");

checkday();

page_header("Pegasus Armor");
output("`c`b`%Pegasus Armor`0`b`c");
$tradeinvalue = round(($session['user']['armorvalue']*.75),0);
$op = httpget('op');
if ($op==""){
	output("`5The fair and beautiful `#Pegasus`5 greets you with a warm smile as you stroll over to her brightly colored gypsy wagon, which is placed, not out of coincidence, right next to `!MightyE`5's weapon shop.");
	output("Her outfit is as brightly colored and outrageous as her wagon, and it is almost (but not quite) enough to make you look away from her huge gray eyes and flashes of skin between her not-quite-sufficient gypsy clothes.`n`n");
	$sql = "SELECT max(level) AS level FROM " . db_prefix("armor") . " WHERE level<=".$session['user']['dragonkills'];
	$result = db_query($sql);
	$row = db_fetch_assoc($result);

	$sql = "SELECT * FROM " . db_prefix("armor") . " WHERE level={$row['level']} ORDER BY value";
	$result = db_query($sql);
	output("`5You look over the various pieces of apparel, and wonder if `#Pegasus`5 would be so good as to try some of them on for you, when you realize that she is busy staring dreamily at `!MightyE`5 through the window of his shop as he, bare-chested, demonstrates the use of one of his fine wares to a customer.");
	output("Noticing for a moment that you are browsing her wares, she glances at your `&%s`5 and says that she'll give you `^%s`5 for them.`0`n`n",$session['user']['armor'],$tradeinvalue);
	$aname = translate_inline("`bName`b");
	$adef = translate_inline("`bDefense`b");
	$acost = translate_inline("`bCost`b");
	rawoutput("<table border='0' cellpadding='0'>");
	rawoutput("<tr class='trhead'><td>");
	output_notl($aname);
	rawoutput("</td><td align='center'>");
	output_notl($adef);
	rawoutput("</td><td align='right'>");
	output_notl($acost);
	rawoutput("</td></tr>");
	for ($i=0;$i<db_num_rows($result);$i++){
		$row = db_fetch_assoc($result);
		$row = modulehook("modify-armor", $row);
		rawoutput("<tr class='".($i%2==1?"trlight":"trdark")."'>");
		rawoutput("<td>");
		$color = "`)";
		if ($row['value']<=($session['user']['gold']+$tradeinvalue)){
			$color = "`&";
			rawoutput("<a href='armor.php?op=buy&id={$row['armorid']}'>");
			output_notl("%s%s`0", $color, $row['armorname']);
			rawoutput("</a>");
			addnav("","armor.php?op=buy&id={$row['armorid']}");
		}else{
			output_notl("%s%s`0", $color, $row['armorname']);
			addnav("","armor.php?op=buy&id={$row['armorid']}");
		}
		rawoutput("</td><td align='center'>");
		output_notl("%s%s`0", $color, $row['defense']);
		rawoutput("</td><td align='right'>");
		output_notl("%s%s`0", $color, $row['value']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>",true);
	villagenav();
}elseif ($op=="buy"){
	$id = httpget('id');
	$sql = "SELECT * FROM " . db_prefix("armor") . " WHERE armorid='$id'";
	$result = db_query($sql);
	if (db_num_rows($result)==0){
		output("`#Pegasus`5 looks at you, confused for a second, then realizes that you've apparently taken one too many bonks on the head, and nods and smiles.");
		addnav("Try again?","armor.php");
		villagenav();
	}else{
		$row = db_fetch_assoc($result);
		$row = modulehook("modify-armor", $row);
		if ($row['value']>($session['user']['gold']+$tradeinvalue)){
			output("`5Waiting until `#Pegasus`5 looks away, you reach carefully for the `%%s`5, which you silently remove from the stack of clothes on which it sits.",$row['armorname']);
			output("Secure in your theft, you begin to turn around only to realize that your turning action is hindered by a fist closed tightly around your throat.  Glancing down, you trace the fist to the arm on which it is attached, which in turn is attached to a very muscular `!MightyE`5.");
			output("You try to explain what happened here, but your throat doesn't seem to be able to open up to let your voice through, let alone essential oxygen.`n`n");
			output("As darkness creeps in on the edge of your vision, you glance pleadingly, but futilly at `%Pegasus`5 who is staring dreamily at `!MightyE`5, her hands clutched next to her face, which is painted with a large admiring smile.`n`n");
			output("`n`nYou wake up some time later, having been tossed unconscious into the street.");
			villagenav();
		}else{
			output("`#Pegasus`5 takes your gold, and much to your surprise she also takes your `%%s`5 and promptly puts a price on it, setting it neatly on another stack of clothes.`n`n",$session['user']['armor']);
			output("In return, she hands you a beautiful  new `%%s`5.`n`n",$row['armorname']);
			output("You begin to protest, \"`@Won't I look silly wearing nothing but my `&%s`@?`5\" you ask.", $row['armorname']);
			output("You ponder it a moment, and then realize that everyone else in the town is doing the same thing.");
			output("\"`@Oh well, when in Rome...`5\"");
			debuglog("spent " . ($row['value']-$tradeinvalue) . " gold on the " . $row['armorname'] . " armor");
			$session['user']['gold']-=$row['value'];
			$session['user']['armor'] = $row['armorname'];
			$session['user']['gold']+=$tradeinvalue;
			$session['user']['defense']-=$session['user']['armordef'];
			$session['user']['armordef'] = $row['defense'];
			$session['user']['defense']+=$session['user']['armordef'];
			$session['user']['armorvalue'] = $row['value'];
			villagenav();
		}
	}
}
page_footer();
?>

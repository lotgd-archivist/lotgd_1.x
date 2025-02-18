<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/showform.php");
require_once("lib/http.php");

check_su_access(SU_EDIT_EQUIPMENT);

tlschema("armor");

page_header("Armor Editor");
$armorlevel = (int)httpget('level');
require_once("lib/superusernav.php");
superusernav();
addnav("Armor Editor");
addnav("Armor Editor Home","armoreditor.php?level=$armorlevel");

addnav("Add armor","armoreditor.php?op=add&level=$armorlevel");
$values = explode(',', $settings->equpimentcosts);
array_unshift($values, 0);
output("`&<h3>Armor for %s Dragon Kills</h3>`0",$armorlevel,true);

$armorarray=array(
	"Armor,title",
	"armorid"=>"Armor ID,hidden",
	"armorname"=>"Armor Name",
	"defense"=>"Defense,range,1," . $settings->maxlevel .",1");
$op = httpget('op');
$id = httpget('id');
if($op=="edit" || $op=="add"){
	if ($op=="edit"){
		$sql = "SELECT * FROM " . DB::prefix("armor") . " WHERE armorid='$id'";
		$result = DB::query($sql);
		$row = DB::fetch_assoc($result);
	}else{
		$sql = "SELECT max(defense+1) AS defense FROM " . DB::prefix("armor") . " WHERE level=$armorlevel";
		$result = DB::query($sql);
		$row = DB::fetch_assoc($result);
	}
	rawoutput("<form action='armoreditor.php?op=save&level=$armorlevel' method='POST'>");
	addnav("","armoreditor.php?op=save&level=$armorlevel");
	showform($armorarray,$row);
	rawoutput("</form>");
}else if($op=="del"){
	$sql = "DELETE FROM " . DB::prefix("armor") . " WHERE armorid='$id'";
	DB::query($sql);
	//output($sql);
	$op = "";
	httpset("op", $op);
}else if($op=="save"){
	$armorid = httppost('armorid');
	$armorname = httppost('armorname');
	$defense = httppost('defense');
	if ($armorid>0){
		$sql = "UPDATE " . DB::prefix("armor") . " SET armorname=\"$armorname\",defense=\"$defense\",value=".$values[$defense]." WHERE armorid='$armorid'";
	}else{
		$sql = "INSERT INTO " . DB::prefix("armor") . " (level,defense,armorname,value) VALUES ($armorlevel,\"$defense\",\"$armorname\",".$values[$defense].")";
	}
	DB::query($sql);
	$op = "";
	httpset("op", $op);
}
if ($op==""){
	$sql = "SELECT max(level+1) AS level FROM " . DB::prefix("armor");
	$res = DB::query($sql);
	$row = DB::fetch_assoc($res);
	$max = $row['level'];
	for ($i=0;$i<=$max;$i++){
		if ($i == 1)
			addnav(array("Armor for %s DK",$i),"armoreditor.php?level=$i");
		else
			addnav(array("Armor for %s DKs",$i),"armoreditor.php?level=$i");
	}
	$sql = "SELECT * FROM " . DB::prefix("armor") . " WHERE level=$armorlevel ORDER BY defense";
	$result= DB::query($sql);
	$ops = translate_inline("Ops");
	$name = translate_inline("Name");
	$cost = translate_inline("Cost");
	$defense = translate_inline("Defense");
	$level = translate_inline("Level");
	$edit = translate_inline("Edit");
	$del = translate_inline("Del");
	$delconfirm = translate_inline("Are you sure you wish to delete this armor?");

	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$ops</td><td>$name</td><td>$cost</td><td>$defense</td><td>$level</td></tr>");
	$number=DB::num_rows($result);
	for ($i=0;$i<$number;$i++){
		$row = DB::fetch_assoc($result);
		rawoutput("<tr class='".($i%2?"trdark":"trlight")."'>");
		rawoutput("<td>[<a href='armoreditor.php?op=edit&id={$row['armorid']}&level=$armorlevel'>$edit</a>|<a href='armoreditor.php?op=del&id={$row['armorid']}&level=$armorlevel' onClick='return confirm(\"$delconfirm\");'>$del</a>]</td>");
		addnav("","armoreditor.php?op=edit&id={$row['armorid']}&level=$armorlevel");
		addnav("","armoreditor.php?op=del&id={$row['armorid']}&level=$armorlevel");
		rawoutput("<td>");
		output_notl($row['armorname']);
		rawoutput("</td><td>");
		output_notl($row['value']);
		rawoutput("</td><td>");
		output_notl($row['defense']);
		rawoutput("</td><td>");
		output_notl($row['level']);
		rawoutput("</td>");
		rawoutput("</tr>");
	}
	rawoutput("</table>");
}
page_footer();
?>

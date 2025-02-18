<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/showform.php");
require_once("lib/http.php");

check_su_access(SU_EDIT_EQUIPMENT);

tlschema("weapon");

page_header("Weapon Editor");
$weaponlevel = (int)httpget("level");
require_once("lib/superusernav.php");
superusernav();

addnav("Editor");
addnav("Weapon Editor Home","weaponeditor.php?level=$weaponlevel");

addnav("Add a weapon","weaponeditor.php?op=add&level=$weaponlevel");
$values = explode(',', $settings->equpimentcosts);
array_unshift($values, 0);
rawoutput("<h3>");
if ($weaponlevel == 1) {
	output("`&Weapons for 1 Dragon Kill`0");
} else {
	output("`&Weapons for %s Dragon Kills`0",$weaponlevel);
}
rawoutput("<h3>");

$weaponarray=array(
	"Weapon,title",
	"weaponid"=>"Weapon ID,hidden",
	"weaponname"=>"Weapon Name",
	"damage"=>"Damage,range,1," . $settings->maxlevel . ",1");
$op = httpget('op');
$id = httpget('id');
if($op=="edit" || $op=="add"){
	if ($op=="edit"){
		$sql = "SELECT * FROM " . DB::prefix("weapons") . " WHERE weaponid='$id'";
		$result = DB::query($sql);
		$row = DB::fetch_assoc($result);
	}else{
		$sql = "SELECT max(damage+1) AS damage FROM " . DB::prefix("weapons") . " WHERE level=$weaponlevel";
		$result = DB::query($sql);
		$row = DB::fetch_assoc($result);
	}
	rawoutput("<form action='weaponeditor.php?op=save&level=$weaponlevel' method='POST'>");
	addnav("","weaponeditor.php?op=save&level=$weaponlevel");
	showform($weaponarray,$row);
	rawoutput("</form>");
}else if($op=="del"){
	$sql = "DELETE FROM " . DB::prefix("weapons") . " WHERE weaponid='$id'";
	DB::query($sql);
	$op = "";
	httpset("op", $op);
}else if($op=="save"){
	$weaponid = (int)httppost("weaponid");
	$damage = httppost("damage");
	$weaponname = httppost("weaponname");
	if ($weaponid>0){
		$sql = "UPDATE " . DB::prefix("weapons") . " SET weaponname=\"$weaponname\",damage=\"$damage\",value=" .  $values[$damage]." WHERE weaponid='$weaponid'";
	}else{
		$sql = "INSERT INTO " . DB::prefix("weapons") . " (level,damage,weaponname,value) VALUES ($weaponlevel,\"$damage\",\"$weaponname\",".$values[$damage].")";
	}
	DB::query($sql);
	//output($sql);
	$op = "";
	httpset("op", $op);
}
if ($op==""){
	$sql = "SELECT max(level+1) as level FROM " . DB::prefix("weapons");
	$res = DB::query($sql);
	$row = DB::fetch_assoc($res);
	$max = $row['level'];
	for ($i=0;$i<=$max;$i++){
		if ($i == 1)
			addnav("Weapons for 1 DK","weaponeditor.php?level=$i");
		else
			addnav(array("Weapons for %s DKs",$i),"weaponeditor.php?level=$i");
	}
	$sql = "SELECT * FROM " . DB::prefix("weapons") . " WHERE level=$weaponlevel ORDER BY damage";
	$result= DB::query($sql);
	$ops = translate_inline("Ops");
	$name = translate_inline("Name");
	$cost = translate_inline("Cost");
	$damage = translate_inline("Damage");
	$level = translate_inline("Level");
	$edit = translate_inline("Edit");
	$del = translate_inline("Del");
	$delconfirm = translate_inline("Are you sure you wish to delete this weapon?");

	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
	rawoutput("<tr class='trhead'><td>$ops</td><td>$name</td><td>$cost</td><td>$damage</td><td>$level</td></tr>");
	$number=DB::num_rows($result);
	for ($i=0;$i<$number;$i++){
		$row = DB::fetch_assoc($result);
		rawoutput("<tr class='".($i%2?"trdark":"trlight")."'>");
		rawoutput("<td>[<a href='weaponeditor.php?op=edit&id={$row['weaponid']}&level=$weaponlevel'>$edit</a>|<a href='weaponeditor.php?op=del&id={$row['weaponid']}&level=$weaponlevel' onClick='return confirm(\"Are you sure you wish to delete this weapon?\");'>$del</a>]</td>");
		addnav("","weaponeditor.php?op=edit&id={$row['weaponid']}&level=$weaponlevel");
		addnav("","weaponeditor.php?op=del&id={$row['weaponid']}&level=$weaponlevel");
		rawoutput("<td>");
		output_notl($row['weaponname']);
		rawoutput("</td><td>");
		output_notl($row['value']);
		rawoutput("</td><td>");
		output_notl($row['damage']);
		rawoutput("</td><td>");
		output_notl($row['level']);
		rawoutput("</td>");
		rawoutput("</tr>");
	}
	rawoutput("</table>");
}
page_footer();
?>

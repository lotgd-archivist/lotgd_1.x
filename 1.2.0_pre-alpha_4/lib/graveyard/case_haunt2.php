<?php
$string="%";
$name = httppost('name');
for ($x=0;$x<strlen($name);$x++){
	$string .= substr($name,$x,1)."%";
}
$sql = "SELECT login,name,level, clanid FROM " . DB::prefix("accounts") . " WHERE name LIKE '".addslashes($string)."' AND locked=0 ORDER BY level,login";
$result = DB::query($sql);

$targetcount=DB::num_rows($result);
$victims=array();
if ($targetcount>0 && $targetcount<=100) {
	while ($row=DB::fetch_assoc($result)) {
		$victims[]=$row;
	}
	$victims=modulehook("modifyhaunttargets",$victims);
	$targetcount=count($victims);
}

if ($targetcount<=0){
	output("`\$%s`) could find no one who matched the name you gave him.",$deathoverlord);
}elseif($targetcount>100){
	output("`\$%s`) thinks you should narrow down the number of people you wish to haunt.",$deathoverlord);
	$search = translate_inline("Search");
	rawoutput("<form action='graveyard.php?op=haunt2' method='POST'>");
	addnav("","graveyard.php?op=haunt2");
	output("Who would you like to haunt? ");
	rawoutput("<input name='name' id='name'>");
	rawoutput("<input type='submit' class='button' value='$search'>");
	rawoutput("</form>");
	rawoutput("<script language='JavaScript'>document.getElementById('name').focus()</script>",true);
}else{
	output("`\$%s`) will allow you to try to haunt these people:`n",$deathoverlord);
	$name = translate_inline("Name");
	$lev = translate_inline("Level");
	rawoutput("<table cellpadding='3' cellspacing='0' border='0'>");
	rawoutput("<tr class='trhead'><td>$name</td><td>$lev</td></tr>");
	$i=0;
	foreach ($victims as $row){
		$i++;
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'><td><a href='graveyard.php?op=haunt3&name=".HTMLEntities($row['login'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."'>");
		output_notl("%s", $row['name']);
		rawoutput("</a></td><td>");
		output_notl("%s", $row['level']);
		rawoutput("</td></tr>",true);
		addnav("","graveyard.php?op=haunt3&name=".HTMLEntities($row['login'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
	}
	rawoutput("</table>",true);
}
addnav(array("Question `\$%s`0 about the worth of your soul",$deathoverlord),"graveyard.php?op=question");
$max = $user->level * 5 + 50;
$favortoheal = round(10 * ($max-$user->soulpoints)/$max);
addnav(array("Restore Your Soul (%s favor)",$favortoheal),"graveyard.php?op=restore");
addnav("Places");
addnav("S?Land of the Shades","shades.php");
addnav("G?The Graveyard","graveyard.php");
addnav("M?Return to the Mausoleum","graveyard.php?op=enter");
?>

<?php
require("lib/installer/installer_sqlstatements.php");
if (httppost("type")>""){
	if (httppost("type")=="install") {
		Session::set('fromversion', -1);
		Session::setNested('dbinfo', 'upgrade', false);
	}else{
		Session::set('fromversion', httppost('version'));
		Session::setNested('dbinfo', 'upgrade', true);
	}
}

if (!Session::get('fromversion')){
	output("`@`c`bConfirmation`b`c");
	output("`2Please confirm the following:`0`n");
	rawoutput("<form action='installer.php?stage=7' method='POST'>");
	rawoutput("<table border='0' cellpadding='0' cellspacing='0'><tr><td valign='top'>");
	output("`2I should:`0");
	rawoutput("</td><td>");
	$version = getsetting("installer_version","-1");
	if ($version != "-1") Session::setNested('dbinfo', 'upgrade', true);
	rawoutput("<input type='radio' value='upgrade' name='type'".(Session::getNested('dbinfo', 'upgrade')?" checked":"").">");
	output(" `2Perform an upgrade from ");
	if ($version=="-1") $version="0.9.7";
	rawoutput("<select name='version'>");
	foreach ($sql_upgrade_statements as $key=>$val) {
		if ($key!="-1"){
			rawoutput("<option value='$key'".($version==$key?" selected":"").">$key</option>");
		}
	}
	rawoutput("</select>");
	rawoutput("<br><input type='radio' value='install' name='type'".(Session::getNested('dbinfo', 'upgrade')?"":" checked").">");
	output(" `2Perform a clean install.");
	rawoutput("</td></tr></table>");
	$submit=translate_inline("Submit");
	rawoutput("<input type='submit' value='$submit' class='button'>");
	rawoutput("</form>");
	Session::set('stagecompleted', $stage - 1);
}else{
	Session::set('stagecompleted', $stage);
	header("Location: installer.php?stage=".($stage+1));
	exit();
}
?>

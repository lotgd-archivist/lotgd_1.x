<?php
// translator ready
// addnews ready
// mail ready
define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);
require_once("common.php");
require_once("lib/errorhandling.php");
require_once("lib/http.php");

tlschema("source");

$url=httpget('url');
if ($url) $forurl = " for $url";

popup_header("Source code$forurl");

if ($session['user']['loggedin'] &&
		$session['user']['superuser'] & SU_VIEW_SOURCE) {
	$dir = str_replace("\\","/",dirname($url)."/");
	$subdir = str_replace("\\","/",dirname($_SERVER['SCRIPT_NAME'])."/");
	$legal_dirs = array(
			$subdir."" => 1,
			$subdir."lib/" => 1,
			$subdir."modules/" => 1
	);
	$illegal_files = array(
			($subdir=="//"?"/":$subdir)."dbconnect.php"=>"Contains sensitive information specific to this installation.",
			($subdir=="//"?"/":$subdir)."dragon.php"=>"If you want to read the dragon script, I suggest you do so by defeating it!",
			($subdir=="//"?"/":$subdir)."output_translator.php"=>"X", // hidden
			($subdir=="//"?"/":$subdir)."pavilion.php"=>"Not released at least for now.",
			($subdir=="//"?"/":$subdir)."source.php"=>"X", //hide completely -- so that people can't see the names of the other completley hidden files.
			($subdir=="//"?"/":$subdir)."remotebackup.php"=>"X", // hide completely
			($subdir=="//"?"/":$subdir)."remotequery.php"=>"X", // hide completely

			($subdir=="//"?"/":$subdir)."lib/datatable.php"=>"X", // hide completely
			($subdir=="//"?"/":$subdir)."lib/dbremote.php"=>"X", //hide completely
			($subdir=="//"?"/":$subdir)."lib/smsnotify.php"=>"X", //hide completely
			($subdir=="//"?"/":$subdir)."modules/addgems.php"=>"X", // hidden
			($subdir=="//"?"/":$subdir)."modules/advertising.php"=>"X", // hidden
			($subdir=="//"?"/":$subdir)."modules/advertising_amazon.php"=>"X", // hidden
			($subdir=="//"?"/":$subdir)."modules/advertising_google.php"=>"X", // hidden
			($subdir=="//"?"/":$subdir)."modules/advertising_splitreason.php"=>"X", // hidden
			($subdir=="//"?"/":$subdir)."modules/avatar.php"=>"X", // hidden
			($subdir=="//"?"/":$subdir)."modules/battlearena.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/blog.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/clues.php"=>"X", // hidden
			($subdir=="//"?"/":$subdir)."modules/extrafights.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/faq_central-server.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/funddrive.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/gardener.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/golinda.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/holiday_leet.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/lycanthropy.php"=>"X", // hidden
			($subdir=="//"?"/":$subdir)."modules/mutagens.php"=>"X", // hidden
			($subdir=="//"?"/":$subdir)."modules/namecolor.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/namedmount.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/pointswage.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/privacy.php"=>"X", // hidden
			($subdir=="//"?"/":$subdir)."modules/store.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/topwebgames.php"=>"X", // not for dist
			($subdir=="//"?"/":$subdir)."modules/tournament.php"=>"X", // hide
	);
	$legal_files=array();

	rawoutput("<h1>");
	output("View Source: ");
	output_notl("%s", htmlentities($url));
	rawoutput("</h1>");
	output("<a href='#source'>Click here for the source,</a> OR`n", true);
	output("`bOther files that you may wish to view the source of:`b");
	rawoutput("<ul>");
	while (list($key,$val)=each($legal_dirs)){
		$skey = substr($key,strlen($subdir));
		if ($key==dirname($_SERVER['SCRIPT_NAME'])) $skey="";
		$d = dir("./$skey");
		if (substr($key,0,2)=="//") $key = substr($key,1);
		if ($key=="//") $key="/";
		// Gaurentee a sort order on source files - Hidehisa Yasuda
		$files = array();
		while (false !== ($entry = $d->read())) {
			if (substr($entry,strrpos($entry,"."))==".php"){
				array_push($files, $entry);
			}
		}
		$d->close();
		asort($files);
		foreach($files as $entry) {
			if ($illegal_files["$key$entry"]!=""){
				if ($illegal_files["$key$entry"]=="X"){
					//we're hiding the file completely.
				}else{
					rawoutput("<li>$skey$entry");
					$reason = translate_inline($illegal_files[$key . $entry]);
					output("&#151; This file cannot be viewed: %s", $reason, true);
					rawoutput("</li>\n");
				}
			}else{
				rawoutput("<li><a href='source.php?url=$key$entry'>$skey$entry</a> &#151; ".date("Y-m-d H:i:s",filemtime($skey.$entry))."</li>\n");
				$legal_files["$key$entry"]=true;
			}
		}
	}
	rawoutput("</ul>");

	rawoutput("<h1><a name='source'>");
	output("Source of: %s", htmlentities($url));
	rawoutput("</a></h1>");

	$page_name = substr($url,strlen($subdir)-1);
	if (substr($page_name,0,1)=="/") $page_name=substr($page_name,1);
	if ($legal_files[$url]){ 
		rawoutput("<table bgcolor=#cccccc>");
		rawoutput("<tr><td>");
		rawoutput("<font size=-1>");
		ob_start();
		show_source($page_name);
		$t = ob_get_contents();
		ob_end_clean();
		rawoutput($t);
		rawoutput("</font>", true);
		rawoutput("</td></tr></table>", true);
	}else if ($illegal_files[$url]!="" && $illegal_files[$url]!="X"){
		$reason = translate_inline($illegal_files[$url]);
		output("`nCannot view this file: %s`n", $reason);
	}else {
		output("`nCannot view this file.`n");;
	}
} else {
	output("Due to the behaviour of people in the past, access to the source code online has been restricted.");
	output("You may download the entirety of the latest publically released stable version from <a href='http://sourceforge.net/projects/lotgd' target='_blank'>SourceForge</a> and the most recent version of the pre-release code from <a href='http://dragonprime.net/users/Kendaer/' target='_blank'>Dragonprime</a>.", true);
	output("You may then work with that code within the restrictions of its license.");
	output("`n`nHopefully this will help put an end to actions like the following:");
	rawoutput("<ul><li>");
	output("Releasing code which they do not own without permission.");
	rawoutput("</li><li>");
	output("Removing valid copyright information from code and replacing it.");
	rawoutput("</li><li>");
	output("Removing portions of the code required to be kept intact by licensing.");
	rawoutput("</li><li>");
	output("Claiming copyright of items which they did not create.");
	rawoutput("</li></ul>");
}
popup_footer();
?>
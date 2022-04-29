<?php
// translator ready
// addnews ready
// mail ready
define("ALLOW_ANONYMOUS",true);
require_once("common.php");
require_once("lib/showform.php");
require_once("lib/http.php");

tlschema("about");

page_header("About Legend of the Green Dragon");
$details = gametimedetails();

checkday();
$op = httpget('op');

if ($op==""){
	$order=array("1","2");
	while (list($key,$val)=each($order)){
		switch($val){
		case "2":
			/* NOTICE
			 * NOTICE Server admins may put their own information here,
			 * NOTICE please leave the main about body untouched.
			 * NOTICE
			 */
			rawoutput("<hr>");
			// output("Put your own information here and uncomment this line by removing the '/' marks.");
			break;
		case "1":
			/* NOTICE
			 * NOTICE This section may not be modified, please modify the
			 * NOTICE Server Specific section above.
			 * NOTICE
			 */
			output("`@Legend of the Green Dragon`nBy Eric Stevens & JT Traub`n`n");
			output("`cLoGD version ");
			output_notl("$logd_version`c");
			/*
			 * This section may not be modified, please modify the Server
			 * Specific section above.
			 */
			output("MightyE tells you, \"`2Legend of the Green Dragon is a remake of and homage to the classic BBS Door game, Legend of the Red Dragon (aka LoRD) by <a href='http://www.rtsoft.com' target='_blank'>Seth Able Robinson</a>.`@\"", true);
			output("`n`n`@\"`2LoRD is now owned by Gameport (<a href='http://www.gameport.com/bbs/lord.html' target='_blank'>http://www.gameport.com/bbs/lord.html</a>), and they retain exclusive rights to the LoRD name and game. ", true);
			output("That's why all content in Legend of the Green Dragon is new, with only a very few nods to the original game, such as the buxom barmaid, Violet, and the handsome bard, Seth.`@\"`n`n");
			/*
			 * This section may not be modified, please modify the Server
			 * Specific section above.
			 */
			output("`@\"`2Although serious effort was made to preserve the original feel of the game, numerous departures were taken from the original game to enhance playability, and to adapt it to the web.`@\"`n`n");
			/*
			 * This section may not be modified, please modify the Server
			 * Specific section above.
			 */
			output("`@\"`2LoGD (after version 0.9.7) is released under a <a href='http://creativecommons.org/licenses/by-nc-sa/2.0/' target='_blank'>Creative Commons License</a>, which essentially means that the source code to the game, and all derivatives of the game must remain open and available upon request.", true);
			output("Version 0.9.7 and before are still available under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GNU General Public License</a> though 0.9.7 will be the last release under that license.", true);
			output("To use any of the new features requires using the 0.9.8 code.  You may explicitly not place code from versions after 0.9.7 into 0.9.7 and release the combined derivative work under the GPL.`@\"`n`n", true);
			/*
			 * This section may not be modified, please modify the Server
			 * Specific section above.
			 */
			output("`@\"`2You may download the latest official version of LoGD at <a href='http://sourceforge.net/projects/lotgd' target='_blank'>http://sourceforge.net/projects/lotgd</a>  and you can play the version currently in development at <a href='http://lotgd.net/'>http://lotgd.net</a>.`@\"`n`n",true);
			output("`@\"`2The most recent *UNSTABLE* pre-release snapshot is available from <a href='http://dragonprime.net/users/Kendaer/' target='_blank'>http://dragonprime.net/users/Kendaer/</a>.", true);
			output("You should attempt to use this code only if you are comfortable with PHP and MySQL and willing to manually keep your code up to date.`@\"`n`n");
			/*
			 * This section may not be modified, please modify the Server
			 * Specific section above.
			 */
			output("`@\"`2Additionally, there is an active modder community located at <a href='http://dragonprime.net' target='_blank'>DragonPrime</a> which may help you find additional features which you may wish to add to your game.", true);
			output("These additional features are unsupported by the authors of the core code.`@\"`n`n");
			/*
			 * This section may not be modified, please modify the Server
			 * Specific section above.
			 */
			output("`@\"`2LoGD is programmed in PHP with a MySQL backend.");
			output("It is known to run on Windows and Linux with appropriate setups.");
			output("The core code is actively written by Eric Stevens and JT Traub, with some pieces by other authors (denoted in the source at these locations), and the code has been released under a <a href='http://creativecommons.org/licenses/by-nc-sa/2.0/' target='_blank'>Creative Commons License</a>.", true);
			output("Users of the source are bound to the terms therein.`@\"`n`n",true);
			/*
			 * This section may not be modified, please modify the Server
			 * Specific section above.
			 */
			output("`@\"`2Users of the source are free to view and modify the source, but original copyright information, and original text from the about page must be preserved, though they may be added to.`@\"`n`n");
			output("`@\"`2We hope you enjoy the game!`@\"");
			/*
			 * This section may not be modified, please modify the Server
			 * Specific section above.
			 */
			break;
		}
	}
	addnav("About LoGD");
	addnav("Game Setup Info","about.php?op=setup");
	addnav("Module Info","about.php?op=listmodules");
	addnav("License Info", "about.php?op=license");
	modulehook("about");
}elseif($op=="setup"){
	addnav("About LoGD");
	addnav("About LoGD","about.php");
	addnav("Module Info","about.php?op=listmodules");
	addnav("License Info", "about.php?op=license");
	$setup = array(
		"Game Setup,title",
		"pvp"=>"Enable Slay Other Players,viewonly",
		"pvpday"=>"Player Fights per day,viewonly",
		"pvpimmunity"=>"Days that new players are safe from PvP,viewonly",
		"pvpminexp"=>"Amount of experience when players become killable in PvP,viewonly",
		"soap"=>"Clean user posts (filters bad language and splits words over 45 chars long),viewonly",
		"newplayerstartgold"=>"Amount of gold to start a new character with,viewonly",
		"New Days,title",
		"fightsforinterest"=>"Player must have fewer than how many forest fights to earn interest?,viewonly",
		"maxinterest"=>"Max Interest Rate (%),viewonly",
		"mininterest"=>"Min Interest Rate (%),viewonly",
		"daysperday"=>"Game days per calendar day,viewonly",
		"specialtybonus"=>"Extra daily uses in specialty area,viewonly",

		"Bank settings,title",
		"borrowperlevel"=>"Max amount player can borrow per level,viewonly",
		"allowgoldtransfer"=>"Are players allowed to transfer gold,viewonly",
		"transferperlevel"=>"Max amount player can transfer per level of recipient (if transfers are enabled),viewonly",
		"mintransferlev"=>"Minimum level a player has to be before they can transfer gold (if transfers are enabled),viewonly",
		"transferreceive"=>"Total transfers a player can receive in one play day (if transfers are enabled),viewonly",
		"maxtransferout"=>"Max amount total a player can transfer to others per level (if transfers are enabled),viewonly",
		
		"Forest,title",
		"turns"=>"Forest Fights per day,viewonly",
		"dropmingold"=>"Forest Creatures always drop at least 1/4 of possible gold,viewonly",
		
		"Mail Settings,title",
		"mailsizelimit"=>"Message size limit per message,viewonly",
		"inboxlimit"=>"Limit # of messages in inbox,viewonly",
		"oldmail"=>"Automatically delete old messages after (days),viewonly",
	
		"Content Expiration,title",
		"expirecontent"=>"Days to keep comments and news?  (0 for infinite),viewonly",
		"expiretrashacct"=>"Days to keep accounts that were never logged in to? (0 for infinite),viewonly",
		"expirenewacct"=>"Days to keep level 1 accounts with no dragon kills? (0 for infinite),viewonly",
		"expireoldacct"=>"Days to keep all other accounts? (0 for infinite),viewonly",
		"LOGINTIMEOUT"=>"Seconds of inactivity before auto-logoff,viewonly"
	);

	$useful = array(
		"Useful Information,title",
		"dayduration"=>"Day Duration,viewonly",
		"curgametime"=>"Current game time,viewonly",
		"curservertime"=>"Current Server Time,viewonly",
		"lastnewday"=>"Last new day,viewonly",
		"nextnewday"=>"Next new day,viewonly"
	);

	$secstonextday = secondstonextgameday($details);
	$useful_vals = array(
		"dayduration"=>round(($details['dayduration']/60/60),0)." hours",
		"curgametime"=>getgametime(),
		"curservertime"=>date("Y-m-d h:i:s a"),
		"lastnewday"=>date("h:i:s a",
			strtotime("-{$details['realsecssofartoday']} seconds")),
		"nextnewday"=>date("h:i:s a",
			strtotime("+{$details['realsecstotomorrow']} seconds"))." (".date("H\\h i\\m s\\s",$secstonextday).")"
	);
	
	output("`@<h3>Settings for this game</h3>`n`n",true);

	$args = array('settings'=>array(),'values'=>array());
	$args = modulehook("showsettings", $args);

	$form = array_merge($setup, $args['settings']);
	$form = array_merge($form, $useful);
	$vals = array_merge($settings, $args['values']);
	$vals = array_merge($vals, $useful_vals);
	showform($form,$vals,true);
}elseif($op=="listmodules"){
	addnav("About LoGD");
	addnav("About LoGD","about.php");
	addnav("Game Setup Info","about.php?op=setup");
	addnav("License Info", "about.php?op=license");
	$sql = "SELECT * from " . db_prefix("modules") . " WHERE active=1 ORDER BY category,formalname";
	$result = db_query($sql);
	$mname = translate_inline("Module Name");
	$mver = translate_inline("Version");
	$mauth = translate_inline("Module Author");
	$mdown = translate_inline("Download Location");
	rawoutput("<table border='0' cellpadding='2' cellspacing='1' bgcolor='#999999'>",true);
	rawoutput("<tr class='trhead'><td>$mname</td><td>$mver</td><td>$mauth</td><td>$mdown</td></tr>",true);
	if (db_num_rows($result) == 0) {
		rawoutput("<tr class='trlight'><td colspan='4' align='center'>");
		output("`i-- No modules installed --`i");
		rawoutput("</td></tr>");
	}
	$cat = "";
	for ($i = 0; $i < db_num_rows($result); $i++) {
		$row = db_fetch_assoc($result);
		if ($cat != $row['category']) {
			rawoutput("<tr class='trhead'><td colspan='4' align='left'>");
			output($row['category']);
			rawoutput(":</td></tr>");
			$cat = $row['category'];
		}

		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
		rawoutput("<td valign='top'>");
		output_notl("`&%s`0", $row['formalname']);
		rawoutput("<td valign='top'>",true);
		output_notl("`^%s`0", $row['version']);
		rawoutput("</td><td valign='top'>");
		output_notl("`^%s`0", $row['moduleauthor'], true);
		rawoutput("</td><td nowrap valign='top'>");
		if ($row['download'] == "core_module") {
			rawoutput("<a href='http://dragonprime.net/users/Kendaer' target='_blank'>");
			output("Core Distribution");
			rawoutput("</a>");
		} elseif ($row['download']) {
			rawoutput("<a href='{$row['download']}' target='_blank'>");
			output("Download");
			rawoutput("</a>");
		} else {
			output("`\$Not publically released.`0");
		}
		rawoutput("</td>");
		rawoutput("</tr>");
	}
	rawoutput("</table>");
}elseif ($op=="license"){
	addnav("About LoGD");
	addnav("About LoGD","about.php");
	addnav("Game Setup Info","about.php?op=setup");
	addnav("Module Info","about.php?op=listmodules");
	output("`@MightyE tells you, \"`2We're going to take a few moments to try and explain this new license and the reasons behind it in plain English.");
	output("The legalese for the license can be found online at <a href='http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode' target='_blank'>http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode</a> and should be read and understood in detail before you use this code.`@\"`n`n", true);
	output("`@\"`2This new license was chosen because of a failing with the GPL.");
	output("It only covered distribution of source if and only if binaries were distributed.`@\"`n`n");
	output("`@\"`2In a web environment, specifically an interpreted web environment such as PHP, merely installing a game does not constitute distribution, and therefore people were taking our work, making modifications to it and not releasing the source code to their modifications so that the entire community could benefit.");
	output("They worked with the letter but not the spirit of the law.`@\"`n`n");
	output("`@\"`2Investigation on the part of the authors however, led to the fact that the right of public performance was one of the rights normally restricted to copyright holders, AND that computer programs, specifically video games and interactive ones such as Legend of the Green Dragon were considered to be publically performed if run from a public server.`@\"`n`n");
	output("`@\"`2The new license restricts public performance of the work unless the source code of the modified work is made available on demand.`@\"`n`n");
	output("`@\"`2In plain English, this means that if you put this game on a web server and allow people 'outside of a normal circle of family and its social acquaintances' to play there, then you are publically performing this work and MUST either a) make any and ALL changes which you make to the game available on request (note this doesn't have to be available via the online source display link, but they must be able to ask you for the code AND receive a complete copy), b) make arrangements privately with the authors wherein they grant you a special license, or c) remove the code entirely from the machine.`@\"`n`n");
	output("`@\"`2We do recognize that people want to have areas of their game which are theirs and theirs alone.");
	output("To that end we will make the following exception to the normal requirements for source code distribution -- any module file which is not modified or derived from a module file included in the base distribution AND which does not require any other modules AND which does not require any modifications to the core code (code distributed with the base release) may be withheld at the authors discretion.`@\"`n`n");
	output("`@\"`2We also want to make very clear that version 0.9.7 (also known as version 0.9.7+jt) was the final version released under the GPL.");
	output("All versions, starting with the 0.9.8-prerelease code are only licensed under the Creative Commons license.");
	output("We EXPLICITLY deny the right to import any code from a 0.9.8-prerelease or later release into a 0.9.7 and earlier release.");
	output("Allowing this would cause that imported code to be released under the GPL and that is not something we wish to allow.");
	output("Authors of modifications to 0.9.7 will need to re-release their modifications as derivatives/modifications to 0.9.8 code and place them under the same Creative Commons license.");
	output("It must be done by the original author since only the original author has the right to change the copyright or license upon their work.");
	output("[Additionally, reworking the modifications will be a good idea anyway as the mechanism for making modifications is substantially cleaner/clearer starting with the 0.9.8-prerelease code.]`@\"");
}
if ($session['user']['loggedin']) {
	addnav("Return to the news","news.php");
}else{
	addnav("Login Page","index.php");
}
page_footer();
?>

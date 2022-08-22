<?php
// PRIVATE_CODE
// addnews ready
// mail ready
// translator ready
//Tournament Mode LoGD
//This file is NOT FOR PUBLIC RELEASE
//This file is NOT GPL'd
//If you have this file and have not been specifically granted permission
//to use or distribute it, you may not use nor distribute it, nor may you make
//derivative works based on this file.  Permission can be granted only by Eric
//Stevens.  Unauthorized use or access to this file is strictly prohibited.
require_once("lib/http.php");
require_once("lib/villagenav.php");

//local defines for tournament mode
define("MOD_TOURNAMENT_LIMBO",2);
define("MOD_TOURNAMENT_NORMAL",1);
define("MOD_TOURNAMENT_IN_CURRENT_ROUND",256);
define("MOD_TOURNAMENT_NOT_IN_CURRENT_ROUND",512);
define("MOD_TOURNAMENT_HIDE_FROM_LEADERBOARD",SU_HIDE_FROM_LEADERBOARD & ~SU_INFINITE_DAYS);
define("MOD_TOURNAMENT_LB_WHERE","(superuser & ".MOD_TOURNAMENT_HIDE_FROM_LEADERBOARD.")=0 AND (dragonkills > 0 OR experience > 0)");

function tournament_getmoduleinfo(){
	$info = array(
		"name"=>"Tournament Mode LoGD",
		"author"=>"Eric Stevens",
		"category"=>"Tournament",
		"version"=>"1.0",
		"settings"=>array(
			"Tournament Mode Settings,title",
			"Caution: characters are regularly expunged (deleted) in tournament mode.`n`n
			`bRecommended Content Expiration settings`b: (in the game configuration)`n
			Days to keep never logged-in accounts: 0 (which is infinite)`n
			Days to keep level 1 (0 DK) accounts: 0 (which is infinite)`n
			Days to keep all other accounts: 0 (which is infinite)`n`n
			If you are starting a new tournament - it's best to enter the round start date as today minus the number of days that a round lasts.
			,note",
			
			"Tournament Timing,title",
			"The new round will start on the first new day AFTER the tournament start date/time.  This time is in local server time.,note",
			"tourney-start"=>"This round started on? (date)|".date("Y-m-d 00:00:00",strtotime("-1 month")),
			"tourney-end"=>"This round will end on? (date)|".date("Y-m-d H 00:00:00"),
			"tourney-length"=>"Duration of each tournament round (eg: 30 days; 1 month; 4 weeks)|1 month",
			"limbo-length"=>"Length of between-round limbo. (eg: 3 days; 1 week; 24 hours)|3 days",
			"tourney-roundnum"=>"Tournament round number:,viewonly|1",
			
			"Tournament Expiration,title",
			"tourney-lbmonths"=>"Months to keep old leaderboards? (takes effect next new round),int|6",
			
			"Tournament Cost,title",
			"roundpointcost"=>"Number of donator points each round costs?|300",
			
			"Tournament Main Server,title",
			"tourney-mainname"=>"Name of 'main' server to display on character creation page.|Legend of the Green Dragon Central Server",
			"tourney-mainlink"=>"Link to 'main' server.|http://lotgd.net/",
			"tourney-mainprefix"=>"Prefix for tables on main server.|",
			
			"Badguy Buffage,title",
			"buffpct"=>"Percent to buff badguys,range,0,100,1|15",
		),
		"prefs"=>array(
			"Tournament User Preferences,title",
			"verified"=>"Rounds of tournament play the player has paid for,int|0",
			"lastroundplayed"=>"Last round the user participated in,int|-1",
			"lastnewgamestart"=>"Last date this user started a new game|0000-00-00",
		)
	);
	return $info;
}

function tournament_install(){
	module_addhook("newday-intercept");
	module_addhook("village");
	module_addhook("superuser");
	module_addhook("buffbadguy");
	if (!db_table_exists(db_prefix("tournamentLB"))){
		$sql = "
			CREATE TABLE " . db_prefix("tournamentLB") . " (
				round int(11) unsigned not null,
				rank tinyint(4) unsigned not null,
				name varchar(25) not null,
				stats text,
				roundstart datetime,
				roundend datetime,
				
				PRIMARY KEY (round,rank)
			)";
			db_query($sql);
	}
	return true;
}

function tournament_uninstall(){
	$sql = "DROP TABLE IF EXISTS " . db_prefix("tournamentLB");
	db_query($sql);
	return true;
}

function tournament_dohook($hook,$args){
	global $session;
	switch($hook){
	case "superuser":
		if ($session['user']['superuser'] & SU_EDIT_CONFIG) addnav("Tournament - End This Round","runmodule.php?module=tournament&op=endround");
		break;
	case "newday-intercept":
		tournament_check_end_of_round();
		tournament_check_can_play();
		if (httpget('resurrection')!="true" && $session['user']['alive']!=true) {
			// Player is dead, in tournament mode, and not being resurrected.
			tournament_newday_intercept();
		}
		if (tournament_status() & MOD_TOURNAMENT_LIMBO)
			redirect("runmodule.php?module=tournament&op=limbo");
		break;
	case "village":
		addnav("Info");
		addnav("L?Tournament Leaderboard","runmodule.php?module=tournament&op=leaderboard");
		if (tournament_status() & MOD_TOURNAMENT_LIMBO){
			addnav("Tournament is in Limbo Mode");
			addnav("Return to Limbo","runmodule.php?module=tournament&op=limbo");
		}
		break;
	case "buffbadguy":
		$baseatk = $args['creatureattack'];
		$basedef = $args['creaturedefense'];
		$basehealth = $args['creaturehealth'];
		$baseline = $args['creatureattack'] + $args['creaturedefense'] + $args['creaturehealth']*10;
		while ( ($args['creatureattack'] + $args['creaturedefense'] + $args['creaturehealth']*10) / $baseline < (1 + get_module_setting("buffpct") /100) ){
			$r = e_rand(1,3);
			switch($r){
			case 1:
				$args['creatureattack']+=$baseatk*0.01;
				break;
			case 2:
				$args['creaturedefense']+=$basedef*0.01;
				break;
			case 3:
				$args['creaturehealth']+=$basehealth*0.1;
				break;
			}
		}
		$args['creaturehealth'] = round($args['creaturehealth'],0);
	}
	return $args;
}

function tournament_check_can_play(){
	if (tournament_status() & MOD_TOURNAMENT_IN_CURRENT_ROUND) return true;
	$boughtrounds = get_module_pref("verified");
	if ($boughtrounds <= 0){
		//they are not yet playing this round, and have purchased no spare rounds.
		page_header("Tournament Mode is Pay-Per-Play");
		output("`@Tournament mode LoGD is a pay-per-play gaming experience.");
		output("In order to help fund the development of <a href='%s' target='_blank'>Legend of the Green Dragon</a>, we run a modified ruleset of competetive gameplay, which costs %s donator points per round to play.", get_module_setting("tourney-mainlink"), get_module_setting("roundpointcost"), true);
		output("Donator points can be acquired via a number of ways, which you can read up about by visiting the Hunter's Lodge on <a href='%s' target='_blank'>our main server</a>.", get_module_setting("tourney-mainlink"), true);
		output("The easiest way of getting donator points is to donate money; you get 100 points per dollar donated.");
		output("However, not all means of earning donator points involve a financial obligation.");
		output("Points from our main server can be used to purchase rounds of game play in the tournament.");
		output("At the end of each tournament round, the game resets, a snapshot is taken of the leaderboard from the previous round, and everyone starts fresh.");
		output("The object of course is to rank the highest on the leaderboard.`n`n");
		output("You are seeing this message because your account has no unused rounds purchased.");
		output("You may use your points from %s`@ to purchase rounds by visiting your tournament user prefs (there is a link to this located in your main nav bar.", get_module_setting("tourney-mainname"));
		addnav("Tournament User Prefs","runmodule.php?module=tournament&op=prefs");
		addnav("Log Out","login.php?op=logout");
		if ($session['user']['superuser'] & SU_EDIT_USERS){
			addnav("Superuser Free Start","runmodule.php?module=tournament&op=start&free=yes");
		}
	}else{
		page_header("It is a new round!");
		output("`@There is a new round in progress, but you have not signed up for it yet.");
		output("You have `%%s`@ unused rounds remaining.", $boughtrounds);
		output("You can start playing in this round by selecting \"Start This Round\" from your nav.");
		addnav("Tournament User Prefs","runmodule.php?module=tournament&op=prefs");
		addnav("Log Out","login.php?op=logout");
		addnav("Start This Round");
		addnav("Start This Round","runmodule.php?module=tournament&op=start");
		global $session;
		if ($session['user']['superuser'] & SU_EDIT_USERS){
			addnav("Superuser Free Start","runmodule.php?module=tournament&op=start&free=yes");
		}
	}
	addnav("Tournament Leaderboard","runmodule.php?module=tournament&op=leaderboard");
	$roundstart = strtotime(get_module_setting("tourney-start"));
	$roundend = strtotime(get_module_setting("tourney-end"));
	$limbostart = $roundend;
	$limboend = strtotime(get_module_setting("tourney-length"),$roundstart);
	require_once("lib/dhms.php");
	if (tournament_status() & MOD_TOURNAMENT_LIMBO){
		output("The tournament is between rounds in limbo mode..");
		output("The next round will start on %s, and will end on %s.", date("D M d, Y h:i a", $roundstart), date("D M d, Y h:i a", $roundend));
		output("Limbo will end in %s.",reltime($limboend,false));
	}else{
		output("There is a tournament currently in progress.");
		output("This round started on %s (%s), and will end on %s (%s).",date("D M d, Y h:i a",$roundstart),reltime($roundstart,false),date("D M d, Y h:i a",$roundend),reltime($roundend,false));
	}
	
	page_footer();
	return false; //never will happen, but might as well put it here :-)
}

function tournament_check_end_of_round(){
	//this function handles end-of-round rotation.  Typically called on
	//new day, all accounts are sent to limbo at the end of this.
	//The new round will start after limbo is over.
	$start = strtotime(get_module_setting("tourney-start"));
	$end = strtotime(get_module_setting("tourney-end"));
	echo "Now: ".date("Y-m-d H:i:s")."<br>";
	echo "Start: ".date("Y-m-d H:i:s",$start)."<br>End:".date("Y-m-d H:i:s",$end)."<br>";
	if ($start > strtotime("now")){
		$next = $start;
	}else{
		$next = strtotime(get_module_setting("tourney-length"),$start);
	}
	echo "Next: ".date("Y-m-d H:i:s",$next)."<br>";
	//$end = strtotime("-1 day",$end);
	debug("Round End: ".date("Y-m-d H:i:s",$end)."`n");
	$roundnum = get_module_setting("tourney-roundnum");
	if (date("YmdHis") > date("YmdHis",$next)){
		$sql = "LOCK TABLES " . db_prefix("tournamentLB") . " WRITE," . db_prefix("module_settings") . " WRITE, " . db_prefix("modules") . " WRITE";
		db_query($sql);
		clear_module_settings();
		$newroundnum = get_module_setting("tourney-roundnum");
		if ($newroundnum == $roundnum){
			//we know we're the ones who have responsibility to rotate rounds.
			set_module_setting("tourney-roundnum",$roundnum+1);
			
			// this done we can unlock the tables to keep this lock as short
			// as possible.
			$sql = "UNLOCK TABLES";
			db_query($sql);

			//redirect all existing accounts to limbo.
			$body = '<html><head><META HTTP-EQUIV=Refresh CONTENT="0; URL=runmodule.php?module=tournament&op=limbo"></head><body>If your browser does not redirect automatically, please <a href="runmodule.php?module=tournament&op=limbo">click here</a>.</body></html>';
			$sql = "UPDATE " . db_prefix("accounts") . " SET allowednavs='".serialize(array("runmodule.php?module=tournament&op=limbo"=>true))."',output='$body',restorepage='news.php',lasthit='0000-00-00 00:00:00'";
			db_query($sql);
			
			//establish our leaderboard
			//first make sure we don't have a round by this number here already!
			$sql = "DELETE FROM " . db_prefix("tournamentLB") . " WHERE round='$roundnum'";
			db_query($sql);
			//next insert values in to the LB.
			$sql = "SELECT name, dragonkills, level, experience FROM " . db_prefix("accounts") . " WHERE ".MOD_TOURNAMENT_LB_WHERE." ORDER BY dragonkills DESC, level DESC, experience DESC LIMIT 25";
			$result = db_query($sql);
			for ($i=0;$i<db_num_rows($result);$i++){
				$row = db_fetch_assoc($result);
				$sql = "INSERT INTO " . db_prefix("tournamentLB") . " (round, rank, name, stats, roundstart, roundend) VALUES ($roundnum, $i, '".addslashes($row['name'])."', '".addslashes(serialize($row))."', '".date("Y-m-d H:i:s",$start)."', '".date("Y-m-d H:i:s",$end)."')";
				db_query($sql);
			}
			$now = date("Y-m-d H:i:s"); //date("Y-m-d H:i:s",strtotime("-".get_module_setting("limbo-length")));
			while (
				get_module_setting("tourney-start")
				 < $now){
				//in case no one logged in for the duration of an entire round, we should keep advancing the new round dates until we reach the round we're currently in.
				echo "now = $now, end = ".get_module_setting("tourney-end")."<br>";
				$start = strtotime(get_module_setting("tourney-start"));
				$end = strtotime(get_module_setting("tourney-length"),$start);
				$end = strtotime("-".get_module_setting("limbo-length"),$end);
				set_module_setting("tourney-start",date("Y-m-d H:i:s",$end));
				set_module_setting("tourney-end",date("Y-m-d H:i:s",$end));
			}
						
		}else{
			//someone else beat us to it.
			$sql = "UNLOCK TABLES";
			db_query($sql);
		}
		//go to limbo, we have no place here.
	}
}

function tournament_newday_intercept(){
	global $session;
	if (httpget("forcealive")==1){
		$session['user']['hitpoints']=$session['user']['maxhitpoints'];
		$session['user']['alive'] = true;
		echo "Go to new day";
		redirect("newday.php");
	}
	$death = getsetting('deathoverlord', '`$Ramius');
	page_header("It is a new day!");
	output("`)A new day has been bestowed upon the living, but your soul continues to linger in the afterlife.`n`n");
	output("Perhaps soon %s`)will find favor with you and return your soul to the living.`n`n", $death);
	output("`2Bankers in the village only give interest to the living.`n");
	$session['user']['soulpoints']=50 + 5 * $session['user']['level'];
	$session['user']['gravefights']=getsetting("gravefightsperday",10);
	$session['user']['recentcomments']=$session['user']['lasthit'];
	$session['user']['deathpower']+=25;
	$session['user']['lasthit'] = date("Y-m-d H:i:s");
	output("%s`2 grants you `^%s`2 soulpoints!`n",$death,$session['user']['soulpoints']);
	output("%s`2 grants you `^%s`2 grave fights!`n",$death,$session['user']['gravefights']);
	output("%s`2 grants you `^25`2 favor for lingering in the afterlife!`n", $death);
	$rp = $session['user']['restorepage'];
	$x = max(strrpos("&",$rp),strrpos("?",$rp));
	if ($x>0) $rp = substr($rp,0,$x);
	if (substr($rp,0,10)=="badnav.php"){
		addnav("Continue","graveyard.php");
	}else{
		addnav("Continue",preg_replace("'[?&][c][=].+'","",$rp));
	}
	if ($session['user']['superuser'] & SU_INFINITE_DAYS){
		addnav("Superuser");
		addnav("Make Me Live","newday.php?forcealive=1");
		addnav("Simulate Resurrection","newday.php?resurrection=true");
	}
	page_footer();
	exit();
}

function tournament_leaderboard(){
	global $session;

	$round = httpget("round");
	if (tournament_status() & MOD_TOURNAMENT_LIMBO){
		if ($round==""){
		 	//in limbo mode, blank round means show highest round number.
			$sql = "SELECT max(round) AS round FROM " . db_prefix("tournamentLB");
			$result = db_query($sql);
			$row = db_fetch_assoc($result);
			$round = $row['round'];
		}
	}

	if ($round > ""){
		$sql = "SELECT * FROM " . db_prefix("tournamentLB") . " WHERE round='$round' ORDER BY rank";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$start = strtotime($row['roundstart']);
		$end = strtotime($row['roundend']);
		$roundnum = (int)$row['round'];
	}else{
		$lbmask = SU_HIDE_FROM_LEADERBOARD & ~SU_INFINITE_DAYS;
		$sql = "SELECT name, dragonkills, level, experience FROM " . db_prefix("accounts") . " WHERE ".MOD_TOURNAMENT_LB_WHERE." ORDER BY dragonkills DESC, level DESC, experience DESC LIMIT 25";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$start = strtotime(get_module_setting("tourney-start"));
		$end = strtotime(get_module_setting("tourney-end"));
		$roundnum = get_module_setting("tourney-roundnum");
	}
	
	page_header("Tournament Statistics");
	addnav("Tournament Preferences","runmodule.php?module=tournament&op=prefs");
	output("`7`bTournament Settings:`b`n");
	output("Round #%s`n",$roundnum);
	output("Start date of this round: %s`n",date("D M d, Y h:i a",$start));
	output("End date of this round: %s`n",date("D M d, Y h:i a",$end));
	
	if ($roundnum == get_module_setting("tourney-roundnum")){
		output("`n`bCurrent Leaderboard:`b`n");
		$current = true;
	}else{
		output("`n`bLeaderboard for Round %s:`b`n",$roundnum);
		$current = false;
	}
	rawoutput("<table border='0' cellpadding='3' cellspacing='0'>");
	rawoutput("<tr class='trhead'><td>".tl('Rank')."</td><td>".tl('Name')."</td><td>".tl('Dragon Kills')."</td><td>".tl('Level')."</td><td>".tl('Experience')."</td></tr>");
	for ($i=0;$i<db_num_rows($result);$i++){
		if ($i>0) $row = db_fetch_assoc($result);
		if (httpget("round")>"") $row = unserialize($row['stats']);
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
		rawoutput("<td>".($i+1)."</td><td>");
		output_notl("%s", $row['name']);
		rawoutput("</td><td>{$row['dragonkills']}</td><td>{$row['level']}</td>");
		if (!$current) 
			rawoutput("<td>{$row['experience']}</td>");
		else
			rawoutput("<td>N/A</td>");
		rawoutput("</tr>",true);
	}
	rawoutput("</table>");
	$sql = "SELECT DISTINCT round FROM " . db_prefix("tournamentLB") . " ORDER BY round DESC";
	$result = db_query($sql);
	addnav("Rounds");
	$round = get_module_setting("tourney-roundnum");
	if (tournament_status() & MOD_TOURNAMENT_NORMAL) addnav("Round {$round} (Current)","runmodule.php?module=tournament&op=leaderboard");
	while ($row = db_fetch_assoc($result)){
		addnav(array("Round %s",$row['round']),"runmodule.php?module=tournament&op=leaderboard&round={$row['round']}");
	}
	addnav("Return");
	if (tournament_status() & MOD_TOURNAMENT_LIMBO){
		addnav("L?Return to Limbo","runmodule.php?module=tournament&op=limbo");
	}elseif ($session['user']['alive']){
		villagenav();
	}else{
		addnav("V?Return to the Shades","shades.php");
	}
	page_footer();
}

function tournament_limbo(){
	global $session;
	$status = tournament_status();
	if ($status & MOD_TOURNAMENT_NORMAL){
		tournament_checkstartround();
		//addnav("This round has started!","newday.php");
		$session['user']['restorepage']='news.php';
		redirect("newday.php");
	}
	require_once("lib/commentary.php");
	require_once("lib/dhms.php");
	addcommentary();
	page_header("Tournament Limbo");
	$roundstart = strtotime(get_module_setting("tourney-start"));
	$roundend = strtotime(get_module_setting("tourney-length"),$roundstart);
	$limbostart = strtotime("-".get_module_setting("limbo-length"),$roundend);
	$limboend = strtotime(get_module_setting("limbo-length"),$limbostart);
	if ($status & MOD_TOURNAMENT_NORMAL){
	 	output("You are in the limbo between tournament rounds.");
		output("The current round started on %s, and will end on %s.`n`n", date("D M d, Y h:i a",$roundstart),date("D M d, Y h:i a",$limbostart));
		output("The round has been in progress for %s.`n",reltime($roundend,false));
		output("The round will end in %s.",reltime($limbostart,false));
	}else{
	 	output("You are in the limbo between tournament rounds.");
		output("The next round will start on %s, and will end on %s.`n`n",date("D M d, Y h:i a",$roundstart),date("D M d, Y h:i a",$roundend));
		output("Limbo will end in %s.",dhms($roundend-strtotime("now"),false));
	}
	output_notl("`n`n");
	addnav("Limbo");
	addnav("Leaderboard","runmodule.php?module=tournament&op=leaderboard");
	addnav("Do Nothing","runmodule.php?module=tournament&op=limbo");
	addnav("Quit","runmodule.php?module=tournament&op=logout");
	if ($session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO){
		addnav("Superuser only:");
		addnav("X?`bSuperuser Grotto`b","superuser.php");
		if ($session['user']['superuser'] & SU_EDIT_CONFIG) addnav("Force New Round Start","runmodule.php?module=tournament&op=beginround");
		if ($status & MOD_TOURNAMENT_IN_CURRENT_ROUND){
			villagenav();
		}else{
			addnav("Tournament Preferences","runmodule.php?module=tournament&op=prefs");
		}
	}
	commentdisplay("", "limbo","Talk");
	page_footer();
}

function tournament_checkstartround(){
	$laststart = get_module_setting("laststart");
	$round = get_module_setting("tourney-roundnum");
	if ($round != $laststart){
		//Looks like we're probably supposed to start the round.
		clear_module_settings();
		$sql = "LOCK TABLE " . db_prefix("module_settings") . " WRITE," . db_prefix("modules") . " READ";
		db_query($sql);
		$laststart = get_module_setting("laststart");
		if ($laststart != $round){
			//we have responsibility, update the laststart field and unlock
			//tables.
			set_module_setting("laststart",$round);
			$sql = "UNLOCK TABLES";
			db_query($sql);
			tournament_newround();
		}else{
			$sql = "UNLOCK TABLES";
			db_query($sql);
		}
		//send this user to the char first-login screen. w/o saving.
		//header("Location: newday.php");
		$session['user']['restorepage']='news.php';
		//echo "go to new day x=checkroundstart<br>";
		redirect("newday.php?x=checkroundstart");
	}
}

function tournament_newround(){
	//Time to charge accounts and clean up data.
	//collect the account ID's
	global $session;
	$defaltsu = SU_HIDE_FROM_LEADERBOARD & ~SU_INFINITE_DAYS;
	$sql = "SELECT acctid FROM " .db_prefix("accounts") . " WHERE ".MOD_TOURNAMENT_LB_WHERE;
	$result = db_query($sql);
	$accts = array(0);
	if (db_num_rows($result) > 0) {
		while ($row = db_fetch_assoc($result)){
			array_push($accts,$row['acctid']);
		}
	}
	$accts = join(",\n",$accts);
	$nochange=array(
		"acctid"=>1,
		"name"=>1, //reset by default
		"sex"=>1,
		"password"=>1,
		"title"=>1, //reset by default
		"gold"=>1, //reset by default
		"login"=>1,
		"locked"=>1,
		"restorepage"=>1, //reset by default
		"loggedin"=>1,
		"superuser"=>1,
		"gentime"=>1,
		"gentimecount"=>1,
		"lastip"=>1,
		"uniqueid"=>1,
		"prefs"=>1,
		"lastmotd"=>1,
		"emailaddress"=>1,
		"emailvalidation"=>1,
		"gensize"=>1,
		"donation"=>1,
		"donationspent"=>1,
		"donationconfig"=>1,
		"bio"=>1,
		"banoverride"=>1,
		"referer"=>1,
		"refererawarded"=>1,
		"biotime"=>1,
		"recentcomments"=>1,
		"clanid"=>1, //? Should we keep clans across rounds?
		"clanrank"=>1, //?
		"clanjoindate"=>1,//?
		"laston"=>1
	);
	$sql = "DESCRIBE " . db_prefix("accounts");
	$result = db_query($sql);
	require_once("lib/titles.php");
	$sql = "";
	while ($row = db_fetch_assoc($result)){
		if ($nochange[$row['Field']]){
		
		}else{
			$sql.="{$row['Field']} = '".addslashes($row['Default'])."', \n";
			if ($session['user']['superuser'] & $defaultsu == 0) $session['user'][$row['Field']] = $row['Default'];
		}
	}
	$mtitle = get_dk_title(0, 0);
	$ftitle = get_dk_title(0, 1);

	$sql = "UPDATE " . db_prefix("accounts") . " SET " . $sql . " title=CASE sex WHEN 0 THEN '$mtitle' WHEN 1 THEN '$ftitle' END, name=concat(title,' ',login), gold='".getsetting("newplayerstartgold",50)."', restorepage='news.php' WHERE acctid IN ($accts)";
	db_query($sql);
	if ($session['user']['superuser'] & MOD_TOURNAMENT_HIDE_FROM_LEADERBOARD > 0){

	}else{
		$session['user']['title'] = get_dk_title(0, $session['user']['sex']);
		$session['user']['name'] = $session['user']['title']." ".$session['user']['login'];
		$session['user']['gold'] = getsetting("newplayerstartgold",50);
		$session['user']['restorepage']="news.php";
		//echo "go to newday, x=tournament_newround<br>";
		redirect("newday.php?x=tournament_newround");
	}
}

function tournament_status(){
	$current = date("Y-m-d H:i:s");
	$start = get_module_setting("tourney-start");
	$end = get_module_setting("tourney-end");
    if ($current < $start || $current > $end){
		debug("Limbo -- now = $current, start1 = $start1, start2 = $start2");
		$return = MOD_TOURNAMENT_LIMBO;
	}else{
		debug("Normal -- now = $current, start1 = $start1, start2 = $start2");
		$return = MOD_TOURNAMENT_NORMAL;
	}
	$lastround = get_module_pref("lastroundplayed");
	$thisround = get_module_setting("tourney-roundnum");
	if ($lastround==$thisround) {
		$return = $return | MOD_TOURNAMENT_IN_CURRENT_ROUND;
	}else{
		$return = $return | MOD_TOURNAMENT_NOT_IN_CURRENT_ROUND;
	}
	return $return;
}

function tournament_run(){
	global $session;
	switch(httpget('op')){
	case "endround":
		//$session['user']['restorepage']='superuser.php';
		//tournament_newround();
		$end = date("Y-m-d H:i:s");
		set_module_setting("tourney-end",$end);

		page_header("Round Ended");
		output("You've ended the tournament round.");
		addnav("Continue");
		addnav("Continue","newday.php");
		page_footer();
		break;
	case "beginround":
		//$start = date("Y-m-d H:i:s");
		//$end = strtotime("-".get_module_setting("tourney-length"),strtotime($start));
		$start = strtotime("-".get_module_setting("tourney-length"));
		$end = strtotime("-".get_module_setting("limbo-length"));
		$start = date("Y-m-d H:i:s",$start);
		$end = date("Y-m-d H:i:s",$end);
		set_module_setting("tourney-start",$start);
		set_module_setting("tourney-end",$end);
		page_header("Round Started");
		output("You've started a new round.");
		addnav("Continue");
		addnav("Continue","newday.php");
		page_footer();
		break;
	case "leaderboard":
		tournament_leaderboard();
		break;
	case "limbo":
		tournament_limbo();
		break;
	case "logout":
		$session['user']['loggedin']=0;
		$session['user']['restorepage']="runmodule.php?module=tournament&op=limbo";
		saveuser();
		$session=array();
		header("Location: index.php");
		break;
	case "prefs":
		page_header("Tournament User Prefs");
		$rounds = get_module_pref("verified");
		output("`@You have `%%s`@ purchased rounds left.",$rounds);
		rawoutput("<form action='runmodule.php?module=tournament&op=buyrounds' method='post' onSubmit='md5pass();'>");
		addnav("","runmodule.php?module=tournament&op=buyrounds");
		output("`@`bPurchase additional rounds`b:`n");
		output("`2You must enter your login information for `3%s `6(%s)`2:`n",get_module_setting("tourney-mainname"),get_module_setting("tourney-mainlink"));
		output("`n<u>U</u>sername:",true);
		rawoutput("<input name='name' id='name' accesskey='u' size='10'>");
		output("`n<u>P</u>assword:",true);
		rawoutput("<input name='password' id='password' type='password' accesskey='p' size='10'>");
		output("`nHow many rounds do you wish to buy?");
		rawoutput("<select name='rounds'><option value='1'>1<option value='2'>2<option value='3'>3<option value='4'>4<option value='5'>5</select><br>");
		output("(Each round costs %s donator points)`n",get_module_setting("roundpointcost"));
		$buy = translate_inline("Buy");
		rawoutput("<input type='submit' class='button' value='$buy'>");
		rawoutput("</form>");
		rawoutput("<script language='JavaScript' src='lib/md5.js'></script>");
		rawoutput("<script language='JavaScript'>
		<!--
		function md5pass(){
			//encode passwords before submission to protect them even from
			//network sniffing attacks.
			var passbox = document.getElementById('password');
			if (passbox.value.substring(0, 5) != '!md5!') {
				passbox.value = '!md5!' + hex_md5(passbox.value);
			}
		}
		//-->
		</script>");
		addnav("Continue","village.php");
		$boughtrounds = get_module_pref("verified");
		if (!(tournament_status() & MOD_TOURNAMENT_IN_CURRENT_ROUND) &&
				$boughtrounds>0){
			addnav(array("You have %s unspent rounds",$boughtrounds));
			addnav("Start this round","runmodule.php?module=tournament&op=start");
		}
		page_footer();
		break;
	case "buyrounds":
		$cost = get_module_setting("roundpointcost") * httppost("rounds");
		page_header("Purchase Rounds");
		output_notl("`@");
		require_once("lib/dump_item.php");
		$password = httppost("password");
		if (substr($password,0,5)=="!md5!"){
			$password = md5(substr($password,5));
		}else{
			$password = md5(md5($password));
		}

		$sql = "SELECT acctid,login,name,password,donation,donationspent FROM " . db_prefix("accounts", get_module_setting("tourney-mainprefix")) . " WHERE login='".httppost('name')."'";
		$result = db_query_remote($sql,get_module_setting("tourney-mainlink"),60);
		$noauth = false;
		if (db_num_rows($result)<=0){
			debug("Account not found");
			$noauth = true;
		}else{
			$row = db_fetch_assoc($result);
			if ($row['password']!=$password){
				debug("Passwords don't match ($password locally, {$row['password']} remote)");
				$noauth = true;
			}
		}
		if ($noauth){
			output("`@The username/password pair you provided was not found, please try again.`n");
			output("`bNote`b: if you just created an account, that change may take up to a minute to appear here.");
		}else{
			if (($row['donation']-$row['donationspent']) >= $cost){
				//The user authenticated successfully, and appears to
				//have enough points.
				$sql = "UPDATE " . db_prefix("accounts", get_module_setting("tourney-mainprefix")) . " SET donationspent=donationspent+$cost WHERE acctid={$row['acctid']} AND (donation-donationspent >= $cost)";
				db_query_remote($sql,get_module_setting("tourney-mainlink"),0);
				// There is no way to verify donation updates remotely!
				// So, we just assume it's good
				if (db_affected_rows()>0){
					//looks good!
					set_module_pref("verified",get_module_pref("verified") + httppost("rounds"));
					output("You have purchased %s additional rounds in the tournament.", httppost("rounds"));
					output("Thanks for playing!");
				}else{
					//somehow their account info got lost, or they no
					//longer have enough points; we'll abort
					output("Oops, something wierd happened.");
					output("Please try again.");
				}
			}else{
				output("`%%s`@ does not have enough donator points to purchase a round.", $row['name']);
				output("You need at least %s points.`n", $cost);
				output("`bNote`b: if you just received points, they may take up to a minute to become active here.");
			}
		}
		addnav("Continue","runmodule.php?module=tournament&op=prefs");
		$boughtrounds = get_module_pref("verified");
		if (!(tournament_status() & MOD_TOURNAMENT_IN_CURRENT_ROUND) &&
				$boughtrounds>0){
			addnav(array("You have %s unspent rounds",$boughtrounds));
			addnav("Start this round","runmodule.php?module=tournament&op=start");
		}
		page_footer();
		break;
	case "start":
		//user is starting in a new round.
		if (httpget("free")!="yes")
			set_module_pref("verified",get_module_pref("verified")-1);
		set_module_pref("lastroundplayed",
				get_module_setting("tourney-roundnum"));
		set_module_pref("lastnewgamestart",date("Y-m-d"));
		$session['user']['restorepage']="news.php";
		echo "go to newday (from op=start)";
		redirect("newday.php");
	}
}

?>

<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/http.php");
require_once("lib/sanitize.php");
require_once("lib/buffs.php");

tlschema("newday");

modulehook("newday-intercept",array());

/***************
 **  SETTINGS **
 ***************/
$turnsperday = getsetting("turns",10);
$maxinterest = ((float)getsetting("maxinterest",10)/100) + 1; //1.1;
$mininterest = ((float)getsetting("mininterest",1)/100) + 1; //1.1;
$dailypvpfights = getsetting("pvpday",3);

$resline = (httpget('resurrection')=="true") ? "&resurrection=true" : "" ;
/******************
 ** End Settings **
 ******************/
$dk = httpget('dk');
if ((count($session['user']['dragonpoints']) <
			$session['user']['dragonkills']) && $dk!="") {
	array_push($session['user']['dragonpoints'],$dk);
	switch($dk){
	case "hp":
		$session['user']['maxhitpoints']+=5;
		break;
	case "at":
		$session['user']['attack']++;
		break;
	case "de":
		$session['user']['defense']++;
		break;
	}
}

$labels = array(
		"hp"=>"Max Hitpoints + 5",
		"ff"=>"Forest Fights + 1",
		"at"=>"Attack + 1",
		"de"=>"Defense + 1",
		"unknown"=>"Unknown Spends (contact an admin to investigate!)",
);
$canbuy = array(
		"hp"=>1,
		"ff"=>1,
		"at"=>1,
		"de"=>1,
		"unknown"=>0,
);
$retargs = modulehook("dkpointlabels", array('desc'=>$labels, 'buy'=>$canbuy));
$labels = $retargs['desc'];
$canbuy = $retargs['buy'];
$pdks = array();
reset($labels);
foreach($labels as $type=>$label) {
	$pdks[$type] = (int)httppost($type);
}

$pdk=httpget("pdk");

$dp = count($session['user']['dragonpoints']);
$dkills = $session['user']['dragonkills'];

if ($pdk==1){
	reset($labels);
	$pdktotal = 0;
	$pdkneg = false;
	modulehook("pdkpointrecalc");
	foreach($labels as $type=>$label) {
		$pdktotal += (int)$pdks[$type];
		if((int)$pdks[$type] < 0) $pdkneg = true;
	}
	if ($pdktotal == $dkills-$dp && !$pdkneg) {
		$dp += $pdktotal;
		$session['user']['maxhitpoints'] += (5 * $pdks["hp"]);
		$session['user']['attack'] += $pdks["at"];
		$session['user']['defense'] += $pdks["de"];
		reset($labels);
		foreach($labels as $type=>$label) {
			$count = 0;
			if (isset($pdks[$type])) $count = (int)$pdks[$type];
			while($count) {
				$count--;
				array_push($session['user']['dragonpoints'],$type);
			}
		}
	}else{
		output("`\$Error: Please spend the correct total amount of dragon points.`n`n");
	}
}

if ($dp < $dkills) {
	if ($dkills-$dp > 1) {
		page_header("Dragon Points");
		output("`@You earn one dragon point each time you slay the dragon.");
		output("Advancements made by spending dragon points are permanent!");
		output("`n`nYou have `^%s`@ unspent dragon points.", $dkills-$dp);
		output("How do you wish to spend them?`n`n");
		output("Be sure that your allocations add up to your total unspent dragon points.");
		$text = "<script type='text/javascript' language='Javascript'>
		<!--
		function pointsLeft() {
				var form = document.getElementById(\"dkForm\");
		";
		reset($labels);
		foreach($labels as $type=>$label) {
			if (isset($canbuy[$type]) && $canbuy[$type]) {
				$text .= "var $type = parseInt(form.$type.value);
				";
			}
		}
		reset($labels);
		foreach($labels as $type=>$label) {
			if (isset($canbuy[$type]) && $canbuy[$type]) {
				$text .= "if (isNaN($type)) $type = 0;
				";
			}
		}
		$text .= "var val = $dkills - $dp ";
		foreach($labels as $type=>$label) {
			if (isset($canbuy[$type]) && $canbuy[$type]) {
				$text .= "- $type";
			}
		}
		$text .= ";
				var absval = Math.abs(val);
				var points = 'points';
				if (absval == 1) points = 'point';

				if (val >= 0)
					document.getElementById(\"amtLeft\").innerHTML = \"<span class='colLtWhite'>You have </span><span class='colLtYellow'>\"+absval+\"</span><span class='colLtWhite'> \"+points+\" left to spend.</span><br />\";
				else
					document.getElementById(\"amtLeft\").innerHTML = \"<span class='colLtWhite'>You have spent </span><span class='colLtRed'>\"+absval+\"</span><span class='colLtWhite'> \"+points+\" too many!</span><br />\";
			}
		// -->
		</script>\n";
		rawoutput($text);
		addnav("Reset", "newday.php?pdk=0$resline");

		$link = appendcount("newday.php?pdk=1$resline");

		rawoutput("<form id='dkForm' action='$link' method='POST'>");
		addnav("",$link);
		rawoutput("<table cellpadding='0' cellspacing='0' border='0' width='200'>");
		reset($labels);
		foreach($labels as $type=>$label) {
			if (isset($canbuy[$type]) && $canbuy[$type]) {
				rawoutput("<tr><td nowrap>");
				output($label);
				output_notl(":");
				rawoutput("</td><td>");
				rawoutput("<input id='$type' name='$type' size='4' maxlength='4' value='{$pdks[$type]}' onKeyUp='pointsLeft();' onBlur='pointsLeft();' onFocus='pointsLeft();'>");
				rawoutput("</td></tr>");
			}
		}
		rawoutput("<tr><td colspan='2'>&nbsp;");
		rawoutput("</td></tr><tr><td colspan='2' align='center'>");
		$click = translate_inline("Spend");
		rawoutput("<input id='dksub' type='submit' class='button' value='$click'>");
		rawoutput("</td></tr><tr><td colspan='2'>&nbsp;");
		rawoutput("</td></tr><tr><td colspan='2' align='center'>");
		rawoutput("<div id='amtLeft'></div>");
		rawoutput("</td></tr>");
		rawoutput("</table>");
		rawoutput("</form>");
		reset($labels);
		$count = 0;
		foreach($labels as $type=>$label) {
			if ($count > 0) break;
			if (isset($canbuy[$type]) && $canbuy[$type]) {
				rawoutput("<script language='JavaScript'>document.getElementById('$type').focus();</script>");
				$count++;
			}
		}
	}else{
		page_header("Dragon Points");
		reset ($labels);
		$dist = array();
		foreach ($labels as $type=>$label) {
			$dist[$type] = 0;  // Initialize the distribution
			if (isset($canbuy[$type]) && $canbuy[$type]) {
				addnav($label, "newday.php?dk=$type$resline");
			}
		}

		output("`@You have `&1`@ unspent dragon point.");
		output("How do you wish to spend it?`n`n");
		output("You earn one dragon point each time you slay the dragon.");
		output("Advancements made by spending dragon points are permanent!");

		for ($i=0; $i<count($session['user']['dragonpoints']); $i++) {
			if (isset($dist[$session['user']['dragonpoints'][$i]])) {
				$dist[$session['user']['dragonpoints'][$i]]++;
			} else {
				$dist['unknown']++;
			}
		}

		output("`n`nCurrently, the dragon points you have already spent are distributed in the following manner.");
		rawoutput("<blockquote>");
		rawoutput("<table>");
		reset ($labels);
		foreach ($labels as $type=>$label) {
			if ($type == 'unknown' && $dist[$type] == 0) continue;
			rawoutput("<tr><td nowrap>");
			output($label);
			output_notl(":");
			rawoutput("</td><td>&nbsp;&nbsp;</td><td>");
			output_notl("`@%s", $dist[$type]);
			rawoutput("</td></tr>");
		}
		rawoutput("</table>");
		rawoutput("</blockquote>");
	}
} elseif (!$session['user']['race'] || $session['user']['race']==RACE_UNKNOWN){
	$setrace = httpget("setrace");
	if ($setrace!=""){
		$vname = getsetting("villagename", LOCATION_FIELDS);
		//in case the module wants to reference it this way.
		$session['user']['race']=$setrace;
		// Set the person to the main village/capital by default
		$session['user']['location'] = $vname;
		modulehook("setrace");
		addnav("Continue","newday.php?continue=1$resline");
	}else{
		output("Where do you recall growing up?`n`n");
		modulehook("chooserace");
	}
	if (navcount()==0){
		clearoutput();
		page_header("No Races Installed");
		output("No races were installed in this game.");
		output("So we'll call you a 'human' and get on with it.");
		if ($session['user']['superuser'] & (SU_MEGAUSER|SU_MANAGE_MODULES)) {
			output("You should go into the module manager off of the super user grotto, install and activate some races.");
		} else {
			output("You might want to ask your admin to install some races, they're really quite fun.");
		}
		$session['user']['race']="Human";
		addnav("Continue","newday.php?continue=1$resline");
		page_footer();
	}else{
		page_header("A little history about yourself");
		page_footer();
	}
}elseif ($session['user']['specialty']==""){
	$setspecialty=httpget('setspecialty');
	if ($setspecialty != "") {
		$session['user']['specialty']=$setspecialty;
		modulehook("set-specialty");
		addnav("Continue","newday.php?continue=1$resline");
	} else {
		page_header("A little history about yourself");
		output("What do you recall doing as a child?`n`n");
		modulehook("choose-specialty");
	}
	if (navcount() == 0) {
		clearoutput();
		page_header("No Specialties Installed");
		output("No specialties were installed in this game.");
		output("So, we'll make you a student of the mystical powers and get on with it.");
		// This is someone who will definately have the rights to install
		// modules.
		if ($session['user']['superuser'] & (SU_MEGAUSER|SU_MANAGE_MODULES)) {
			output("You should go into the module manager off of the super user grotto, install and activate some specialties.");
		} else {
			output("You might want to ask your admin to install some specialties, as they are quite fun (and helpful).");
		}
		$session['user']['specialty'] = "MP";
		addnav("Continue","newday.php?continue=1$resline");
		page_footer();
	}else{
		page_footer();
	}
}else{
	page_header("It is a new day!");
	rawoutput("<font size='+1'>");
	output("`c`b`#It is a New Day!`0`b`c");
	rawoutput("</font>");
	$resurrection = httpget('resurrection');

	if ($session['user']['alive']!=true){
		$session['user']['resurrections']++;
		output("`@You are resurrected!  This is resurrection number %s.`0`n",$session['user']['resurrections']);
		$session['user']['alive']=true;
		invalidatedatacache("list.php-warsonline");
	}
	$session['user']['age']++;
	$session['user']['seenmaster']=0;
	output("You open your eyes to discover that a new day has been bestowed upon you. It is day number `^%s.`0",$session['user']['age']);
	output("You feel refreshed enough to take on the world!`n");
	output("`2Turns for today set to `^%s`2.`n",$turnsperday);

	$turnstoday = "Base: $turnsperday";
	$args = modulehook("pre-newday",
			array("resurrection"=>$resurrection, "turnstoday"=>$turnstoday));
	$turnstoday = $args['turnstoday'];

	$interestrate = e_rand($mininterest*100,$maxinterest*100)/(float)100;
	if ($session['user']['turns']>getsetting("fightsforinterest",4) && $session['user']['goldinbank']>=0) {
		$interestrate=1;
		output("`2Today's interest rate: `^0% (Bankers in this village only give interest to those who work for it)`2.`n");
	} elseif (getsetting("maxgoldforinterest", 100000) && $session['user']['goldinbank']>=getsetting("maxgoldforinterest", 100000)) {
		$interestrate=1;
		output("`2Today's interest rate: `^0%% (The bank will not pay interest on accounts equal or greater than %s to retain solvency)`2.`n", getsetting("maxgoldforinterest", 100000));
	}else{
		output("`2Today's interest rate: `^%s%% `n",($interestrate-1)*100);
		if ($session['user']['goldinbank']>=0){
			output("`2Gold earned from interest: `^%s`2.`n",(int)($session['user']['goldinbank']*($interestrate-1)));
		}else{
			output("`2Interest Accrued on Debt: `^%s`2 gold.`n",-(int)($session['user']['goldinbank']*($interestrate-1)));
		}
	}

	//clear all standard buffs
	$tempbuf = unserialize($session['user']['bufflist']);
	$session['user']['bufflist']="";
	strip_all_buffs();
	tlschema("buffs");
	while(list($key,$val)=@each($tempbuf)){
		if (array_key_exists('survivenewday', $val) &&
				$val['survivenewday']==1){
			//$session['bufflist'][$key]=$val;
			if (array_key_exists('schema', $val) && $val['schema'])
				tlschema($val['schema']);
			apply_buff($key,$val);
			if (array_key_exists('newdaymessage', $val) &&
					$val['newdaymessage']) {
				output($val['newdaymessage']);
				output_notl("`n");
			}
			if (array_key_exists('schema', $val) && $val['schema'])
				tlschema();
		}
	}
	tlschema();

	output("`2Hitpoints have been restored to `^%s`2.`n",$session['user']['maxhitpoints']);

	reset($session['user']['dragonpoints']);
	$dkff=0;
	while(list($key,$val)=each($session['user']['dragonpoints'])){
		if ($val=="ff"){
			$dkff++;
		}
	}
	if ($session['user']['hashorse']){
		$buff = unserialize($playermount['mountbuff']);
		if (!isset($buff['schema']) || $buff['schema'] == "")
			$buff['schema']="mounts";
		apply_buff('mount',$buff);
	}
	if ($dkff>0) {
		output("`n`2You gain `^%s`2 forest %s from spent dragon points!",
				$dkff, translate_inline($dkff == 1?"fight":"fights"));
	}
	$r1 = e_rand(-1,1);
	$r2 = e_rand(-1,1);
	$spirits = $r1+$r2;
	if ($resurrection=="true"){
		addnews("`&%s`& has been resurrected by `\$Ramius`&.",$session['user']['name']);
		$spirits=-6;
		$session['user']['deathpower']-=100;
		$session['user']['restorepage']="village.php?c=1";
	}

	$sp = array((-6)=>"Resurrected", (-2)=>"Very Low", (-1)=>"Low",
			(0)=>"Normal", 1=>"High", 2=>"Very High");
	$sp = translate_inline($sp);
	output("`n`2You are in `^%s`2 spirits today!`n",$sp[$spirits]);
	if (abs($spirits)>0){
		if($spirits>0){
			$gain=translate_inline("gain");
		}else{
			$gain=translate_inline("lose");
		}
		$sff = abs($spirits);
		output("`2As a result, you `^%s %s forest %s`2 for today!`n",
				$gain, $sff, translate_inline($sff==1?"fight":"fights"));
	}
	$rp = $session['user']['restorepage'];
	$x = max(strrpos("&",$rp),strrpos("?",$rp));
	if ($x>0) $rp = substr($rp,0,$x);
	if (substr($rp,0,10)=="badnav.php"){
		addnav("Continue","news.php");
	}else{
		addnav("Continue", cmd_sanitize($rp));
	}

	$session['user']['laston'] = date("Y-m-d H:i:s");
	$bgold = $session['user']['goldinbank'];
	$session['user']['goldinbank']*=$interestrate;
	$nbgold = $session['user']['goldinbank'] - $bgold;

	if ($nbgold != 0) {
		debuglog(($nbgold >= 0 ? "earned " : "paid ") . abs($nbgold) . " gold in interest");
	}
	$turnstoday .= ", Spirits: $spirits, DK: $dkff";
	$session['user']['turns']=$turnsperday+$spirits+$dkff;
	$session['user']['hitpoints'] = $session['user']['maxhitpoints'];
	$session['user']['spirits'] = $spirits;
	if ($resurrection != "true")
		$session['user']['playerfights'] = $dailypvpfights;
	$session['user']['transferredtoday'] = 0;
	$session['user']['amountouttoday'] = 0;
	$session['user']['seendragon'] = 0;
	$session['user']['seenmaster'] = 0;
	$session['user']['fedmount'] = 0;
	if ($resurrection!="true"){
		$session['user']['soulpoints']=50 + 5 * $session['user']['level'];
		$session['user']['gravefights']=getsetting("gravefightsperday",10);
	}
	$session['user']['boughtroomtoday'] = 0;
	$session['user']['recentcomments']=$session['user']['lasthit'];
	$session['user']['lasthit'] = gmdate("Y-m-d H:i:s");
	if ($session['user']['hashorse']){
		$msg = $playermount['newday'];
		require_once("lib/substitute.php");
		$msg = substitute_array("`n`&".$msg."`0`n");
		output($msg);
		require_once("lib/mountname.php");
		list($name, $lcname) = getmountname();

		$mff = (int)$playermount['mountforestfights'];
		$session['user']['turns'] += $mff;
		$turnstoday.=", Mount: $mff";
		if ($mff > 0) {
			$state = translate_inline("gain");
			$color = "`^";
		} elseif ($mff < 0) {
			$state = translate_inline("lose");
			$color = "`$";
		}
		$mff = abs($mff);
		if ($mff != 0) {
			output("`n`&Because of %s`&, you %s%s %s`& forest %s for today!`n`0", $lcname, $color, $state, $mff, translate_inline($mff==1?'fight':'fights'));
		}
	}else{
		output("`n`&You strap your `%%s`& to your back and head out for some adventure.`0",$session['user']['weapon']);
	}
	if ($session['user']['hauntedby']>""){
		output("`n`n`)You have been haunted by %s`); as a result, you lose a forest fight!",$session['user']['hauntedby']);
		$session['user']['turns']--;
		$session['user']['hauntedby']="";
		$turnstoday.=", Haunted: -1";
	}

	//Let's do a new day operation that will only fire off for
	//one user on the whole server.

	//check last time we did this vs now to see if it was a different game day.
	$lastnewdaysemaphore = convertgametime(strtotime(getsetting("newdaySemaphore","0000-00-00 00:00:00") . " +0000"));
	$gametoday = gametime();
	if (gmdate("Ymd",$gametoday)!=gmdate("Ymd",$lastnewdaysemaphore)){
		// it appears to be a different game day, acquire semaphore and
		// check again.
		$sql = "LOCK TABLES " . db_prefix("settings") . " WRITE";
		db_query($sql);
		clearsettings();
		$lastnewdaysemaphore = convertgametime(strtotime(getsetting("newdaySemaphore","0000-00-00 00:00:00") . " +0000"));

		$gametoday = gametime();
		if (gmdate("Ymd",$gametoday)!=gmdate("Ymd",$lastnewdaysemaphore)){
			//we need to run the hook, update the setting, and unlock.
			savesetting("newdaySemaphore",gmdate("Y-m-d H:i:s"));
			$sql = "UNLOCK TABLES";
			db_query($sql);

			//run the hook.
			modulehook("newday-runonce",array());
		}else{
			//someone else beat us to it, unlock.
			$sql = "UNLOCK TABLES";
			db_query($sql);
		}
	}
	$args = modulehook("newday",
			array("resurrection"=>$resurrection, "turnstoday"=>$turnstoday));
	$turnstoday = $args['turnstoday'];
	debuglog("New Day Turns: $turnstoday");

	if (strtotime(getsetting("lastdboptimize", date("Y-m-d H:i:s", strtotime("-1 day")))) < strtotime("-1 day")){
		savesetting("lastdboptimize",date("Y-m-d H:i:s"));
		$result = db_query("SHOW TABLES");
		$tables = array();
		$start = getmicrotime();
		for ($i=0;$i<db_num_rows($result);$i++){
			list($key,$val)=each(db_fetch_assoc($result));
			db_query("OPTIMIZE TABLE $val");
			array_push($tables,$val);
		}
		$time = round(getmicrotime() - $start,2);
		require_once("lib/gamelog.php");
		gamelog("Optimized tables: ".join(", ",$tables)." in $time seconds.","maintenance");
	}
}
page_footer();
?>

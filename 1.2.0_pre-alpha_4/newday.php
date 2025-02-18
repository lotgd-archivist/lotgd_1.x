<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/http.php");
require_once("lib/sanitize.php");
require_once("lib/buffs.php");

tlschema("newday");
//mass_module_prepare(array("newday-intercept", "newday"));
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
if ((count($user->dragonpoints) <
			$user->dragonkills) && $dk!="") {
	$user->addDragonPoints($dk);
	switch($dk){
	case "hp":
		$user->maxhitpoints+=5;
		break;
	case "at":
		$user->attack++;
		break;
	case "de":
		$user->defense++;
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
foreach($labels as $type=>$label) {
	$pdks[$type] = (int)httppost($type);
}
$pdk=httpget("pdk");
$dp = count($user->dragonpoints);
$dkills = $user->dragonkills;

if ($pdk==1){
	$pdktotal = 0;
	$pdkneg = false;
	modulehook("pdkpointrecalc");
	foreach($labels as $type=>$label) {
		$pdktotal += (int)$pdks[$type];
		if((int)$pdks[$type] < 0) $pdkneg = true;
	}
	if ($pdktotal == $dkills-$dp && !$pdkneg) {
		$dp += $pdktotal;
		$user->maxhitpoints += (5 * $pdks["hp"]);
		$user->attack += $pdks["at"];
		$user->defense += $pdks["de"];
		foreach($labels as $type=>$label) {
			$count = 0;
			if (isset($pdks[$type])) $count = (int)$pdks[$type];
			while($count) {
				$count--;
				$user->addDragonPoints($type);
			}
		}
	}else{
		output("`\$Error: Please spend the correct total amount of dragon points.`n`n");
	}
}

if ($dp < $dkills) {
	require_once("lib/newday/dragonpointspend.php");
} elseif (!$user->race || $user->race==RACE_UNKNOWN){
	require_once("lib/newday/setrace.php");
}elseif ($user->specialty==""){
	require_once("lib/newday/setspecialty.php");
}else{
	page_header("It is a new day!");
	rawoutput("<font size='+1'>");
	output("`c`b`#It is a New Day!`0`b`c");
	rawoutput("</font>");
	$resurrection = httpget('resurrection');

	if ($user->alive!=true){
		$user->resurrections++;
		output("`@You are resurrected!  This is resurrection number %s.`0`n",$user->resurrections);
		$user->alive=true;
		invalidatedatacache("list.php-warsonline");
	}
	$user->age++;
	$user->seenmaster=0;
	output("You open your eyes to discover that a new day has been bestowed upon you. It is day number `^%s.`0",$user->age);
	output("You feel refreshed enough to take on the world!`n");
	output("`2Turns for today set to `^%s`2.`n",$turnsperday);

	$turnstoday = "Base: $turnsperday";
	$args = modulehook("pre-newday",
			array("resurrection"=>$resurrection, "turnstoday"=>$turnstoday));
	$turnstoday = $args['turnstoday'];

	$interestrate = e_rand($mininterest*100,$maxinterest*100)/(float)100;
	if ($user->turns>getsetting("fightsforinterest",4) && $user->goldinbank>=0) {
		$interestrate=1;
		output("`2Today's interest rate: `^0% (Bankers in this village only give interest to those who work for it)`2.`n");
	} elseif (getsetting("maxgoldforinterest", 100000) && $user->goldinbank>=getsetting("maxgoldforinterest", 100000)) {
		$interestrate=1;
		output("`2Today's interest rate: `^0%% (The bank will not pay interest on accounts equal or greater than %s to retain solvency)`2.`n", getsetting("maxgoldforinterest", 100000));
	}else{
		output("`2Today's interest rate: `^%s%% `n",($interestrate-1)*100);
		if ($user->goldinbank>=0){
			output("`2Gold earned from interest: `^%s`2.`n",(int)($user->goldinbank*($interestrate-1)));
		}else{
			output("`2Interest Accrued on Debt: `^%s`2 gold.`n",-(int)($user->goldinbank*($interestrate-1)));
		}
	}

	//clear all standard buffs
	$tempbuf = unserialize($user->bufflist);
	$user->bufflist="";
	strip_all_buffs();
	tlschema("buffs");
	foreach ($tempbuf as $key=>$val) {
		if (isset($val['survivenewday']) &&
				$val['survivenewday']==1){
			if (isset($val['schema']) && $val['schema'])
				tlschema($val['schema']);
			apply_buff($key,$val);
			if (isset($val['newdaymessage']) &&
					$val['newdaymessage']) {
				output($val['newdaymessage']);
				output_notl("`n");
			}
			if (isset($val['schema']) && $val['schema'])
				tlschema();
		}
	}
	tlschema();

	output("`2Hitpoints have been restored to `^%s`2.`n",$user->maxhitpoints);

	$dkff=0;
	foreach($user->dragonpoints as $val){
		if ($val=="ff"){
			$dkff++;
		}
	}
	if ($user->hashorse){
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
	$resurrectionturns=$spirits;
	if ($resurrection=="true"){
		addnews("`&%s`& has been resurrected by %s`&.",$user->name,getsetting('deathoverlord','`$Ramius'));
		$spirits=-6;
		$resurrectionturns=getsetting('resurrectionturns',-6);
		if (strstr($resurrectionturns,'%')) {
			$resurrectionturns=strtok($resurrectionturns,'%');
			$resurrectionturns=(int)$resurrectionturns;
			if ($resurrectionturns<-100) $resurrectionturns=-100;
			$resurrectionturns=round(($turnsperday+$dkff)*($resurrectionturns/100),0);
		} else {
			if ($resurrectionturns<-($turnsperday+$dkff)) $resurrectionturns=-($turnsperday+$dkff);
		}
		$user->deathpower-=100;
		$user->restorepage="village.php?c=1";
	}

	$sp = array((-6)=>"Resurrected", (-2)=>"Very Low", (-1)=>"Low",
			(0)=>"Normal", 1=>"High", 2=>"Very High");
	$sp = translate_inline($sp);
	output("`n`2You are in `^%s`2 spirits today!`n",$sp[$spirits]);
	if (abs($spirits)>0){
		if($resurrectionturns>0){
			$gain=translate_inline("gain");
		}else{
			$gain=translate_inline("lose");
		}
		$sff = abs($resurrectionturns);
		output("`2As a result, you `^%s %s forest %s`2 for today!`n",
				$gain, $sff, translate_inline($sff==1?"fight":"fights"));
	}
	$rp = $user->restorepage;
	$x = max(strrpos("&",$rp),strrpos("?",$rp));
	if ($x>0) $rp = substr($rp,0,$x);
	if (!$rp || substr($rp,0,10)=="badnav.php"){
		addnav("Continue","news.php");
	}else{
		addnav("Continue", cmd_sanitize($rp));
	}

	$user->laston = date("Y-m-d H:i:s");
	$bgold = $user->goldinbank;
	$user->goldinbank*=$interestrate;
	$nbgold = $user->goldinbank - $bgold;

	if ($nbgold != 0) {
		debuglog(($nbgold >= 0 ? "earned " : "paid ") . abs($nbgold) . " gold in interest");
	}
	$turnstoday .= ", Spirits: $resurrectionturns, DK: $dkff";
	$user->turns=$turnsperday+$resurrectionturns+$dkff;
	$user->hitpoints = $user->maxhitpoints;
	$user->spirits = $spirits;
	if ($resurrection != "true")
		$user->playerfights = $dailypvpfights;
	$user->transferredtoday = 0;
	$user->amountouttoday = 0;
	$user->seendragon = 0;
	$user->seenmaster = 0;
	$user->fedmount = 0;
	if ($resurrection!="true"){
		$user->soulpoints=50 + 5 * $user->level;
		$user->gravefights=getsetting("gravefightsperday",10);
	}
	$user->boughtroomtoday = 0;
	$user->recentcomments=$user->lasthit;
	$user->lasthit = gmdate("Y-m-d H:i:s");
	if ($user->hashorse){
		$msg = $playermount['newday'];
		require_once("lib/substitute.php");
		$msg = substitute_array("`n`&".$msg."`0`n");
		output($msg);
		require_once("lib/mountname.php");
		list($name, $lcname) = getmountname();

		$mff = (int)$playermount['mountforestfights'];
		$user->turns += $mff;
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
		output("`n`&You strap your `%%s`& to your back and head out for some adventure.`0",$user->weapon);
	}
	if ($user->hauntedby>""){
		output("`n`n`)You have been haunted by %s`); as a result, you lose a forest fight!",$user->hauntedby);
		$user->turns--;
		$user->hauntedby="";
		$turnstoday.=", Haunted: -1";
	}

	require_once("lib/extended-battle.php");
	unsuspend_companions("allowinshades");

	if (!getsetting("newdaycron",0)) {
		//check last time we did this vs now to see if it was a different game day.
		$lastnewdaysemaphore = convertgametime(strtotime(getsetting("newdaySemaphore","0000-00-00 00:00:00") . " +0000"));
		$gametoday = gametime();
		if (gmdate("Ymd",$gametoday)!=gmdate("Ymd",$lastnewdaysemaphore)){
				// it appears to be a different game day, acquire semaphore and
				// check again.
            $sql = "LOCK TABLES " . DB::prefix("settings") . " WRITE";
            DB::query($sql);
            clearsettings();
            $lastnewdaysemaphore = convertgametime(strtotime(getsetting("newdaySemaphore","0000-00-00 00:00:00") . " +0000"));
                $gametoday = gametime();
            if (gmdate("Ymd",$gametoday)!=gmdate("Ymd",$lastnewdaysemaphore)){
                //we need to run the hook, update the setting, and unlock.
                savesetting("newdaySemaphore",gmdate("Y-m-d H:i:s"));
                $sql = "UNLOCK TABLES";
                DB::query($sql);
				require("lib/newday/newday_runonce.php");
			}else{
	            //someone else beat us to it, unlock.
                $sql = "UNLOCK TABLES";
                DB::query($sql);
			}
		}

	}
	$args = modulehook("newday",
			array("resurrection"=>$resurrection, "turnstoday"=>$turnstoday));
	$turnstoday = $args['turnstoday'];
	debuglog("New Day Turns: $turnstoday");

}
page_footer();
?>

<?php
// translator ready
// mail ready
// addnews ready

// Heidi, the well-wisher module. Gifting to players
/* ver 1.0 by Shannon Brown => SaucyWench -at- gmail -dot- com */
/* 29 Nov 2004 */

require_once("lib/http.php");
require_once("lib/villagenav.php");

function heidi_getmoduleinfo(){
	$info = array(
		"name"=>"Well-wisher module",
		"version"=>"1.0",
		"author"=>"Shannon Brown",
		"category"=>"Village",
		"download"=>"core_module",
		"settings"=>array(
			"Heidi's Hut Settings,title",
			"changeallowed"=>"How many PvP may players exchange per day?,range,0,".getsetting("pvpday", 3) . ",1|1",
			"allowgift"=>"Allow gifting to other players?,bool|1",
			"findperc"=>"Percent of time a player will find a gift by their pillow?,range,0,100,5|25",
			"turnmult"=>"Multiplier for the extra turns?,floatrange,0,50,2.5|10",
			"turnadd"=>"Addition for the extra turns?,range,0,10,1|3",
			"heidiloc"=>"Where does Heidi appear,location|".getsetting("villagename", LOCATION_FIELDS)
		),
		"prefs"=>array(
			"Heidi's Hut User Preferences,title",
			"pvpchange"=>"How many changes have been done today?,int|0",
			"newdayturns"=>"How many turns did the player have at new day?,int|0",
			"addgold"=>"Donation amount set for this player for today,int|0",
		)
	);
	return $info;
}

function heidi_install(){
	module_addhook("changesetting");
	module_addhook("village");
	module_addhook("newday");
	return true;
}

function heidi_uninstall(){
	return true;
}

function heidi_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "changesetting":
		if ($args['setting'] == "villagename") {
			if ($args['old'] == get_module_setting("heidiloc")) {
				set_module_setting("heidiloc", $args['new']);
			}
		}
		break;
	case "village":
		$allowgift=get_module_setting("allowgift");
		$changeallowed=get_module_setting("changeallowed");
		if (($session['user']['location']==get_module_setting("heidiloc")) &&
				($allowgift || $changeallowed>0)) {
			tlschema($args['schemas']['marketnav']);
			addnav($args['marketnav']);
			tlschema();
			addnav("H?Heidi's Place","runmodule.php?module=heidi");
		}
		break;
	case "newday":
		set_module_pref("pvpchange",0);
		$turns = getsetting("turns",10) + $session['user']['spirits'];
		reset($session['user']['dragonpoints']);
		while(list($key,$val)=each($session['user']['dragonpoints'])){
			if ($val=="ff") $turns++;
		}
		set_module_pref("newdayturns",$turns);
		$echance=e_rand(0, 100);
		$mult=e_rand(200,400);
		$addgold=round($mult*($session['user']['level']/max(10,$session['user']['dragonkills'])));
		if ($session['user']['dragonkills']<6) $addgold*1.5;
		if ($echance >= get_module_setting("findperc"))
			$addgold=0;
		set_module_pref("addgold",$addgold);
		debug("addgold is ".$addgold);
		if ($addgold>1){
			// they are a recipient
			$session['user']['gold']+=$addgold;
			output("`n`5Beside your pillow is a small leather bag containing %s gold, and a note: `^Blessings to ye, child, for someone cared enough to send ye a gift.", $addgold);
			output("`5Wondering who it is from, you add it to your purse.`n");
			debuglog("gained $addgold gold from an anonymous gift.");
		}
		break;
	}
	return $args;
}

function heidi_run(){
	global $session;

	$op = httpget("op");
	$addgold=get_module_pref("addgold");
	$addgold*=round(min(1,$session['user']['dragonkills'])*max(1,$session['user']['dragonkills']*0.5/$session['user']['maxhitpoints']));
	$addgold-=max(1000,$session['user']['maxhitpoints']);
	if ($addgold<=0) $addgold=10*$session['user']['level'];
	$pvpchange=get_module_pref("pvpchange");
	$changeallowed=get_module_setting("changeallowed");
	$allowgift=get_module_setting("allowgift");
	$turnadd=get_module_setting("turnadd");
	$turnmult=get_module_setting("turnmult");
	$newdayturns=get_module_pref("newdayturns");
	$turngain=ceil($newdayturns*$turnmult/100)+$turnadd;

	page_header("Heidi's Place");

	villagenav();
	output("`&`c`bHeidi, the Well-wisher`b`c`n");
	if ($changeallowed) {
		addnav("Burn Blue Candle - lose 1 PvP", "runmodule.php?module=heidi&op=blue");
	}
	if ($allowgift) {
		addnav(array("Make a Donation - %s gold",$addgold), "runmodule.php?module=heidi&op=give");
	}
	if ($op==""){
		output("`7You step into Heidi's small hut, and are greeted by a pleasant-looking felyne with a smile on her face.`n`n");
		output("\"`&It is so lovely to see you, %s!",$session['user']['name']);
		output("`&Welcome, then, and reflect on the beauty of the well.`7\"`n`n");
		output("`7A small fountain trickles into what looks more like a pool than a well.");
		output("`7As Heidi whistles softly, crystals in the bottom of the pool begin to glow, eventually forming the numbers: %s.`n`n",$addgold);
		output("`7\"`&Perhaps ye have come to give! Or to burn the blue?`7\"");
	}elseif ($op=="give" && $session['user']['gold']<$addgold && $allowgift){
		// you don't have enough money.
		output("`7Heidi eyes you with concern.`n`n");
		output("\"`&Child, ye be trying to give, when ye have not the affluence to give.`7`n`n");
		output("`&Take comfort, for the mother sees inside thy heart today.`7\"`n`n");
		output("You're not so sure you understand who this mother is, but you realize that you haven't enough gold to donate what the well requests, and so you leave quietly.`n`n");
	} elseif ($op=="give" && $allowgift) {
		output("`7You lean forward, and place %s gold into the well.`n`n",$addgold);
		output("Heidi concentrates on a small candle for several moments, whispering words you cannot understand.`n`n");
		output("\"`&Somewhere, come the morning fair, someone less fortunate shall wake up with a gift of gold.`7`n`n");
		output("`&The pleasure in giving, be within you today!`7\" she exclaims.`n`n");
		debuglog("gave $addgold gold as an anonymous gift to someone less fortunate.");
		$session['user']['gold']-=$addgold;
		apply_buff('heidi', array(
			"name"=>"`QUnselfishness`0",
			"rounds"=>15,
			"defmod"=>1.05,
			"survivenewday"=>1,
			"roundmsg"=>"`QGiving to others makes you feel empowered.`0",
		));
		// Why is this charging for a pvp exchange??
		//$pvpchange++;
		//set_module_pref("pvpchange",$pvpchange);
	}elseif($session['user']['playerfights']==0 || $pvpchange>=$changeallowed){
		// you have no PvP left today or have already burned the blue enough today
		output("`7Heidi eyes you with a smile.`n`n");
		if ($session['user']['playerfights']==0) {
			output("\"`&'Tis all very well to want peace, when one has none extra to give!`7\"`n`n");
		} else {
			// No more burning allowed today.
			output("\"`&Your desire for peace is noble, but is misplaced at this time.`7\"`n`n");
		}
		output("\"`&Perhaps tomorrow ye will come to see me, before ye slay your enemies in fury?`7\"`n`n");
	}else{
		output("`7Heidi takes a small, sky-blue candle, lights it and places it in a small silver holder.");
		output("She regards you with a smile.`n`n");
		output("\"`&Your gesture of kindness to your fellow beings shall reward you.");
		output("Go in peace, warrior.`7\"`n`n");		
		output("`6You `@gain`6 %s turns!`n`n",$turngain);
		$session['user']['turns']+=$turngain;
		$session['user']['playerfights']--;
		$pvpchange++;
		set_module_pref("pvpchange",$pvpchange);
		debuglog("exchanged one PvP fight for $turngain forest fights.");
		$newdayturns*=0.75; // second and subsequent burns have lower benefit
		set_module_pref("newdayturns",$newdayturns);
	}
	page_footer();
}

?>
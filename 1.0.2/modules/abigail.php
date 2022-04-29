<?php
// addnews ready
// translator ready
// mail ready

/* Abigail the Street Hawker */
/* ver 1.0 by Shannon Brown => SaucyWench -at- gmail -dot- com */
/* 23 Aug 2004 */

// default settings add average 6 charm points per 10 gems spent

require_once("lib/villagenav.php");
require_once("lib/http.php");

function abigail_getmoduleinfo(){
	$info = array(
		"name"=>"Abigail the Street Hawker",
		"version"=>"1.0",
		"author"=>"Shannon Brown",
		"category"=>"Village Specials",
		"download"=>"core_module",
		"settings"=>array(
			"Abigail the Street Hawker - Settings,title",
			"cost"=>"Number of gems the items cost,int|2",
			"charmchance"=>"What is the chance that the partner will like the gift?,range,5,100,5|80",
		),
		"prefs"=>array(
			"Abigail the Street Hawker User Preferences,title",
			"bought"=>"Purchased Gift Today?,bool|0",
			"trinket"=>"Last gift offered?,boots",
			"liked"=>"Liked the gift from their lover,bool|0",
		)
	);
	return $info;
}

function abigail_install(){
	module_addhook("newday");
	// we only want this to happen if they haven't seen her already today
	module_addeventhook("village","\$bought=get_module_pref(\"bought\", \"abigail\");return (\$bought?0:50);");
	return true;
}

function abigail_uninstall(){
	return true;
}

function abigail_dohook($hookname,$args){
	global $session;
	switch($hookname){
	case "newday":
		if (get_module_pref("bought")==1) {
			output("`n`n`5Just as you get ready to set off for the day, a messenger boy hands you a note from %s`5.", $session['user']['sex']?"`^Seth":"`%Violet");
			if (get_module_pref("liked")==1){
				output("\"`%What a wonderful surprise! Your gift was very thoughtful! I shall show everyone!\"`n`n");
				output("`^You gain some charm!`n");
				$session['user']['charm']+=2;
			} else{
				output("\"`%I can't believe you think you can win my approval with a cheap gift like that!\"`n`n");
				output("`^You `\$lose`^ some charm.`n");
				if ($session['user']['charm']>2){
					$session['user']['charm']-=2;
				} else $session['user']['charm'] = 0;
			}
		}
		set_module_pref("liked",0);
		set_module_pref("bought",0);
		break;
	}
	return $args;
}

function abigail_runevent($type) {
	global $session;
	$from = "village.php?";
	$session['user']['specialinc'] = "module:abigail";

	$gemword="gems";
	$cost=get_module_setting("cost");
	if ($cost==1){
		$gemword="gem";
	}


	$trinket=get_module_pref("trinket");

	$op = httpget('op');
	if ($op == "") {
		if ($session['user']['sex']) {
			$gift = array(
				"pair of cufflinks",
				"leather belt",
				"hat",
				"pair of boots",
			);
		}
		else {
			$gift = array(
				"pair of earrings",
				"pair of satin slippers",
				"jeweled necklace",
				"pretty bracelet",
			);
		}
		$gift = translate_inline($gift);
		$trinket = $gift[e_rand(0, count($gift)-1)];
		set_module_pref("trinket",$trinket);
		output("`7While you are wandering idly, minding your own business, you are approached by a diminutive elf in a green cloak. `n`n");
		$greeting = translate_inline($session['user']['sex']?"Madam":"Sir");
		output("\"`&Happy Day to ye, %s!", $greeting);
		output("Can I interest you in a lovely %s for somebody special?", $trinket);
		output("It's a fine gift, crafted with care and skill!");

		if ($cost == 1) {
			output("And, for you, only `%%s`& gem!", $cost);
		} else  {
			output("And, for you, only `%%s`& gems!", $cost);
		}
		output_notl("`7\"`n`n");

		output("`7You survey the %s, admiring the fine craftsmanship, and try to imagine %s`7 wearing such a gift.", $trinket, ($session['user']['sex']?"`^Seth":"`%Violet"));
		addnav("Purchase this gift",$from."op=shop");
		addnav("Don't buy anything",$from."op=nope");
	}elseif($op=="nope"){
		output("`7You decide not to buy the %s from Abigail.`n`n",$trinket);
		output("`7You're sure that %s`7 wouldn't like something like that, anyway.`n", $session['user']['sex']?"`^Seth":"`%Violet");
		$session['user']['specialinc'] = "";
	}elseif($session['user']['gems']<$cost){
		if($session['user']['gems']==0){
			output("`7Abigail stares at your empty hand.`n`n");
		} else {
			if($session['user']['gems']==1){
				output("`7Abigail stares at the single gem in your hand.`n`n");
			} else {
				output("`7Abigail stares at the %s gems in your hand.`n`n", $session['user']['gems']);
			}
		}
		output("`7How can you buy %s`7 a gift without enough gems?", $session['user']['sex']?"`^Seth":"`%Violet");
		addnav("Walk Away",$from."op=leave");
	}elseif($op=="shop"){
		villagenav();
		set_module_pref("bought",1);
		$session['user']['gems']-=$cost;
		debuglog("spent $cost gems on a gift for their lover");
		if ($cost == 1) {
			output("`7Agreeing to buy the %s, you hand over the %s gem.`n`n",
					$trinket, $cost);
		} else {
			output("`7Agreeing to buy the %s, you hand over the %s gems.`n`n",
					$trinket, $cost);
		}
		output("`7Abigail promises to have the %s delivered to %s`7 right away.`n`n", $trinket, $session['user']['sex']?"`^Seth":"`%Violet");
		output("`7You can't wait to find out what %s`7 thinks of the gift!", $session['user']['sex']?"`^Seth":"`%Violet");

		$likechance=(e_rand(1,100));
		$charmchance=get_module_setting("charmchance");
		if ($likechance<=$charmchance) {
			$newval=get_module_pref("liked");
			$newval++;
			set_module_pref("liked",$newval);
		}
		else {
			set_module_pref("liked",-1);
		}
	}
	
	if ($op == "leave") {
		$session['user']['specialinc'] = "";
		output("`5Not having any gems to buy a gift for %s`5, you wander sadly away.`n`n", $session['user']['sex']?"`^Seth":"`%Violet");
	}
	if ($op != "") {
		$session['user']['specialinc'] = "";
	}
}
?>

<?php
	$info = array(
		"name"=>"Multiple Cities",
		"version"=>"1.0",
		"author"=>"Eric Stevens",
		"category"=>"Village",
		"download"=>"core_module",
		"allowanonymous"=>true,
		"override_forced_nav"=>true,
		"settings"=>array(
			"Cities Settings,title",
			"allowance"=>"Daily Travel Allowance,int|3",
			"coward"=>"Penalise Cowardice for running away?,bool|1",
			"travelspecialchance"=>"Chance for a special during travel,int|7",
			"safechance"=>"Chance to be waylaid on a safe trip,range,1,100,1|50",
			"dangerchance"=>"Chance to be waylaid on a dangerous trip,range,1,100,1|66",
		),
		"prefs"=>array(
			"Cities User Preferences,title",
			"traveltoday"=>"How many times did they travel today?,int|0",
			"homecity"=>"User's current home city.|",
		),
		"prefs-mounts"=>array(
			"Cities Mount Preferences,title",
			"extratravel"=>"How many free travels does this mount give?,int|0",
		),
		"prefs-drinks"=>array(
			"Cities Drink Preferences,title",
			"servedcapital"=>"Is this drink served in the capital?,bool|1",
		),
	);
?>
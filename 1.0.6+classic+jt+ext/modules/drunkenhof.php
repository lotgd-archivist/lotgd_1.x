<?php

//
// This is a modified version of 'classhof', originally by Aelia and DaveS. 
// This version has been changed to only deal with the modified version of 
// the Drunken Master specialty at logd.dragoncat.net.
//
// -blarg-
// 2007/09/10
//

function drunkenhof_getmoduleinfo() {
    $info = array(
        "name" => "Class HoF - Drunken Master",
        "author" => "Aelia, with DaveS<br>modified by Ben Wong",
        "version" => "1.02+bw",
        "download" => "http://logd.dragoncat.net/~blarg/",
        "category" => "AW-Specialties",
        "settings"=> array(
					"Drunken Master HoF Settings,title",
					"pp"=>"Number of players to show per page on the HoF?,int|25",
        ),
    );
    return $info;
}

function drunkenhof_install() {
	module_addhook("footer-hof");
	return true;
}

function drunkenhof_uninstall() {
    return true;
}

function drunkenhof_dohook($hookname,$args) {
    global $session,$resline;
    switch ($hookname) {
		case "footer-hof":
			addnav("Warrior Rankings");
			addnav("Drunkeness","runmodule.php?module=drunkenhof&op=hof");
		break;
    }
    return $args;
}

function drunkenhof_run(){
	global $session;
	$op = httpget('op');

	page_header("Drunken Masters");
	if ($op=="hof"){
		$spec = "AW-DM";
		$modulename = "specialtydrunkenmaster";
		$levelname = "dmlevel";
		$classname = "Drunken Master";
		$ccode = "`Q";

		$pp = get_module_setting("pp");
		$page = httpget('page');
		$pageoffset = (int)$page;
		if ($pageoffset > 0) $pageoffset--;
		$pageoffset *= $pp;
		$limit = " LIMIT $pageoffset,$pp";
		$sql = "SELECT COUNT(*) AS c FROM " . db_prefix("module_userprefs") . " WHERE modulename = '$modulename' AND setting = '$levelname' AND value > 0";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		$total = $row['c'];
		$count = db_num_rows($result);
		if (($pageoffset + $pp) < $total){
			$cond = $pageoffset + $pp;
		}else{
			$cond = $total;
		}

		$sql = "SELECT ".db_prefix("module_userprefs").".value, ".db_prefix("module_userprefs").".userid, ".db_prefix("accounts").".name FROM " . db_prefix("module_userprefs") . "," . db_prefix("accounts") . " WHERE acctid = userid AND modulename = '$modulename' AND setting = '$levelname' AND value > 0 ORDER BY (value+0) DESC $limit";
		$result = db_query($sql);
		$count = db_num_rows($result);
		$rank = translate_inline("Rank");
		$name = translate_inline("Name");
		$level = translate_inline("Level");
		$none = translate_inline("No $classname".(substr($classname,-1)=="s"?"":"s")." enrolled!");

		output("`b`c$ccode Followers Stumbling Down the Path of the $classname `0`c`b`n`n");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' align='center' bgcolor='#999999'>");
		rawoutput("<tr class='trhead'><td>$rank</td><td>$name</td><td>$level</td></tr>");
		if ($total == 0) output_notl("<tr class='trlight'><td colspan='3' align='center'>`&$none`0</td></tr>",true);
		else{
			for($i = $pageoffset; $i < $cond && $count; $i++) {
				$row = db_fetch_assoc($result);
				if ($row['name']==$session['user']['name']){
					rawoutput("<tr class='trhilight'><td>");
				}else{
					rawoutput("<tr class='".($i%2?"trdark":"trlight")."'><td>");
				}
				$j=$i+1;
				output_notl("$ccode$j.");
				rawoutput("</td><td>");
				output_notl("$ccode%s`0",$row['name']);
				rawoutput("</td><td>");			
				$levelranknumber = get_module_pref($levelname,$modulename,$row['userid']);
				if ($levelranknumber >= 1) {
					if ($levelranknumber < 6) {
						$levelrankname="Drunken Initiate";
					}
					elseif ($levelranknumber < 18) {
						$levelrankname="Drunken Novice";
					}
					elseif ($levelranknumber < 32) {
						$levelrankname="Drunken Adept";
					}
					elseif ($levelranknumber < 48) {
						$levelrankname="Drunken Master";
					}
					elseif ($levelranknumber < 100) {
						$levelrankname="Drunken Grand Master";
					}
					else {
						$levelrankname="Drunken Master Guru";
					}
				}
				output_notl("`c$ccode%s`c`0",$levelrankname);
				rawoutput("</td></tr>");
			}
		}
		rawoutput("</table>");
		if ($total>$pp){
			addnav("Pages");
			for ($p=0;$p<$total;$p+=$pp){
				addnav(array("Page %s (%s-%s)", ($p/$pp+1), ($p+1), min($p+$pp,$total)), "runmodule.php?module=drunkenhof&op=hof&spec=".$spec."&page=".($p/$pp+1));
			}
		}
	}
	addnav("Return");
	addnav("Hall of Fame", "hof.php");
	villagenav();
	page_footer();
}
?>
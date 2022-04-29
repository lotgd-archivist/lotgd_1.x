<?php
// addnews ready
// translator ready
// mail ready
function reltime($date,$short=true){
	$now = strtotime("now");
	$x = abs($now - $date);
	$d = (int)($x/86400);
	$x = $x % 86400;
	$h = (int)($x/3600);
	$x = $x % 3600;
	$m = (int)($x/60);
	$x = $x % 60;
	$s = (int)($x);
	if ($short){
		if ($d > 0)
			$o = $d."d".($h>0?$h."h":"");
		elseif ($h > 0)
			$o = $h."h".($m>0?$m."m":"");
		elseif ($m > 0)
			$o = $m."m".($s>0?$s."s":"");
		else
			$o = $s."s";
	}else{
		if ($d > 0)
			$o = "$d day".($d>1?"s":"").($h>0?", $h hour".($h>1?"s":""):"");
		elseif ($h > 0)
			$o = "$h hour".($h>1?"s":"").($m>0?", $m minute".($m>1?"s":""):"");
		elseif ($m > 0)
			$o = "$m minute".($m>1?"s":"").($s>0?", $s second".($s>1?"s":""):"");
		else
			$o = $s." second".($s>0?"s":"");		
	}
	return $o;
}

function relativedate($indate){
	$laston = round((strtotime("now")-strtotime($indate)) / 86400,0) . " days";
	if (substr($laston,0,2)=="1 ")
		$laston=translate_inline("1 day");
	elseif (date("Y-m-d",strtotime($laston)) == date("Y-m-d"))
		$laston=translate_inline("Today");
	elseif (date("Y-m-d",strtotime($laston)) == date("Y-m-d",strtotime("-1 day")))
		$laston=translate_inline("Yesterday");
	elseif (strpos($indate,"0000-00-00")!==false)
		$laston = translate_inline("Never");
	else {
		$laston= sprintf_translate("%s days", round((strtotime("now")-strtotime($indate)) / 86400,0));
		rawoutput(tlbutton_clear());
	}
	return $laston;
}

function checkday() {
	global $session,$revertsession,$REQUEST_URI;
	if ($session['user']['loggedin']){
		output_notl("<!--CheckNewDay()-->",true);
		if(is_new_day()){
			$session=$revertsession;
			$session['user']['restorepage']=$REQUEST_URI;
			$session['allowednavs']=array();
			addnav("","newday.php");
			redirect("newday.php");
		}
	}
}

function is_new_day(){
	global $session;
	$t1 = gametime();
	if ($session['user']['lasthit'] == "0000-00-00 00:00:00") return true;
	$t2 = convertgametime(strtotime($session['user']['lasthit']));
	$d1 = date("Y-m-d",$t1);
	$d2 = date("Y-m-d",$t2);
	if ($d1!=$d2){
		return true;
	}else{
		$now = gmdate("U",strtotime("now"));
		$lasthit = gmdate("U",strtotime($session['user']['lasthit']));
		if ($now - $lasthit > 86400 / getsetting("daysperday",4)){
			return true;
		}else{
			return false;
		}
	}
}

function getgametime(){
	return date("g:i a",gametime());
}

function gametime(){
	$time = convertgametime(strtotime("now"));
	return $time;
}

function convertgametime($intime){
	if ((int)getsetting("daysperday",4) < 2){
		//Use the requested time when there are less than 2 days per day,
		//otherwise you'll never get a new day.
		return $intime;
	}
	
	//calculate the GMT offset in hours.
	$gmtoffset = (int)date("O") / 100;
	//account for half-hour offsets (eg, -0630), or really any other
	//partial hour offset.
	$gmtoffset = (int)$gmtoffset + ($gmtoffset - (int)$gmtoffset) / 60 * 100;
	
	//adjust the requested time by the game offset
	$intime -= getsetting("gameoffsetseconds",0);
	
	//Midnight and now, GMT
	$midnight = gmmktime(0,0,0,date("m",$intime),date("d",$intime),date("Y",$intime));
	$now = gmmktime(date("H",$intime),date("i",$intime),date("s",$intime),date("m",$intime),date("d",$intime),date("Y",$intime)) ;
	
	//Seconds since GMT midnight.
	$s = $now - $midnight;
	
	// Figure out the days since midnight
	$d = $s * getsetting("daysperday",4) - $gmtoffset * 60 * 60;
	
	//Game date = seconds since midnight * days per day minus GMT offset.
	return $d;
}

function gametimedetails(){
	$ret = array();
	$ret['now'] = date("Y-m-d 00:00:00");
	$ret['gametime'] = gametime();
	$ret['daysperday'] = getsetting("daysperday", 4);
	$ret['secsperday'] = 86400/$ret['daysperday'];
	$ret['today'] = strtotime(date("Y-m-d 00:00:00", $ret['gametime']));
	$ret['tomorrow'] =
		strtotime(date("Y-m-d H:i:s",$ret['gametime'])." + 1 day");
	$ret['tomorrow'] = strtotime(date("Y-m-d 00:00:00",$ret['tomorrow']));
	$ret['secssofartoday'] = $ret['gametime'] - $ret['today'];
	$ret['secstotomorrow'] = $ret['tomorrow']-$ret['gametime'];
	$ret['realsecssofartoday'] = $ret['secssofartoday'] / $ret['daysperday'];
	$ret['realsecstotomorrow'] = $ret['secstotomorrow'] / $ret['daysperday'];
	$ret['dayduration'] = ($ret['tomorrow']-$ret['today'])/$ret['daysperday'];
	return $ret;
}

function secondstonextgameday($details=false) {
	if ($details===false) $details = gametimedetails();
	return strtotime("{$details['now']} + {$details['realsecstotomorrow']} seconds");
}

function getmicrotime(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}


?>

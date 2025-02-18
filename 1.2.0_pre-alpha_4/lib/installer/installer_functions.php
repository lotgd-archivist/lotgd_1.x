<?php
function create_db($dbname){
	output("`n`2Attempting to create your database...`n");
	$sql = "CREATE DATABASE $dbname";
	DB::query($sql);
	$error = DB::error();
	if (!$error){
		if (DB::select_db($dbname)){
			output("`@Success!`2  I was able to create the database and connect to it!`n");
		}else{
			output("`\$It seems I was not successful.`2  I didn't get any errors trying to create the database, but I was not able to connect to it.");
			output("I'm not sure what would have caused this error, you might try asking around in <a href='http://lotgd.net/forum/' target='_blank'>the LotGD.net forums</a>.");
		}
	}else{
		output("`\$It seems I was not successful.`2 ");
		output("The error returned by the database server was:");
		rawoutput("<blockquote>$error</blockquote>");
	}

}

$tipid=0;
function tip(){
	global $tipid;
	$tip = translate_inline("Tip");
	output_notl("<div style='cursor: pointer; cursor: hand; display: inline;' onMouseOver=\"tip$tipid.style.visibility='visible'; tip$tipid.style.display='inline';\" onMouseOut=\"tip$tipid.style.visibility='hidden'; tip$tipid.style.display='none';\">`i[ `b{$tip}`b ]`i",true);
	rawoutput("<div class='debug' id='tip$tipid' style='position: absolute; width: 200px; max-width: 200px; float: right;'>");
	$args = func_get_args();
	call_user_func_array("output",$args);
	rawoutput("</div></div>");
	rawoutput("<script language='JavaScript'>var tip$tipid = document.getElementById('tip$tipid'); tip$tipid.style.visibility='hidden'; tip$tipid.style.display='none';</script>");
	$tipid++;
}

function descriptors($prefix=""){
	require_once("lib/all_tables.php");
	$array = get_all_tables();
	$out = array();
	foreach ($array as $key=>$val) {
		$out[$prefix.$key]=$val;
	}
	return $out;
}

function availableWrappers() {
	$available = array();
	foreach (scandir('lib/classes/dbwrapper') as $file) {
		if($file{0} == '.') continue;
		if(substr($file, 0, 10) != 'dbwrapper_') continue;
		$class = substr($file, 0, -4);
		include_once 'lib/classes/dbwrapper/' . $file;
		if(!class_exists($class) || !call_user_func(array($class, 'canUse'))) {
			continue;
		}
		$type = eval("return $class::\$type;");
		$class = substr($class, 10);
		$available[$class] = $type;
	}
	return $available;
}

//This function is borrowed from the php manual.
function return_bytes($val) {
	$val = trim($val);
	$last = strtolower($val{strlen($val)-1});
	switch($last) {
		// The 'G' modifier is available since PHP 5.1.0
		case 'g':
		$val *= 1024;
		case 'm':
		$val *= 1024;
		case 'k':
		$val *= 1024;
	}
	return $val;
}
?>

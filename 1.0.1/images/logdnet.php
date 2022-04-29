<?php
// addnews ready
// mail ready
// translator ready
session_start();
//session_register("session");
if (isset($_GET['op']) && $_GET['op']=="register"){
	if (!isset($_SESSION['logdnet']) || $_SESSION['logdnet']['']==""){
		//register with LoGDnet
		$a = $_GET['a'];
		$c = $_GET['c'];
		$l = $_GET['l'];
		$d = $_GET['d'];
		$e = $_GET['e'];
		$v = $_GET['v'];
		$u = $_GET['u'];
		$url = $u. //central server
			"logdnet.php?". //logdnet script
			"addy=".rawurlencode($a). //server URL
			"&desc=".rawurlencode($d). //server description
			"&version=".rawurlencode($v). //game version
			"&admin=".rawurlencode($e). //admin email
			"&c=".$c. // player count (for my own records, this isn't used
					  // in the sorting mechanism)
			"&v=2".   // LoGDnet version.
			"&l=".$l. // primary language of this server -- you may change
					  // this if it turns out to be inaccurate.
			"";
		require_once("../lib/pullurl.php");
		$info = @pullurl($url);
		if ($info !== false) {
			$info = base64_decode(join("",$info));
			$_SESSION['logdnet'] = unserialize($info);
			echo "//registered\n";
			echo "//$url\n";
		}
	} else {
		$info = true;
	}
	if ($info !== false) {
		require_once("../lib/sanitize.php");
		$o = addslashes($_SESSION['logdnet']['']);
		$o = str_replace("\n\r","\n",$o);
		$o = str_replace("\r","\n",$o);
		$o = str_replace("\n","\\n",$o);
		echo "document.write(\"".sprintf(
					$o,
					full_sanitize($_SESSION['session']['user']['name']),
					htmlentities($_SESSION['session']['user']['login']) .
					":".$_SERVER['HTTP_HOST'].$_SERVER['HTTP_REFERER']).
			"\");";
	} else {
		// We failed to connect to central, just use our local image!
		$image = join("",file("paypal1.gif"));
		header("Content-Type: image/gif");
		header("Content-Length: ".strlen($image));
		echo $image;
	}
}elseif (isset($_SESSION['logdnet'])){
	header("Content-Type: ".$_SESSION['logdnet']['content-type']);
	header("Content-Length: ".strlen($_SESSION['logdnet']['image']));
	echo $_SESSION['logdnet']['image'];
}else{
	$image = join("",file("paypal1.gif"));
	header("Content-Type: image/gif");
	header("Content-Length: ".strlen($image));
	echo $image;
}
?>

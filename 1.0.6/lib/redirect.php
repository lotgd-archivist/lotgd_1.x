<?php
// translator ready
// addnews ready
// mail ready
function redirect($location,$reason=false){
	global $session,$REQUEST_URI;
	// This function is deliberately not localized.  It is meant as error
	// handling.
	if ($location!="badnav.php"){
		$session['allowednavs']=array();
		addnav("",$location);
	}
	if (strpos($location,"badnav.php")===false)
		$session['output']=
			"<a href=\"".HTMLEntities($location)."\">Click here.</a>";
	restore_buff_fields();
	$session['debug'].="Redirected to $location from $REQUEST_URI.  $reason<br>";
	saveuser();
	@header("Location: $location");
	echo "<html><head><meta http-equiv='refresh' content='0;url=$location'></head></html>";
	//echo "<a href='$location'>$location</a><br><br>";
	//echo $location;
	//echo $session['debug'];
	exit();
}
?>

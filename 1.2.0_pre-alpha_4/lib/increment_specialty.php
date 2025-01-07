<?php
// translator ready
// addnews ready
// mail ready

function increment_specialty($colorcode, $spec=false){
	global $user;
	if ($spec !== false) {
		$revertspec = $user->specialty;
		$user->specialty = $spec;
	}
	tlschema("skills");
	if ($user->specialty!=""){
		modulehook("incrementspecialty",
				array("color"=>$colorcode));
	}else{
		output("`7You have no direction in the world, you should rest and make some important decisions about your life.`0`n");
	}
	tlschema();
	if ($spec !== false) {
		$user->specialty = $revertspec;
	}
}
?>

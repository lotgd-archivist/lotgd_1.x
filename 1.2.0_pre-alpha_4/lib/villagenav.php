<?php
// translator ready
// addnews ready
// mail ready
function villagenav($extra=false)
{
	global $user;
	$loc = $user->location;
	if ($extra === false) $extra="";
	$args = modulehook("villagenav");
	if (isset($args['handled']) && $args['handled']) return;
	tlschema("nav");
	if ($user->alive) {
		addnav(array("V?Return to %s", $loc), "village.php$extra");
	} else {
		// user is dead
		addnav("S?Return to the Shades","shades.php");
	}
	tlschema();
}
?>

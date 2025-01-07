<?php
// translator ready
// addnews ready
// mail ready
function superusernav()
{
	global $SCRIPT_NAME, $user;
	tlschema("nav");
	addnav("Navigation");
	if ($user->superuser &~ SU_DOESNT_GIVE_GROTTO) {
		$script = substr($SCRIPT_NAME,0,strpos($SCRIPT_NAME,"."));
		if ($script != "superuser") {
			$args = modulehook("grottonav");
			if (!isset($args['handled']) || !$args['handled']) {
				addnav("G?Return to the Grotto", "superuser.php");
			}
		}
	}
	$args = modulehook("mundanenav");
	if (!isset($args['handled']) || !$args['handled'])
		addnav("M?Return to the Mundane", "village.php");
	tlschema();
}
?>

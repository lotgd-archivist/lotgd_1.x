<?php
require_once 'common.php';
require_once 'lib/showform.php';

check_su_access(SU_EDIT_ADVANCE_CONFIG);
if(!Settings::doShowAdvance()) {
	redirect('configuration.php', 'Advance settings disabled.');
	die();
}

tlschema("configuration");

page_header("Advance Game Settings");

$op = httpget('op');
if($op == "save") {
	require_once 'lib/gamelog.php';
	// These need to be positive ints
	$equpimentcosts = httppost('equpimentcosts');
	$equpimentcosts = explode(',', $equpimentcosts);
	foreach ($equpimentcosts as &$cost) {
		$cost = abs((int)$cost);
	}
	httppostset('equpimentcosts', implode(',', $equpimentcosts));
	
	// These need to be positive ints
	$experiance = httppost('experiance');
	$experiance = explode(',', $experiance);
	foreach ($experiance as &$cost) {
		$cost = abs((int)$cost);
	}
	httppostset('experiance', implode(',', $experiance));
	
	$post = httpallpost();
	$old = clone $settings;
	foreach($post as $key=>$val) {
		if(!isset($settings[$key]) || (stripslashes($val) != $settings[$key])) {
			if(!isset($old[$key]))
				$old[$key] = "";
			savesetting($key, stripslashes($val));
			output("Setting %s to %s`n", $key, stripslashes($val));
			gamelog("`@Changed core setting `^$key`@ from `#{$old[$key]}`@ to `&$val`0", "settings");
			// Notify every module
		// modulehook("changesetting", array("module" => "core", "setting" => $key, "old" => $old[$key], "new" => $val), true); // Do modules really need to be told about these?
		}
	}
	output("`^Settings saved.`0");
}
require_once ("lib/superusernav.php");
superusernav();
addnav("Module Manager", "modules.php");
addnav("Game Settings");
if($user->superuser & SU_EDIT_CONFIG)
	addnav("Standard settings", "configuration.php");
addnav("Advance Game Settings", "advance_config.php");
addnav("", $REQUEST_URI);

module_editor_navs('settings', 'configuration.php?op=modulesettings&module=');

$advance = array(
	'Max Level,title',
	'This setting changes the highest level that a player can reach in the game.,note',
	'If you change this&#44; you must also add in more creatures and masters and`nupdate the experiance array.,note',
	'Otherwise&#44; your players will be able to go straight from level 15 to your`nhighest and will constantly be fighting doppledangers. Also&#44; their masters`nwill be the same.,note',
	'maxlevel'=>'The highest possible level in the game,int',
	'Equpiment Costs,title',
	'This setting changes the base costs of all new weapons and armour in the game.,note',
	'If you want to increase the attack of weapons and the defence of armour&#44; you must also add in any necessary&#44;`nadditonal costs.,note',
	'equpimentcosts'=>'A comma separated list of the costs of equipment,text',
	'Experiance,title',
	'This setting changes the base amount of experiance for each level that a player can reach.,note',
	'If you add in extra levels&#44; you `bMUST`b append an amount to this settings.,note',
	'experiance'=>'A comma separated list of the experiance thresholds,text',
	'Commentary,title',
	'This setting changes the input length for the commentary.,note',
	'Warning: The commentary.comment field must reflect the size you selected. Default is 200!,note',
	'chatlength'=>'A number defining the amount of chars for chatinput,enum,200,Default (200),300,300,500,500,700,700,1000,1000',
);

loadsettings();
$vals = $settings->toArray();
	
rawoutput("<form action='advance_config.php?op=save' method='post'>");
addnav("", "advance_config.php?op=save");
showform($advance, $vals);
rawoutput("</form>");

page_footer();

<?php
/**
 * Settings handler functions
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage settings
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
// translator ready
// addnews ready
// mail ready

/**
 * Handler function for {@link Settings::savesetting()}
 *
 * @param string $settingname The name of the setting to set
 * @param mixed $value What to set the setting to
 * @return bool True on success
 */
function savesetting($settingname,$value){
	return Settings::savesetting($settingname, $value);
}

/**
 * Handler function for {@link Settings::loadsettings()}
 */
function loadsettings(){
	return Settings::loadsettings();
}

/**
 * Handler function for {@link Settings::clearsettings()}
 */
function clearsettings(){
	Settings::clearsettings();
}

/**
 * Handler function for {@link Settings::getsetting()}
 *
 * @param string $settingname The name of the setting to get
 * @param mixed $default The default value
 * @return mixed The setting's value
 */
function getsetting($settingname,$default){
	return Settings::getsetting($settingname, $default);
}

?>

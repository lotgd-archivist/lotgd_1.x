<?php
/**
 * Prefs User class file
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage users
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
/**
 * Prefs Users class
 *
 * The class used to change a users prefs, such as
 * password, which can't be changed by the normal
 * {@link User} class for security reasons.
 * 
 * Also shows how to get around the restrictions
 * set by the {@link User} class.
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage users
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
class PrefsUser extends User {
	function setPassword($newPassword) {
		if(strpos($newPassword, '!md5!') !== false) {
			$password = md5(substr($newPassword, -32));
		} elseif (strpos($newPassword, '!md52!') !== false) {
			$password = substr($newPassword, -32);
		} else {
			$password = md5(md5($newPassword));
		}
		$this->values['password'] = $password;
		$this->changed['password'] = true;
	}
	
	static public function getUser($user) {
		if(is_numeric($user)) {
			// acctid
			$user = (int)$user;
			$sql = 'SELECT acctid FROM `' . DB::prefix('accounts') . '` WHERE `acctid`=' . $user;
		} else {
			// login
			$user = DB::escape_string($user);
			$sql = 'SELECT acctid FROM `' . DB::prefix('accounts') . '` WHERE `login` = \'' . $user . '\'';
		}
		$res = DB::query($sql);
		if(DB::num_rows($res)) {
			$row = DB::fetch_assoc($res);
			unset(self::$users[$row['acctid']]);
			$newUser = new self($row['acctid']);
			self::$users[$row['acctid']] = $newUser;
			return $newUser;
		} else {
			return false;
		}
	}
}

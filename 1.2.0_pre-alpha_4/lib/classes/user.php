<?php
/**
 * User class file
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage users
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
/**
 * Users class
 *
 * 
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage users
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
class User implements Iterator, ArrayAccess {
	/**
	 * User values
	 *
	 * @var array
	 */
	protected $values = array();
	/**
	 * Values that have been changed
	 *
	 * @var array
	 */
	protected $changed = array();
	/**
	 * Original values for better handling of what has changed
	 * 
	 * @var array
	 */
	protected $original = array();
	/**
	 * Stores extra values for the current session
	 * 
	 * @var array
	 */
	protected $extras = array();
	/**
	 * Holds all the users that have been requested
	 * 
	 * This is used to ensure that users are only got once
	 * per page hit, even if requested multiple times.
	 *
	 * @var array
	 */
	static protected $users = array();
	
	static public function getNonUser() {
		return new self(0);
	}
	/**
	 * Gets a user from the database
	 * 
	 * Uses either acctid or login depending on the value of <var>$user</var>.
	 * If <var>$user</var> is numeric, it uses acctid, otherwise login.
	 *
	 * @todo Provide a way to force login or acctid
	 * @param int|string $user
	 * @return User
	 */
	static public function getUser($user) {
		$sql = 'SELECT acctid FROM `' . DB::prefix('accounts') . '` WHERE ';
		if(is_numeric($user)) {
			// acctid
			$user = (int)$user;
			$sql .= '`acctid`=' . $user;
		} else {
			// login
			$user = DB::escape_string($user);
			$sql .= '`login` = \'' . $user . '\'';
		}
		$sql .= ' AND locked = 0';
		$res = DB::query($sql);
		if(DB::num_rows($res)) {
			$row = DB::fetch_assoc($res);
			if(isset(self::$users[$row['acctid']])) {
				return self::$users[$row['acctid']];
			}
			$newUser = new self($row['acctid']);
			self::$users[$row['acctid']] = $newUser;
			return $newUser;
		} else {
			return false;
		}
	}
	/**
	 * Creates a User from the given acctid
	 *
	 * @param int $acctid
	 */
	protected function __construct($acctid) {
		$acctid = (int)$acctid;
		$sql = "SELECT * FROM `" . DB::prefix("accounts") . "` WHERE `acctid`={$acctid} LIMIT 1";
		$res = DB::query($sql);
		if(DB::num_rows($res) < 1) {
			// This shouldn't happen
			return;
		}
		$row = DB::fetch_assoc($res);
		if(is_array($row)) {
			$this->values = $row;
			$this->original = $row;
		}
	}
	
	public function __toString() {
		return output_array($this->values);
	}
	
	/**
	 * Gets the given value
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		if(method_exists($this, 'get' . $name)) {
			$method = 'get' . $name;
			return $this->$method();
		}
		if(isset($this->values[$name])) {
			return $this->values[$name];
		} elseif(isset($this->extras[$name])) {
			return $this->extras[$name];
		} else {
			return null;
		}
	}
	/**
	 * Gets the users dragonpoints
	 * 
	 * Called by {@link User::__get()} when dragonpoints
	 * is requested. Ensures that an array is returned.
	 * 
	 * @usedby User::__get()
	 * @return array
	 */
	public function getdragonpoints() {
		if(is_array($this->values['dragonpoints'])) {
			return $this->values['dragonpoints'];
		} else {
			$this->values['dragonpoints'] = unserialize($this->values['dragonpoints']);
			if(!is_array($this->values['dragonpoints'])) {
				if($this->values['dragonpoints']) {
					$this->values['dragonpoints'] = array($this->values['dragonpoints']);
				} else {
					$this->values['dragonpoints'] = array();
				}
			}
			return $this->values['dragonpoints'];
		}
	}
	/**
	 * Gets the users prefs
	 * 
	 * Called by {@link User::__get()} when prefs
	 * is requested. Ensures that an array is returned.
	 * 
	 * @usedby User::__get()
	 * @return array
	 */
	public function getprefs() {
		if(!isset($this->values['prefs'])) {
			$this->values['prefs'] = array();
			$this->values['prefs']['attractedTo'] = ((array_key_exists('sex', $this->values) && $this->values['sex']) == SEX_MALE ? SEX_FEMALE : SEX_MALE);
			return $this->values['prefs'];
		}
		if(is_array($this->values['prefs'])) {
			if(!isset($this->values['prefs']['attractedTo'])) {
				$this->values['prefs']['attractedTo'] = ($this->values['sex'] == SEX_MALE ? SEX_FEMALE : SEX_MALE);
				$this->changed['prefs'] = true;
			}
			return $this->values['prefs'];
		} else {
			$this->values['prefs'] = unserialize($this->values['prefs']);
			if(!is_array($this->values['prefs'])) {
				if($this->values['prefs']) {
					$this->values['prefs'] = array($this->values['prefs']);
				} else {
					$this->values['prefs'] = array();
				}
			}
			if(!isset($this->values['prefs']['attractedTo'])) {
				$this->values['prefs']['attractedTo'] = ($this->values['sex'] == SEX_MALE ? SEX_FEMALE : SEX_MALE);
				$this->changed['prefs'] = true;
			}
			return $this->values['prefs'];
		}
	}
	/**
	 * Adds dragon points to the user
	 * 
	 * If the points provided is an array, the
	 * two arrays are merged. Else the point it
	 * appended to the end of the array.
	 * 
	 * @param $point array|string The point(s) to add
	 * @uses User::getdragonpoints()
	 */
	public function addDragonPoints($point) {
		// Ensure it's an array
		$this->getdragonpoints();
		if(is_array($point)) {
			$this->values['dragonpoints'] = array_merge($this->values['dragonpoints'], $point);
		} else {
			$this->values['dragonpoints'][] = $point;
		}
		$this->changed['dragonpoints'] = true;
	}
	/**
	 * Adds or sets a user preference
	 * 
	 * @param $pref The name of the preference
	 * @param $value The value to set the preference to
	 * @uses User::getprefs()
	 */
	public function addPref($pref, $value) {
		// Ensure it's an array
		$this->getprefs();
		$this->values['prefs'][$pref] = $value;
		if($this->values['prefs'] != $this->original['prefs']) $this->changed['prefs'] = true;
		else $this->changed['prefs'] = false;
	}
	/**
	 * Checks if the given value is set
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function __isset($name) {
		if(method_exists($this, 'has' . $name)) {
			$method = 'has' . $name;
			return $this->$method();
		}
		return isset($this->values[$name]);
	}
	/**
	 * Sets the given value
	 * 
	 * Has checks to ensure that the type for the
	 * value is the correct one.
	 *
	 * @param string $name The name of the value
	 * @param mixed $value The value to set it to
	 */
	public final function __set($name, $value) {
		if(method_exists($this, 'set' . $name)) {
			$method = 'set' . $name;
			return $this->$method($value);
		}
		// 
		// mmm, very big switch
		switch ($name) {
			case 'acctid':
			case 'password':
			case 'login':
			case 'regdate':
				// Not allowed to change these. If you must, use SQL or subclass.
				debug('Tried setting ' . $name . '. Not allowing.');
				break;
			case 'seenmaster':
			case 'loggedin':
			case 'seendragon':
			case 'locked':
			case 'sentnotice':
			case 'refererawarded':
			case 'banoverride':
			case 'beta':
			case 'slaydragon':
			case 'fedmount':
				// Booleans
				if(array_key_exists($name, $this->values) && $value == $this->values[$name]) break;
				$this->values[$name] = (bool)$value;
				if(!array_key_exists($name, $this->original) || $this->values[$name] != $this->original[$name]) $this->changed[$name] = true;
				else $this->changed[$name] = false;
				break;
			case 'alive':
				if(array_key_exists('alive', $this->values) && $value == $this->values['alive']) break;
				// Let them have their hook, but only if we actually changed
				$this->values[$name] = (bool)$value;
				if(array_key_exists('alive', $this->original) && $this->values[$name] != $this->original[$name]) $this->changed[$name] = true;
				else $this->changed[$name] = false;
				if(!$value) {
					// we died!
					modulehook('killedplayer', array('acctid'=>$this->acctid));
				} else {
					// we revived!
					modulehook('resurrectplayer', array('acctid'=>$this->acctid));
				}
			case 'experience':
			case 'gold':
			case 'level':
			case 'defense':
			case 'attack':
			case 'marriedto':
			case 'hitpoints':
			case 'gems':
			case 'weaponvalue':
			case 'armorvalue':
			case 'turns':
			case 'resurrections':
			case 'weapondmg':
			case 'armordef':
			case 'age':
			case 'charm':
			case 'playerfights':
			case 'dragonkills':
			case 'transferredtoday':
			case 'soulpoints':
			case 'gravefights':
			case 'deathpower':
			case 'donation':
			case 'donationspent':
			case 'amountouttoday':
			case 'pk':
			case 'dragonage':
			case 'bestdragonage':
				// Unsigned ints. Warn if trying to set <0.
				$value = (int)$value;
				if($value < 0) {
					debug('Tried setting ' . $name . ' to ' . $value . ' when it must be >= 0. Setting to 0 instead.');
					$value = 0;
				}
				if($value == $this->values[$name]) break;
				$this->values[$name] = $value;
				if($this->values[$name] != $this->original[$name]) $this->changed[$name] = true;
				else $this->changed[$name] = false;
				break;
			case 'laston':
			case 'lastmotd':
			case 'lasthit':
			case 'pvpflag':
			case 'recentcomments':
			case 'biotime':
			case 'clanjoindate':
				// datetimes
				$time = strtotime($value);
				if(array_key_exists($name, $this->values) && date('Y-m-d H:i:s', $time) == $this->values[$name]) break;
				$this->values[$name] = date('Y-m-d H:i:s', $time);
				if(!array_key_exists($name, $this->original) || $this->values[$name] != $this->original[$name]) $this->changed[$name] = true;
				else $this->changed[$name] = false;
				break;
			case 'sex':
				if($value == $this->values[$name]) break;
				if($value == SEX_MALE || $value == SEX_FEMALE) {
					$this->values['sex'] = $value;
					$this->changed[$name] = true;
				} else {
					debug('Tried setting sex to ' . $value . ' which isn\'t a sex constant. Not allowing.');
				}
				break;
			case 'maxhitpoints':
				$min = $this->level * 10 - 9;
				$value = (int)$value;
				if($value < $min) {
					debug('Tried setting maxhitpoints to ' . $value . ' when min to prevent permadeath on DK is ' . $min . '. Setting to ' . $min . ' instead.');
					$value = $min;
				}
				if($value == $this->values[$name]) break;
				$this->values[$name] = $value;
				if($this->values[$name] != $this->original[$name]) $this->changed[$name] = true;
				else $this->changed[$name] = false;
				break;
			default:
				if(!array_key_exists($name, $this->values)) {
					$this->extras[$name] = $value;
					break;
				}
				if($value == $this->values[$name]) break;
				$this->values[$name] = $value;
				if(!array_key_exists($name, $this->original) || $this->values[$name] != $this->original[$name]) $this->changed[$name] = true;
				else $this->changed[$name] = false;
		}
		return $this->$name;
	}
	/**
	 * Prevents unsetting of values
	 *
	 * @param string $name
	 */
	public function __unset($name) {
		if(method_exists($this, 'remove' . $name)) {
			$method = 'remove' . $name;
			$this->$method();
		}
		// No. (:
		debug("Trying to unset user value $name. Not doing.");
	}
	/**
	 * Writes the user to the database on page termination
	 *
	 */
	public function __destruct() {
		if (defined("NO_SAVE_USER") || $this->acctid == 0) return;
		if(is_array($this->values['dragonpoints'])) {
			$this->values['dragonpoints'] = serialize($this->values['dragonpoints']);
		}
		if(is_array($this->values['prefs'])) {
			$this->values['prefs'] = serialize($this->values['prefs']);
		}
		if($this->hitpoints == 0 && $this->alive == true) $this->alive = false;
		elseif($this->hitpoints != 0 && $this->alive == false) $this->alive = true;
		$sql = "UPDATE `" . db_prefix("accounts") . "` SET ";
		$changed = array_keys($this->changed);
		if(!count($changed)) {
			return;
		} else {
			$this->values['laston'] = date('Y-m-d H:i:s');
			$changed[] = 'laston';
		}
		foreach($changed as $val) {
			$sql .= "`{$val}`='";
			$sql .= DB::escape_string($this->values[$val]);
			$sql .= "', ";
		}
		$sql = substr($sql, 0, -2);
		$sql .= 'WHERE acctid=' . $this->acctid;
		DB::query($sql);
	}
	// Iterator methods
	/**
	* Rewinds the values array
	*/
	public function rewind() {
		reset($this->values);
	}

	/**
	 * Returns the current item in the values array
	 *
	 * @return mixed
	 */
	public function current() {
		return current($this->values);
	}

	/**
	 * Returns the current key in the values array
	 *
	 * @return string
	 */
	public function key() {
		return key($this->values);
	}

	/**
	 * Returns the next item in the values array
	 *
	 * @return mixed
	 */
	public function next() {
		return next($this->values);
	}

	/**
	 * Checks if the current item is valid
	 *
	 * @return bool
	 */
	public function valid() {
		return ($this->current() !== false);
	}
	
	// ArrayAccess methods
	/**
	* Checks if an item in the values array is set
	*
	* @param string $key The key of the item
	* @return bool If the item is set
	* @uses User::__isset()
	*/
	public function offsetExists($key) {
		return $this->__isset($key);
	}

	/**
	 * Returns the item for a given key
	 *
	 * @param string $key The key of the item
	 * @return mixed The item
	 * @uses User::__get()
	 */
	public function offsetGet($key) {
		return $this->__get($key);
	}

	/**
	 * Sets the item for a given key
	 *
	 * @param string $key The key of the item
	 * @param mixed $value The value that the key is to be set to
	 * @uses User::__set()
	 */
	public function offsetSet($key, $value) {
		$this->__set($key, $value);
	}

	/**
	 * Unsets an item in the values array
	 *
	 * @param string $key The key of the item
	 * @return bool If the item is set
	 * @uses User::__unset()
	 */
	public function offsetUnset($key) {
		$this->__unset($key);
	}
}

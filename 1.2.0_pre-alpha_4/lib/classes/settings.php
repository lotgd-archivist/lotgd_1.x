<?php
/**
 * Settings class file
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage settings
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
/**
 * Settings class
 *
 * This class is a holder for all settings
 * and provides methods for accessing them.
 *
 * It implements Iterator, so you can go over
 * all the settings in a foreach loop.
 *
 * It implements ArrayAccess, so you can
 * access settings for a variable containing
 * the singleton as if it was an array.
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage settings
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
class Settings implements Iterator, ArrayAccess {
	/**#@+
	 * @static
	 */
	/**
	 * @var Settings The single instance of the Settings class
	 */
	protected static $singleton;
	/**
	 * @var array The loaded settings
	 */
	protected static $settings = array();
	/**
	 * @var array Any new settings to insert
	 */
	protected static $insert = array();
	/**
	 * @var array Any modified settings to update
	 */
	protected static $update = array();
	/**
	 * @var boolean Use datacaching
	 */
	protected static $usecache = null;
	/**
	 * @var string The path to the datacache
	 */
	protected static $cachepath = null;
	/**@-*/
	
	/**
	 * Returns the single instance of the Settings class.
	 *
	 * If the class doesn't exist, it is created.
	 *
	 * @static
	 * @return Settings The single instance
	 */
	public static function getSingleon() {
		if(!isset(self::$singleton)) {
			$c = __CLASS__;
			self::$singleton = new $c;
		}
		return self::$singleton;
	}

	/**
	 * Returns a setting.
	 *
	 * If a get method exists (get$setting), that method
	 * is called. Otherwise, $this->getsetting() is
	 * called.
	 *
	 * Used when access a setting via $settings->setting
	 * or $settings['setting'].
	 *
	 * @uses Settings::getsetting()
	 * @param string $name The name of the setting to get
	 * @return mixed The setting
	 */
	public function __get($name) {
		if(method_exists($this, 'get' . $name)) {
			$method = 'get' . $name;
			return $this->$method();
		} else {
			return $this->getsetting($name, false);
		}
	}

	/**
	 * Updates a setting.
	 *
	 * If a set method exists (set$setting), that method
	 * is called. Otherwise, $this->savesetting() is
	 * called.
	 *
	 * Used when changing a setting via $settings->setting
	 * or $settings['setting'].
	 *
	 * @uses Settings::savesetting()
	 * @param string $name The name of the setting to set
	 * @param mixed $value What to set the setting to
	 * @return bool True on success
	 */
	public function __set($name, $value) {
		if(method_exists($this, 'set' . $name)) {
			$method = 'set' . $name;
			return $this->$method($value);
		} else {
			return $this->savesetting($name, $value);
		}
	}
	
	/**
	 * Enables the showing of advance settings in the grotto
	 */
	public static function enableShowAdvance() {
		if(!isset(self::$settings['showAdvance'])) {
			self::$settings['showAdvance'] = true;
			self::$insert['showAdvance'] = true;
		} else {
			self::$settings['showAdvance'] = !self::$settings['showAdvance'];
			self::$update['showAdvance'] = self::$settings['showAdvance'];
		}
	}
	
	/**
	 * Returns if we show advance settings
	 * 
	 * @return boolean
	 */
	public static function doShowAdvance() {
		return isset(self::$settings['showAdvance']) && self::$settings['showAdvance'];
	}
	
	/**
	 * Sets if we're using datacache
	 * 
	 * Can only be set once (hopefully in
	 * dbconnect.php)
	 * 
	 * @param boolean $host Use datacache
	 */
	public static function setUseCache($usecache) {
		if(self::$usecache === null) {
			self::$usecache = (bool)$usecache;
		}
	}
	
	/**
	 * Sets the datacache path
	 * 
	 * Can only be set once (hopefully in
	 * dbconnect.php)
	 * 
	 * @param string $cachepath The datacache path
	 */
	public static function setCachePath($cachepath) {
		if(self::$cachepath === null) {
			self::$cachepath = $cachepath;
		}
	}

	/**
	 * Checks is a setting is set.
	 *
	 * @param string $name The name of the setting to check.
	 * @return bool If the settings is set.
	 */
	public function __isset($name) {
		$name = substr($name, 0, 20);
		return isset(self::$settings[$name]);
	}

	/**
	 * Unsets a setting
	 *
	 * @param string $name The name of the setting to unset
	 * @return null
	 */
	public function __unset($name) {
		$name = substr($name, 0, 20);
		unset(self::$settings[$name]);
	}

	/**
	 * Creates the Settings class and loads settings.
	 */
	protected function __construct() {
		$this->loadsettings();
	}

	/**
	 * Saves the settings and destroys the class.
	 */
	public function __destruct() {
		$this->clearsettings();
	}

	/**
	 * Loads settings from the database
	 */
	static public function loadsettings() {
		if(!self::$settings) {
			// Done here so that datacache() works
			self::$settings['usedatacache'] = self::$usecache;
			self::$settings['datacachepath'] = self::$cachepath;
			$settings=datacache("game-settings");
			if (!is_array($settings)){
				$settings=array();
				$sql = "SELECT * FROM " . DB::prefix("settings");
				$result = DB::query($sql);//DB::query_cached($sql,"game-settings");
				while ($row = DB::fetch_assoc($result)) {
					$settings[$row['setting']] = $row['value'];
				}
				DB::free_result($result);
				updatedatacache("game-settings",$settings);
			}
			self::$settings = $settings;
			self::$settings['usedatacache'] = self::$usecache;
			self::$settings['datacachepath'] = self::$cachepath;
		}
	}

	/**
	 * Saves and empties the settings array
	 *
	 * @uses Settings::writesettings()
	 */
	static public function clearsettings() {
		self::writesettings();
		self::$settings = array();
	}

	/**
	 * Writes settings to the database.
	 *
	 * Inserts all new settings (All items in the
	 * {@link Settings::$insert} array) into the database.
	 *
	 * Updates all modified settings (All items in the
	 * {@link Settings::$update} array) in the database.
	 *
	 * If the database was changed, the datacache is updaed.
	 *
	 * The {@link Settings::$insert} and {@link Settings::$update}
	 * arrays are then emptied.
	 *
	 * @todo Use REPLACE INTO instead of INSERT and UPDATE
	 */
	static protected function writesettings() {
		foreach (self::$insert as $settingname=>$value) {
			$sql = "INSERT INTO " . DB::prefix("settings") . " (setting,value) VALUES (\"".addslashes($settingname)."\",\"".addslashes($value)."\")";
			DB::query($sql);
		}
		foreach (self::$update as $settingname=>$value) {
			$sql = "UPDATE " . DB::prefix("settings") . " SET value=\"".addslashes($value)."\" WHERE setting=\"".addslashes($settingname)."\"";
			DB::query($sql);
		}
		if(count(self::$update) || count(self::$insert)) {
			invalidatedatacache('game-settings');
		}
		self::$update = array();
		self::$insert = array();
	}

	/**
	 * Saves a setting
	 *
	 * If the setting already exists, {@link Settings::$update}
	 * is updated. Otherwise, {@link Settings::$insert} is updated.
	 *
	 * @see Settings::writesettings()
	 * @param string $settingname The name of the setting to set
	 * @param mixed $value What to set the setting to
	 * @return bool True on success
	 */
	static public function savesetting($settingname,$value) {
		$settingname = substr($settingname, 0, 20);
		self::loadsettings();
		if(isset(self::$settings[$settingname])) {
			if(self::$settings[$settingname] != $value) {
				self::$settings[$settingname] = $value;
				self::$update[$settingname] = $value;
			}
		} elseif ($value) {
			self::$insert[$settingname] = $value;
			self::$settings[$settingname] = $value;
		} else {
			return false;
		}
		return true;
	}

	/**
	 * Gets a settings
	 *
	 * Saves the setting with the default value if
	 * it isn't already in the settings array.
	 *
	 * @param string $settingname The name of the setting to get
	 * @param mixed $default The default value
	 * @return mixed The setting's value
	 */
	static public function getsetting($settingname, $default) {
		$settingname = substr($settingname, 0, 20);
		if(!isset(self::$settings[$settingname])) {
			self::loadsettings();
		} else {
			return self::$settings[$settingname];
		}
		if(!isset(self::$settings[$settingname])) {
			self::savesetting($settingname, $default);
			return $default;
		} else {
			return self::$settings[$settingname];
		}
	}

	/**
	 * Returns all the settings as an array
	 *
	 * @return array
	 */
	public function toArray() {
		return self::$settings;
	}

	// Iterator methods
	/**
	* Rewinds the settings array
	*/
	public function rewind() {
		reset(self::$settings);
	}

	/**
	 * Returns the current item in the settings array
	 *
	 * @return mixed
	 */
	public function current() {
		return current(self::$settings);
	}

	/**
	 * Returns the current key in the settings array
	 *
	 * @return string
	 */
	public function key() {
		return key(self::$settings);
	}

	/**
	 * Returns the next item in the settings array
	 *
	 * @return mixed
	 */
	public function next() {
		return next(self::$settings);
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
	* Checks if an item in the settings array is set
	*
	* @param string $key The key of the item
	* @return bool If the item is set
	* @uses Settings::__isset()
	*/
	public function offsetExists($key) {
		return $this->__isset($key);
	}

	/**
	 * Returns the item for a given key
	 *
	 * @param string $key The key of the item
	 * @return mixed The item
	 * @uses Settings::__get()
	 */
	public function offsetGet($key) {
		return $this->__get($key);
	}

	/**
	 * Sets the item for a given key
	 *
	 * @param string $key The key of the item
	 * @param mixed $value The value that the key is to be set to
	 * @uses Settings::__set()
	 */
	public function offsetSet($key, $value) {
		$this->__set($key, $value);
	}

	/**
	 * Unsets an item in the settings array
	 *
	 * @param string $key The key of the item
	 * @return bool If the item is set
	 * @uses Settings::__unset()
	 */
	public function offsetUnset($key) {
		$this->__unset($key);
	}
}

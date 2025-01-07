<?php
/**
 * Session class file
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2009, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage sessions
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
/**
 * Session class
 *
 * This class allows changing session values.
 *
 * It implements Iterator, so you can go over
 * all the values in a foreach loop.
 *
 * It implements ArrayAccess, so you can
 * access values for a variable containing
 * the singleton as if it was an array.
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2009, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage sessions
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
class Session implements ArrayAccess, Iterator, Serializable{
	/**
	 * @var array The session information
	 */
	protected $session = array();
	/**
	 * @var array The original session information, used for reverting
	 */
	protected $revert = array();
	/**#@+
	 * @static
	 */
	/**
	 * @var Session The single instance of the Session class
	 */
	protected static $instance = null;
	/**
	 * Checks if a key exists
	 *
	 * @param scalar $key
	 * @return bool
	 * @uses Session::offsetExists()
	 */
	public static function exists($key) {
		return self::create()->offsetExists($key);
	}
	/**
	 * Gets a key
	 *
	 * @param scalar $key
	 * @return mixed
	 * @uses Session::offsetGet()
	 */
	public static function get($key) {
		return self::create()->offsetGet($key);
	}
	/**
	 * Gets a nested key
	 *
	 * @param scalar $key
	 * @param scalar $otherKey
	 * @return mixed
	 * @uses Session::offsetGetNested()
	 */
	public static function getNested($key, $otherKey) {
		return self::create()->offsetGetNested($key, $otherKey);
	}
	/**
	 * Sets a key to a value
	 *
	 * @param scalar $key
	 * @param mixed $value
	 * @return void
	 * @uses Session::offsetSet()
	 */
	public static function set($key, $value) {
		self::create()->offsetSet($key, $value);
	}
	/**
	 * Sets a nested key to a value
	 *
	 * @param scalar $key
	 * @param scalar $otherkey
	 * @param mixed $value
	 * @return void
	 * @uses Session::offsetSetNested()
	 */
	public static function setNested($key, $otherkey, $value) {
		self::create()->offsetSetNested($key, $otherkey, $value);
	}
	/**
	 * Deletes a key
	 *
	 * @param scalar $key
	 * @return void
	 * @uses Session::offsetUnset()
	 */
	public static function delete($key) {
		self::create()->offsetUnset($key);
	}
	/**
	 * Deletes a nested key
	 *
	 * @param scalar $key
	 * @param scalar $otherKey
	 * @return void
	 * @uses Session::offsetUnsetNested()
	 */
	public static function deleteNested($key, $otherKey) {
		self::create()->offsetUnsetNested($key, $otherKey);
	}
	/**
	 * Empties the session array
	 *
	 * @uses Session::wipe()
	 */
	public static function clean() {
		self::create()->wipe();
	}
	/**
	 * Creates the instance of the Session class
	 * 
	 * This is used to ensure that only one Session class
	 * exists, so we don't get multiple things trying to
	 * access session data
	 *
	 * @return Session
	 * @uses Session::__construct()
	 */
	public static function create() {
		if(self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Unsets the instance of the Session class
	 *
	 * This is used if you need the session to be written
	 * before the normal garabage collection that does it
	 * at the end of script execution.
	 * 
	 * @uses Session::__destruct();
	 */
	public static function destruct() {
		//unset(self::$instance);
		self::$instance=null;
	}
	/**
	 * Reverts {@link Session::$session} to {@link Session::$revert}
	 * 
	 * @uses Session::restore()
	 */
	public static function revert() {
		self::create()->restore();
	}
	/**
	 * Returns the session array
	 *
	 * @return array
	 * @uses Session::getRecord()
	 */
	public static function record() {
		return self::create()->getRecord();
	}
	/**@-*/
	/**
	 * Creates a Session class
	 *
	 * The constructor for the Session class.
	 * 
	 * Starts a session and loads data from it
	 * into {@link Session::$session}.
	 */
	protected function __construct() {
		session_start();
		if(!isset($_SESSION['session'])) {
			$_SESSION['session'] = array();
		}
		if(!is_array($_SESSION['session'])) {
			$_SESSION['session'] = array($_SESSION['session']);
		}
		$this->session = $_SESSION['session'];
		$this->revert = $_SESSION['session'];
	}
	/**
	 * Destories the Session class
	 * 
	 * The destructor for the Session class
	 * 
	 * Writes {@link Session::$session} to the Session
	 * superglobal, writes the session and invalidates
	 * the instance variable.
	 */
	public function __destruct() {
		$_SESSION['session'] = $this->session;
		session_write_close();
		self::$instance = false;
	}
	/**
	 * Wipes the session array
	 */
	public function wipe() {
		$this->session = array();
	}
	/**
	 * Reverts {@link Session::$session} to {@link Session::$revert}
	 */
	public function restore() {
		$this->session = $this->revert;
	}
	/**
	 * Returns the session array
	 *
	 * @return array
	 */
	public function getRecord() {
		if(!$this) {
			debug_print_backtrace();
		}
		return $this->session;
	}
	// ArrayAccess Methods
	/**
	 * Checks if a key exists
	 *
	 * @param scalar $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return isset($this->session[$offset]);
	}
	/**
	 * Gets a key
	 *
	 * @param scalar $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		return isset($this->session[$offset]) ? $this->session[$offset] : null;
	}
	/**
	 * Gets a nested key
	 *
	 * @param scalar $offset
	 * @param scalar $otherOffest
	 * @return mixed
	 */
	public function offsetGetNested($offset, $otherOffest) {
		return isset($this->session[$offset][$otherOffest]) ? $this->session[$offset][$otherOffest] : null;
	}
	/**
	 * Sets a key to a value
	 *
	 * @param scalar $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		$this->session[$offset] = $value;
	}
	/**
	 * Sets a nested key to a value
	 *
	 * @param scalar $offset
	 * @param scalar $otherOffset
	 * @param mixed $value
	 */
	public function offsetSetNested($offset, $otherOffset, $value) {
		$this->session[$offset][$otherOffset] = $value;
	}
	/**
	 * Unsets a key
	 *
	 * @param scalar $offset
	 */
	public function offsetUnset($offset) {
		unset($this->session[$offset]);
	}
	/**
	 * Unsets a nested key
	 *
	 * @param scalar $offset
	 * @param scalar $otherOffset
	 */
	public function offsetUnsetNested($offset, $otherOffset) {
		unset($this->session[$offset][$otherOffset]);
	}
	// Iterator methods
	/**
	 * Returns the current item in the array
	 *
	 * @return mixed
	 */
	public function current() {
		return current($this->session);
	}
	/**
	 * Returns the key for the current item in the array
	 *
	 * @return scalar
	 */
	public function key() {
		return key($this->session);
	}
	/**
	 * Advances by one in the array
	 */
	public function next() {
		next($this->session);
	}
	/**
	 * Rewinds the array to the beginning
	 */
	public function rewind() {
		reset($this->session);
	}
	/**
	 * Checks if the current item in the array is valid
	 *
	 * @return bool
	 * @uses Session::current()
	 */
	public function valid() {
		return (bool)$this->current() !== null;
	}
	// Serializable methods
	/**
	 * Serializes the class
	 * 
	 * Returns the serialized version of {@link 
	 * Session::$session}
	 *
	 * @return string
	 */
	public function serialize() {
		return serialize($this->session);
	}
	/**
	 * Unserializes the class
	 *
	 * Unserializes the data and shoves it into
	 * {@link Session::$session}
	 * 
	 * @param string $serialized
	 */
	public function unserialize($serialized) {
		$this->session = unserialize($serialized);
	}
}

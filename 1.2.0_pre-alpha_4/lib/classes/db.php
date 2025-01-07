<?php
/**
 * Database handler class file
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage database
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
/**
 * Database handler class
 *
 * This class provides the functions for
 * setting database variables and handlers
 * for the relevant wrapper class.
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage database
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
class DB {
	/**#@+
	 * @static
	 */
	/**
	 * @var string The connection type
	 */
	protected static $type = 'MySQL';
	/**
	 * @var string Database host
	 */
	protected static $host = null;
	/**
	 * @var string Database user
	 */
	protected static $user = null;
	/**
	 * @var string Database password
	 */
	protected static $pass = null;
	/**
	 * @var string Database name
	 */
	protected static $name = null;
	/**
	 * @var string Database prefix
	 */
	protected static $prefix = null;
	/**
	 * @var array Special database prefixes. Tablename => prefix.
	 */
	protected static $special_prefixes = array();
	/**
	 * @var DBWrapper Database wrapper instance
	 */
	protected static $connection = null;
	/**
	 * @var boolean Connected to the database
	 */
	protected static $connected = false;
	/**
	 * @var string The connection class name
	 */
	protected static $class = 'DBWrapper_MySQL';

	/**
	 * Sets the connection type.
	 *
	 * Can only be changed before connecting to
	 * the database.
	 *
	 * Checks to ensure that the type given exists
	 * and that it can be used then changes
	 * {@link DB::$type}, {@link DB::$class} and
	 * creates an instance which is then set to
	 * {@link DB::$connection}.
	 *
	 * @param string $type The connection type
	 */
	public static function setType($type) {
		if(self::$connected || !file_exists('lib/classes/dbwrapper/dbwrapper_' . strtolower($type) . '.php')) {
			return;
		}
		$class = 'DBWrapper_' . $type;
		if(!call_user_func(array($class, 'canUse'))) {
			return;
		}
		$c = new $class();
		self::$type = $type;
		self::$connection = $c;
		self::$class = get_class($c);
	}
	/**
	 * Sets the database host
	 *
	 * Can only be set once (hopefully in
	 * dbconnect.php)
	 *
	 * @param string $host The database host
	 */
	public static function setHost($host) {
		if(self::$host === null) {
			self::$host = $host;
		}
	}
	/**
	 * Sets the database user
	 *
	 * Can only be set once (hopefully in
	 * dbconnect.php)
	 *
	 * @param string $user The database user
	 */
	public static function setUser($user) {
		if(self::$user === null) {
			self::$user = $user;
		}
	}
	/**
	 * Sets the database password
	 *
	 * Can only be set once (hopefully in
	 * dbconnect.php)
	 *
	 * @param string $pass The database password
	 */
	public static function setPass($pass) {
		if(self::$pass === null) {
			self::$pass = $pass;
		}
	}
	/**
	 * Sets the database name
	 *
	 * Can only be set once (hopefully in
	 * dbconnect.php)
	 *
	 * @param string $name The database name
	 */
	public static function setName($name) {
		if(self::$name === null) {
			self::$name = $name;
		}
	}
	/**
	 * Sets the database prefix
	 *
	 * Can only be set once (hopefully in
	 * dbconnect.php)
	 *
	 * @param string $prefix The database prefix
	 */
	public static function setPrefix($prefix) {
		if(self::$prefix === null) {
			self::$prefix = $prefix;
		}
	}
	/**
	 * Base function for preforming a query on a database
	 *
	 * @param string $sql The query to run
	 * @param boolean $die Die on error
	 * @return resource
	 * @uses DBWrapper::query()
	 */
	public static function query($sql, $die=true) {
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		return self::$connection->query($sql, $die);
	}
	/**
	 * Base function for preforming a cached query on a database
	 *
	 * @param string $sql The query to run
	 * @param string $name The name of the cache
	 * @param integer $duration The time in seconds to keep the cache for
	 * @return array
	 * @uses DBWrapper:query_cached()
	 */
	static public function &query_cached($sql,$name,$duration=900) {
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		return self::$connection->query_cached($sql, $name, $duration);
	}
	/**
	 * Base function for returning a database error message
	 *
	 * @param resource $link The database resource
	 * @return string
	 * @uses DBWrapper::error()
	 */
	static public function error($link=false) {
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		return self::$connection->error($link);
	}
	/**
	 * Base function for fetching an array from a resource
	 *
	 * @param resource $result The query resource
	 * @return array
	 * @uses DBWrapper::fetch_assoc()
	 */
	static public function fetch_assoc(&$result) {
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		return self::$connection->fetch_assoc($result);
	}
	/**
	 * Base function for returning the insert id
	 *
	 * @return integer
	 * @uses DBWrapper::insert_id()
	 */
	static public function insert_id() {
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		return self::$connection->insert_id();
	}
	/**
	 * Base function for counting the number of rows returned
	 *
	 * @param resource $result The query resource
	 * @return integer
	 * @uses DBWrapper::num_rows()
	 */
	static public function num_rows($result) {
		return self::$connection->num_rows($result);
	}
	/**
	 * Base function for counting the number of affected rows
	 *
	 * @param resource $link The database resource
	 * @return integer
	 * @uses DBWrapper::affected_rows()
	 */
	static public function affected_rows($link=false) {
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		return self::$connection->affected_rows($link);
	}
	/**
	 * Base function for creating a persistent connection to the database
	 *
	 * Sets {@link DB::$connected} to true on success.
	 *
	 * Will only run once.
	 *
	 * @return resource
	 * @uses DBWrapper::pconnect()
	 */
	static public function pconnect() {
		if(self::$connected) {
			return self::$connected;
		}
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		$res = self::$connection->pconnect(self::$host, self::$user, self::$pass);
		if($res) {
			self::$connected = true;
		}
		return $res;
	}
	/**
	 * Base function for creating a connection to the database
	 *
	 * Sets {@link DB::$connected} to true on success.
	 *
	 * Will only run once.
	 *
	 * @return resource
	 * @uses DBWrapper::connect()
	 */
	static public function connect() {
		if(self::$connected) {
			return self::$connected;
		}
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		$res = self::$connection->connect(self::$host, self::$user, self::$pass);
		if($res) {
			self::$connected = true;
		}
		return $res;
	}
	/**
	 * Base function for returning the database server's version
	 *
	 * @return string
	 * @uses DBWrapper::get_server_version()
	 */
	static public function get_server_version() {
		return self::$connection->get_server_version();
	}
	/**
	 * Base function for selecting a database
	 *
	 * @param string $dbname The name of the database
	 * @return boolean
	 * @uses DBWrapper::select_db()
	 */
	static public function select_db($dbname = false) {
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		if(!$dbname) {
			$dbname = self::$name;
		}
		return self::$connection->select_db($dbname);
	}
	/**
	 * Base function for freeing a result
	 *
	 * @param resource $result The query resource
	 * @uses DBWrapper::free_result()
	 */
	static public function free_result($result) {
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		self::$connection->free_result($result);
	}
	/**
	 * Base function for determining if a table exists
	 *
	 * @param string $tablename The name of the table
	 * @return boolean
	 * @uses DBWrapper::table_exists()
	 */
	static public function table_exists($tablename) {
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		return self::$connection->table_exists($tablename);
	}
	/**
	 * Base function for escaping a string
	 * 
	 * @param string $string The string to escape
	 * @return string
	 */
	static public function escape_string($string) {
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		return self::$connection->escape_string($string);
	}
	/**
	 * Prefixes a table name
	 *
	 * @param string $tablename The table name
	 * @param string $force A string to use as the prefix
	 * @return string
	 */
	static public function prefix($tablename, $force=false) {
		if ($force === false) {
			// The following file should be used to override or modify the
			// special_prefixes array to be correct for your site.  Do NOT
			// do this unles you know EXACTLY what this means to you, your
			// game, your county, your state, your nation, your planet and
			// your universe!
			if (file_exists("prefixes.php")) {
				$special_prefixes = array();
				require_once "prefixes.php";
				self::$special_prefixes = $special_prefixes;
			}
			if (isset(self::$special_prefixes[$tablename])) {
				$prefix = self::$special_prefixes[$tablename];
			} else {
				$prefix = self::$prefix;
			}
		} else {
			$prefix = $force;
		}
		return $prefix . $tablename;
	}
	/**
	 * Gets the dbinfo from the wrapper
	 * 
	 * @uses DBWrapper::$dbinfo
	 * @return array
	 */
	static public function info() {
		if(!self::$connection) {
			self::$connection = new self::$class;
		}
		return self::$connection->dbinfo;
	}
	/**#@-*/
}

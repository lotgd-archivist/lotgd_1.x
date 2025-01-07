<?php
/**
 * MySQLi Object Oriented Database wrapper class file
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage database
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
/**
 * MySQLi Object Oriented Database wrapper class
 *
 * This class provides methods for connecting
 * to and preforming operations on a MySQL
 * database using the mysqli class.
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage database
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
class DBWrapper_MySQLi_OOS extends DBWrapper {
	/**
	  * @var string The class type
	  */
	 static public $type = 'MySQLi - Object Oriented';
	 /**
	  * @var MySQLi The connection resource
	  */
	 protected $link;
	/**
	 * Runs a given query on a MySQL database
	 *
	 * @param string $sql The query to run
	 * @param boolean $die Die on error
	 * @return resource
	 */
	public function query($sql, $die=true) {
		if(!$this->link) {
			return array();
		}

		global $user;

		$this->dbinfo['queriesthishit']++;

		$starttime = getmicrotime();
		$r = $this->link->query($sql);

		if(!$r && $die) {
			if(IS_INSTALLER) {
				return array();
			}
			if (1 || $user->superuser & SU_DEVELOPER){ // TODO: Check to see if we keep the one
				require_once "lib/show_backtrace.php";
				die(
					"<pre>".HTMLEntities($sql, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</pre>"
					.$this->error()
					.show_backtrace()
					);
			}else{
				die("A most bogus error has occurred.  I apologise, but the page you were trying to access is broken.  Please use your browser's back button and try again.");
			}
		}

		$endtime = getmicrotime();
		if($endtime - $starttime >= 1.00 && ($user->superuser & SU_DEBUG_OUTPUT)) {
			$s = trim($sql);
			if (strlen($s) > 800) {
				$s = substr($s,0,400)." ... ".substr($s,-400);
			}
			debug("Slow Query (".round($endtime-$starttime,2)."s): ".(HTMLEntities($s, ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`n");
		}

		$this->dbinfo['affected_rows'] =$this->affected_rows();
		$this->dbinfo['querytime'] += $endtime-$starttime;

		return $r;
	}
	/**
	 * Runs a given query on a MySQL database and caches it
	 *
	 * This function takes advantage of the data caching library to make
	 * all of the other DB::functions act just like MySQL queries but rely
	 * instead on disk cached data.
	 *
	 * Standard is 15 minutes.
	 *
	 * @uses DBWrapper_MySQLi_OOS::query()
	 * @param string $sql The query to run
	 * @param string $name The name of the cache
	 * @param integer $duration The time in seconds to keep the cache for
	 * @return array
	 */
	public function &query_cached($sql,$name,$duration=900) {
		$data = datacache($name,$duration);
		if (is_array($data)){
			$this->dbinfo['affected_rows']=-1;
			return $data;
		}else{
			$result = $this->query($sql);
			$data = array();
			while ($row = $this->fetch_assoc($result)) {
				$data[] = $row;
			}
			updatedatacache($name,$data);
			return $data;
		}
	}
	/**
	 * Returns the latest MySQL error message
	 *
	 * @param resource $link The database resource
	 * @return string
	 */
	public function error($link=false) {
		if ($link) {
			$r = @mysqli_error($link);
		} else {
			$r = @$this->link->error;
		}
		if ($r=="" && defined("DB_NODB") && !defined("DB_INSTALLER_STAGE4")) {
			return "The database connection was never established";
		}
		return $r;
	}
	/**
	 * Returns a MySQLi resource as an associative array
	 *
	 * @param resource $result The query resource
	 * @return array
	 */
	public function fetch_assoc(&$result) {
		if (is_array($result)){
			//cached data
			if (list(,$val)=each($result)) {
				return $val;
			} else {
				return false;
			}
		}else{
			return $result->fetch_assoc();
		}
	}
	/**
	 * Returns the insert id
	 *
	 * @return integer
	 */
	public function insert_id() {
		if(!$this->link) {
			return -1;
		}
		return $this->link->insert_id;
	}
	/**
	 * Returns the number of rows returned
	 *
	 * @param resource $result The query resource
	 * @return integer
	 */
	public function num_rows($result) {
		if (is_array($result)){
			return count($result);
		}else{
			return $result->num_rows;
		}
	}
	/**
	 * Returns the number of affected rows
	 *
	 * @param resource $link The database resource
	 * @return integer
	 */
	public function affected_rows($link=false) {
		if (isset($this->dbinfo['affected_rows'])) {
			return $this->dbinfo['affected_rows'];
		}
		if (!$this->link) {
			return 0;
		}
		if (!$link) {
			$r = $this->link->affected_rows;
		}else{
			$r = mysqli_affected_rows($link);
		}
		return $r;
	}
	/**
	 * Creates a connection to a MySQL database
	 *
	 * MySQLi has no persistent connections, so this
	 * just uses {@link DBWrapper_MySQLi_OOS::connect()}
	 *
	 * @param string $host The database host
	 * @param string $user The database user
	 * @param string $pass The database user's password
	 * @uses DBWrapper_MySQLi_OOS::connect()
	 * @return resource
	 */
	public function pconnect($host,$user,$pass) {
		return $this->connect($host, $user, $pass);
	}
	/**
	 * Creates a persistent connection to a MySQL database
	 *
	 * @param string $host The database host
	 * @param string $user The database user
	 * @param string $pass The database user's password
	 * @return resource
	 */
	public function connect($host,$user,$pass) {
		$this->link = new MySQLi($host, $user, $pass);
		return $this->link;
	}
	/**
	 * Returns the version of the MySQL server
	 *
	 * @return string
	 */
	public function get_server_version() {
		return $this->link->server_version;
	}
	/**
	 * Selects a MySQL database
	 *
	 * @param string $dbname The name of the database
	 * @return boolean
	 */
	public function select_db($dbname) {
		return $this->link->select_db($dbname);
	}
	/**
	 * Frees a MySQLi resource
	 *
	 * @param resource $result The query resource
	 */
	public function free_result($result) {
		if (is_array($result)){
			//cached data
			unset($result);
		}else{
			$result->close();
		}
	}
	/**
	 * Determines if a table exists in the current database
	 *
	 * @param string $tablename The name of the table
	 * @return boolean
	 */
	public function table_exists($tablename) {
		return (bool)$this->num_rows($this->query('SHOW TABLES LIKE \'' . $tablename . '\''));
	}
	/**
	 * Escapes a string
	 * 
	 * @param string $string The string to escape
	 * @return string
	 */
	public function escape_string($string) {
		return $this->link->real_escape_string($string);
	}
	/**
	 * Determines if MySQL can be used as a database type.
	 *
	 * Checks that the mysqli extension is loaded.
	 */
	static public function canUse() {
		return (bool)extension_loaded('mysqli');
	}
}

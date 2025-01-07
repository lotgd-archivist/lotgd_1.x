<?php
/**
 * Database wrapper class file
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage database
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
/**
 * Database wrapper class
 *
 * This class provides the basis for
 * classes that connect to and operate
 * on a database.
 * 
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage database
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
abstract class DBWrapper {
	/**
	 * @var resource Database link resource
	 */
	protected $link = false;
	/**
	 * @var array Information about database queries.
	 */
	 public $dbinfo = array('queriesthishit'=>0, 'querytime'=>0);
	 /**
	  * @var string The class type
	  */
	 static public $type;
	
	/**
	 * Base function for preforming a query on a database
	 * 
	 * @param string $sql The query to run
	 * @param boolean $die Die on error
	 * @return resource
	 */
	abstract public function query($sql, $die=true);
	/**
	 * Base function for preforming a cached query on a database
	 * 
	 * @param string $sql The query to run
	 * @param string $name The name of the cache
	 * @param integer $duration The time in seconds to keep the cache for
	 * @return array
	 */
	abstract public function &query_cached($sql,$name,$duration=900);
	/**
	 * Base function for returning a database error message
	 * 
	 * @param resource $link The database resource
	 * @return string
	 */
	abstract public function error($link=false);
	/**
	 * Base function for fetching an array from a resource
	 * 
	 * @param resource $result The query resource
	 * @return array
	 */
	abstract public function fetch_assoc(&$result);
	/**
	 * Base function for returning the insert id
	 * 
	 * @return integer
	 */
	abstract public function insert_id();
	/**
	 * Base function for counting the number of rows returned
	 * 
	 * @param resource $result The query resource
	 * @return integer
	 */
	abstract public function num_rows($result);
	/**
	 * Base function for counting the number of affected rows
	 * 
	 * @param resource $link The database resource
	 * @return integer 
	 */
	abstract public function affected_rows($link=false);
	/**
	 * Base function for creating a persistent connection to the database
	 * 
	 * @param string $host The database host
	 * @param string $user The database user
	 * @param string $pass The database user's password
	 * @return resource
	 */
	abstract public function pconnect($host,$user,$pass);
	/**
	 * Base function for creating a connection to the database
	 * 
	 * @param string $host The database host
	 * @param string $user The database user
	 * @param string $pass The database user's password
	 * @return resource
	 */
	abstract public function connect($host,$user,$pass);
	/**
	 * Base function for returning the database server's version
	 * 
	 * @return string
	 */
	abstract public function get_server_version();
	/**
	 * Base function for selecting a database
	 * 
	 * @param string $dbname The name of the database
	 * @return boolean
	 */
	abstract public function select_db($dbname);
	/**
	 * Base function for freeing a result
	 * 
	 * @param resource $result The query resource
	 */
	abstract public function free_result($result);
	/**
	 * Base function for determining if a table exists
	 * 
	 * @param string $tablename The name of the table
	 * @return boolean
	 */
	abstract public function table_exists($tablename);
	/**
	 * Base function for escaping a string
	 * 
	 * @param string $string The string to escape
	 * @return string
	 */
	abstract public function escape_string($string);
	/**
	 * Base function for determinig if the wrapper type can be used
	 * 
	 * @return boolean
	 */
	abstract static public function canUse();
}

<?php
// addnews ready
// translator ready
// mail ready
require_once("lib/errorhandling.php");
require_once("lib/datacache.php");

if (file_exists("lib/dbremote.php")) {
	require_once "lib/dbremote.php";
}

function db_query($sql, $die=true){
 	return DB::query($sql, $die);
}

//& at the start returns a reference to the data array.
//since it's possible this array is large, we'll save ourselves
//the overhead of duplicating the array, then destroying the old
//one by returning a reference instead.
function &db_query_cached($sql,$name,$duration=900){
	//this function takes advantage of the data caching library to make
	//all of the other DB::functions act just like MySQL queries but rely
	//instead on disk cached data.
	//if (getsetting("usedatacache", 0) == 1) debug("DataCache: $name");
	//standard is 15 minutes, als hooks don't need to be cached *that* often, normally you invalidate the cache properly
	return DB::query_cached($sql, $name, $duration);
}

function db_error($link=false){
	return DB::error($link);
}


function db_fetch_assoc(&$result){
	return DB::fetch_assoc($result);
}


function db_insert_id(){
 	return DB::insert_id();
}


function db_num_rows($result){
	return DB::num_rows($result);
}


function db_affected_rows($link=false){
	return DB::affected_rows($link);
}


function db_pconnect($host,$user,$pass){
	return DB::pconnect($host, $user, $pass);
}

function db_connect($host,$user,$pass){
	return DB::connect($host, $user, $pass);
}

function db_get_server_version()
{
	return DB::get_server_version();
}

function db_select_db($dbname){
	return DB::select_db($dbname);
}

function db_free_result($result){
	return DB::free_result($result);
}

function db_table_exists($tablename){
 	return DB::table_exists($tablename);
}

function db_prefix($tablename, $force=false) {
	return DB::prefix($tablename, $force);
}

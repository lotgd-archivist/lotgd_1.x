<?php
// PRIVATE_CODE
// addnews ready
// mail ready
// translator ready

// This is called remotely, which is why it's in the top level rather than in
// the library

$remote_hosts = array(
	// [redacted by archivist]
	// [redacted by archivist]
	// [redacted by archivist]
	"192.168.0.1"=>true,
	"192.168.0.2"=>true,
	"127.0.0.1"=>true,
	// [redacted by archivist] //central can talk to itself :-)
);
if (isset($remote_hosts[$_SERVER['REMOTE_ADDR']]) && $remote_hosts[$_SERVER['REMOTE_ADDR']]){
	define("ALLOW_ANONYMOUS",true);
	require_once("common.php");
	//retrieve our personalized datatable hash for our weak xor obfuscation.
	require_once("lib/datatable.php");
	$sql = $_GET['x'];
	$sql = base64_decode($sql);
	//the first 4 bytes of the sql will be an offset for the data table.
	$offset = (int)substr($sql,0,4);
	$sql = substr($sql,4);
	$datatable = substr($datatable,$offset).substr($datatable,0,$offset);
	//build our xor field
	$xor = $datatable;
	while (strlen($xor) < strlen($sql)){
		$xor.=$datatable;
	}
	$xor = substr($xor,0,strlen($sql));
	//retrieve original SQL
	$sql = $sql ^ $xor;
	//get the data and store it in an array of the same format as cached data.
	$result = db_query($sql);
	$info = array("affected_rows"=>db_affected_rows());
	$data = array();
	if ($result && !is_bool($result)) {
		while ($row = db_fetch_assoc($result)){
			array_push($data,$row);
		}
	}
	
	//serialize the data for return
	if (httpget("v") == "2"){
		//version 2 serialization allows us to pass back meta data.
		$data = serialize(array("info"=>$info,"data"=>$data));
	}else{
		//version 1 serialization only lets us pass data.
		$data = serialize($data);
	}
	//build the xor field for return
	$xor = "";
	while (strlen($xor) < strlen($data)){
		$xor .= $datatable;
	}
	$xor = substr($xor,0,strlen($data));
	//obfuscate data
	$data = $data ^ $xor;
	//output the obfuscated data
	echo base64_encode($data);
}
?>

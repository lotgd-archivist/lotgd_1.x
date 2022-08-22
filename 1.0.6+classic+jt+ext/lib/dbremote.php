<?php
// addnews ready
// translator ready
// mail ready
// PRIVATE_CODE
function &db_query_remote($sql,$server,$duration=600){
	//this function returns data from a remote server. 
	//default is 10 minute cache.
	global $dbinfo;
	$name = "remotedata-".md5($sql);
	$data = datacache($name,$duration);
	
	if (is_array($data)){
		reset($data);
		return $data;
	}else{
		//datatable is our shared secret key.
		require("lib/datatable.php");
		//offset the data table
		$offset = e_rand(0,strlen($datatable)) % 10000; //we only have 4 bytes to send the offset.
		$datatable = substr($datatable,$offset).substr($datatable,0,$offset);
		//build the xor obfuscation table for outbound data
		$xor = $datatable;
		while (strlen($xor) < strlen($sql)){
			$xor .= $datatable;
		}
		$xor = substr($xor,0,strlen($sql));
		//obfuscate the sql
		$sql = $sql ^ $xor;
		//prepend the offset
		$sql = substr($offset."    ",0,4).$sql;
		//encode the query
		$sql = base64_encode($sql);
		//get the data
		$url = $server."remotequery.php?v=2&x=".rawurlencode($sql);
		require_once("lib/pullurl.php");
		$data = @pullurl($url);
		if ($data === false) {
			$data = array();
			return $data;
		}
		$data = @join("",$data);
		$data = base64_decode($data);
		//build the xor obfuscation table for inbound data
		$xor = $datatable;
		while (strlen($xor) < strlen($data)){
			$xor .= $datatable;
		}
		$xor = substr($xor,0,strlen($data));
		//deobfuscate data
		$data = $data ^ $xor;
		//unserialize data
		$data = unserialize($data);
		$info = $data['info'];
		if (is_array($info)){
			reset($info);
			while (list($key,$val)=each($info)){
				$dbinfo[$key] = $val;
			}
		}
		$data = $data['data'];
		if ($data == NULL) $data = array();
		if ($duration>0) updatedatacache($name,$data);
		if (is_array($data)) reset($data);
		return $data;
	}
}
?>

<?php
// translator ready
// addnews ready
// mail ready
function debuglog($message,$target=false,$userid=false,$field=false,$value=false,$consolidate=true){
	if ($target===false) $target=0;
	global $user;
	if ($userid === false) $userid = $user->acctid;
	$corevalue = $value;
	$id=0;
	if ($field !== false && $value !==false && $consolidate){
		$sql = "SELECT * FROM ".DB::prefix("debuglog")." WHERE actor=$userid AND field='$field' AND date>'".date("Y-m-d 00:00:00")."'";
		$result = DB::query($sql);
		if (DB::num_rows($result)>0){
			$row = DB::fetch_assoc($result);
			$value = $row['value']+$value;
			$message = $row['message'];
			$id = $row['id'];
		}
	}
	if ($corevalue!==false) $message.=" ($corevalue)";
	if ($field===false) $field="";
	if ($value===false) $value=0;
	if ($id > 0){
		$sql = "UPDATE ".DB::prefix("debuglog")."
			SET
				date='".date("Y-m-d H:i:s")."',
				actor='$userid',
				target='$target',
				message='".addslashes($message)."',
				field='$field',
				value='$value'
			WHERE
				id=$id
				";
	}else{
		$sql = "INSERT INTO " . DB::prefix("debuglog") . " (id,date,actor,target,message,field,value) VALUES($id,'".date("Y-m-d H:i:s")."',$userid,$target,'".addslashes($message)."','$field','$value')";
	}
	DB::query($sql);
}

?>

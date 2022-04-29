<?php
// translator ready
// addnews ready
// mail ready

function savesetting($settingname,$value){
	global $settings;
	loadsettings();
	if ($value>""){
		if (!isset($settings[$settingname])){
			$sql = "INSERT INTO " . db_prefix("settings") . " (setting,value) VALUES (\"".addslashes($settingname)."\",\"".addslashes($value)."\")";
		}else{
			$sql = "UPDATE " . db_prefix("settings") . " SET value=\"".addslashes($value)."\" WHERE setting=\"".addslashes($settingname)."\"";
		}
		db_query($sql);
		$settings[$settingname]=$value;
		if (db_affected_rows()>0) {
			invalidatedatacache("game-settings");
			return true;
		}else{
			return false;
		}
	}
	return false;
}

function loadsettings(){
	global $settings;
	// as this seems to be a common complaint, examine the execution path
	// of this function, it will only load the settings once per page hit,
	// in subsequent calls to this function, $settings will be an array,
	// thus this function will do nothing.
	if (!is_array($settings)){
		$settings=array();
		$sql = "SELECT * FROM " . db_prefix("settings");
		$result = db_query_cached($sql,"game-settings");
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			$settings[$row['setting']] = $row['value'];
		}
		db_free_result($result);
	}
}

function clearsettings(){
	//scraps the loadsettings() data to force it to reload.
	global $settings;
	unset($settings);
}

function getsetting($settingname,$default){
	global $settings;
	loadsettings();
	if (!isset($settings[$settingname])){
		savesetting($settingname,$default);
		return $default;
	}else{
		if (trim($settings[$settingname])=="")
			$settings[$settingname]=$default;
		return $settings[$settingname];
	}
}
?>

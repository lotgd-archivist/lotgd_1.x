<?php
// translator ready
// addnews ready
// mail ready

function saveuser(){
	global $session,$dbqueriesthishit,$baseaccount;
	if (defined("NO_SAVE_USER")) return false;
	
	if ($session['loggedin'] && $session['user']['acctid']!=""){
		// Any time we go to save a user, make SURE that any tempstat changes
		// are undone.
		restore_buff_fields();

		$session['user']['allowednavs']=serialize($session['allowednavs']);
		$tempval = $session['user']['allowednavs'];
		$session['user']['bufflist']=serialize($session['bufflist']);
		$sql="";
		reset($session['user']);
		while(list($key,$val)=each($session['user'])){
			if (is_array($val)) $val = serialize($val);
			//only update columns that have changed.
			if ($baseaccount[$key]!=$val){
				// Convert bools to 0/1 so that DB doesn't fart itself.
				if (is_bool($val)) $val = $val ? 1 : 0;
				$newval = ($val === null) ? "null" : "'".addslashes($val)."'";
				$sql.="$key=$newval, ";
			}
		}
		//due to the change in the accounts table -> moved output -> save everyhit
		$sql.="laston='".date("Y-m-d H:i:s")."', ";
		$sql = substr($sql,0,strlen($sql)-2);
		$sql="UPDATE " . db_prefix("accounts") . " SET " . $sql .
			" WHERE acctid = ".$session['user']['acctid'];
		$res = db_query($sql);
		if (isset($session['output']) && $session['output']) {
			$sql_output="UPDATE " . db_prefix("accounts_output") . " SET output='".addslashes($session['output'])."' WHERE acctid={$session['user']['acctid']};";
			$result=db_query($sql_output);
			if (db_affected_rows($result)<1) {
				$sql_output="REPLACE INTO " . db_prefix("accounts_output") . " VALUES ({$session['user']['acctid']},'".addslashes($session['output'])."');";
				db_query($sql_output);
			}
		}
		unset($session['bufflist']);
		$session['user'] = array(
			"acctid"=>$session['user']['acctid'],
			"login"=>$session['user']['login'],
		);
	}
}

?>

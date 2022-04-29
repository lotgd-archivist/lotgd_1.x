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

		$session['user']['output']=$session['output'];
		$session['user']['allowednavs']=serialize($session['allowednavs']);
		$session['user']['bufflist']=serialize($session['bufflist']);
		$sql="";
		$changes=0;
		$updates = "";
		reset($session['user']);
		$doesntcountforupdate = array(
			"gold"=>true,
			"goldinbank"=>true,
		);
		while(list($key,$val)=each($session['user'])){
			if (is_array($val)) $val = serialize($val);
			//only update columns that have changed.
			if ($baseaccount[$key]!=$val){
				$sql.="$key='".addslashes($val)."', ";
				if (!isset($doesntcountforupdate[$key])) $changes++;
				$updates .= "$key, ";
			}
		}
		// only update their laston when they've updated portions of their
		// account that don't change each page hit.  Tolerance is 6.
		if ($changes>0) $sql.="laston='".date("Y-m-d H:i:s")."', ";
		$sql = substr($sql,0,strlen($sql)-2);
		$sql="UPDATE " . db_prefix("accounts") . " SET " . $sql .
			" WHERE acctid = ".$session['user']['acctid'];
		if ($changes > 0) db_query($sql);
		unset($session['bufflist']);
		$session['user'] = array(
			"acctid"=>$session['user']['acctid'],
			"login"=>$session['user']['login'],
		);
	}
}

?>

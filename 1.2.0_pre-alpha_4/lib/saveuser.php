<?php
// translator ready
// addnews ready
// mail ready

function saveuser(){
	global $companions,$user;
	
	if (defined("NO_SAVE_USER") || $user->acctid == 0) return;
	if (Session::get('loggedin') && $user->acctid){
		// Any time we go to save a user, make SURE that any tempstat changes
		// are undone.
		restore_buff_fields();
		$user->allowednavs=serialize(Session::get('allowednavs'));
		$user->bufflist=serialize(Session::get('bufflist'));
		if (isset($companions) && is_array($companions)) $user->companions=serialize($companions);
		//due to the change in the accounts table -> moved output -> save everyhit
		Session::set('user', array(
			"acctid"=>$user->acctid,
			"login"=>$user->login,
		));
		
		$user = null;
		if (Session::get('output') && !@gzuncompress(Session::get('output'))) {
			Session::set('output', gzcompress(Session::get('output')));
			
			$sql_output="REPLACE INTO " . DB::prefix("accounts_output") . " VALUES (" . Session::getNested('user', 'acctid') . ",'".addslashes(Session::get('output'))."');";
			DB::query($sql_output);
		}
		
		Session::delete('bufflist');
	}
}

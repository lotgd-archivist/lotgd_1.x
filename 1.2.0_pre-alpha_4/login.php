<?php
// mail ready
// addnews ready
// translator ready
define('ALLOW_ANONYMOUS',true);
require_once('common.php');
require_once('lib/systemmail.php');
require_once('lib/checkban.php');
require_once('lib/http.php');

tlschema('login');
translator_setup();
$op = httpget('op');
$name = httppost('name');
$iname = getsetting('innname', LOCATION_INN);
$vname = getsetting('villagename', LOCATION_FIELDS);

if ($name){
	if (Session::get('loggedin')){
		redirect('badnav.php');
	}else{
		$password = httppost('password');
		$password = stripslashes($password);
		if (substr($password, 0, 5) == '!md5!') {
			$password = md5(substr($password, 5));
		} elseif (substr($password, 0, 6) == '!md52!') {// && strlen($password) == 38) {
			$force = httppost('force');
			if ($force) {
				$password = addslashes(substr($password, 6));
			} else {
				$password='no hax0rs for j00!';
			}
		} else {
			$password = md5(md5($password));
		}
		global $user; // just incase
		$user = User::getUser($name);
		if ($user && $user->password == $password){
			Session::set('user', $user);
			$companions = @unserialize($user->companions);
			if (!is_array($companions)) $companions = array();
			checkban($user->login); //check if this account is banned
			checkban(); //check if this computer is banned
			// If the player isn't allowed on for some reason, anything on
			// this hook should automatically call page_footer and exit
			// itself.
			modulehook('check-login');

			if ($user->emailvalidation && $user->emailvalidation{0} != 'x'){
				Session::delete('user');
				Session::set('message', translate_inline('`4Error, you must validate your email address before you can log in.'));
				echo appoencode(Session::get('message'));
				exit();
			}else{
				Session::set('loggedin', true);
				Session::set('laston', date('Y-m-d H:i:s'));
				Session::set('sentnotice', 0);
				$user->sentnotice = 0;
				Session::set('bufflist', unserialize($user->bufflist));
				if (!is_array(Session::get('bufflist'))) {
					Session::set('bufflist', array());
				}
				if (!is_array($user->dragonpoints)) {
					$user->dragonpoints=array();
				}
				invalidatedatacache('charlisthomepage');
				invalidatedatacache('list.php-warsonline');
				$user->laston = date('Y-m-d H:i:s');

				// Handle the change in number of users online
				translator_check_collect_texts();

				// Let's throw a login module hook in here so that modules
				// like the stafflist which need to invalidate the cache
				// when someone logs in or off can do so.
				modulehook('player-login');
				//ob_end_flush();
				if ($user->loggedin){
					Session::set('allowednavs', unserialize($user->allowednavs));
					$link = '<a href=\'' . $user->restorepage . '\'>' . $user->restorepage . '</a>';
					$str = sprintf_translate('Sending you to %s, have a safe journey', $link);
					$page = $user->restorepage;
					saveuser();
					header('Location: ' . $page);
					//echo $str;
					exit();
				}

				DB::query('UPDATE ' . DB::prefix('accounts') . ' SET loggedin='.true.', laston=\''.date('Y-m-d H:i:s').'\' WHERE acctid = ' . $user->acctid);

				$user->loggedin = true;
				$location = $user->location;
				if ($user->location == $iname) {
					$user->location = $vname;
				}
				if ($user->restorepage){
					redirect($user->restorepage);
				}else{
					if ($location == $iname) {
						redirect('inn.php?op=strolldown');
					}else{
						redirect('news.php');
					}
				}
			}
		}else{
			Session::set('message', translate_inline('`4Error, your login was incorrect`0'));
			//now we'll log the failed attempt and begin to issue bans if
			//there are too many, plus notify the admins.
			$sql = 'DELETE FROM ' . DB::prefix('faillog') . ' WHERE date<\''.date('Y-m-d H:i:s',strtotime('-'.(getsetting('expirecontent',180)/4).' days')).'\'';
			checkban();
			DB::query($sql);
			$sql = 'SELECT acctid FROM ' . DB::prefix('accounts') . ' WHERE login=\'' . $name . '\'';
			$result = DB::query($sql);
			if (DB::num_rows($result)>0){
				// just in case there manage to be multiple accounts on
				// this name.
				while ($row=DB::fetch_assoc($result)){
					$post = httpallpost();
					$sql = 'INSERT INTO ' . DB::prefix('faillog') . ' VALUES (0,\''.date('Y-m-d H:i:s').'\',\''.addslashes(serialize($post)).'\',\'' . $_SERVER['REMOTE_ADDR'] . '\',' . $row['acctid'] . ',\'' . DB::escape_string($_COOKIE['lgi']) . '\')';
					DB::query($sql);
					$sql = 'SELECT ' . DB::prefix('faillog') . '.*,' . DB::prefix('accounts') . '.superuser,name,login FROM ' . DB::prefix('faillog') . ' INNER JOIN ' . DB::prefix('accounts') . ' ON ' . DB::prefix('accounts') . '.acctid=' . DB::prefix('faillog') . '.acctid WHERE ip=\'' . $_SERVER['REMOTE_ADDR'] . '\' AND date>\''.date('Y-m-d H:i:s',strtotime('-1 day')).'\'';
					$result2 = DB::query($sql);
					$c=0;
					$alert='';
					$su=false;
					while ($row2=DB::fetch_assoc($result2)){
						if ($row2['superuser']>0) {$c+=1; $su=true;}
						$c+=1;
						$alert.='`3' . $row2['date'] . '`7: Failed attempt from `&' . $row2['ip'] . '`7 [`3' . $row2['id'] . '`7] to log on to `^' . $row2['login'] . '`7 (' . $row2['name'] . '`7)`n';
					}
					if ($c>=10){
						// 5 failed attempts for superuser, 10 for regular user
						$banmessage=translate_inline('Automatic System Ban: Too many failed login attempts.');
						$sql = 'INSERT INTO ' . DB::prefix('bans') . ' VALUES (\'' . $_SERVER['REMOTE_ADDR'] . '\',\'\',\''.date('Y-m-d H:i:s',strtotime('+'.($c*3).' hours')).'\',\'' . $banmessage . '\',\'System\',\'0000-00-00 00:00:00\')';
						DB::query($sql);
						if ($su){
							// send a system message to admins regarding
							// this failed attempt if it includes superusers.
							$sql = 'SELECT acctid FROM ' . DB::prefix('accounts') .' WHERE (superuser&'.SU_EDIT_USERS.')';
							$result2 = DB::query($sql);
							$subj = translate_mail(array('`#%s failed to log in too many times!',$_SERVER['REMOTE_ADDR']),0);
							$number=DB::num_rows($result2);
							for ($i=0;$i<$number;$i++){
								$row2 = DB::fetch_assoc($result2);
								//delete old messages that
								$sql = 'DELETE FROM ' . DB::prefix('mail') . ' WHERE msgto=' . $row2['acctid'] . ' AND msgfrom=0 AND subject = \''.serialize($subj).'\' AND seen=0';
								DB::query($sql);
								if (DB::affected_rows()>0) $noemail = true; else $noemail = false;
								$msg = translate_mail(array('This message is generated as a result of one or more of the accounts having been a superuser account.  Log Follows:`n`n%s',$alert),0);
								systemmail($row2['acctid'],$subj,$msg,0,$noemail);
							}//end for
						}//end if($su)
					}//end if($c>=10)
				}//end while
			}//end if (DB::num_rows)
			redirect('index.php');
		}
	}
}elseif ($op=='logout'){
	if ($user->loggedin){
		$location = $user->location;
		if ($location == $iname) {
			$user->restorepage='inn.php?op=strolldown';
		} else {
			$user->restorepage='news.php';
		}
		$user->loggedin = 0;
		$sql = 'UPDATE ' . DB::prefix('accounts') . ' SET loggedin=0,restorepage=\'' . $user->restorepage . '\' WHERE acctid = '.$user->acctid;
		DB::query($sql);
		invalidatedatacache('charlisthomepage');
		invalidatedatacache('list.php-warsonline');

		// Handle the change in number of users online
		translator_check_collect_texts();

		// Let's throw a logout module hook in here so that modules
		// like the stafflist which need to invalidate the cache
		// when someone logs in or off can do so.
		modulehook('player-logout');
		
		// Force the restorepage into allowed navs. This wasn't happening on login
		// so needs to be done here, or we get a badnav.
		// Badnavs are bad.
		addnav('', $user->restorepage);
		// Force a save before we get kicked out. This is needed to ensure that allowednavs is updated.
		saveuser();
	}
	Session::clean();
	redirect('index.php');
}
// If you enter an empty username, don't just say oops.. do something useful.
Session::clean();
Session::set('message', translate_inline('`4Error, your login was incorrect`0'));
redirect('index.php');

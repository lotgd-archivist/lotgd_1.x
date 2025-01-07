<?php
// translator ready
// addnews ready
// mail ready

function do_forced_nav($anonymous,$overrideforced){
	global $REQUEST_URI,$user;
	rawoutput('<!--\nAllowAnonymous: '.($anonymous?'True':'False').'\nOverride Forced Nav: '.($overrideforced?'True':'False').'\n-->');
	if (Session::get('loggedin')){
		$user = User::getUser(Session::getNested('user', 'acctid'));
		if ($user){
			Session::set('user', $user);
			Session::set('bufflist', unserialize($user->bufflist));
			if (!is_array(Session::get('bufflist'))) {
				Session::set('bufflist', array());
			}
			if (is_array(unserialize($user->allowednavs))) {
				Session::set('allowednavs', unserialize($user->allowednavs));
			} else {
				Session::set('allowednavs', array($user->allowednavs));
			}
			if (!$user->loggedin || ( (date('U') - strtotime($user->laston)) > getsetting('LOGINTIMEOUT',900)) ){
				Session::clean();
				redirect('index.php?op=timeout','Account not logged in but session thinks they are.');
			}
		}else{
			Session::clean();
			Session::set('message', translate_inline('`4Error, your login was incorrect`0','login'));
			redirect('index.php','Account Disappeared!');
		}
		if (Session::getNested('allowednavs', $REQUEST_URI) && $overrideforced!==true){
			Session::set('allowednavs', array());
		}else{
			if ($overrideforced!==true){
				redirect('badnav.php','Navigation not allowed to ' . $REQUEST_URI);
			}
		}
	}else{
		$user = User::getNonUser();
		Session::set('user', $user);
		if (!$anonymous){
			Session::set('message', translate_inline('You are not logged in, this may be because your session timed out.','login'));
			redirect('index.php?op=timeout','Not logged in: ' . $REQUEST_URI);
		}
	}
}

<?php
//PRIVATE_CODE

function admin_quickbar_getmoduleinfo(){
	$info = array(
		"name"=>"Admin Quickbar",
		"author"=>"Eric Stevens",
		"version"=>"1.0",
		"category"=>"Administrative",
		//"download"=>"core_module",
		"settings"=>array(
			"Admin Quickbar Settings,title",
			"The admin quickbar will show only for those who meet any of the below criteria; you should be careful about opening this up to too many players as the quick bar will enable players to escape the normal game flow; escaping from a battle gone wrong and the like.,note",
			"show_mounts"=>"Show to mount editors,bool|1",
			"show_creatures"=>"Show to creature editors,bool|1",
			"show_petitions"=>"Show to petition viewers,bool|1",
			"show_moderator"=>"Show to comment moderators,bool|0",
			"show_donations"=>"Show to donation editors,bool|1",
			"show_users"=>"Show to user editors,bool|1",
			"show_config"=>"Show to game config editors,bool|1",
			"show_infinite_days"=>"Show to users with infinite days,bool|0",
			"show_equipment"=>"Show to equipment editors,bool|0",
			"show_paylog"=>"Show to paylog editors,bool|1",
			"show_modules"=>"Show to module managers,bool|1",
			"show_translator"=>"Show to translators,bool|0",
			"show_raw_sql"=>"Show to raw SQL / raw PHP users,bool|1",
		)
	);
	return $info;
}

function admin_quickbar_install(){
	module_addhook("everyheader");
	return true;
}
function admin_quickbar_uninstall(){
	return true;
}

function admin_quickbar_dohook($hookname,$args){
	if (!admin_quickbar_enabled()){
		return $args;
	}
	
	switch($hookname){
	case "everyheader":
		admin_quickbar_draw();
		break;
	}
	return $args;
}

function admin_quickbar_run(){
	$action = str_replace(' ',"",httppost('action'));
	
	$perms = admin_quickbar_get_exec_perms();
	
	switch($action){
	case "edituser":
	case "useredit":
	case "user":
		if ($perms['users']){
			require_once("lib/lookup_user.php");
			list($users, $err) = lookup_user(httppost('arguments'), "acctid");
			if (!$err && db_num_rows($users) > 0){
				$user = db_fetch_assoc($users);
				redirect("user.php?op=edit&userid={$user['acctid']}");
				exit();
			}else{
				if (!$err) $err = "`\$Unknown error`0";
				$err .= "`nArguments were '%s'";
				admin_quickbar_error($err, httppost('arguments'));
			}
		}
		break;
	case "debuglog":
	case "debugloguser":
	case "userlog":
	case "userdebuglog":
	case "log":
		if ($perms['user']){
			require_once("lib/lookup_user.php");
			list($users, $err) = lookup_user(httppost('arguments'), "acctid");
			if (!$err && db_num_rows($users) > 0){
				$user = db_fetch_assoc($users);
				redirect("user.php?op=debuglog&userid={$user['acctid']}");
			}else{
				if (!$err) $err = "`\$Unknown error`0";
				$err .= "`nArguments were '%s'";
				admin_quickbar_error($err, httppost('arguments'));
			}
		}		
		break;
	case "paylog":
		if ($perms['paylog']){
			redirect("paylog.php");
		}
		break;
	case "settings":
	case "config":
		if ($perms['config']){
			redirect("configuration.php");
		}
		break;
	case "modulesettings":
	case "moduleconfig":
		if ($perms['modules']){
			if (httppost('arguments') > ""){
				redirect("configuration.php?op=modulesettings&module=".httppost('arguments'));
			}else{
				admin_quickbar_error("You must supply a module name as the argument to %s.",$action);
			}
		}
		break;
	case "logout":
		redirect("login.php?op=logout");
		break;
	case "sql":
	case "rawsql":
		if ($perms['sql']){
			redirect("rawsql.php");
		}
		break;
	case "php":
	case "rawphp":
		if ($perms['sql']){
			redirect("rawsql.php?op=php");
		}
		break;
	case "newday":
		if ($perms['infinite_days']){
			redirect("newday.php");
		}
	default:
		$result = modulehook("admin_quickbar_action",array("action"=>$action,"args"=>httppost('arguments'),"handled"=>false));
		if (!$result['handled']){
			admin_quickbar_error("The action %s is not a valid command.",$action);
		}
		break;
	}
	debug($perms); 
	admin_quickbar_error("You do not have permission to perform action %s.",$action);
}

function admin_quickbar_error(){
	$args = func_get_args();
	page_header("Quickbar Error");
	output("`\$There was a problem running your quick bar action:`0`n");
	output("`bAction:`b %s`n",httppost('action'));
	output("`bArguments:`b %s`n",httppost('arguments'));
	output("`n");
	call_user_func_array('output',$args);
	require_once("lib/superusernav.php");
	superusernav();
	page_footer();
}

function admin_quickbar_draw(){
	global $REQUEST_URI;
	rawoutput("<div class='debug'>");
	rawoutput("<form action='runmodule.php?module=admin_quickbar&op=do' method='POST'>");
	addnav("","runmodule.php?module=admin_quickbar&op=do");
	output("Admin Quickbar Action:");
	rawoutput("<input name='action'>");
	output("Arguments:");
	rawoutput("<input name='arguments'>");
	$submit = tl("Go");
	rawoutput("<input type='hidden' value=\"{htmlentities($REQUEST_URI)}\">");
	rawoutput("<input type='submit' value='$submit'>");
	output("`nExamples: user MightyE, log Mightye, paylog, settings, modulesettings admin_quickbar, logout, sql, php, newday");
	rawoutput("</form>");
	rawoutput("</div>");
}

function admin_quickbar_enabled(){
	$perms = admin_quickbar_get_view_perms();
	$view = false;
	foreach($perms as $title=>$perm){
		if ($perm) $view = true;
	}
	return $view;
}

function admin_quickbar_get_view_perms(){
	global $session;
	$exec_perms = admin_quickbar_get_exec_perms();
	$perms = modulehook("admin_quickbar_get_view_perms",array());
	
	$perms['mounts']        = get_module_setting("show_mounts")        &&  $exec_perms['mounts'];
	$perms['creatures']     = get_module_setting("show_creatures")     &&  $exec_perms['creatures'];
	$perms['petitions']     = get_module_setting("show_petitions")     &&  $exec_perms['petitions'];
	$perms['comments']      = get_module_setting("show_moderator")     &&  $exec_perms['comments'];
	$perms['donations']     = get_module_setting("show_donations")     &&  $exec_perms['donations'];
	$perms['users']         = get_module_setting("show_users")         &&  $exec_perms['users'];
	$perms['config']        = get_module_setting("show_config")        &&  $exec_perms['config'];
	$perms['infinite_days'] = get_module_setting("show_infinite_days") &&  $exec_perms['infinite_days'];
	$perms['equipment']     = get_module_setting("show_equipment")     &&  $exec_perms['equipment'];
	$perms['paylog']        = get_module_setting("show_paylog")        &&  $exec_perms['paylog'];
	$perms['modules']       = get_module_setting("show_modules")       &&  $exec_perms['modules'];
	$perms['translator']    = get_module_setting("show_translator")    &&  $exec_perms['translator'];
	$perms['sql']           = get_module_setting("show_raw_sql")       &&  $exec_perms['sql'];
	return $perms;
}
function admin_quickbar_get_exec_perms(){
	global $session;
	$perms = modulehook("admin_quickbar_get_exec_perms",array());
	
	$perms['mounts']        = $session['user']['superuser'] & SU_EDIT_MOUNTS ? true : false;
	$perms['creatures']     = $session['user']['superuser'] & SU_EDIT_CREATURES ? true : false;
	$perms['petitions']     = $session['user']['superuser'] & SU_EDIT_PETITIONS ? true : false;
	$perms['comments']      = $session['user']['superuser'] & (SU_EDIT_COMMENTS | SU_MODERATE_CLANS | SU_AUDIT_MODERATION) ? true : false;
	$perms['donations']     = $session['user']['superuser'] & SU_EDIT_DONATIONS ? true : false;
	$perms['users']         = $session['user']['superuser'] & SU_EDIT_USERS ? true : false;
	$perms['config']        = $session['user']['superuser'] & SU_EDIT_CONFIG ? true : false;
	$perms['infinite_days'] = $session['user']['superuser'] & SU_INFINITE_DAYS ? true : false;
	$perms['equipment']     = $session['user']['superuser'] & SU_EDIT_EQUIPMENT ? true : false;
	$perms['paylog']        = $session['user']['superuser'] & SU_EDIT_PAYLOG ? true : false;
	$perms['modules']       = $session['user']['superuser'] & SU_MANAGE_MODULES ? true : false;
	$perms['translator']    = $session['user']['superuser'] & SU_IS_TRANSLATOR ? true : false;
	$perms['sql']           = $session['user']['superuser'] & SU_RAW_SQL ? true : false;
	return $perms;
}
?>

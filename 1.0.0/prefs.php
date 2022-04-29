<?php
// addnews ready
// mail ready
// translator ready

require_once("lib/http.php");

$skin = httppost('template');
if ($skin > "") {
	setcookie("template",$skin,strtotime("+45 days"));
	$_COOKIE['template']=$skin;
}

require_once("lib/villagenav.php");
require_once("common.php");

tlschema("prefs");

require_once("lib/is_email.php");
require_once("lib/showform.php");
require_once("lib/sanitize.php");

page_header("Preferences");

$op = httpget('op');

if ($op=="suicide" && getsetting("selfdelete",0)!=0) {
	$userid = httpget('userid');
	require_once("lib/charcleanup.php");
	char_cleanup($userid, CHAR_DELETE_SUICIDE);
	$sql = "DELETE FROM " . db_prefix("accounts") . " WHERE acctid='$userid'";
	db_query($sql);
	output("Your character has been deleted!");
	addnews("`#%s quietly passed from this world.",$session['user']['name']);
	addnav("Login Page", "index.php");
	$session=array();
	$session['user'] = array();
	$session['loggedin'] = false;
	$session['user']['loggedin'] = false;
	invalidatedatacache("charlisthomepage");
	invalidatedatacache("list.php-warsonline");
} else {

	checkday();
	if ($session['user']['alive']){
		villagenav();
	}else{
		addnav("Return to the news","news.php");
	}
	
	$post = httpallpost();
	if (count($post)==0){
	}else{
		$pass1 = httppost('pass1');
		$pass2 = httppost('pass2');
		if ($pass1!=$pass2){
			output("`#Your passwords do not match.`n");
		}else{
			if ($pass1!=""){
				if (strlen($pass1)>3){
					if (substr($pass1,0,5)!="!md5!"){
						$pass1 = md5(md5($pass1));
					}else{
						$pass1 = md5(substr($pass1,5));
					}
					$session['user']['password']=$pass1;
					output("`#Your password has been changed.`n");
				}else{
					output("`#Your password is too short.");
					output("It must be at least 4 characters.`n");
				}
			}
		}
		reset($post);
		$nonsettings = array(
			"pass1"=>1,
			"pass2"=>1,
			"email"=>1,
			"template"=>1,
			"bio"=>1
		);
		while (list($key,$val)=each($post)){
			// If this is one we don't save, skip
			if (isset($nonsettings[$key]) && $nonsettings[$key]) continue;
			// If this is a module userpref handle and skip
			if (strstr($key, "___")) {
				$val = httppost($key);
				$x = explode("___", $key);
				$module = $x[0];
				$key = $x[1];
				set_module_pref($key, $val, $module);
				continue;
			}
			$session['user']['prefs'][$key]=httppost($key);
		}
		$bio = stripslashes(httppost('bio'));
		$bio = comment_sanitize($bio);
		if ($bio!=$session['user']['bio']){
			if ($session['user']['biotime']>"9000-01-01"){
				output("`n`\$You cannot modify your bio.");
				output("It has been blocked by the administrators!`0`n");
			}else{
				$session['user']['bio']=$bio;
				$session['user']['biotime']=date("Y-m-d H:i:s");
			}
		}
		$email = httppost('email');
		if ($email!=$session['user']['emailaddress']){
			if (is_email($email)){
				if (getsetting("requirevalidemail",0)==1){
					output("`#Your email cannot be changed, system settings prohibit it.");
					output("(Emails may only be changed if the server allows more than one account per email.)");
					output("Use the Petition link to ask the  server administrator to change your email address if this one is no longer valid.`n");
				}else{
					output("`#Your email address has been changed.`n");
					$session['user']['emailaddress']=$email;
				}
			}else{
				if (getsetting("requireemail",0)==1){
					output("`#That is not a valid email address.`n");
				}else{
					output("`#Your email address has been changed.`n");
					$session['user']['emailaddress']=$email;
				}
			}
		}
		output("Settings Saved");
	}

	$form=array(
		"Account Preferences,title",
		"pass1"=>"Password,password",
		"pass2"=>"Retype,password",
		"email"=>"Email Address",
		"Display Preferences,title",
		"template"=>"Skin,theme",
		"language"=>"Language (Not Yet Complete),enum,en,English,de,Deutsch,dk,Danish,es,Espa�ol,it,Italian,test,Test Translator",
		"tabconfig"=>"Show config sections in tabs,bool",
		"Game Behaviour Preferences,title",
		"emailonmail"=>"Send email when you get new Ye Olde Mail?,bool",
		"systemmail"=>"Send email for system generated messages?,bool",
		"dirtyemail"=>"Allow profanity in received Ye Olde Poste messages?,bool",
		"timestamp"=>"Show timestamps in commentary?,enum,0,None,1,Real Time [12/25 1:27pm],2,Relative Time (1h35m)",
		"timeoffset"=>array("Hours to offset time displays as (%s currently displays as %s)?,int", date("m/d h:ia"), date("m/d h:ia",strtotime(($session['user']['prefs']['timeoffset']>=0?"+":"")."{$session['user']['prefs']['timeoffset']} hours"))),
		"ihavenocheer"=>"`0Always disable all holiday related text replacements (such as a`1`0l`1`0e => e`1`0g`1`0g n`1`0o`1`0g for December),bool",
		"bio"=>"Short Character Biography (255 chars max)`n",
	);
	rawoutput("<script language='JavaScript' src='lib/md5.js'></script>");
	$warn = translate_inline("Your password is too short.  It must be at least 4 characters long.");
	rawoutput("<script language='JavaScript'>
	<!--
	function md5pass(){
		//encode passwords before submission to protect them even from network sniffing attacks.
		var passbox = document.getElementById('pass1');
		if (passbox.value.len < 4 && passbox.value.len > 0){
			alert('$warn');
			return false;
		}else{
			var passbox2 = document.getElementById('pass2');
			if (passbox2.value.substring(0, 5) != '!md5!') {
				passbox2.value = '!md5!' + hex_md5(passbox2.value);
			}
			if (passbox.value.substring(0, 5) != '!md5!') {
				passbox.value = '!md5!' + hex_md5(passbox.value);
			}
			return true;
		}
	}
	//-->
	</script>");
	//
	$prefs = $session['user']['prefs'];
	$prefs['bio'] = $session['user']['bio'];
	$prefs['template'] = $_COOKIE['template'];
	if ($prefs['template'] == "")
		$prefs['template'] = getsetting("defaultskin", "jade.htm");
	$prefs['email'] = $session['user']['emailaddress'];
	// Default tabbed config to true
	if (!isset($prefs['tabconfig'])) $prefs['tabconfig'] = 1;

	// Okay, allow modules to add prefs one at a time.
	// We are going to do it this way to *ensure* that modules don't conflict
	// in namespace.
	$sql = "SELECT modulename FROM " . db_prefix("modules") . " WHERE infokeys LIKE '%|prefs|%' AND active=1 ORDER BY modulename";
	$result = db_query($sql);
	while ($row = db_fetch_assoc($result)) {
		$module = $row['modulename'];
		$info = get_module_info($module);
		if (count($info['prefs']) <= 0) continue;
		$msettings = array();
		$mdata = array();
		$found = 0;
		while (list($key, $val) = each($info['prefs'])) {
			$isuser = preg_match("/^user_/", $key);
			$ischeck = preg_match("/^check_/", $key);

			if (!$isuser && !$ischeck && !strstr($val,",title"))
				continue;
			if ($isuser) {
				$found = 1;
			}
			// If this is a check preference, we need to call the modulehook
			// checkuserpref  (requested by cortalUX)
			if ($ischeck) {
				$args = modulehook("checkuserpref", array("name"=>$key),
						false, $module);
				if (!$args['allow']) continue;
				$found = 1;
			}

			if (is_array($val)) {
				$v = $val[0];
				$x = explode("|", $v);
				$val[0] = $x[0];
				$x[0] = $val;
			} else {
				$x = explode("|", $val);
			}
			$msettings[$module."___".$key] = $x[0];
			if (array_key_exists(1, $x)) {
				$mdata[$module."___".$key] = $x[1];
			}
		}

		// If we found a user editable one
		if ($found) {
			// Collect the values
			$sql = "SELECT * FROM " . db_prefix("module_userprefs") . " WHERE modulename='$module' AND (setting LIKE 'user_%' OR setting LIKE 'check_%') AND userid='".$session['user']['acctid']."'";
			$result1 = db_query($sql);
			while($row1 = db_fetch_assoc($result1)) {
				$mdata[$module."___".$row1['setting']] = $row1['value'];
			}
			$form = array_merge($form, $msettings);
			$prefs = array_merge($prefs, $mdata);
		}
	}

	rawoutput("<form action='prefs.php?op=save' method='POST' onSubmit='return(md5pass)'>");
	showform($form,$prefs);
	rawoutput("</form>");
	addnav("","prefs.php?op=save");

	// Stop clueless lusers from deleting their character just because a
	// monster killed them.
	if ($session['user']['alive'] && getsetting("selfdelete",0)!=0) {
		rawoutput("<form action='prefs.php?op=suicide&userid={$session['user']['acctid']}' method='POST'>");
		$deltext = translate_inline("Delete Character");
		$conf = translate_inline("Are you sure you wish to delete your character?");
		rawoutput("<input type='submit' class='button' value='$deltext' onClick='return confirm(\"$conf\");'>");
		rawoutput("</form>");
		addnav("","prefs.php?op=suicide&userid={$session['user']['acctid']}");
	}
}
page_footer();
?>

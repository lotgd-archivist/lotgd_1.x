<?php
if (file_exists("dbconnect.php")){
	$success=true;
	$initial=false;
}else{
	$initial = true;
	output("`@`c`bWriting your dbconnect.php file`b`c");
	output("`2I'm attempting to write a file named 'dbconnect.php' to your site root.");
	output("This file tells LoGD how to connect to the database, and is necessary to continue installation.`n");
	$dbinfo = Session::get('dbinfo');
	$dbconnect =
	"<?php\n"
	."//This file automatically created by installer.php on ".date("M d, Y h:i a")."\n"
	."DB::setHost('{$dbinfo['DB_HOST']}');\n"
	."DB::setUser('{$dbinfo['DB_USER']}');\n"
	."DB::setPass('{$dbinfo['DB_PASS']}');\n"
	."DB::setName('{$dbinfo['DB_NAME']}');\n"
	."DB::setPrefix('{$dbinfo['DB_PREFIX']}');\n"
	."DB::setHost('{$dbinfo['DB_TYPE']}');\n"
	."Settings::setUseCache(". ((int)$dbinfo['DB_USEDATACACHE']) .");\n"
	."Settings::setCachePath('{$dbinfo['DB_DATACACHEPATH']}');\n"
	."?>\n";
	$failure = file_put_contents('dbconnect.php', $dbconnect);
	if (!$failure){
		output("`n`\$Unfortunately, I was not able to write your dbconnect.php file.");
		output("`2You will have to create this file yourself, and upload it to your web server.");
		output("The contents of this file should be as follows:`3");
		rawoutput("<blockquote><pre>".htmlentities($dbconnect, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</pre></blockquote>");
		output("`2Create a new file, past the entire contents from above into it (everything from and including `3<?php`2 up to and including `3?>`2 ).");
		output("When you have that done, save the file as 'dbconnect.php' and upload this to the location you have LoGD at.");
		output("You can refresh this page to see if you were successful.");
	}else{
		output("`n`@Success!`2  I was able to write your dbconnect.php file, you can continue on to the next step.");
		$success=true;
	}
}
if ($success && !$initial){
	$version = getsetting("installer_version","-1");
	require 'dbconnect.php';
	if (version_compare($version, '1.1.1 Dragonprime Edition', '<')) {
		$sql = "SELECT setting, value FROM ".DB::prefix("settings")." WHERE setting IN ('usedatacache', 'datacachepath')";
		$result = DB::query($sql);
		$dbconnect =
			"<?php\n"
			."//This file automatically created by installer.php on ".date("M d, Y h:i a")."\n"
			."DB::setHost('{$DB_HOST}');\n"
			."DB::setUser('{$DB_USER}');\n"
			."DB::setPass('{$DB_PASS}');\n"
			."DB::setName('{$DB_NAME}');\n"
			."DB::setPrefix('{$DB_PREFIX}');\n";
		if(defined('DBTYPE')) {
			$dbconnect .= "DB::setType('" . DBTYPE . "');";
		} else {
			$dbconnect .= "DB::setType('mysql');";
		}
		$dbconnect .=
			"Settings::setUseCache(". ((int)$DB_USEDATACACHE).");\n"
			."Settings::setCachePath('".addslashes($DB_DATACACHEPATH)."');\n"
			."?>\n";
			// Check if the file is writeable for us. If yes, we will change the file and notice the admin
			// if not, they have to change the file themselves...
			$failure = file_put_contents('dbconnect.php', $dbconnect);
			if (!$failure) {
				output("`2With this new version the settings for datacaching had to be moved to `idbconnect.php`i.");
				output("Due to your system settings and privleges for this file, I was not able to perform the changes by myself.");
				output("This part involves you: We have to ask you to replace the content of your existing `idbconnect.php`i with the following code:`n`n`&");
				rawoutput("<blockquote><pre>".htmlentities($dbconnect, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</pre></blockquote>");
				output("`2This will let you use your existing datacaching settings.`n`n");
				output("If you have done this, you are ready for the next step.");
			} else {
				output("`n`^You are ready for the next step.");
			}
	} elseif(isset($DB_HOST)) {
		$dbconnect =
			"<?php\n"
			."//This file automatically created by installer.php on ".date("M d, Y h:i a")."\n"
			."DB::setHost('{$DB_HOST}');\n"
			."DB::setUser('{$DB_USER}');\n"
			."DB::setPass('{$DB_PASS}');\n"
			."DB::setName('{$DB_NAME}');\n"
			."DB::setPrefix('{$DB_PREFIX}');\n";
		if(defined('DBTYPE')) {
			$dbconnect .= "DB::setType('" . DBTYPE . "');";
		} else {
			$dbconnect .= "DB::setType('mysql');";
		}
		$dbconnect .=
			"Settings::setUseCache(". ((int)$DB_USEDATACACHE).");\n"
			."Settings::setCachePath('".addslashes($DB_DATACACHEPATH)."');\n"
			."?>\n";
			// Check if the file is writeable for us. If yes, we will change the file and notice the admin
			// if not, they have to change the file themselves...
			$failure = file_put_contents('dbconnect.php', $dbconnect);
			if (!$failure) {
				output("`2With this new version the settings for the database and datacaching have a new format.");
				output("Due to your system settings and privleges for `idbconnect.php`i, I was not able to perform the changes by myself.");
				output("This part involves you: We have to ask you to replace the content of your existing `idbconnect.php`i with the following code:`n`n`&");
				rawoutput("<blockquote><pre>".htmlentities($dbconnect, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</pre></blockquote>");
				output("`2This will let you use your existing database settings.`n`n");
				output("If you have done this, you are ready for the next step.");
			} else {
				output("`n`^You are ready for the next step.");
			}
	} else {
		output("`n`^You are ready for the next step.");
	}
}else if(!$success) {
	Session::set('stagecompleted', 5);
}
?>

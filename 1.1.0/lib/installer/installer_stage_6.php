<?php
if (file_exists("dbconnect.php")){
	$success=true;
}else{
	output("`@`c`bWriting your dbconnect.php file`b`c");
	output("`2I'm attempting to write a file named 'dbconnect.php' to your site root.");
	output("This file tells LoGD how to connect to the database, and is necessary to continue installation.`n");
	$dbconnect =
	"<?php\n"
	."//This file automatically created by installer.php on ".date("M d, Y h:i a")."\n"
	."\$DB_HOST = \"{$session['dbinfo']['DB_HOST']}\";\n"
	."\$DB_USER = \"{$session['dbinfo']['DB_USER']}\";\n"
	."\$DB_PASS = \"{$session['dbinfo']['DB_PASS']}\";\n"
	."\$DB_NAME = \"{$session['dbinfo']['DB_NAME']}\";\n"
	."\$DB_PREFIX = \"{$session['dbinfo']['DB_PREFIX']}\";\n"
	."\$DB_USEDATACACHE = {$session['dbinfo']['DB_USEDATACACHE']};\n"
	."\$DB_DATACACHEPATH = \"{$session['dbinfo']['DB_DATACACHEPATH']}\";\n"
	."?>\n";
	$fp = @fopen("dbconnect.php","w+");
	if ($fp){
		if (fwrite($fp,
		$dbconnect
		)!==false){
			output("`n`@Success!`2  I was able to write your dbconnect.php file, you can continue on to the next step.");
		}else{
			$failure=true;
		}
		fclose($fp);
	}else{
		$failure=true;
	}
	if ($failure){
		output("`n`\$Unfortunately, I was not able to write your dbconnect.php file.");
		output("`2You will have to create this file yourself, and upload it to your web server.");
		output("The contents of this file should be as follows:`3");
		rawoutput("<blockquote><pre>".htmlentities($dbconnect, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."</pre></blockquote>");
		output("`2Create a new file, past the entire contents from above into it (everything from and including `3<?php`2 up to and including `3?>`2 ).");
		output("When you have that done, save the file as 'dbconnect.php' and upload this to the location you have LoGD at.");
		output("You can refresh this page to see if you were successful.");
	}else{
		$success=true;
	}
}
if ($success){
	output("`n`^You are ready for the next step.");
}else{
	$session['stagecompleted']=5;
}
?>
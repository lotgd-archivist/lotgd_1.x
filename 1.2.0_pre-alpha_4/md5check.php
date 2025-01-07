<?php

require_once("common.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");

check_su_access(SU_VIEW_SOURCE);
tlschema("md5check");

require_once("lib/superusernav.php");
superusernav();

page_header('File Check');

output("Checking core files...`n`n");
$sums = file('lib/md5file');

$zero = true;

foreach ($sums as $line=>$sum) {
	list($md5, $file) = explode("  ", $sum);
	$file = substr($file, 0, strlen($file) - 1);
	if(!$md5) continue;
	if(!is_file($file)) {
		$zero = false;
		output("Missing file `b`^%s`0`b.`n`n", $file);
	} else {
		$check = md5_file($file);
		if($check != $md5) {
			$zero = false;
			output("File `b`^%s`0`b differs from core.`n", $file);
			output("Has md5: %s, should be: %s.`n`n", $check, $md5);
		}
	}
}
if($zero) {
	output("All files correct.");
}

addnav('Refresh');
addnav('Refresh', 'md5check.php');

page_footer();

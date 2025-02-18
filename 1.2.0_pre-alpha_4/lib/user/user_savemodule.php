<?php
//save module settings.
$userid = httpget('userid');
$module = httpget('module');
$post = httpallpost();
$post = modulehook("validateprefs", $post, true, $module);
if (isset($post['validation_error']) && $post['validation_error']) {
	tlschema("module-$module");
	$post['validation_error'] =
		translate_inline($post['validation_error']);
	tlschema();
	output("Unable to change settings: `\$%s`0", $post['validation_error']);
} else {
	foreach ($post as $key=>$val) {
		output("Setting %s to %s`n", $key, stripslashes($val));
		$sql = "REPLACE INTO " . DB::prefix("module_userprefs") . " (modulename,userid,setting,value) VALUES ('$module','$userid','$key','$val')";
		DB::query($sql);
	}
	output("`^Preferences for module %s saved.`n", $module);
}
$op = "edit";
httpset("op", "edit");
httpset("subop", "module", true);
?>

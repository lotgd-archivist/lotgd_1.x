<?php
$sql = "SELECT name from " . DB::prefix("accounts") . " WHERE acctid='$userid'";
$res = DB::query($sql);
require_once("lib/charcleanup.php");
char_cleanup($userid, CHAR_DELETE_MANUAL);
while ($row = DB::fetch_assoc($res)) {
	addnews("`#%s was unmade by the gods.", $row['name'], true);
	debuglog("deleted user" . $row['name'] . "'0");
}
$sql = "DELETE FROM " . DB::prefix("accounts") . " WHERE acctid='$userid'";
DB::query($sql);
output( DB::affected_rows()." user deleted.");
?>

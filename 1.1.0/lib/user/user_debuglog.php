<?php
if ($petition !=""){
	addnav("Navigation");
	addnav("Return to the petition","viewpetition.php?op=view&id=$petition");
}
	$sql = "SELECT count(id) AS c FROM " . db_prefix("debuglog") . " WHERE actor=$userid or target=$userid";
$result = db_query($sql);
$row = db_fetch_assoc($result);
$max = $row['c'];
	$start = (int)httpget('start');
//This statement is not capable of using any indices in MySQL, as MySQL will
//only use one index per table scan, and an OR on two different columns
//means that no one index will get all rows, thus a full table scan is always
//necessary.
$sql = "SELECT
	".db_prefix("debuglog").".*,
	a1.name as actorname,
	a2.name as targetname
FROM " . db_prefix("debuglog") . "
LEFT JOIN " . db_prefix("accounts") . " AS a1
ON
	a1.acctid=" . db_prefix("debuglog").".actor
LEFT JOIN " . db_prefix("accounts") . " AS a2
ON
	a2.acctid=" . db_prefix("debuglog"). ".target
WHERE
	" . db_prefix("debuglog") . ".actor=$userid
OR	" . db_prefix("debuglog") . ".target=$userid
ORDER by
	" . db_prefix("debuglog") . ".date DESC,
	" . db_prefix("debuglog") . ".id ASC
LIMIT $start,500";
$next = $start+500;
$prev = $start-500;
addnav("Operations");
addnav("Edit user info","user.php?op=edit&userid=$userid$returnpetition");
addnav("Refresh", "user.php?op=debuglog&userid=$userid&start=$start$returnpetition");
addnav("Debug Log");
if ($next < $max) {
	addnav("Next page","user.php?op=debuglog&userid=$userid&start=$next$returnpetition");
}
if ($start > 0) {
	addnav("Previous page",
			"user.php?op=debuglog&userid=$userid&start=$prev$returnpetition");
}
$result = db_query($sql);
$odate = "";
while ($row = db_fetch_assoc($result)) {
	$dom = date("D, M d",strtotime($row['date']));
	if ($odate != $dom){
		output_notl("`n`b`@%s`0`b`n", $dom);
		$odate = $dom;
	}
	$time = date("H:i:s", strtotime($row['date']))." (".
		reltime(strtotime($row['date'])).")";
	output_notl("`#%s (%s) `^%s - `&%s`7 %s`0", $row['field'],
			$row['value'], $time, $row['actorname'], $row['message']);
		if ($row['target'])
		output(" \-- Recipient = `\$%s`0", $row['targetname']);
	output_notl("`n");
}
?>
<?php
$output="";
$sql = "SELECT output FROM " . DB::prefix("accounts_output") . " WHERE acctid='$userid'";
$result = DB::query($sql);
$row = DB::fetch_assoc($result);
echo str_replace(".focus();",".blur();",str_replace("<iframe src=","<iframe Xsrc=",gzuncompress($row['output'])));
exit();
?>

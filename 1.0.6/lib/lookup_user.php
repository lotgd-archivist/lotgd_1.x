<?php

function lookup_user($query, $order){
	$err = "";
	$searchresult = false;
	$sql = "SELECT acctid,login,name,level,laston,loggedin,gentimecount,gentime,lastip,uniqueid,emailaddress FROM " . db_prefix("accounts");

	if ($query != "") {
		// First try for an exact match on username or login
		$where = "WHERE login='$query' OR name='$query'";
		$searchresult = db_query($sql . " $where  ORDER BY '$order' LIMIT 2");
	}

	if ($query !== false || $searchresult) {
		if (db_num_rows($searchresult) != 1) {
			// we've didn't find an exact match
			$where="WHERE login LIKE '%$query%' OR acctid LIKE '%$query%' OR name LIKE '%$query%' OR emailaddress LIKE '%$query%' OR lastip LIKE '%$query%' OR uniqueid LIKE '%$query%' OR gentimecount LIKE '%$query%' OR level LIKE '%$query%'";
			$searchresult = db_query($sql . " $where  ORDER BY '$order' LIMIT 101");
		}
		if (db_num_rows($searchresult)<=0){
			$err = "`\$No results found`0";
		}elseif (db_num_rows($searchresult)>100){
			$err = "`\$Too many results found, narrow your search please.`0";
		}else{
			// Everything is good
		}
	}
	return array($searchresult, $err);
}
?>

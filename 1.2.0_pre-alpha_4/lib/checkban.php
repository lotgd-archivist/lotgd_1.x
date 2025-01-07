<?php
// translator ready
// addnews ready
// mail ready
function checkban($login=false){
	if (Session::get('banoverride'))
		return false;
	if ($login===false){
		$ip=$_SERVER['REMOTE_ADDR'];
		$id=$_COOKIE['lgi'];
	}else{
		$sql = "SELECT lastip,uniqueid,banoverride,superuser FROM " . DB::prefix("accounts") . " WHERE login='$login'";
		$result = DB::query($sql);
		$row = DB::fetch_assoc($result);
		if ($row['banoverride'] || ($row['superuser'] &~ SU_DOESNT_GIVE_GROTTO)){
			Session::set('banoverride', true);
			return false;
		}
		DB::free_result($result);
		$ip=$row['lastip'];
		$id=$row['uniqueid'];
	}
	$sql = "SELECT * FROM " . DB::prefix("bans") . " where ((substring('$ip',1,length(ipfilter))=ipfilter AND ipfilter<>'') OR (uniqueid='$id' AND uniqueid<>'')) AND (banexpire='0000-00-00' OR banexpire>='".date("Y-m-d")."')";
	$result = DB::query($sql);
	if (DB::num_rows($result)>0){
		Session::clean();
		tlschema("ban");
		Session::set('message', translate_inline("`n`4You fall under a ban currently in place on this website:`n"));
		while ($row = DB::fetch_assoc($result)) {
			Session::set('message', Session::get('message') . $row['banreason'] . '`n');
			if ($row['banexpire']=='0000-00-00')
				Session::set('message', Session::get('message') . translate_inline("  `\$This ban is permanent!`0"));
			else
				Session::set('message', Session::get('message') . sprintf_translate("  `^This ban will be removed `\$after`^ %s.`0",date("M d, Y",strtotime($row['banexpire']))));
			$sql = "UPDATE " . DB::prefix("bans") . " SET lasthit='".date("Y-m-d H:i:s")."' WHERE ipfilter='{$row['ipfilter']}' AND uniqueid='{$row['uniqueidid']}'";
			DB::query($sql);
			Session::set('message', Session::get('message') . '`n');
		}
		Session::set('message', Session::get('message') . translate_inline("`4If you wish, you may appeal your ban with the petition link."));
		tlschema();
		header("Location: index.php");
		exit();
	}
	DB::free_result($result);
	return false;
}

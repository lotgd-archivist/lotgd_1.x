<?php
function get_partner($player=false)
{
	global $user;
	if ($player === false) {
		$partner = getsetting("barmaid", "`%Violet");
		if ($user->sex != SEX_MALE) {
			$partner = getsetting("bard", "`^Seth");
		}
	} else {
		if ($user->marriedto == INT_MAX) {
			$partner = getsetting("barmaid", "`%Violet");
			if ($user->sex != SEX_MALE) {
				$partner = getsetting("bard", "`^Seth");
			}
		} else {
			$sql = "SELECT name FROM ".DB::prefix("accounts")." WHERE acctid = {$user->marriedto}";
			$result = DB::query($sql);
			if ($row = DB::fetch_assoc($result)) {
				$partner = $row['name'];
			} else {
				$user->marriedto = 0;
				$partner = getsetting("barmaid", "`%Violet");
				if ($user->sex != SEX_MALE) {
					$partner = getsetting("bard", "`^Seth");
				}
			}
		}
	}
//	No need to translate names...
//	tlschema("partner");
//	$partner = translate_inline($partner);
//	tlschema();
	return $partner;
}

?>

<?php
// addnews ready (duh ;))
// translator ready
// mail ready
function addnews(){
	// Format: addnews($text[, $sprintf_style_replacement1
	//					  [, $sprintf_style_replacement2...]]
	//					  [, $hidefrombio]);
	// We can pass arrays for the sprintf style replacements, which
	// represent separate translation sets in the same format as output().
	// Eg:
	//   addnews("%s defeated %s in %s `n%s","Joe","Hank","the Inn",
	//		   array("\"Your mother smelt of elderberries,\" taunted %s.",
	//				 "Joe"));
	// Note that the sub-translation does need its own %s location in the
	// master output.
	global $user;
	$args = func_get_args();
	array_unshift($args, $user->acctid);
	return call_user_func_array("addnews_for_user", $args);
}

function addnews_for_user()
{
	global $translation_namespace;
	// this works just like addnews, except it can be used to add a message
	// to a different player other than the triggering player.
	$args = func_get_args();
	$users = array_shift($args);
	$news = array_shift($args);
	$hidefrombio = false;

	if (count($args)>0){
		$arguments=array();
		foreach($args as $key=>$val){
			if ($key==count($args)-1 && $val===true){
				//if the last argument is true, we're hiding from bio;
				//don't put this in the array.
				$hidefrombio=true;
			}else{
				array_push($arguments,$val);
			}
		}
		$arguments = serialize($arguments);
	}else{
		$arguments="";
	}
	if ($hidefrombio === true) $users = 0;
	$sql = "INSERT INTO " . DB::prefix("news") .
		" (newstext,newsdate,accountid,arguments,tlschema) VALUES ('" .
		addslashes($news) . "','" . date("Y-m-d H:i:s") . "'," .
		$users .",'".addslashes($arguments)."','".$translation_namespace."')";
	return DB::query($sql);
}

?>

<?php
// PRIVATE_CODE
// addnews ready
// mail ready
function clues_getmoduleinfo(){
	$info = array(
		"name"=>"Contest Spontaneous Clues",
		"version"=>"1.0",
		"author"=>"Eric Stevens",
		"category"=>"General",
		"settings"=>array(
			"clues"=>"Clue Data,viewonly|".serialize(array()),
			"start"=>"Start Date,dayrange,+30 days,+1 day|2004-06-30 00:00:00",
			"end"=>"End Date,dayrange,+30 days,+1 day|2004-06-30 00:00:00",
			"running"=>"Clue game is running now?,bool|0",
		),
		"prefs"=>array(
			"seenclues"=>"Clues they've seen,viewonly|".serialize(array()),
			"disqualified"=>"User is disqualified,bool|0",
		),
	);
	return $info;
}

function clues_install(){
	module_addhook("superuser");	
	module_addhook("newday-runonce");
	if (get_module_setting("running"))
		clues_setup_hooks();
	else
		clues_drop_hooks();
	return true;
}

function clues_uninstall() {
	return true;
}

function clues_dohook($hookname,$args){
	if ($hookname=="newday-runonce"){
		if (get_module_setting("running")){
			if (strtotime("now") >= strtotime(get_module_setting("end"))){
				//game is over.
				set_module_setting("running",0);
				clues_drop_hooks();
			}
		}else{
			$now = strtotime("now");
			if ($now >= strtotime(get_module_setting("start")) && $now < strtotime(get_module_setting("end"))){
				//new round starts.
				//erase the seen clues user pref for this module.
				$sql = "DELETE FROM " . db_prefix("module_userprefs") . " WHERE modulename='clue' AND setting='seenclues'";
				db_query($sql);
				clues_setup_hooks();
				set_module_setting("running",1);
			}
		}
	}elseif ($hookname=="superuser"){
		global $session;
		if ($session['user']['superuser'] & SU_MANAGE_MODULES) {
			addnav("Module Configurations");
			addnav("Manage Clues","runmodule.php?module=clues&op=editgame&admin=true");
		}
	}
	return $args;
}

function clues_run(){
	$op=httpget("op");
	check_su_access(SU_MANAGE_MODULES);
	page_header("Edit Clues");
	require_once("lib/superusernav.php");
	superusernav();
	addnav("Clue Editor");
	addnav("Clue Editor Home","runmodule.php?module=clues&op=editgame");
	addnav("Add Clues");
	$types = array(
		"creature"=>"Creature Clue",
		"location"=>"Location Clue (regexp)",
		"namedhook"=>"Named Module Hook",
		);
	while (list($key,$val)=each($types)){
		addnav("Add $val","runmodule.php?module=clues&op=add&type=".$key);
	}
	reset($types);
	$clues = unserialize(get_module_setting("clues"));
	if (!is_array($clues)) $clues = array();
	if ($op=="editgame"){
		rawoutput("<table border='1'>");
		rawoutput("<tr class='trhead'><td>Ops</td><td>Type</td><td>Chance</td><td>Text</td><td>Extras</td></tr>");
		while (list($key,$val)=each($clues)){
			rawoutput("<tr><td>[ <a href='runmodule.php?module=clues&op=edit&type={$val['type']}&id={$key}'>Edit</a>|");
			rawoutput("<a href='runmodule.php?module=clues&op=del&id={$key}' onClick='return(confirm(\"Are you sure you wish to delete this clue?\"));'>Del</a> ]</td><td>{$val['type']}</td><td>");
			addnav("","runmodule.php?module=clues&op=edit&type={$val['type']}&id={$key}");
			addnav("","runmodule.php?module=clues&op=del&id={$key}");
			rawoutput($val['chance']."</td><td>");
			output($val['text']);
			rawoutput("</td><td style='overflow: hidden; width: 150px;'>");
			switch($val['type']){
			case "creature":
				$sql = "SELECT creaturename FROM " . db_prefix("creatures") . " WHERE creatureid={$val['creatureid']}";
				$result = db_query($sql);
				$row = db_fetch_assoc($result);
				output("Creature name: ".$row['creaturename']);
				break;
			case "namedhook":
				output("Hook name: ".$val['hookname']);
				break;
			case "location":
				output("Regexp: ".$val['uri']);
				break;
			}
			rawoutput("</td></tr>");
		}
		rawoutput("</table>");
	}elseif ($op=="add" || $op=="edit"){
		require_once("lib/showform.php");
		$htype=httpget("type");

		switch($htype){
		case "creature":
			$info = array("creatureid"=>"Creature ID (get from creature editor),int");
			$type = "defeating a creature";
			break;
		case "location":
			$info = array("uri"=>"Regular Expression for URI",100=>"The regular expression only applies to portions of the URI after the question mark (?).,note");
			$type="being in a certain location (by regexp)";
			break;
		case "namedhook":
			$info = array("hookname"=>"Hook Name");
			$type="a given named module hook";
		}
		$form = array("Add a new event for $type.,title")
		 + $info
		 + array("text"=>"Clue Text","chance"=>"Percent chance you'll see this clue here,int","type"=>"Type,viewonly");
		 
		//fetch the data
		$id = httpget("id");
		if (isset($clues[$id])) $data = $clues[$id];
		//$data['test'] = "Testing ' Testing \\' Testing \\\\' Testing";
		//$form['test'] = "Testing Field";
		if (!is_array($data)) $data = array("type"=>$htype);
		
		rawoutput("<form action='runmodule.php?module=clues&op=save&type=$htype&id=$id' method='POST'>");
		addnav("","runmodule.php?module=clues&op=save&type=$htype&id=$id");
		showform($form,$data);
		rawoutput("</form>");
	}elseif ($op=="save"){
		require_once("lib/sanitize.php");
		$htype = httpget("type");
		$post = stripslashes_array(httpallpost());
		$post['type'] = $htype;
		
		switch($htype){
		case "creature":
			$hook = "battle-victory";
			break;
		case "location":
			$hook = "footer-".substr($post['uri'],0,strpos(stripslashes($post['uri']),'.'));
			break;
		case "namedhook":
			$hook = $post['hookname'];
			break;
		}
		
		$post['hookname'] = $hook;
		
		$id = httpget("id");
		if ($id>"" && isset($clues[$id])) 
			$clues[$id] = $post;
		else
			array_push($clues,$post);
		$redirect = "runmodule.php?module=clues&op=editgame";
	}elseif ($op=="del"){
		$redirect = "runmodule.php?module=clues&op=editgame";
		$id = httpget("id");
		if (isset($clues[$id])) unset($clues[$id]);
	}
	$clues = serialize($clues);
	if ($clues != get_module_setting("clues")){
		clues_drop_hooks();
		set_module_setting("clues",$clues);
		if (get_module_setting("running"))
			clues_setup_hooks();
	}
	if (isset($redirect)) redirect($redirect);
	page_footer();
}

function clues_doclue($hookname,$args){
	if (get_module_pref("disqualified")) return;

	$rand = e_rand(1,100);
	$seenclues = unserialize(get_module_pref("seenclues"));
	$clues = unserialize(get_module_setting("clues"));
	if (!is_array($clues)) $clues = array();
	if (!is_array($seenclues)) $seenclues = array();

	while (list($key,$val)=each($clues)){
		//debug("Looking at $hookname to {$val['hookname']}");
		if ($hookname == $val['hookname']){
			debug(" + We found the right hook ($hookname)");
			if ($rand<=(int)$val['chance']){
				debug(" + + We met our random chance!");
				$output="";
				switch($val['type']){
				case "creature":
					if ($args['type'] == 'pvp'){
						//we don't do this for pvp.
					}else{
						if ($args['creatureid']==$val['creatureid']){
							$output .= $val['text'];
						}
					}
					break;
				case "location":
					global $REQUEST_URI;
					if (preg_match("'".$val['uri']."'",$REQUEST_URI)>0){
						$output .= $val['text'];
					}else{
						debug(" + + + '{$val['uri']}' does not match '$REQUEST_URI'");
					}
					break;
				case "namedhook":
					$output .= $val['text'];
					break;
				}//end switch
				if ($output>""){
					$seenclues[$key]=$val['text'];
					output ($output);
					//don't break, just in case there happens to be 2 clues at the same spot.
					//break;
				}
			}else{
				debug(" + + Did not meet random chance ($rand/{$val['chance']}).");
			}//end if
		}
	}
	
	$seenclues = serialize($seenclues);
	if ($seenclues!=get_module_pref("seenclues")){
		//we've just seen a new clue!
		set_module_pref("seenclues",$seenclues);
	}
}

function clues_drop_hooks(){
	/*
	$clues = unserialize(get_module_setting("clues"));
	if (!is_array($clues)) $clues = array();
	while (list($key,$val)=each($clues)){
		module_drophook($val['hookname']);
	}
	*/
	//leak free!
	$sql = "DELETE FROM " . db_prefix("module_hooks") . " WHERE modulename='clues' AND function='clues_doclue'";
	db_query($sql);
}

function clues_setup_hooks(){
	//this function assumes the game is running.  You shouldn't call it unless you're sure this is the case.
	//it also assumes that there are no clues hooks already in place.
	$clues = unserialize(get_module_setting("clues"));	
	if (!is_array($clues)) $clues = array();
	while (list($key,$val)=each($clues)){
		module_addhook($val['hookname'],"clues_doclue");
	}
}
?>

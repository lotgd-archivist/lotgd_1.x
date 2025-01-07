<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/http.php");

check_su_access(SU_EDIT_CREATURES);

tlschema("creatures");

$creaturestats = array();

require_once 'lib/creatures.php';
for ($i=1;$i<=$settings->maxlevel+3;$i++){
	$creaturestats[$i] = creature_stats($i);
}

page_header("Creature Editor");

require_once("lib/superusernav.php");
superusernav();

$op = httpget("op");
$subop = httpget("subop");
if ($op == "save"){
	$forest = (int)(httppost('forest'));
	$grave = (int)(httppost('graveyard'));
	$id = httppost('creatureid');
	if (!$id) $id = httpget("creatureid");
	if ($subop == "") {
		$post = httpallpost();
		$lev = (int)httppost('creaturelevel');
		if ($id){
			$sql = "";
			foreach ($post as $key=>$val) {
				if (substr($key,0,8)=="creature") $sql.="$key = '$val', ";
			}
			foreach ($creaturestats[$lev] as $key=>$val) {
				if ( $key!="creaturelevel" && substr($key,0,8)=="creature"){
					$sql.="$key = \"".addslashes($val)."\", ";
				}
			}
			$sql.=" forest='$forest', ";
			$sql.=" graveyard='$grave' ";
			$sql="UPDATE " . DB::prefix("creatures") . " SET " . $sql . " WHERE creatureid='$id'";
			DB::query($sql) or output("`\$".DB::error(LINK)."`0`n`#$sql`0`n");
		}else{
			$cols = array();

			foreach ($post as $key=>$val) {
				if (substr($key,0,8)=="creature") {
					$cols[$key] = $val;
				}
			}
			$cols['forest'] = $forest;
			$cols['graveyard'] = $grave;
			foreach ($creaturestats[$lev] as $key=>$val) {
				if ($key!="creaturelevel"&& substr($key,0,8)=="creature"){
					$cols[$key] = $val;
				}
			}
			$sql="INSERT INTO " . DB::prefix("creatures") . " (".join(",",array_keys($cols)).",createdby) VALUES (\"".join("\",\"",$cols)."\",\"".addslashes($user->login)."\")";
			DB::query($sql);
			$id = DB::insert_id();
		}
		if (DB::affected_rows()) {
			output("`^Creature saved!`0`n");
		} else {
			output("`^Creature `\$not`^ saved!`0`n");
		}
	} elseif ($subop == "module") {
		// Save module settings
		$module = httpget("module");
		$post = httpallpost();
		foreach ($post as $key=>$val) {
			set_module_objpref("creatures", $id, $key, $val, $module);
		}
		output("`^Saved!`0`n");
	}
	// Set the httpget id so that we can do the editor once we save
	httpset("creatureid", $id, true);
	// Set the httpget op so we drop back into the editor
	httpset("op", "edit");
}

$op = httpget('op');
$id = httpget('creatureid');
if ($op=="del"){
	$sql = "DELETE FROM " . DB::prefix("creatures") . " WHERE creatureid = '$id'";
	DB::query($sql);
	if (DB::affected_rows()>0){
		output("Creature deleted`n`n");
	}else{
		output("Creature not deleted: %s", DB::error(LINK));
	}
	$op="";
	httpset('op', "");
}
if ($op=="" || $op=="search"){
	$level = httpget("level");
	if (!$level) $level = 1;
	$q = httppost("q");
	if ($q) {
		$where = "creaturename LIKE '%$q%' OR creatureweapon LIKE '%$q%' OR creaturelose LIKE '%$q%' OR createdby LIKE '%$q%'";
	} else {
		$where = "creaturelevel='$level'";
	}
	$sql = "SELECT * FROM " . DB::prefix("creatures") . " WHERE $where ORDER BY creaturelevel,creaturename";
	$result = DB::query($sql);
	// Search form
	$search = translate_inline("Search");
	rawoutput("<form action='creatures.php?op=search' method='POST'>");
	output("Search by field: ");
	rawoutput("<input name='q' id='q'>");
	rawoutput("<input type='submit' class='button' value='$search'>");
	rawoutput("</form>");
	rawoutput("<script language='JavaScript'>document.getElementById('q').focus();</script>",true);
	addnav("","creatures.php?op=search");

	addnav("Levels");
	$sql1 = "SELECT count(creatureid) AS n,creaturelevel FROM " . DB::prefix("creatures") . " group by creaturelevel order by creaturelevel";
	$result1 = DB::query($sql1);
	while ($row = DB::fetch_assoc($result1)) {
		addnav(array("Level %s: (%s creatures)", $row['creaturelevel'], $row['n']),
				"creatures.php?level={$row['creaturelevel']}");
	}
	// There is no reason to allow players to add creatures to the levels above the max
	if ($level <= $settings->maxlevel + 1) {
		addnav("Edit");
		addnav("Add a creature","creatures.php?op=add&level=$level");
	}
	$opshead = translate_inline("Ops");
	$idhead = translate_inline("ID");
	$name = translate_inline("Name");
	$lev = translate_inline("Level");
	$weapon = translate_inline("Weapon");
	$winmsg = translate_inline("Win");
	$diemsg = translate_inline("Die");
	$author = translate_inline("Author");
	$edit = translate_inline("Edit");
	$confirm = translate_inline("Are you sure you wish to delete this creature?");
	$del = translate_inline("Del");

	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
	rawoutput("<tr class='trhead'>");
	rawoutput("<td>$opshead</td><td>$idhead</td><td>$name</td><td>$lev</td><td>$weapon</td><td>$winmsg</td><td>$diemsg</td><td>$author</td></tr>");
	addnav("","creatures.php");
	$number=DB::num_rows($result);
	for ($i=0;$i<$number;$i++){
		$row = DB::fetch_assoc($result);
		rawoutput("<tr class='".($i%2==0?"trdark":"trlight")."'>", true);
		rawoutput("<td>[ <a href='creatures.php?op=edit&creatureid={$row['creatureid']}'>");
		output_notl("%s", $edit);
		rawoutput("</a> | <a href='creatures.php?op=del&creatureid={$row['creatureid']}&level={$row['creaturelevel']}' onClick='return confirm(\"$confirm\");'>");
		output_notl("%s", $del);
		rawoutput("</a> ]</td><td>");
		addnav("","creatures.php?op=edit&creatureid={$row['creatureid']}");
		addnav("","creatures.php?op=del&creatureid={$row['creatureid']}&level={$row['creaturelevel']}");
		output_notl("%s", $row['creatureid']);
		rawoutput("</td><td>");
		output_notl("%s", $row['creaturename']);
		rawoutput("</td><td>");
		output_notl("%s", $row['creaturelevel']);
		rawoutput("</td><td>");
		output_notl("%s", $row['creatureweapon']);
		rawoutput("</td><td>");
		output_notl("%s", $row['creaturewin']);
		rawoutput("</td><td>");
		output_notl("%s", $row['creaturelose']);
		rawoutput("</td><td>");
		output_notl("%s", $row['createdby']);
		rawoutput("</td></tr>");
	}
	rawoutput("</table>");
}else{
	$level = httpget('level');
	if (!$level) $level = 1;
	if ($op=="edit" || $op=="add"){
		require_once("lib/showform.php");
		addnav("Edit");
		addnav("Creature properties", "creatures.php?op=edit&creatureid=$id");
		addnav("Add");
		addnav("Add Another Creature", "creatures.php?op=add&level=$level");
		module_editor_navs("prefs-creatures", "creatures.php?op=edit&subop=module&creatureid=$id&module=");
		if ($subop == "module") {
			$module = httpget("module");
			rawoutput("<form action='creatures.php?op=save&subop=module&creatureid=$id&module=$module' method='POST'>");
			module_objpref_edit("creatures", $module, $id);
			rawoutput("</form>");
			addnav("", "creatures.php?op=save&subop=module&creatureid=$id&module=$module");
		} else {
			if ($op=="edit" && $id!=""){
				$sql = "SELECT * FROM " . DB::prefix("creatures") . " WHERE creatureid=$id";
				$result = DB::query($sql);
				if (DB::num_rows($result)<>1){
					output("`4Error`0, that creature was not found!");
				}else{
					$row = DB::fetch_assoc($result);
				}
				$level = $row['creaturelevel'];
			} else {
				$row = $creaturestats[$level];
				$row["creatureid"] = 0;
				$row['createdby'] = full_sanitize($user->name);
			}
			$sql1 = "SELECT `scriptid`, `description` FROM `" . DB::prefix('scripts') . '`';
			$res1 = DB::query_cached($sql1, 'ai_scripts', 86400);
			$enum = ',0,None';
			while($row1 = DB::fetch_assoc($res1)) {
				$enum .= ',' . $row1['scriptid'] . ',' . htmlentities($row1['description'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
			}
			$form = array(
				"Creature Properties,title",
				"creatureid"=>"Creature id,hidden",
				"creaturename"=>"Creature Name",
				"creatureweapon"=>"Weapon",
				"creaturewin"=>"Win Message (Displayed when the creature kills the player)",
				"creaturelose"=>"Death Message (Displayed when the creature is killed by the player)",
				// Highest to make a non-forest available monster
				// (ie, graveyard only)_
				"creaturelevel"=>"Level,range,1," . ($settings->maxlevel + 3) . ",1",
				"forest"=>"Creature is in forest?,bool",
				"graveyard"=>"Creature is in graveyard?,bool",
				"creatureaiscriptid"=>"Creature's A.I.,enum" . $enum,
				'creaturehealth'=>'Creature\'s health,int',
				'creatureexp'=>'Creature\'s experience base,int',
				'creaturegold'=>'Creature\'s gold,int',
				'creatureattack'=>'Creature\'s attack,int',
				"creaturedefense"=>"Creature's defense,int",
				"createdby"=>"Name of the creature creator,text",
			);
			rawoutput("<form action='creatures.php?op=save' method='POST'>");
			showform($form, $row);
			rawoutput("</form>");
			addnav("","creatures.php?op=save");
		}
	}else{
		$module = httpget("module");
		rawoutput("<form action='mounts.php?op=save&subop=module&creatureid=$id&module=$module' method='POST'>");
		module_objpref_edit("creatures", $module, $id);
		rawoutput("</form>");
		addnav("", "creatures.php?op=save&subop=module&creatureid=$id&module=$module");
	}
	addnav("Navigation");
	addnav("Return to the creature editor","creatures.php?level=$level");
}
page_footer();
?>

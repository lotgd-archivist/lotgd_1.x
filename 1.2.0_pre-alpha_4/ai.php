<?php
// translator ready
// addnews ready
// mail ready
require_once("common.php");
require_once("lib/http.php");

check_su_access(SU_RAW_SQL);

tlschema("ai");

page_header("AI Script Editor");

require_once("lib/superusernav.php");
superusernav();

$op = httpget("op");
// op:
// -- saves: script + description
// -- scan: scan folder
// -- add: script + description
// -- edits: script + description
// -- editf: viewonly: file, file contents. + description
// -- savef: description (+ file on insert)
if ($op == "saves"){
	$id = httppost('scriptid');
	if (!$id) $id = httpget("scriptid");
	$post = httpallpost();
	if ($id){
		// Want description, script. Unset file.
		$sql = "";
		foreach ($post as $key=>$val) {
			if($key == "script" || $key == "description") {
				$sql.="$key = '$val', ";
			}
		}
		$sql .= 'file = NULL';
		$sql="UPDATE " . DB::prefix("scripts") . " SET " . $sql . " WHERE scriptid='$id'";
		DB::query($sql) or output("`\$".DB::error(LINK)."`0`n`#$sql`0`n");
	}else{
		$cols = array();
		$vals = array();

		$cols[] = 'script';
		$vals[] = $post['script'];
		$cols[] = 'description';
		$vals[] = $post['description'];
		$cols[] = 'file';
		$vals[] = '';
			
		$sql="INSERT INTO " . DB::prefix("scripts") . " (".join(",",$cols).") VALUES (\"".join("\",\"",$vals)."\")";
		DB::query($sql);
		$id = DB::insert_id();
	}
	if (DB::affected_rows()) {
		output("`^Script saved!`0`n");
	} else {
		output("`^Script `\$not`^ saved!`0`n");
	}
	// Set the httpget id so that we can do the editor once we save
	httpset("scriptid", $id, true);
	// Set the httpget op so we drop back into the editor
	httpset("op", "edits");
} elseif ($op == "savef"){
	$id = httppost('scriptid');
	if (!$id) $id = httpget("scriptid");
	$post = httpallpost();
	if ($id){
		// Want description. Unset script.
		$sql = "";
		foreach ($post as $key=>$val) {
			if($key == "description") {
				$sql.="$key = '$val', ";
			}
		}
		$sql .= 'script = NULL';
		$sql="UPDATE " . DB::prefix("scripts") . " SET " . $sql . " WHERE scriptid='$id'";
		DB::query($sql) or output("`\$".DB::error(LINK)."`0`n`#$sql`0`n");
		if (DB::affected_rows()) {
			output("`^Script saved!`0`n");
		} else {
			output("`^Script `\$not`^ saved!`0`n");
		}
	}else{
		foreach ($post as $file=>$data) {
			if(!is_array($data) || !$data['description']) continue;
			$cols = array();
			$vals = array();

			$cols[] = 'file';
			$vals[] = $file;
			$cols[] = 'description';
			$vals[] = $data['description'];
			$cols[] = 'script';
			$vals[] = '';

			$sql="INSERT INTO " . DB::prefix("scripts") . " (".join(",",$cols).") VALUES (\"".join("\",\"",$vals)."\")";
			DB::query($sql);
			$id = DB::insert_id();
			if (DB::affected_rows()) {
				output("`^Script saved!`0`n");
			} else {
				output("`^Script `\$not`^ saved!`0`n");
			}
		}
	}
	// Set the httpget id so that we can do the editor once we save
	httpset("scriptid", $id, true);
	// Set the httpget op so we drop back into the editor
	httpset("op", "editf");
}

// op:
// -- saves: script + description							# done
// -- scan: scan folder										# done
// -- add: script + description								# done
// -- edits: script + description							# done
// -- editf: viewonly: file, file contents. + description	# done
// -- savef: description (+ file on insert)					# done
$op = httpget('op');
$id = httpget('scriptid');
if ($op=="del"){
	$sql = "DELETE FROM " . DB::prefix("scripts") . " WHERE scriptid = '$id'";
	DB::query($sql);
	if (DB::affected_rows()>0){
		output("Script deleted`n`n");
	}else{
		output("Script not deleted: %s", DB::error(LINK));
	}
	$op="";
	httpset('op', "");
}
if ($op=="" || $op=="search"){
	$q = httppost("q");
	if ($q) {
		$where = "WHERE description LIKE '%$q%' OR script LIKE '%$q%' OR file LIKE '%$q%'";
	} else {
		$where = "";
	}
	$sql = "SELECT * FROM " . DB::prefix("scripts") . " $where ORDER BY file,description";
	$result = DB::query($sql);
	// Search form
	$search = translate_inline("Search");
	rawoutput("<form action='ai.php?op=search' method='POST'>");
	output("Search by field: ");
	rawoutput("<input name='q' id='q'>");
	rawoutput("<input type='submit' class='button' value='$search'>");
	rawoutput("</form>");
	rawoutput("<script language='JavaScript'>document.getElementById('q').focus();</script>",true);
	addnav("","ai.php?op=search");

	addnav("Edit");
	addnav("Add a script","ai.php?op=add");
	addnav("Scan scripts folder", 'ai.php?op=scan');

	$opshead = translate_inline("Ops");
	$idhead = translate_inline("ID");
	$description = translate_inline("Description");
	$file = translate_inline("File");
	$script = translate_inline("Script");
	$edit = translate_inline("Edit");
	$confirm = translate_inline("Are you sure you wish to delete this script?");
	$del = translate_inline("Del");

	rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
	rawoutput("<tr class='trhead'>");
	rawoutput("<td>$opshead</td><td>$idhead</td><td>$description</td><td>$file</td><td>$script</td></tr>");
	addnav("","ai.php");
	$number=DB::num_rows($result);
	for ($i=0;$i<$number;$i++){
		$row = DB::fetch_assoc($result);
		if($row['file']) $editt = 'f';
		else $editt = 's';
		rawoutput("<tr class='".($i%2==0?"trdark":"trlight")."'>", true);
		rawoutput("<td>[ <a href='ai.php?op=edit$editt&scriptid={$row['scriptid']}'>");
		output_notl("%s", $edit);
		rawoutput("</a> | <a href='ai.php?op=del&scriptid={$row['scriptid']}' onClick='return confirm(\"$confirm\");'>");
		output_notl("%s", $del);
		rawoutput("</a> ]</td><td>");
		addnav("","ai.php?op=edit$editt&scriptid={$row['scriptid']}");
		addnav("","ai.php?op=del&scriptid={$row['scriptid']}");
		output_notl("%s", $row['scriptid']);
		rawoutput("</td><td>");
		output_notl("%s", $row['description']);
		rawoutput("</td><td>");
		if(!$row['file']) output("No");
		else output_notl("%s", $row['file']);
		rawoutput("</td><td><pre>");
		if($row['file']) $row['script'] = file_get_contents('scripts/' . $row['file']);
		if(strlen($row['script']) > 50) output_notl(substr($row['script'], 0, 50) . '...');
		else output_notl($row['script']);
		rawoutput("</pre></td></tr>");
	}
	rawoutput("</table>");
}elseif ($op == 'scan') {
	$sql = "SELECT file FROM " . DB::prefix('scripts') . " WHERE file != ''";
	$res = DB::query($sql);
	$seenfiles = array();
	while($row = DB::fetch_assoc($res)) {
		$seenfiles[$row['file']] = 1;
	}
	$files = array();
	if ($handle = opendir("scripts")){
		while (false !== ($file = readdir($handle))){
			if ($file[0] == ".") continue;
			if (!isset($seenfiles[$file]) && !is_dir('scripts/' . $file)){
				$files[] = $file;
			}
		}
		closedir($handle);
	}
	addnav("Navigation");
	addnav("Return to the script editor","ai.php");
	addnav("Add");
	addnav("Add a script","ai.php?op=add");
	addnav("Scan scripts folder", 'ai.php?op=scan');
	if(count($files) == 0) {
		output("No new scripts files found.");
	} else {
		$submit = translate_inline('Submit');
		rawoutput("<form action='ai.php?op=savef' method='post'>");
		addnav('', 'ai.php?op=savef');
		$description = translate_inline("Description");
		$file = translate_inline("File");
		$script = translate_inline("Script");

		rawoutput("<table border=0 cellpadding=2 cellspacing=1 bgcolor='#999999'>");
		rawoutput("<tr class='trhead'>");
		rawoutput("<td>$description</td><td>$file</td><td>$script</td></tr>");
		$number=count($files);
		for ($i=0;$i<$number;$i++){
			rawoutput("<tr class='".($i%2==0?"trdark":"trlight")."'>", true);
			rawoutput("<td>");
			rawoutput("<input size='50' name='" . $files[$i] . "[description]' />");
			rawoutput("</td><td>");
			output_notl("%s", $files[$i]);
			rawoutput("</td><td><pre>");
			$script = file_get_contents('scripts/' . $files[$i]);
			if(strlen($script) > 50) output_notl(substr($script, 0, 50) . '...');
			else output_notl($script);
			rawoutput("</pre></td></tr>");
		}
		rawoutput("</table>");
		rawoutput("<input type='submit' value='$submit' />");
		rawoutput("</form>");
	}
}elseif ($op=="edits" || $op=="add"){
	require_once("lib/showform.php");
	addnav("Edit");
	addnav("Script properties", "ai.php?op=edits&scriptid=$id");
	addnav("Add");
	addnav("Add a script","ai.php?op=add");
	addnav("Scan scripts folder", 'ai.php?op=scan');
	if ($op=="edits" && $id!=""){
		$sql = "SELECT * FROM " . DB::prefix("scripts") . " WHERE scriptid=$id";
		$result = DB::query($sql);
		if (DB::num_rows($result)<>1){
			output("`4Error`0, that script was not found!");
		}else{
			$row = DB::fetch_assoc($result);
		}
	} else {
		$row = array("scriptid"=>0);
	}
	$form = array(
		"Script Properties,title",
		"scriptid"=>"Script id,hidden",
		"description"=>"Description",
		"script"=>"Script,textarearesizeable",
	);
	rawoutput("<form action='ai.php?op=saves' method='post'>");
	showform($form, $row);
	rawoutput("</form>");
	addnav("","ai.php?op=saves");
	addnav("Navigation");
	addnav("Return to the script editor","ai.php");
}elseif ($op=="editf"){
	require_once("lib/showform.php");
	addnav("Edit");
	addnav("Script properties", "ai.php?op=editf&scriptid=$id");
	addnav("Add");
	addnav("Add a script","ai.php?op=add");
	addnav("Scan scripts folder", 'ai.php?op=scan');
	$sql = "SELECT * FROM " . DB::prefix("scripts") . " WHERE scriptid=$id";
	$result = DB::query($sql);
	if (DB::num_rows($result)<>1){
		output("`4Error`0, that script was not found!");
		addnav("Navigation");
		addnav("Return to the script editor","ai.php");
		page_footer();
	}else{
		$row = DB::fetch_assoc($result);
	}
	$row['script'] = file_get_contents('scripts/' . $row['file']);
	$form = array(
		"Script Properties,title",
		"scriptid"=>"Script id,hidden",
		"description"=>"Description",
		"file"=>"Path,viewonly",
		"script"=>"Script,viewonly",
	);
	rawoutput("<form action='ai.php?op=savef' method='post'>");
	showform($form, $row);
	rawoutput("</form>");
	addnav("","ai.php?op=savef");
	addnav("Navigation");
	addnav("Return to the script editor","ai.php");
}
page_footer();
?>

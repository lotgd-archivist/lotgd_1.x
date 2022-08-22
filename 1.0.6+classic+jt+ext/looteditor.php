<?php
// PRIVATE_CODE
require_once("common.php");
page_header("Loot Editor");
check_su_access(SU_EDIT_ITEMS);
require_once("lib/superusernav.php");
superusernav();
addnav("",$REQUEST_URI);
addnav("Loot Functions");
addnav("Loot List","looteditor.php");
addnav("Add an item","looteditor.php?op=add");

$op = httpget("op");

if ($op=="add"){
	if (httppost("type") == ""){
		$items = getAllItems();
		rawoutput("<form action='looteditor.php?op=add' method='POST'>");
		addnav("","looteditor.php?op=add");
		output("`bAdd an item`b`n");
		output("Choose the item type: ");
		rawoutput("<select name='type' style='white-space: pre;'>");
		rawoutput(makeItemSelectOptions($items['item']));
		rawoutput("</select>");
		$go = translate_inline("Next");
		rawoutput("<input type='submit' class='button' value='$go'>");
		rawoutput("</form>");
	}else{
		$baseClass = item::instantiate(httppost("type"));
		if ($baseClass !== false){
			output("`b`@Create a new `^%s`@ (%s).`b`n",httppost("type"),expandClassPath(httppost("type")));
			rawoutput("<form action='looteditor.php?op=save&itemid=0&type=".rawurlencode(httppost("type"))."' method='POST'>");
			addnav("","looteditor.php?op=save&itemid=0&type=".rawurlencode(httppost("type")));
			showConfigForm($baseClass);
			$save = translate_inline("Save");
			rawoutput("<input type='submit' class='button' value='$save'>");
			rawoutput("</form>");
		}else{
			output("`\$Was not able to load the class of type %s.",httppost("type"));
			$op = "";
			httpset("op", "");
		}
	}
}elseif ($op=="edit"){
	$sql = "SELECT itemid,object FROM " . db_prefix("items") . " WHERE itemid='".httpget("itemid")."'";
	$result = db_query($sql);
	if (db_num_rows($result) > 0){
		$row = db_fetch_assoc($result);
		$object = item::unserialize($row['object']);
		rawoutput("<form action='looteditor.php?op=save&itemid={$row['itemid']}&type=".rawurlencode(get_class($object))."' method='POST'>");
		addnav("","looteditor.php?op=save&itemid={$row['itemid']}&type=".rawurlencode(get_class($object)));
		showConfigForm($object);
		$save = translate_inline("Save");
		$savenew = translate_inline("Save as New");
		rawoutput("<input type='submit' class='button' value='$save'>");
		rawoutput("<input type='submit' class='button' value='$savenew' name='savenew'>");
		rawoutput("</form>");
	}else{
		output("`^That item no longer exists.");
		httpset("op", "");
		$op = "";
	}
}elseif (httpget("op")=="save"){
	$object = item::instantiate(httpget("type"));
	$post = httpallpost();
	require_once("lib/stripslashes_deep.php");
	$post = stripslashes_deep($post);
	reset($post);
	while (list($key,$val)=each($post)){
		$object->setAttribute($key,$val);
	}
	if (httpget("itemid") > 0 and httppost("savenew")==""){
		$sql = "
			UPDATE " . db_prefix("items") . " SET
				name='".httppost("name")."',
				object='".addslashes($object->serialize())."',
				category='".httppost("category")."',
				classname='".expandClassPath(get_class($object))."',
				editor='".addslashes($session['user']['name'])."',
				lastupdate='".date("Y-m-d H:i:s")."'
			WHERE
				itemid = '".httpget("itemid")."'";
		debug("<pre>$sql</pre>");
		db_query($sql);
		output("Item saved.");
	}else{
		$sql = "
			INSERT INTO " . db_prefix("items") . " (
				name,object,category,classname,author,editor,lastupdate
			) VALUES (
				'".httppost("name")."',
				'".addslashes($object->serialize())."',
				'".httppost("category")."',
				'".expandClassPath(get_class($object))."',
				'".addslashes($session['user']['name'])."',
				'".addslashes($session['user']['name'])."',
				'".date("Y-m-d H:i:s")."'
			)";
		debug("<pre>$sql</pre>");
		db_query($sql);
		output("`^Item saved.");
	}
	$op = "";
	httpset("op", "");
} elseif ($op == "delete") {
	$sql = "DELETE FROM " . db_prefix("items") . " WHERE itemid = '" . httpget("itemid") . "'";
	db_query($sql);
	output("`^Item deleted.");
	// XXX: Still need to get rid of existing copies!!
	$op = "";
	httpset("op", "");
}

if ($op == ""){
	rawoutput("<table>");
	rawoutput("<tr class='trhead'><td>".tl("Ops")."</td><td>".tl("Name")."</td><td>".tl("Category")."</td><td>".tl("Class")."</td><td>".tl("Edits")."</td></tr>");
	$sql = "SELECT itemid,name,category,classname,author,editor,lastupdate FROM " . db_prefix("items") . " ORDER BY classname";
	$result = db_query($sql);
	$i=0;
	while ($row = db_fetch_assoc($result)){
		$i++;
		rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>");
		rawoutput("<td>");
		$edit = translate_inline("Edit");
		$delete = translate_inline("Delete");
		rawoutput("[<a href='looteditor.php?op=edit&itemid={$row['itemid']}'>$edit</a>");
		rawoutput("|<a href='looteditor.php?op=delete&itemid={$row['itemid']}'>$delete</a>]</td>");
		addnav("","looteditor.php?op=edit&itemid={$row['itemid']}");
		addnav("","looteditor.php?op=delete&itemid={$row['itemid']}");
		tlschema("items");
		rawoutput("<td>");
		output($row['name']);
		rawoutput("</td>");
		rawoutput("<td>");
		output($row['category']);
		rawoutput("</td>");
		rawoutput("<td>{$row['classname']}</td>");
		output_notl("<td>`@A: `2{$row['author']}`n`@E: `2{$row['editor']}`0</td>",true);
		tlschema();
		rawoutput("</tr>");
	}
	rawoutput("</table>");
}

page_footer();

function showConfigForm($class){
	$basevals = get_class_vars(get_class($class));
	reset($basevals);
	$vals = array();
	while (list($key,$val)=each($basevals)){
		$vals[$key] = $class->getAttribute($key);
	}
	$baseconfig = $class->getConfig();
	reset($baseconfig);
	$config = array();
	while (list($key,$val)=each($baseconfig)){
		$config = array_merge($config,$val);
	}
	require_once("lib/showform.php");
	showform($config,$vals,true);
}

function makeItemSelectOptions($items,$prefix=""){
	if (count($items)==0) return "";
	reset($items);
	$i=0;
	$output = "";
	while (list($key,$val)=each($items)){
		$output .= "<option value='$key'>{$prefix}{$key}</option>";
		$i++;
		if ($i == count($items)){
			$subprefix = $prefix."--";
		}else{
			$subprefix = $prefix."--";
		}
		$output .= makeItemSelectOptions($val,$subprefix);
	}
	return $output;
}
?>

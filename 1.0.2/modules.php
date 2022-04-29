<?php
// addnews ready
// translator ready
// mail ready
require_once("common.php");
require_once("lib/http.php");
require_once("lib/sanitize.php");
check_su_access(SU_MANAGE_MODULES);
tlschema("modulemanage");

page_header("Module Manager");

require_once("lib/superusernav.php");
superusernav();

addnav("Module Categories");

addnav("",$REQUEST_URI);
$op = httpget('op');
$module = httpget('module');

if ($op == 'mass'){
	if (httppost("activate")) $op = "activate";
	if (httppost("deactivate")) $op = "deactivate";
	if (httppost("uninstall")) $op = "uninstall";
	if (httppost("reinstall")) $op = "reinstall";
	if (httppost("install")) $op = "install";
	$module = httppost("module");
}
$theOp = $op;
if (is_array($module)){
	$modules = $module;
}else{
	if ($module) $modules = array($module);
	else $modules = array();
}
reset($modules);
while (list($key,$module)=each($modules)){
	$op = $theOp;
	output("`2Performing `^%s`2 on `%%s`0`n", translate_inline($op), $module);
	if($op=="install"){
		if (install_module($module)){
	
		}else{
			httpset('cat','');
		}
		$op="";
		httpset('op', "");
	}elseif($op=="uninstall"){
		if (uninstall_module($module)) {
		} else {
			output("Unable to inject module.  Module not uninstalled.`n");
		}
		$op="";
		httpset('op', "");
	}elseif($op=="activate"){
		activate_module($module);
		$op="";
		httpset('op', "");
		invalidatedatacache("inject-$module");
	}elseif($op=="deactivate"){
		deactivate_module($module);
		$op="";
		httpset('op', "");
		invalidatedatacache("inject-$module");
	}elseif($op=="reinstall"){
		$sql = "UPDATE " . db_prefix("modules") . " SET filemoddate='0000-00-00 00:00:00' WHERE modulename='$module'";
		db_query($sql);
		// We don't care about the return value here at all.
		injectmodule($module, true);
		$op="";
		httpset('op', "");
		invalidatedatacache("inject-$module");
	}
}

$install_status = get_module_install_status();
$uninstmodules = $install_status['uninstalledmodules'];
$seencats = $install_status['installedcategories'];
$ucount = $install_status['uninstcount'];

ksort($seencats);
addnav(array(" ?Uninstalled - (%s modules)", $ucount), "modules.php");
reset($seencats);
foreach ($seencats as $cat=>$count) {
	addnav(array(" ?%s - (%s modules)", $cat, $count), "modules.php?cat=$cat");
}

$cat = httpget('cat');

if ($op==""){
	if ($cat) {
		$tcat = translate_inline($cat);
		output("`n`b%s Modules`b`n", $tcat);
		$deactivate = translate_inline("Deactivate");
		$activate = translate_inline("Activate");
		$uninstall = translate_inline("Uninstall");
		$reinstall = translate_inline("Reinstall");
		$strsettings = translate_inline("Settings");
		$strnosettings = translate_inline("`\$No Settings`0");
		$uninstallconfirm = translate_inline("Are you sure you wish to uninstall this module?  All user preferences and module settings will be lost.  If you wish to temporarily remove access to the module, you may simply deactivate it.");
		$status = translate_inline("Status");
		$mname = translate_inline("Module Name");
		$ops = translate_inline("Ops");
		$mauth = translate_inline("Module Author");
		$inon = translate_inline("Installed On");
		$installstr = translate_inline("by %s");
		$active = translate_inline("`@Active`0");
		$inactive = translate_inline("`\$Inactive`0");
		rawoutput("<form action='modules.php?op=mass&cat=$cat' method='POST'>");
		addnav("","modules.php?op=mass&cat=$cat");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' bgcolor='#999999'>",true);
		rawoutput("<tr class='trhead'><td>&nbsp;</td><td>$ops</td><td>$status</td><td>$mname</td><td>$mauth</td><td>$inon</td></tr>");
		$sql = "SELECT * FROM " . db_prefix("modules") . " WHERE category='$cat' ORDER BY installdate DESC";
		$result = db_query($sql);
		if (db_num_rows($result)==0){
			rawoutput("<tr class='trlight'><td colspan='6' align='center'>");
			output("`i-- No Modules Installed--`i");
			rawoutput("</td></tr>");
		}
		for ($i=0;$i<db_num_rows($result);$i++){
			$row = db_fetch_assoc($result);
			rawoutput("<tr class='".($i%2?"trlight":"trdark")."'>",true);
			rawoutput("<td nowrap valign='top'>");
			rawoutput("<input type='checkbox' name='module[]' value=\"{$row['modulename']}\">");
			rawoutput("</td><td valign='top' nowrap>[ ");
			if ($row['active']){
				rawoutput("<a href='modules.php?op=deactivate&module={$row['modulename']}&cat=$cat'>");
				output_notl($deactivate);
				rawoutput("</a>");
				addnav("","modules.php?op=deactivate&module={$row['modulename']}&cat=$cat");
			}else{
				rawoutput("<a href='modules.php?op=activate&module={$row['modulename']}&cat=$cat'>");
				output_notl($activate);
				rawoutput("</a>");
				addnav("","modules.php?op=activate&module={$row['modulename']}&cat=$cat");
			}
			rawoutput(" |<a href='modules.php?op=uninstall&module={$row['modulename']}&cat=$cat' onClick='return confirm(\"$uninstallconfirm\");'>");
			output_notl($uninstall);
			rawoutput("</a>");
			addnav("","modules.php?op=uninstall&module={$row['modulename']}&cat=$cat");
			rawoutput(" | <a href='modules.php?op=reinstall&module={$row['modulename']}&cat=$cat'>");
			output_notl($reinstall);
			rawoutput("</a>");
			addnav("","modules.php?op=reinstall&module={$row['modulename']}&cat=$cat");
		
			if (strstr($row['infokeys'], "|settings|")) {
				rawoutput(" | <a href='configuration.php?op=modulesettings&module={$row['modulename']}'>");
				output_notl($strsettings);
				rawoutput("</a>");
				addnav("","configuration.php?op=modulesettings&module={$row['modulename']}");
			} else {
				output_notl(" | %s", $strnosettings);
			}
			
			rawoutput(" ]</td><td valign='top'>");
			output_notl($row['active']?$active:$inactive);
			rawoutput("</td><td nowrap valign='top'>");
			output_notl("%s", $row['formalname']);
			rawoutput("<br>");
			output_notl("(%s)", $row['modulename']);
			rawoutput("</td><td valign='top'>");
			output_notl("`#%s`0", $row['moduleauthor'], true);
			rawoutput("</td><td nowrap valign='top'>");
			$line = sprintf($installstr, $row['installedby']);
			output_notl("%s", $row['installdate']);
			rawoutput("<br>");
			output_notl("%s", $line);
			rawoutput("</td></tr>");
		}
		rawoutput("</table><br />");
		$activate = translate_inline("Activate");
		$deactivate = translate_inline("Deactivate");
		$reinstall = translate_inline("Reinstall");
		$uninstall = translate_inline("Uninstall");
		rawoutput("<input type='submit' name='activate' class='button' value='$activate'>");
		rawoutput("<input type='submit' name='deactivate' class='button' value='$deactivate'>");
		rawoutput("<input type='submit' name='reinstall' class='button' value='$reinstall'>");
		rawoutput("<input type='submit' name='uninstall' class='button' value='$uninstall'>");
		rawoutput("</form>");
	} else {
		output("`bUninstalled Modules`b`n");
		$install = translate_inline("Install");
		$mname = translate_inline("Module Name");
		$ops = translate_inline("Ops");
		$mauth = translate_inline("Module Author");
		$categ = translate_inline("Category");
		$fname = translate_inline("Filename");
		rawoutput("<form action='modules.php?op=mass&cat=$cat' method='POST'>");
		addnav("","modules.php?op=mass&cat=$cat");
		rawoutput("<table border='0' cellpadding='2' cellspacing='1' bgcolor='#999999'>",true);
		rawoutput("<tr class='trhead'><td>&nbsp;</td><td>$ops</td><td>$mname</td><td>$mauth</td><td>$categ</td><td>$fname</td></tr>");
		if (count($uninstmodules) > 0) {
			reset($uninstmodules);
			$count = 0;
			foreach($uninstmodules as $key=>$shortname) {
				$moduleinfo = get_module_info($shortname);
				if (!$moduleinfo || empty($moduleinfo)) continue;
				rawoutput("<tr class='".($count%2?"trlight":"trdark")."'>");
				rawoutput("<td><input type='checkbox' name='module[]' value='{$shortname}'></td>");
				rawoutput("<td nowrap valign='top'>");
				rawoutput("[ <a href='modules.php?op=install&module=$shortname&cat={$moduleinfo['category']}'>");
				output_notl($install);
				rawoutput("</a>]</td>");
				addnav("","modules.php?op=install&module=$shortname&cat={$moduleinfo['category']}");
				rawoutput("<td nowrap valign='top'>");
				rawoutput($moduleinfo['name']." ".$moduleinfo['version']);
				rawoutput("</td><td valign='top'>");
				output_notl("`#%s`0", $moduleinfo['author'], true);
				rawoutput("</td><td valign='top'>");
				rawoutput($moduleinfo['category']);
				rawoutput("</td><td valign='top'>");
				rawoutput($shortname . ".php");
				rawoutput("</td>");
				rawoutput("</tr>");
				if (count($moduleinfo['requires'])){
					rawoutput("<tr class='".($count%2?"trlight":"trdark")."'>");
					rawoutput("<td>&nbsp;</td>");
					rawoutput("<td colspan='5'>");
					output("`bRequires:`b`n");
					reset($moduleinfo['requires']);
					while (list($key,$val)=each($moduleinfo['requires'])){
						$info = explode("|",$val);
						if (module_check_requirements(array($key=>$val))){
							output_notl("`@");
						}else{
							output_notl("`\$");
						}
						output_notl("$key {$info[0]} -- {$info[1]}`n");
					}
					rawoutput("</td>");
					rawoutput("</tr>");
				}
				$count++;
			}
		} else {
			rawoutput("<tr class='trlight'><td colspan='5' align='center'>");
			output("`i--No uninstalled modules were found--`i");
			rawoutput("</td></tr>");
		}
		rawoutput("</table><br />");
		$install = translate_inline("Install");
		rawoutput("<input type='submit' name='install' class='button' value='$install'>");
	}	
}

page_footer();
?>

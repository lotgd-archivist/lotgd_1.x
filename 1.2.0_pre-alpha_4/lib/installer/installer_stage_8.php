<?php
require_once("lib/installer/installer_functions.php");
if (isset($_POST['modules'])){
	Session::set('moduleoperations', $_POST['modules']);
	Session::set('stagecompleted', $stage);
	header("Location: installer.php?stage=".($stage+1));
	exit();
}elseif (is_array(Session::get('moduleoperations'))){
	Session::set('stagecompleted', $stage);
}else{
	Session::set('stagecompleted', $stage - 1);
}
output("`@`c`bManage Modules`b`c");
output("Legend of the Green Dragon supports an extensive module system.");
output("Modules are small self-contained files that perform a specific function or event within the game.");
output("For the most part, modules are independant of each other, meaning that one module can be installed, uninstalled, activated, and deactivated without negative impact on the rest of the game.");
output("Not all modules are ideal for all sites, for example, there's a module called 'Multiple Cities,' which is intended only for large sites with many users online at the same time.");
output("`n`n`^If you are not familiar with Legend of the Green Dragon, and how the game is played, it is probably wisest to choose the default set of modules to be installed.");
output("`n`n`@There is an extensive community of users who write modules for LoGD at <a href='http://dragonprime.net/'>http://dragonprime.net/</a>.",true);
$phpram = ini_get("memory_limit");
if (return_bytes($phpram) < 12582912 && $phpram!=-1 && !Session::get('overridememorylimit') && !Session::getNested('dbinfo', 'upgrade')) {// 12 MBytes
	// enter this ONLY if it's not an upgrade and if the limit is really too low
	output("`n`n`\$Warning: Your PHP memory limit is set to a very low level.");
	output("Smaller servers should not be affected by this during normal gameplay but for this installation step you should assign at least 12 Megabytes of RAM for your PHP process.");
	output("For now we will skip this step, but before installing any module, make sure to increase you memory limit.");
	output("`nYou can proceed at your own risk. Be aware that a blank screen indicates you *must* increase the memory limit.");
	output("`n`nTo override click again on \"Set Up Modules\".");
	Session::set('stagecompleted', 8);
	Session::set('overridememorylimit', true);
	Session::set('skipmodules', true);
} else {
	if (Session::get('overridememorylimit')) {
		output("`4`n`nYou have been warned... you are now working on your own risk.`n`n");
		Session::set('skipmodules', false);
	}
	$submit = translate_inline("Save Module Settings");
	$install = translate_inline("Select Recommended Modules");
	$reset = translate_inline("Reset Values");
	$all_modules = array();
	$sql = "SELECT * FROM ".DB::prefix("modules")." ORDER BY category,active DESC,formalname";
	$result = @DB::query($sql);
	if ($result!==false){
		while ($row = DB::fetch_assoc($result)){
			if (!isset($all_modules[$row['category']])){
				$all_modules[$row['category']] = array();
			}
			$row['installed']=true;
			$all_modules[$row['category']][$row['modulename']] = $row;
		}
	}
	$install_status = get_module_install_status();
	$uninstalled = $install_status['uninstalledmodules'];
	$invalidmodule = array(
				"version"=>"",
				"author"=>"",
				"category"=>"Invalid Modules",
				"download"=>"",
				"description"=>"",
				"invalid"=>true,
			);
	foreach ($uninstalled as $modulename) {
		$row = array();
		//test if the file is a valid module or a lib file/whatever that got in, maybe even malcode that does not have module form
		$file = file_get_contents("modules/$modulename.php");
		if (stripos($file,$modulename."_getmoduleinfo")===false ||
			//strpos($file,$shortname."_dohook")===false ||
			//do_hook is not a necessity
			stripos($file,$modulename."_install")===false ||
			stripos($file,$modulename."_uninstall")===false) {
			//here the files has neither do_hook nor getinfo, which means it won't execute as a module here --> block it + notify the admin who is the manage modules section
			$moduleinfo=array_merge($invalidmodule,array("name"=>$modulename.".php ".appoencode(translate_inline("(`\$Invalid Module! Contact Author or check file!`0)"))));
		} else {
			$moduleinfo= get_module_info($modulename);
		}
		//end of testing
		$row['installed'] = false;
		$row['active'] = false;
		$row['category'] = $moduleinfo['category'];
		$row['modulename'] = $modulename;
		$row['formalname'] = $moduleinfo['name'];
		$row['description'] = $moduleinfo['description'];
		$row['moduleauthor'] = $moduleinfo['author'];
		$row['invalid'] = (isset($moduleinfo['invalid']))?$moduleinfo['invalid']:false;
		if (!isset($all_modules[$row['category']])){
			$all_modules[$row['category']] = array();
		}
		$all_modules[$row['category']][$row['modulename']] = $row;
	}
	if (count($all_modules) == 0) {
		Session::set('skipmodules', true);
		Session::set('stagecompleted', $stage);
		header("Location: installer.php?stage=".($stage+1));
		exit();
	}
	output_notl("`0");
	rawoutput("<form action='installer.php?stage=".$stage."' method='POST'>");
	rawoutput("<input type='submit' value='$submit' class='button'>");
	rawoutput("<input type='button' onClick='chooseRecommendedModules();' class='button' value='$install' class='button'>");
	rawoutput("<input type='reset' value='$reset' class='button'><br>");
	rawoutput("<table cellpadding='1' cellspacing='1'>");
	ksort($all_modules);
	$x=0;
	foreach ($all_modules as $categoryName=>$categoryItems) {
		rawoutput("<tr class='trhead'><td colspan='6'>".tl($categoryName)."</td></tr>");
		rawoutput("<tr class='trhead'><td>".tl("Uninstalled")."</td><td>".tl("Installed")."</td><td>".tl("Activated")."</td><td>".tl("Recommended")."</td><td>".tl("Module Name")."</td><td>".tl("Author")."</td></tr>");
		foreach ($categoryItems as $modulename=>$moduleinfo) {
			$x++;
			//if we specified things in a previous hit on this page, let's update the modules array here as we go along.
			$moduleinfo['realactive'] = $moduleinfo['active'];
			$moduleinfo['realinstalled'] = $moduleinfo['installed'];
			if (is_array(Session::get('moduleoperations')) && Session::getNested('moduleoperations', $modulename)){
				$ops = explode(",",Session::getNested('moduleoperations', $modulename));
				foreach ($ops as $op) {
					switch($op){
						case "uninstall":
						$moduleinfo['installed'] = false;
						$moduleinfo['active'] = false;
						break;
						case "install":
						$moduleinfo['installed'] = true;
						$moduleinfo['active'] = false;
						break;
						case "activate":
						$moduleinfo['installed'] = true;
						$moduleinfo['active'] = true;
						break;
						case "deactivate":
						$moduleinfo['installed'] = true;
						$moduleinfo['active'] = false;
						break;
						case "donothing":
						break;
					}
				}
			}
			rawoutput("<tr class='".($x%2?"trlight":"trdark")."'>");
			if ($moduleinfo['realactive']){
				$uninstallop = "uninstall";
				$installop = "deactivate";
				$activateop = "donothing";
			}elseif ($moduleinfo['realinstalled']){
				$uninstallop = "uninstall";
				$installop = "donothing";
				$activateop = "activate";
			}else{
				$uninstallop = "donothing";
				$installop = "install";
				$activateop = "install,activate";
			}
			$uninstallcheck = false;
			$installcheck = false;
			$activatecheck = false;
			if ($moduleinfo['active']){
				$activatecheck = true;
			}elseif ($moduleinfo['installed']){
				//echo "<font color='red'>$modulename is installed but not active.</font><br>";
				$installcheck = true;
			}else{
				//echo "$modulename is uninstalled.<br>";
				$uninstallcheck = true;
			}
			if (isset($moduleinfo['invalid']) && $moduleinfo['invalid'] == true) {
				rawoutput("<td><input type='radio' name='modules[$modulename]' id='uninstall-$modulename' value='$uninstallop' checked disabled></td>");
				rawoutput("<td><input type='radio' name='modules[$modulename]' id='install-$modulename' value='$installop' disabled></td>");
				rawoutput("<td><input type='radio' name='modules[$modulename]' id='activate-$modulename' value='$activateop' disabled></td>");
			} else {
				rawoutput("<td><input type='radio' name='modules[$modulename]' id='uninstall-$modulename' value='$uninstallop'".($uninstallcheck?" checked":"")."></td>");
				rawoutput("<td><input type='radio' name='modules[$modulename]' id='install-$modulename' value='$installop'".($installcheck?" checked":"")."></td>");
				rawoutput("<td><input type='radio' name='modules[$modulename]' id='activate-$modulename' value='$activateop'".($activatecheck?" checked":"")."></td>");
			}
			output_notl("<td>".(in_array($modulename,$recommended_modules)?tl("`^Yes`0"):tl("`\$No`0"))."</td>",true);
			require_once("lib/sanitize.php");
			rawoutput("<td><span title=\"" .
			(isset($moduleinfo['description']) &&
			$moduleinfo['description'] ?
			$moduleinfo['description'] :
			sanitize($moduleinfo['formalname'])). "\">");
			output_notl("`@");
			if (isset($moduleinfo['invalid']) && $moduleinfo['invalid'] == true) {
				rawoutput($moduleinfo['formalname']);
			} else {
				output($moduleinfo['formalname']);
			}
			output_notl(" [`%$modulename`@]`0");
			rawoutput("</span></td><td>");
			output_notl("`#{$moduleinfo['moduleauthor']}`0", true);
			rawoutput("</td>");
			rawoutput("</tr>");
		}
	}
	rawoutput("</table>");
	rawoutput("<br><input type='submit' value='$submit' class='button'>");
	rawoutput("<input type='button' onClick='chooseRecommendedModules();' class='button' value='$install' class='button'>");
	rawoutput("<input type='reset' value='$reset' class='button'>");
	rawoutput("</form>");
	rawoutput("<script language='JavaScript'>
function chooseRecommendedModules(){
	var thisItem;
	var selectedCount = 0;
");
	foreach ($recommended_modules as $val) {
		rawoutput("thisItem = document.getElementById('activate-$val'); ");
		rawoutput("if (!thisItem.checked) { selectedCount++; thisItem.checked=true; }\n");
	}
	rawoutput("
	alert('I selected '+selectedCount+' modules that I recommend, but which were not already selected.');
}");
	if (!Session::getNested('dbinfo', 'upgrade')){
		rawoutput("
	chooseRecommendedModules();");
	}
	rawoutput("
</script>");
}
?>

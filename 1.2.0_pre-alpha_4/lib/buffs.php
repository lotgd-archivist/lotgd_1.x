<?php
// addnews ready
// translator ready
// mail ready

$buffreplacements = array();
$debuggedbuffs = array();
function calculate_buff_fields(){
	global $buffreplacements, $debuggedbuffs, $user;
	if (!($buffs = Session::get('bufflist'))) return;

	//run temp stats
	foreach ($buffs as $buffname=>$buff) {
		if (!isset($buff['tempstats_calculated'])){
			foreach($buff as $property=>$value){
				if (substr($property,0,9)=='tempstat-'){
					apply_temp_stat(substr($property,9),$value);
				}
			}//end while
			$buffs[$buffname]['tempstats_calculated']=true;
		}//end if
	}//end foreach
	$output = '';
	//process calculated buff fields.
	if (!is_array($buffreplacements)) $buffreplacements = array();
	foreach ($buffs as $buffname=>$buff) {
		if (!isset($buff['fields_calculated'])){
			foreach ($buff as $property=>$value) {
				//calculate dynamic buff fields
				$origstring = $value;
				//Simple <module|variable> replacements for get_module_pref('variable','module')
				$value = preg_replace("/<([A-Za-z0-9]+)\\|([A-Za-z0-9]+)>/","get_module_pref('\\2','\\1')",$value);
				//simple <variable> replacements for $user->variable
				$value = preg_replace("/<([A-Za-z0-9]+)>/","\$user->\\1",$value);
				$oldsu=$user->superuser;
//				if (!defined("OLDSU")) {
//					define("OLDSU", $user->superuser);
//				}
				if ($value != $origstring){
					if (strtolower(substr($value,0,6))=="debug:"){
						$errors="";
						$origstring = substr($origstring,6);
						$value = substr($value,6);
						if (!isset($debuggedbuffs[$buffname])) $debuggedbuffs[$buffname]=array();

						ob_start();
						$val = eval("return $value;");
						$errors = ob_get_contents();
						ob_end_clean();

						if (!isset($debuggedbuffs[$buffname][$property])){
							if ($errors==""){
								debug("Buffs[$buffname][$property] evaluates successfully to $val");
							}else{
								debug("Buffs[$buffname][$property] has an evaluation error<br>"
								.htmlentities($origstring, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))." becomes <br>"
								.htmlentities($value, ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."<br>"
								.$errors);
								$val="";
							}
							$debuggedbuffs[$buffname][$property]=true;
						}

						$origstring="debug:".$origstring;
						$value="debug".$value;
					}else{
						$val = eval("return $value;");
					}
				}else{
					$val = $value;
				}

				$user->superuser = $oldsu;
				//Avoiding PHP bug 27646
				// (http://bugs.php.net/bug.php?id=27646&edit=2) -
				// Unserialize doesn't recognize NAN, -INF and INF
				if (function_exists('is_nan')) {
					if (is_numeric($val) &&
							(is_nan($val) || is_infinite($val)))
						$val=$value;
				} else {
					// We have an older version of PHP, so, let's try
					// something else.
					$l = strtolower("$val");
					if ((substr($l, 3) == "nan") || (substr($l, -3) == "inf"))
						$val = $value;
				}
				if (!isset($output)) $output = "";
				if ($output == "" && (string)$val != (string)$origstring){
					$buffreplacements[$buffname][$property] = $origstring;
					$buffs[$buffname][$property] = $val;
				}//end if
				unset($val);
			}//end foreach
			$buffs[$buffname]['fields_calculated']=true;
		}//end if
	}//end foreach
	Session::set('bufflist', $buffs);
}//end function

function restore_buff_fields(){
	global $buffreplacements;
	$buffs = Session::get('bufflist');
	if (is_array($buffreplacements)){
		foreach ($buffreplacements as $buffname=>$val) {
			foreach ($val as $property=>$value) {
				if (isset($buffs[$buffname])){
					$buffs[$buffname][$property] = $value;
					unset($buffs[$buffname]['fields_calculated']);
				}//end if
			}//end foreach
			unset($buffreplacements[$buffname]);
		}//end foreach
	}//end if

	//restore temp stats
	if (!is_array($buffs)) $buffs = array();
	foreach ($buffs as $buffname=>$buff) {
		if (isset($buff['tempstats_calculated']) && $buff['tempstats_calculated']){
			
			foreach ($buff as $property=>$value) {			
				
				if (substr($property,0,9)=='tempstat-'){
					apply_temp_stat(substr($property,9),-$value);
				}
			}//end foreach
			unset($buffs[$buffname]['tempstats_calculated']);
		}//end if
	}//end foreach
	Session::set('bufflist', $buffs);
}//end function

function apply_buff($name,$buff){
	debug($name);
	debug($buff);
	global $buffreplacements, $translation_namespace;

	if (!isset($buff['schema']) || $buff['schema'] == "") {
		$buff['schema'] = $translation_namespace;
	}

	if (isset($buffreplacements[$name])) unset($buffreplacements[$name]);
	if (Session::getNested('bufflist', $name)){
		//we'll need to unapply buff fields before applying this buff since
		//it's already set.
		restore_buff_fields();
	}
	$buff = modulehook("modify-buff", array("name"=>$name, "buff"=>$buff));
	Session::setNested('bufflist', $name, $buff['buff']);
	calculate_buff_fields();
}

function apply_companion($name,$companion,$ignorelimit=false){
	global $companions, $user;
	if (!is_array($companions)) {
		$companions = @unserialize($user->companions);
	}
	$companionsallowed = getsetting("companionsallowed", 1);
	$args = modulehook("companionsallowed", array("maxallowed"=>$companionsallowed));
	$companionsallowed = $args['maxallowed'];
	$current = 0;
	foreach (array_keys($companions) as $thisname) {
		if (isset($companion['ignorelimit']) && $companion['ignorelimit'] == true) {
		} else {
			if ($thisname != $name)
			++$current;
		}
	}
	if ($current < $companionsallowed || $ignorelimit == true) {
		if (isset($companions[$name])) {
			unset($companions[$name]);
		}
		if (!isset($companion['ignorelimit']) && $ignorelimit == true) {
			$companion['ignorelimit'] = true;
		}
		$companions[$name] = $companion;
		$user->companions = createstring($companions);
		return true; // success!
	} else {
		debug("Failed to add companion due to restrictions regarding the maximum amount of companions allowed.");
		return false;
	}
}


function strip_buff($name){
	global $buffreplacements;
	restore_buff_fields();
	if (Session::getNested('bufflist', $name))
		Session::deleteNested('bufflist', $name);
	if (isset($buffreplacements[$name]))
		unset($buffreplacements[$name]);
	calculate_buff_fields();
}

function strip_all_buffs(){
	$thebuffs = Session::get('bufflist');
	foreach (array_keys($thebuffs) as $buffname) {
		strip_buff($buffname);
	}
}

function has_buff($name){
	return (bool)Session::getNested('bufflist', $name);
}

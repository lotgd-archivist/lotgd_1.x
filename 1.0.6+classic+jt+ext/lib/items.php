<?php
// addnews ready
// translator ready
// mail ready
// PRIVATE_CODE

function expandClassPath($className){
	$class = $className;
	$classPath = "";
	while ($class > ""){
		if ($classPath > "") $classPath=".".$classPath;
		$classPath = $class.$classPath;
		$class = get_parent_class($class);
	}
	return $classPath;
}

function getAllItems(){
	debug("Use of the getAllItems() function can be an intensive process as it has to include and instantiate every object in the classes/ folder, please use it cautiously!");
	$d = dir("classes/");
	$classes = array();
	while (($entry = $d->read())!==false){
		if (substr($entry,-4) == ".php"){
			require_once("classes/{$entry}");
			$class = substr($entry,0,-4);
			$temp = new $class;
			$classes[$class] = explode(".",expandClassPath($class));
		}
	}
	reset($classes);
	$allClasses = array();
	while (list($key,$val)=each($classes)){
		$pointer =& $allClasses;
		reset($val);
		while (list($k,$v)=each($val)){
			if (!isset($pointer[$v])){
				$pointer[$v] = array();
			}
			$pointer =& $pointer[$v];
		}
	}
	debug($allClasses);
	return $allClasses;
}
?>

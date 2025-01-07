<?php

function __autoload($classname) {
	$file = dirname(__FILE__) . '/classes/' . strtolower($classname) . '.php';
	if(file_exists($file)) {
		include_once $file;
	}
	if(!class_exists($classname)) {
		$explode = explode('_', $classname);
		$file = dirname(__FILE__) . '/classes/' . strtolower($explode[0]) . '/' . strtolower($classname) . '.php';
		if(file_exists($file)) {
			include_once $file;
		}
	}
}

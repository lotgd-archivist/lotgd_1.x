<?php
//PRIVATE_CODE

function dropthet_getmoduleinfo(){
	$info = array(
		'name'=>"Drop the T",
		'version'=>'1.0',
		'author'=>'Eric Stevens',
		'category'=>'Administrative',
		'download'=>'',
	);
	return $info;
}

function dropthet_install(){
	module_addhook("holiday");
	module_addhook("everyfooter");
	module_addhook("footer-popup");
	return true;
}

function dropthet_uninstall(){
	return true;
}

function dropthet_dohook($hookname,$args){
	switch($hookname){
	case "footer-popup":
	case "everyfooter":
		if (!array_key_exists('headscript',$args) || !is_array($args['headscript'])){
			$args['headscript'] = array();
		}
		array_push($args['headscript'],"<style type='text/css'>em { color: #999999; font-style: normal; }</style>");
		break;
	case "holiday":
		if ($args['type'] == 'title') return $args;
		$args['text'] = str_replace('t','`tt`t',$args['text']);
		$args['text'] = str_replace('T','`tT`t',$args['text']);
		break;
	}
	return $args;
}
?>

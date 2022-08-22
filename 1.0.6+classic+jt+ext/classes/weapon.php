<?php
// PRIVATE_CODE
require_once("equippable.php");
class weapon extends equippable {
	var $damage = 0;
	function getConfig(){
		$config = parent::getConfig();
		$config['stats'] = array(
			"Player Stats,title",
			"damage"=>"Weapon Damage,int",
		);
		return $config;
	}
	function onSwing($damageDone){
	
	}
}
?>

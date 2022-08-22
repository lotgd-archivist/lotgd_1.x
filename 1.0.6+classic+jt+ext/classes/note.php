<?php
// PRIVATE_CODE
class note extends item {
	var $text="";
	function note(){
		$this->bulk = 0.1;
		$this->weight = 0.1;
		$this->value = 0;
		$this->category = "Letters";
		parent::item();
	}
	function getConfig(){
		$config = parent::getConfig();
		$config['basic']['text'] = "Note Text,textarea";
		return $config;
	}
}
?>

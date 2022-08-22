<?php
// PRIVATE_CODE
require_once("item.php");
class equippable extends item {
	var $slots=array();
	var $classes=array();
	var $races=array();
	var $levelreq = 0;
	var $skillreq = "";
	var $dkreq = 0;
	function equippable(){
		$specialties = modulehook("specialtynames");
		reset($specialties);
		while (list($key,$val)=each($specialties)){
			$this->classes[$key]=1;
		}
		
		$races = modulehook("racenames");
		reset($races);
		while (list($key,$val)=each($races)){
			$this->races[$key]=1;
		}
		parent::item();
	}
	function isEquippable(){
		global $session;
		if (isset($classes[$session['user']['specialty']]) && $classes[$session['user']['specialty']]){
			if ($session['user']['level'] >= $levelreq){
				if ($session['user']['dragonkills'] >= $dkreq){
					if (isset($races[$session['user']['race']]) && $races[$session['user']['race']]){
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	function getConfig(){
		$config = parent::getConfig();
		$config['requirements'][] = "Use Restrictions,title";
		$config['requirements']["levelreq"] = "Level Requirement,int";
		$config['requirements']['dkreq'] = "Dragon Kill Requirement,int";
		
		$config['requirements']['classes'] = "Specialty Restriction,checklist";
		$specialties = modulehook("specialtynames");
		reset($specialties);
		while (list($key,$val)=each($specialties)){
			$config['requirements']['classes'] .= ",{$key},{$val}";
		}

		$config['requirements']['races'] = "Race Restriction,checklist";
		$races = modulehook("racenames");
		reset($races);
		while (list($key,$val)=each($races)){
			$config['requirements']['races'] .= ",{$key},{$val}";
		}
		return $config;
	}
	function getSlots(){
		return $slots;
	}
	function setAttribute($name,$val){
		if ($name == "slots" || $name=="classes"){
			if (is_array($val)){
				$this->$name = $val;
			}else{
				$this->$name = array_flip(explode(",",$val));
			}
		}else{
			parent::setAttribute($name,$val);
		}
	}
}
?>

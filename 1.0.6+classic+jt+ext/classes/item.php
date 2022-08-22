<?php
// PRIVATE_CODE
//The LoGD base class.  Every item should extend this, or another class that is some derivative of this.

class item {
	var $name="";
	var $desc="";
	var $bulk=1;
	var $weight=1;
	var $value=0;
	var $category="Generic";
	function item(){
		
	}
	function onAcquire(){} //stub
	function onUnacquire(){} //stub
	function getDescription(){
		return "`b`&{$this->name}`0`n"
			."`>`2Weight: `@{$this->weight}`n"
			."`2Bulk: `@{$this->bulk}`n"
			."`7{$this->desc}`0";
	}
	function getAttribute($name){
		if (isset($this->$name)) return $this->$name;
		return false;
	}
	function setAttribute($name,$value){
		if (isset($this->$name)){
			$this->$name = $value;
			return true;
		}else{
			return false;
		}
	}
	function getConfig(){
		return array(
			"basic"=>array(
				"Basic Properties,title",
				"name"=>"Name",
				"desc"=>"Description",
				"value"=>"Value,int",
				"category"=>"Category",
			),
			"physical"=>array(
				"Physical Properties,title",
				"bulk"=>"Bulk,float",
				"weight"=>"Weight,float",
			),
		);
	}
	function getItemBox($prefix=""){
		return "<div style='width: 200px; max-width: 200px; height: 75px; max-height: 75px;'>"
			.$prefix
			.appoencode($this->getDescription())
			."</div>";
	}
	// management functions
	function sanitizeValues(){
		//stub -- this function is called any time an object 
		//is loaded from the database and its class definition
		//file has been modified.  Whenever you define one of
		//these, you should make sure to call parent::sanitizeValues()
		//as the first operation.
	}
	function serialize(){
		//returns the current item in our own serialized form.
		return serialize(
			array(
				"classname"=>get_class($this),
				"serialdata"=>serialize($this),
				"serialtime"=>strtotime("now"),
				)		
		);
	}
	function unserialize($serialdata){
		//returns an item from our serialized format
		$info = unserialize($serialdata);
		if (class_exists($info['classname'])){
			$return = unserialize($info['serialdata']);
		}
		if (file_exists("classes/{$info['classname']}.php")){
			require_once("classes/{$info['classname']}.php");
			$return = unserialize($info['serialdata']);
		}else{
			trigger_error("Unable to include class definition: classes/{$info['classname']}.php", E_USER_ERROR);
			return false;
		}
		//check the modification time of the class and its parents to see if we 
		//need to call sanitizeValues()
		$class = $info['classname'];
		while ($class > ""){
			if (file_exists("classes/{$class}.php")){
				if (filemtime("classes/{$class}.php") > $info['serialtime']){
					debug("The class file classes/{$class}.php is more recent than this instance of {$info['classname']}.");
					$return->sanitizeValues();
					break;
				}
			}
			$class = get_parent_class($class);
		}
		return $return;
	}
	function instantiate($class){
		if (!class_exists($class)){
			if (file_exists("classes/$class.php")){
				require_once("classes/$class.php");
			}else{
				trigger_error("Unable to include class definition: classes/{$info['classname']}.php", E_USER_ERROR);
				return false;
			}
		}
		return new $class;
	}
}
?>

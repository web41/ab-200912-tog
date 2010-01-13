<?php

class App {
	
	public function set($name,$value) {
		//$temp_path = 'C:\TEMP\'; // for Windows server
		//$temp_path = '/tmp/';      // for Unix server
		$temp_path = dirname(Prado::getApplication()->Request->ApplicationFilePath).DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR;
		$strvalue = serialize($value);
		// generate a storeable representation of
		// the value, with it's type and structure
		$file_pointer = fopen($temp_path . $name, 'w');
		if(!(fwrite($file_pointer,$strvalue))) {
			return false;  //success
		} else {
			return true;   //not able to write file
		}
	}
	
	public function get($name) {
		//$temp_path = 'C:\TEMP\'; // for Windows server
		//$temp_path = '/tmp/';      // for Unix server
		$temp_path = dirname(Prado::getApplication()->Request->ApplicationFilePath).DIRECTORY_SEPARATOR."assets".DIRECTORY_SEPARATOR;
		// file(path) returns an array, so we implode it
		if (file_exists($temp_path . $name)) {
			if(!($strvalue = implode("",file($temp_path . $name)))) {
				return false; //could not get file
			} else {
				$value =  unserialize($strvalue);
				return $value;  //here's the value of the variable
			}
		} else return false;
	}
}

?>
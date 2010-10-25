<?php

/**
 * This class renders the REST response data as XML.
 */
class TwilperRenderer {
	
	var $itemName;
	/**
	 * Constructor.
	 */
	function render($data, $collectionName, $className) {
		header('Content-Type: text/plain');
		$itemName = $className;
		echo($this->buildJSON($data, $collectionName));
	}
	
	function array2json($arr) { 
		if(function_exists('json_encode')) return json_encode($arr); //Lastest versions of PHP already has this functionality. 
		$parts = array(); 
		$is_list = false; 
		
		//Find out if the given array is a numerical array 
		$keys = array_keys($arr); 
		$max_length = count($arr)-1; 
		if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1 
			$is_list = true; 
			for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position 
				if($i != $keys[$i]) { //A key fails at position check. 
					$is_list = false; //It is an associative array. 
					break; 
				} 
			} 
		} 
		
		foreach($arr as $key=>$value) { 
			if(is_array($value)) { //Custom handling for arrays 
				if($is_list) $parts[] = $this->array2json($value); /* :RECURSION: */ 
				else $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */ 
			} else { 
				$str = ''; 
				if(!$is_list) $str = '"' . $key . '":'; 
				
				//Custom handling for multiple data types 
				if(is_numeric($value)) $str .= $value; //Numbers 
				elseif($value === false) $str .= 'false'; //The booleans 
				elseif($value === true) $str .= 'true'; 
				else $str .= '"' . addslashes($value) . '"'; //All other things 
				// :TODO: Is there any more datatype we should be in the lookout for? (Object?) 
				
				$parts[] = $str; 
			} 
		} 
		$json = implode(',',$parts); 
		
		if($is_list) return '[' . $json . ']';//Return numerical JSON 
		return '{' . $json . '}';//Return associative JSON 
	}
	
	public function buildJSON($data, $startElement = 'twitreplyObject', $xml_version = '1.0', $xml_encoding = 'UTF-8'){
		if(!is_array($data)){
			$err = 'Invalid variable type supplied, expected array not found on line '.__LINE__." in Class: ".__CLASS__." Method: ".__METHOD__;
			trigger_error($err);
			if($this->_debug) echo $err;
			return false; //return false error occurred
		}
		
		
		return $this->array2json($data);
	}
}

?>
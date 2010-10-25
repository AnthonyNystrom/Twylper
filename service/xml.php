<?php
require_once 'xmlBuilder.php';
/**
 * This class renders the REST response data as XML.
 */
class TwilperRenderer {
	
	var $itemName;
	/**
	 * Constructor.
	 */
	function render($data, $collectionName, $className) {
		header('Content-Type: text/xml');
		$itemName = $className;
		$xml = new xmlBuilder();
		
		$xml->push($collectionName);
		foreach ($data as $topic) {
			$xml->push($className);
			foreach($topic as $key => $value){
			
				if (is_numeric($key))
					continue;
				$xml->element($key, $value);	
			}
			$xml->pop();
		}
		
		$xml->pop();
		
		echo( $xml->getXml());
	}
}

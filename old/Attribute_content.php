<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_content extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		
		
		$childNodes = array();
		foreach ( $node->childNodes as $child ) {
			$childNodes [] = $node->ownerDocument->cloneNode($child, true);
		}
		$node->removeChilds();
			
		foreach($childNodes as $child){
			$node->appendChild($child);
		}
		return self::STOP_NODE;
	}
}

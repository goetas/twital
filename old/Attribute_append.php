<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_append extends Attribute{
	function start(xml\XMLDomElement  $node, \DOMAttr $att){
		$pi = $this->dom->createTextNode("{{ $att->value) }}");
		$node->appendChild($pi);
	}
}
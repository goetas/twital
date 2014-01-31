<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_include_replace extends Attribute_include{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		if($att->value[0]=="#"){
			$att->value = $this->compiler->getTemplate()->getRef().$att->value;
		}
		$pi = $this->dom->createProcessingInstruction("php",$this->generatePI($node,$att));
		$node->parentNode->replaceChild($pi, $node);
		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}
}

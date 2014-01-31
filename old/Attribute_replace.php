<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_replace extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		if(strlen(trim($att->value))>0){
			$pi = $this->dom->createProcessingInstruction("php","print( ".$this->compiler->parsedExpression($att->value)." ) ; ");
			$node->parentNode->replaceChild($pi, $node);
		}else{
			$node->remove();
		}
		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}


}

<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_replace_cdata extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){

		$pi = $this->dom->createProcessingInstruction("php","print('<![CDATA[' . ".$this->compiler->parsedExpression($att->value)." .']]>' ) ; ");
		$node->parentNode->replaceChild($pi, $node);

		return self::STOP_NODE;
	}


}

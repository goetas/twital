<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_capture extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){

		$piS = $this->dom->createProcessingInstruction("php"," ob_start(); ");
		$piE = $this->dom->createProcessingInstruction("php"," \$$att->value = ob_get_clean() ; ");

		$node->parentNode->insertBefore($piS, $node);
		$node->parentNode->insertAfter($piE, $node);

	}

}

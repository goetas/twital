<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Twital;
use DOMException;
use goetas\atal\Attribute;
class Attribute_template extends Attribute_block_def{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		parent::start($node, $att);

		$php = Attribute_call::prepareCode($att, $this->compiler);


		$piS = $this->dom->createProcessingInstruction( "php", $php);
		$node->parentNode->replaceChild($piS, $node);

		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}
}



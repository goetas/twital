<?php
namespace goetas\atal\attribute;
use goetas\xml;
use Exception;
use goetas\atal\Attribute;
use goetas\atal\Compiler;
class Attribute_call extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$php = self::prepareCode($att, $this->compiler);


		$piS = $this->dom->createProcessingInstruction( "php", $php);
		$node->parentNode->replaceChild($piS, $node);

		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}
	public static function prepareCode(\DOMAttr $att, Compiler $compiler){
		return Attribute_block_call::prepareCode($att, $compiler, '$this->', 1);
	}

}

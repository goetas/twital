<?php
namespace goetas\atal\attribute;
use goetas\xml;
use Exception;
use goetas\atal\Attribute;
use goetas\atal\Compiler;
class Attribute_block_parent extends Attribute_block_call{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$pi = $this->dom->createProcessingInstruction("php",self::prepareCode($att, $this->compiler, 'parent::', 1));
		$node->parentNode->replaceChild($pi, $node);
		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}
}

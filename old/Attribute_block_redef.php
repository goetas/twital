<?php
namespace goetas\atal\attribute;
use goetas\xml;
use Exception;
use goetas\atal\Attribute;
use goetas\atal\Compiler;
use goetas\atal\Twital;
class Attribute_block_redef extends Attribute_block_def{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		parent::start($node, $att);
		return self::STOP_NODE | self::STOP_ATTRIBUTE;
	}
}


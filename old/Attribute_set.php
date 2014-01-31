<?php
namespace goetas\atal\attribute;
use goetas\xml;
use Exception;
use goetas\atal\Attribute;
class Attribute_set extends Attribute{
	function start(xml\XMLDomElement $node, \DOMAttr $att){
		$expressions=$this->compiler->splitExpression($att->value,";");
		foreach (array_reverse($expressions) as $expression){
			$mch=array();
			if(preg_match("/^(". "[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)([^=]*)\\s*=\\s*(.+)/", $expression, $mch)){
				$pi = $this->dom->createTextNode("{% $mch[1]$mch[2] = $mch[3] %}");
				$node->parentNode->insertBefore($pi, $node);
			}else{
				throw new Exception("Sintassi plugin set non valida: '$att->value'");
			}
		}
	}
}

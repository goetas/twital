<?php
namespace goetas\atal\attribute;
use goetas\atal;
use goetas\xml;
use goetas\atal\Attribute;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use ArrayIterator;
use DOMAttr;
class Attribute_forelse extends Attribute {

	function start(xml\XMLDomElement $node, DOMAttr $att) {


		$name = uniqid ( 'l' );


		$mch = $this->compiler->splitExpression ( $att->value, " as " );
		$itname = "\$__tal_" . $name;

		$code .= " $itname = " . ($mch [0] [0] == "$" && $mch [0] [strlen ( $mch [0] ) - 1] != ")" ? "" . $mch [0] : $mch [0]) . "; \n ";

		$code .= " if ( ( is_array($itname) || ( $itname instanceof Countable ))  &&  count($itname)==0 ) {\n";


		$pi = $this->dom->createProcessingInstruction ( "php", $code );
		$node->parentNode->insertBefore ( $pi, $node );

		$codeEnd = '';
		$codeEnd .= " \n } \n";


		$pi = $this->dom->createProcessingInstruction ( "php", $codeEnd );
		$node->parentNode->insertAfter ( $pi, $node );
	}
}

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
class Attribute_foreach extends Attribute {

	function start(xml\XMLDomElement $node, DOMAttr $att) {
		$pi = $this->dom->createTextNode ( "{% for $att->value %}" );
		$node->parentNode->insertBefore ( $pi, $node );

		$pi = $this->dom->createTextNode ( "{% endfor %}" );
		$node->parentNode->insertAfter ( $pi, $node );
	}

}
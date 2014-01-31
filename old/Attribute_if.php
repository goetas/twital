<?php
namespace goetas\atal\attribute;
use goetas\xml;
use goetas\atal\Attribute;
class Attribute_if extends Attribute {
	public function start(xml\XMLDomElement $node, \DOMAttr $att) {
		
		$piS = $this->dom->createTextNode( "{% if $att->value  %}");
		$piE = $this->dom->createTextNode( "{% endif  %}" );

		$node->parentNode->insertBefore( $piS, $node );
		$node->parentNode->insertAfter( $piE, $node );

	}
}

<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use DOMAttr;

class CaptureAttribute implements Attribute
{

    public function visit(DOMAttr $att, Compiler $twital)
    {
        $node = $att->ownerElement;

        $pi = $node->ownerDocument->createTextNode("{% set " . html_entity_decode($att->value) . " %}");
        $node->parentNode->insertBefore($pi, $node);

        $pi = $node->ownerDocument->createTextNode("{% endset %}");

        $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after

        $node->removeAttributeNode($att);
    }
}

<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use DOMAttr;

class ContentAttribute implements Attribute
{

    function visit(DOMAttr $att, Compiler $twital)
    {
        $node = $att->ownerElement;
        $node->removeChilds();
        $pi = $node->ownerDocument->createTextNode("{{ " . html_entity_decode($att->value) . " }}");
        $node->appendChild($pi);

        $node->removeAttributeNode($att);
    }
}
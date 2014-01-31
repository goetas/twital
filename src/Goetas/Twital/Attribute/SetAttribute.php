<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\TwitalLoader;
use DOMAttr;

class SetAttribute implements Attribute
{

    function visit(DOMAttr $att, TwitalLoader $twital)
    {
        $node = $att->ownerElement;

        $pi = $node->ownerDocument->createTextNode("{% {$att->localName} " . html_entity_decode($att->value) . " %}");
        $node->parentNode->insertBefore($pi, $node);

        $node->removeAttributeNode($att);
    }
}
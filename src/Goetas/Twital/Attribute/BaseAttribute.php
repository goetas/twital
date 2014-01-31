<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;
use DOMAttr;
use Goetas\Twital\DOMHelper;
use DOMException;
class BaseAttribute implements AttributeBase
{
    function visit(DOMAttr $att, Compiler $twital)
    {
        $node = $att->ownerElement;

        $pi = $node->ownerDocument->createTextNode("{% {$att->localName} " . html_entity_decode($att->value) . " %}");
        $node->parentNode->insertBefore($pi, $node);

        $pi = $node->ownerDocument->createTextNode("{% end{$att->localName} %}");


        $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after

        $node->removeAttributeNode($att);
    }
}
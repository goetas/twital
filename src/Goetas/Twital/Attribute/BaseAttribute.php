<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\TwitalLoader;
use DOMAttr;
use Goetas\Twital\DOMHelper;

class BaseAttribute implements AttributeBase
{
    function visit(DOMAttr $att, TwitalLoader $twital)
    {
        $node = $att->ownerElement;

        $pi = $node->ownerDocument->createTextNode("{% {$att->localName} " . html_entity_decode($att->value) . " %}");
        $node->parentNode->insertBefore($pi, $node);

        $pi = $node->ownerDocument->createTextNode("{% end{$att->localName} %}");
        DOMHelper::insertAfter($node->parentNode, $pi, $node);

        $node->removeAttributeNode($att);
    }
}
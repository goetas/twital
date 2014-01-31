<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\TwitalLoader;
use DOMAttr;
use Goetas\Twital\DOMHelper;

class CaptureAttribute implements Attribute
{

    function visit(DOMAttr $att, TwitalLoader $twital)
    {
        $node = $att->ownerElement;

        $pi = $node->ownerDocument->createTextNode("{% set " . html_entity_decode($att->value) . " %}");
        $node->parentNode->insertBefore($pi, $node);

        $pi = $node->ownerDocument->createTextNode("{% endset %}");

        DOMHelper::insertAfter($node->parentNode, $pi, $node);

        $node->removeAttributeNode($att);
    }
}
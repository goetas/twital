<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\TwitalLoader;
use DOMAttr;
use Goetas\Twital\DOMHelper;

class OmitAttribute implements Attribute
{

    function visit(DOMAttr $att, TwitalLoader $twital)
    {
        $node = $att->ownerElement;

        $pi = $node->ownerDocument->createTextNode("{% set _tmp_omit = " . html_entity_decode($att->value) . " %}");
        $node->parentNode->insertBefore($pi, $node);

        $pi = $node->ownerDocument->createTextNode("{% if not _tmp_omit %}");
        $node->parentNode->insertBefore($pi, $node);

        $pi = $node->ownerDocument->createTextNode("{% if not _tmp_omit %}");
        $node->appendChild($pi);

        $pi = $node->ownerDocument->createTextNode("{% endif %}");
        DOMHelper::insertAfter($node->parentNode, $pi, $node);

        $node->removeAttributeNode($att);
    }
}
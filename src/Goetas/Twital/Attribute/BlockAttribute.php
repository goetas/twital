<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;
use DOMAttr;
class BlockAttribute implements AttributeBase
{
    public function visit(DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $pi = $context->createControlNode("{$att->localName} " . html_entity_decode($att->value));

        if ($node->firstChild) {
            $node->insertBefore($pi, $node->firstChild);
        }else{
            $node->appendChild($pi);
        }

        $pi = $context->createControlNode("end{$att->localName}");
        $node->appendChild($pi);

        $node->removeAttributeNode($att);
    }
}

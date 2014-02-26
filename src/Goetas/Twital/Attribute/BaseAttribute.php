<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\CompilationContext;
use DOMAttr;
class BaseAttribute implements AttributeBase
{
    public function visit(DOMAttr $att, CompilationContext $context)
    {
        $node = $att->ownerElement;

        $pi = $context->crateContolNode("{$att->localName} " . html_entity_decode($att->value));
        $node->parentNode->insertBefore($pi, $node);

        $pi = $node->crateContolNode("end{$att->localName}");

        $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after

        $node->removeAttributeNode($att);
    }
}

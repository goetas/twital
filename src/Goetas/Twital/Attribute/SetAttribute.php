<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\CompilationContext;
use DOMAttr;

class SetAttribute implements Attribute
{

    public function visit(DOMAttr $att, CompilationContext $context)
    {
        $node = $att->ownerElement;

        $pi = $context->crateContolNode("set ".html_entity_decode($att->data));

        $node->parentNode->insertBefore($pi, $node);

        $node->removeAttributeNode($att);
    }
}

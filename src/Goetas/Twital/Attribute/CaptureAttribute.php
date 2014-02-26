<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\CompilationContext;
use DOMAttr;

class CaptureAttribute implements Attribute
{

    public function visit(DOMAttr $att, CompilationContext $context)
    {
        $node = $att->ownerElement;

        $pi = $context->createControlNode("set " . html_entity_decode($att->value) );
        $node->parentNode->insertBefore($pi, $node);

        $pi = $context->createControlNode("endset");

        $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after

        $node->removeAttributeNode($att);
    }
}

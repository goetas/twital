<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\CompilationContext;
use DOMAttr;
class IfAttribute implements AttributeBase
{
    public function visit(DOMAttr $att, CompilationContext $context)
    {
        $node = $att->ownerElement;
        if($att->value!=="1" && $att->value!=="true"){

            $pi = $context->createControlNode("if " . html_entity_decode($att->value));
            $node->parentNode->insertBefore($pi, $node);

            $pi = $context->createControlNode("end{$att->localName}");

            $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after
        }
        $node->removeAttributeNode($att);
    }
}

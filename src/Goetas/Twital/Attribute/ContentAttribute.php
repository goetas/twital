<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\CompilationContext;
use DOMAttr;
use Goetas\Twital\DOMHelper;

class ContentAttribute implements Attribute
{

    public function visit(DOMAttr $att, CompilationContext $context)
    {
        $node = $att->ownerElement;
        DOMHelper::removeChilds($node);
        $pi = $context->createControlNode("{{ " . html_entity_decode($att->value) . " }}");
        $node->appendChild($pi);

        $node->removeAttributeNode($att);
    }
}

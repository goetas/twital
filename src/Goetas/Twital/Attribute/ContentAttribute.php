<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use DOMAttr;
use Goetas\Twital\Helper\DOMHelper;

class ContentAttribute implements Attribute
{

    public function visit(DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        DOMHelper::removeChilds($node);
        $pi = $context->createPrintNode( html_entity_decode($att->value));
        $node->appendChild($pi);

        $node->removeAttributeNode($att);
    }
}

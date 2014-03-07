<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use DOMAttr;
use Goetas\Twital\Helper\DOMHelper;

class OmitAttribute implements Attribute
{

    public function visit(DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $pi = $context->createControlNode("set _tmp_omit = " . html_entity_decode($att->value) );
        $node->parentNode->insertBefore($pi, $node);

        $pi = $context->createControlNode("if not _tmp_omit");
        $node->parentNode->insertBefore($pi, $node);

        $pi = $context->createControlNode("if not _tmp_omit");
        $node->appendChild($pi);

        $pi = $context->createControlNode("endif");
        DOMHelper::insertAfter($node->parentNode, $pi, $node);

        $node->removeAttributeNode($att);
    }
}

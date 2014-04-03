<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use DOMAttr;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class TranslateAttribute implements Attribute
{

    public function visit(DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $with = '';
        if ($att->value) {
            $with = "with ".html_entity_decode($att->value);
        }
        $start = $context->createControlNode("trans $with");
        $end = $context->createControlNode("endtrans");

        $node->insertBefore($start, $node->firstChild);

        $node->appendChild($end);

        $node->removeAttributeNode($att);
    }
}

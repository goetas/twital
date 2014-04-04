<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use DOMAttr;
use Goetas\Twital\Helper\DOMHelper;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class OmitAttribute implements Attribute
{

    public function visit(DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $pi = $context->createControlNode("set __tmp_omit = " . html_entity_decode($att->value));
        $node->parentNode->insertBefore($pi, $node);

        $pi = $context->createControlNode("if not __tmp_omit");
        $node->parentNode->insertBefore($pi, $node);

        $pi = $context->createControlNode("endif");
        if ($node->firstChild) {
            $node->insertBefore($pi, $node->firstChild);
        } else {
            $node->appendChild($pi);
        }

        $pi = $context->createControlNode("if not __tmp_omit");
        $node->appendChild($pi);

        $pi = $context->createControlNode("endif");

        if ($node->parentNode->nextSibling) {
            $node->parentNode->insertBefore($pi, $node->parentNode->nextSibling);
        } else {
            $node->parentNode->appendChild($pi);
        }

        $node->removeAttributeNode($att);

        if ($att->value == "true" || $att->value == "1") {
            foreach (iterator_to_array($node->attributes) as $att) {
                $node->removeAttributeNode($att);
            }
        }

        return Attribute::STOP_ATTRIBUTE;
    }
}

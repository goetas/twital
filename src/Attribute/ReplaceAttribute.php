<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use Goetas\Twital\Helper\DOMHelper;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class ReplaceAttribute implements Attribute
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        $pi = $context->createPrintNode(html_entity_decode($att->value));

        $node->parentNode->replaceChild($pi, $node);

        $node->removeAttributeNode($att);
        return Attribute::STOP_NODE;
    }
}

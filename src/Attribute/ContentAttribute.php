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
class ContentAttribute implements Attribute
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        DOMHelper::removeChilds($node);
        $pi = $context->createPrintNode(html_entity_decode($att->value));
        $node->appendChild($pi);

        $node->removeAttributeNode($att);
    }
}

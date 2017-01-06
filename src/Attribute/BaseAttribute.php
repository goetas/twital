<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class BaseAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $pi = $context->createControlNode("{$att->localName} " . html_entity_decode($att->value));
        $node->parentNode->insertBefore($pi, $node);

        $pi = $context->createControlNode("end{$att->localName}");

        $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after

        $node->removeAttributeNode($att);
    }
}

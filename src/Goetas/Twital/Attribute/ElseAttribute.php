<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;
use Goetas\Twital\Exception;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class ElseAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        if (!$prev = IfAttribute::findPrevElement($node)) {
            throw new Exception("The attribute 'elseif' must be the very next sibling of an 'if' of 'elseif' attribute");
        }

        $pi = $context->createControlNode("else");
        $node->parentNode->insertBefore($pi, $node);

        $pi = $context->createControlNode("endif");
        $node->parentNode->insertBefore($pi, $node->nextSibling);

        $node->removeAttributeNode($att);
    }
}

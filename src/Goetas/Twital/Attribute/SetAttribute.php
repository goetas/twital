<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use Goetas\Twital\Helper\ParserHelper;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class SetAttribute implements Attribute
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $sets = ParserHelper::staticSplitExpression(html_entity_decode($att->value), ",");
        foreach ($sets as $set) {
            $pi = $context->createControlNode("set ".$set);
            $node->parentNode->insertBefore($pi, $node);
        }

        $node->removeAttributeNode($att);
    }
}

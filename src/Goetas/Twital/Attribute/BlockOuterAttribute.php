<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;
use DOMAttr;

/**
 * This will translate '<div t:block-outer="name">foo</div>' into '{% block name%}<div>foo</div>{% endblock %}'
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class BlockOuterAttribute implements AttributeBase
{
    public function visit(DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $pi = $context->createControlNode("block " . $att->value);
        $node->parentNode->insertBefore($pi, $node);

        $pi = $context->createControlNode("endblock");
        $node->parentNode->insertBefore($pi, $node->nextSibling);

        $node->removeAttributeNode($att);
    }
}

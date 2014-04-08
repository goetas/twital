<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;
use DOMAttr;

/**
 * This will translate '<div t:block-inner="name">foo</div>' into '<div>{% block name%}foo{% endblock %}</div>'
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class BlockInnerAttribute implements AttributeBase
{
    public function visit(DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        $pi = $context->createControlNode("block " . $att->value);

        if ($node->firstChild) {
            $node->insertBefore($pi, $node->firstChild);
        }else{
            $node->appendChild($pi);
        }

        $pi = $context->createControlNode("endblock");
        $node->appendChild($pi);

        $node->removeAttributeNode($att);
    }
}

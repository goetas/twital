<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;
use Goetas\Twital\Helper\DOMHelper;
use Goetas\Twital\Twital;

/**
 * This will translate '<div t:block-inner="name">foo</div>' into '<div>{% block name%}foo{% endblock %}</div>'
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class BlockInnerAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        $node->removeAttributeNode($att);

        // create sandbox and append it to the node
        $sandbox = $node->ownerDocument->createElementNS(Twital::NS, "sandbox");

        // move all child to sandbox to sandbox
        while ($node->firstChild) {
            $child = $node->removeChild($node->firstChild);
            $sandbox->appendChild($child);
        }
        $node->appendChild($sandbox);

        //$context->compileAttributes($node);
        $context->compileChilds($sandbox);


        $start = $context->createControlNode("block " . $att->value);
        $end = $context->createControlNode("endblock");

        $sandbox->insertBefore($start, $sandbox->firstChild);
        $sandbox->appendChild($end);

        DOMHelper::replaceWithSet($sandbox, iterator_to_array($sandbox->childNodes));
    }
}

<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;
use Goetas\Twital\Helper\DOMHelper;
use Goetas\Twital\Twital;

/**
 * This will translate '<div t:block-outer="name">foo</div>' into '{% block name%}<div>foo</div>{% endblock %}'
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class BlockOuterAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        $node->removeAttributeNode($att);

        // create sandbox
        $sandbox = $node->ownerDocument->createElementNS(Twital::NS, "sandbox");
        $node->parentNode->insertBefore($sandbox, $node);

        // move to sandbox
        $node->parentNode->removeChild($node);
        $sandbox->appendChild($node);

        $context->compileAttributes($node);
        $context->compileChilds($node);


        $start = $context->createControlNode("block " . $att->value);
        $end = $context->createControlNode("endblock");

        $sandbox->insertBefore($start, $sandbox->firstChild);
        $sandbox->appendChild($end);

        DOMHelper::replaceWithSet($sandbox, iterator_to_array($sandbox->childNodes));
    }
}

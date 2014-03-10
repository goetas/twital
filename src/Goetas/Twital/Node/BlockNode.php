<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\Compiler;
use Goetas\Twital\Helper\DOMHelper;
use Exception;
use Goetas\Twital\Twital;
class BlockNode implements Node
{
    public function visit(\DOMElement $node, Compiler $context)
    {
        if (! $node->hasAttribute("name")) {
            throw new Exception("Name attribute is required");
        }
        $xp = new \DOMXPath($node->ownerDocument);

        $currPrima = $node->previousSibling;

        $sandbox = $node->ownerDocument->createElementNS(Twital::NS, "sandbox");
        $node->parentNode->insertBefore($sandbox,$node);
        $node->parentNode->removeChild($node);
        $sandbox->appendChild($node);

        $context->compileAttributes($node);
        $context->compileChilds($node);

        $start = $context->createControlNode("block " . $node->getAttribute("name") );
        $end = $context->createControlNode("endblock");

        $sandbox->insertBefore($start, $sandbox->firstChild);
        $sandbox->appendChild($end);

        DOMHelper::replaceWithSet($sandbox, iterator_to_array($sandbox->childNodes));
        DOMHelper::replaceWithSet($node, iterator_to_array($node->childNodes));
    }
}
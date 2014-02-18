<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\Compiler;

use Goetas\Twital\DOMHelper;
use Exception;
class BlockNode implements Node
{
    function visit(\DOMElement $node, Compiler $twital)
    {
        if (! $node->hasAttribute("name")) {
            throw new Exception("Name attribute is required");
        }
        $xp = new \DOMXPath($node->ownerDocument);

        $currPrima = $node->previousSibling;

        $sandbox = $node->ownerDocument->createElementNS(Compiler::NS, "sandbox");
        $node->parentNode->insertBefore($sandbox,$node);
        $node->parentNode->removeChild($node);
        $sandbox->appendChild($node);

        $twital->applyTemplatesToAttributes($node);
        $twital->applyTemplatesToChilds($node);

        $start = $node->ownerDocument->createTextNode("\n{% block " . $node->getAttribute("name") . " %}");
        $end = $node->ownerDocument->createTextNode("{% endblock %}\n");

        $sandbox->insertBefore($start, $sandbox->firstChild);
        $sandbox->appendChild($end);



        DOMHelper::replaceWithSet($sandbox, iterator_to_array($sandbox->childNodes));
        DOMHelper::replaceWithSet($node, iterator_to_array($node->childNodes));
    }
}
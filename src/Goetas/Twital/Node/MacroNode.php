<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\TwitalLoader;
use goetas\xml;
use Goetas\Twital\DOMHelper;

class MacroNode implements Node
{

    function visit(xml\XMLDomElement $node, TwitalLoader $twital)
    {
        if (! $node->hasAttribute("name")) {
            throw new Exception("Name atribute is required");
        }
        
        $twital->applyTemplatesToChilds($node);
        
        $pi = $node->ownerDocument->createTextNode("{% macro " . $node->getAttribute("name") . "(" . $node->getAttribute("args") . ") %}");
        $node->parentNode->insertBefore($pi, $node);
        
        $ref = $pi;
        while ($child = $node->firstChild) {
            $node->removeChild($child);
            DOMHelper::insertAfter($node->parentNode, $child, $ref);
            $ref = $child;
        }
        
        $pi = $node->ownerDocument->createTextNode("{% endmacro %}");
        DOMHelper::insertAfter($node->parentNode, $pi, $node);
        
        $node->remove();
    }
}
<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use goetas\xml;
use Goetas\Twital\TwitalLoader;
use Goetas\Twital\DOMHelper;

class ExtendsNode implements Node
{

    function visit(xml\XMLDomElement $node, TwitalLoader $twital)
    {
        if (! $node->hasAttribute("name") && ! $node->hasAttribute("name-exp")) {
            throw new Exception("name or name-exp atribute is required");
        }
        
        $twital->applyTemplatesToChilds($node);
        
        $pi = $node->ownerDocument->createTextNode("{% extends " . ($node->hasAttribute("name-exp") ? $node->getAttribute("name-exp") : ("'" . $node->getAttribute("name") . "'")) . " %}");
        DOMHelper::insertAfter($node->parentNode, $pi, $node);
        $ref = $pi;
        while ($child = $node->firstChild) {
            $node->removeChild($child);
            DOMHelper::insertAfter($node->parentNode, $child, $ref);
            $ref = $child;
        }
        $node->remove();
    }
}
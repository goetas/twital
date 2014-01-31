<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Exception;
use Goetas\Twital\Compiler;
use Goetas\Twital\DOMHelper;

class ExtendsNode implements Node
{

    function visit(\DOMElement $node, Compiler $twital)
    {
        if (! $node->hasAttribute("name") && ! $node->hasAttributeNS(Compiler::NS,"name")) {
            throw new Exception("name or name-exp atribute is required");
        }

        foreach (iterator_to_array($node->childNodes) as $child){
            if(!($child instanceof \DOMElement)){
                $child->parentNode->removeChild($child);
            }
        }

        $twital->applyTemplatesToChilds($node);

        $ext = $node->ownerDocument->createTextNode("{% extends " . ($node->hasAttributeNS(Compiler::NS, "name") ? $node->getAttributens(Compiler::NS, "name") : ("'" . $node->getAttribute("name") . "'")) . " %}");

        $set = iterator_to_array($node->childNodes);
        array_unshift($set, $ext);

        DOMHelper::replaceWithSet($node, $set);

    }
}
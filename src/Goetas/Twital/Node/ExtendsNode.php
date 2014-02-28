<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Exception;
use Goetas\Twital\CompilationContext;
use Goetas\Twital\DOMHelper;

class ExtendsNode implements Node
{

    function visit(\DOMElement $node, CompilationContext $context)
    {
        if (! $node->hasAttribute("name") && ! $node->hasAttributeNS(CompilationContext::NS,"name")) {
            throw new Exception("name or name-exp attribute is required");
        }

        foreach (iterator_to_array($node->childNodes) as $child){
            if(!($child instanceof \DOMElement)){
                $child->parentNode->removeChild($child);
            }
        }

        $context->getCompiler()->compileChilds($node);

        $ext = $context->createControlNode("extends " . ($node->hasAttributeNS(CompilationContext::NS, "name") ? $node->getAttributens(CompilationContext::NS, "name") : ("'" . $node->getAttribute("name") . "'")));

        $set = iterator_to_array($node->childNodes);
        array_unshift($set, $ext);

        DOMHelper::replaceWithSet($node, $set);

    }
}
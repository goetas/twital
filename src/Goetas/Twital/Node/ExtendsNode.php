<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Exception;
use Goetas\Twital\CompilationContext;
use Goetas\Twital\DOMHelper;
use Goetas\Twital\Twital;

class ExtendsNode implements Node
{

    public function visit(\DOMElement $node, CompilationContext $context)
    {
        if (! $node->hasAttribute("from") && ! $node->hasAttributeNS(Twital::NS,"from")) {
            throw new Exception("name or name-exp attribute is required");
        }

        // remove any non-element node
        foreach (iterator_to_array($node->childNodes) as $child){
            if(!($child instanceof \DOMElement)){
                $child->parentNode->removeChild($child);
            }
        }
        $filename = $node->hasAttributeNS(Twital::NS, "from") ? $node->getAttributens(CompilationContext::NS, "from") : ("'" . $node->getAttribute("from") . "'");
        $context->compileChilds($node);

        $ext = $context->createControlNode("extends {$filename}");

        $set = iterator_to_array($node->childNodes);
        array_unshift($set, $ext);

        DOMHelper::replaceWithSet($node, $set);

    }
}
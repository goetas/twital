<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\Compiler;
use Goetas\Twital\Helper\DOMHelper;
use Exception;

class MacroNode implements Node
{

    public function visit(\DOMElement $node, Compiler $context)
    {
        if (! $node->hasAttribute("name")) {
            throw new Exception("Name attribute is required");
        }

        $context->compileChilds($node);

        $set = iterator_to_array($node->childNodes);

        $start = $context->createControlNode("macro " . $node->getAttribute("name") . "(" . $node->getAttribute("args") . ")");
        array_unshift($set, $start);


        $set[] = $context->createControlNode("endmacro");

        DOMHelper::replaceWithSet($node, $set);
    }
}

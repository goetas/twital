<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\Compiler;
use goetas\xml;
use Goetas\Twital\DOMHelper;

class MacroNode implements Node
{

    function visit(\DOMElement $node, Compiler $twital)
    {
        if (! $node->hasAttribute("name")) {
            throw new Exception("Name attribute is required");
        }

        $twital->applyTemplatesToChilds($node);

        $set = iterator_to_array($node->childNodes);

        $start = $node->ownerDocument->createTextNode("{% macro " . $node->getAttribute("name") . "(" . $node->getAttribute("args") . ") %}");
        array_unshift($set, $start);


        $set[] = $node->ownerDocument->createTextNode("{% endmacro %}");

        DOMHelper::replaceNodeWithSet($node, $set);
    }
}
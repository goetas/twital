<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\Compiler;
use Goetas\Twital\DOMHelper;
use Exception;

class BlockCallNode implements Node
{

    function visit(\DOMElement $node, Compiler $twital)
    {
        if ($node->hasAttributeNS(Compiler::NS, "name")) {
            $name = $node->getAttributeNS(Compiler::NS, "name");
        } elseif ($node->hasAttribute("name")) {
            $name = "'" . $node->getAttributeNS("name") . "'";
        } else {
            $name = "'parent'";
        }

        $txt = $node->ownerDocument->createTextNode("{{ block($name) }}");
        $node->parentNode->replaceChild($txt, $node);
    }
}
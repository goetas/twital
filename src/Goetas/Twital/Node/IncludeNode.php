<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\Compiler;
use goetas\xml;
use Goetas\Twital\Exception;

class IncludeNode implements Node
{

    function visit(\DOMElement $node, Compiler $twital)
    {
        if (! $node->hasAttribute("name") && ! $node->hasAttribute("name-exp")) {
            throw new Exception("Name or name-exp atribute is required");
        }
        
        $code = "{% include ";
        $code .= ($node->hasAttribute("name-exp") ? $node->getAttribute("name-exp") : ("'" . $node->getAttribute("name") . "'"));
        $code .= $node->getAttribute("ignore-missing") ? " ignore missing" : "";
        $code .= $node->hasAttribute("with") ? (" with " . $node->getAttribute("with")) : "";
        $code .= $node->getAttribute("sandboxed") == "true" ? " sandboxed = true " : "";
        $code .= " %}";
        $pi = $node->ownerDocument->createTextNode($code);
        
        $node->parentNode->replaceChild($pi, $node);
    }
}
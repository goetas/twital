<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\CompilationContext;
use Goetas\Twital\Exception;

class IncludeNode implements Node
{

    function visit(\DOMElement $node, CompilationContext $context)
    {
        if (! $node->hasAttribute("name") && ! $node->hasAttribute("name-exp")) {
            throw new Exception("Name or name-exp attribute is required");
        }

        $code = "include ";
        $code .= ($node->hasAttribute("name-exp") ? $node->getAttribute("name-exp") : ("'" . $node->getAttribute("name") . "'"));
        $code .= $node->getAttribute("ignore-missing") ? " ignore missing" : "";
        $code .= $node->hasAttribute("with") ? (" with " . $node->getAttribute("with")) : "";
        $code .= $node->getAttribute("sandboxed") == "true" ? " sandboxed = true " : "";
        $code .= "";
        $pi = $context->createControlNode($code);

        $node->parentNode->replaceChild($pi, $node);
    }
}
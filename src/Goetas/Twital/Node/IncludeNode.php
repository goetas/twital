<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\Compiler;
use Goetas\Twital\Exception;

class IncludeNode implements Node
{

    public function visit(\DOMElement $node, Compiler $context)
    {

        $code = "include ";

        if ($node->hasAttribute("from-exp")) {
            $code .= $node->getAttribute("from-exp");
        } elseif ($node->hasAttribute("from")) {
            $code .= "'" . $node->getAttribute("from") . "'";
        } else {
            throw new Exception("The 'from' or 'from-exp' attribute is required");
        }


        if ($node->hasAttribute("ignore-missing") && $node->hasAttribute("ignore-missing") !== false) {
            $code .= " ignore missing";
        }
        if ($node->hasAttribute("with")) {
            $code .= " with " . $node->getAttribute("with");
        }
        if ($node->hasAttribute("only") && $node->getAttribute("only") !== "false") {
            $code .= " ignore missing";
        }
        if ($node->hasAttribute("sandboxed") && $node->getAttribute("sandboxed") !== "false") {
            $code .= " sandboxed = true";
        }

        $pi = $context->createControlNode($code);
        $node->parentNode->replaceChild($pi, $node);
    }
}
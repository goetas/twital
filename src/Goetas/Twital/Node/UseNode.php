<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\Compiler;
use Exception;
class UseNode implements Node
{
    public function visit(\DOMElement $node, Compiler $context)
    {

        $code = "use ";

        if ($node->hasAttribute("from-exp")) {
            $code .= $node->getAttribute("from-exp");
        } elseif ($node->hasAttribute("from")) {
            $code .= '"' . $node->getAttribute("from") . '"';
        } else {
            throw new Exception("The 'from' or 'from-exp' attribute is required");
        }

        if ($node->hasAttribute("with")) {
            $code .= " with " . $node->getAttribute("with");
        }

        $pi = $context->createControlNode($code);
        $node->parentNode->replaceChild($pi, $node);
    }
}
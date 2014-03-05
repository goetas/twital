<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\CompilationContext;
use Goetas\Twital\Exception;

class IncludeNode implements Node
{

    public function visit(\DOMElement $node, CompilationContext $context)
    {
        if ($node->hasAttribute("from-exp")) {
            $filename = $node->getAttribute("from-exp");
        } elseif ($node->hasAttribute("from")) {
            $filename = "'" . $node->getAttribute("from") . "'";
        } else {
            throw new Exception("The 'from' or 'from-exp' attribute is required");
        }

        // remove any non-element node
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (! ($child instanceof \DOMElement)) {
                $child->parentNode->removeChild($child);
            }
        }

        $context->compileChilds($node);

        $code = "include {$filename}";

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
<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Exception;
use Goetas\Twital\CompilationContext;
use Goetas\Twital\DOMHelper;
use Goetas\Twital\Twital;

class EmbedNode implements Node
{

    public function visit(\DOMElement $node, CompilationContext $context)
    {
        if ($node->hasAttribute("from-exp")) {
            $filename = $node->getAttribute("from-exp");
        } elseif ($node->hasAttribute("from-exp")) {
            $filename = "'" . $node->getAttribute("from") . "'";
        } else {
            throw new Exception("name or name-exp attribute is required");
        }

        // remove any non-element node
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (! ($child instanceof \DOMElement)) {
                $child->parentNode->removeChild($child);
            }
        }

        $context->compileChilds($node);

        $code = "embed {$filename}";

        if ($node->hasAttribute("ignore-missing") && $node->hasAttribute("ignore-missing") !== false) {
            $code .= " ignore missing";
        }
        if ($node->hasAttribute("with")) {
            $code .= " with " . $node->getAttribute("with");
        }
        if ($node->hasAttribute("only") && $node->getAttribute("only") !== "false") {
            $code .= " ignore missing";
        }

        $ext = $context->createControlNode($code);

        $set = iterator_to_array($node->childNodes);
        array_unshift($set, $ext);

        DOMHelper::replaceWithSet($node, $set);
    }
}
<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Exception;
use Goetas\Twital\Compiler;
use Goetas\Twital\Helper\DOMHelper;
use Goetas\Twital\Twital;

class EmbedNode implements Node
{

    public function visit(\DOMElement $node, Compiler $context)
    {
        if ($node->hasAttribute("from-exp")) {
            $filename = $node->getAttribute("from-exp");
        } elseif ($node->hasAttribute("from")) {
            $filename = '"' . $node->getAttribute("from") . '"';
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

        $n = $node->ownerDocument->createTextNode("\n");
        array_unshift($set, $n);
        array_unshift($set, $ext);

        $set[] = $context->createControlNode("endembed");

        DOMHelper::replaceWithSet($node, $set);
    }
}
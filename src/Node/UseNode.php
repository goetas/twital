<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Compiler;
use Goetas\Twital\Exception;
use Goetas\Twital\Node;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class UseNode implements Node
{
    public function visit(\DOMElement $node, Compiler $context)
    {
        $code = "use ";

        if ($node->hasAttribute("from")) {
            $code .= '"' . $node->getAttribute("from") . '"';
        } else {
            throw new Exception("The 'from' attribute is required");
        }

        if ($node->hasAttribute("with")) {
            $code .= " with " . $node->getAttribute("with");
        }

        $pi = $context->createControlNode($code);
        $node->parentNode->replaceChild($pi, $node);
    }
}

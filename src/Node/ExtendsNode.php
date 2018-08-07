<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Compiler;
use Goetas\Twital\Exception;
use Goetas\Twital\Helper\DOMHelper;
use Goetas\Twital\Node;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class ExtendsNode implements Node
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

        $context->compileChilds($node);

        $ext = $context->createControlNode("extends {$filename}");

        $set = iterator_to_array($node->childNodes);
        if (count($set)) {
            $n = $node->ownerDocument->createTextNode("\n");
            array_unshift($set, $n);
        }
        array_unshift($set, $ext);

        DOMHelper::replaceWithSet($node, $set);
    }
}

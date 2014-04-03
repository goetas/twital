<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\Compiler;
use Goetas\Twital\Helper\DOMHelper;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class OmitNode implements Node
{
    public function visit(\DOMElement $node, Compiler $context)
    {
        DOMHelper::replaceWithSet($node, iterator_to_array($node->childNodes));
    }
}
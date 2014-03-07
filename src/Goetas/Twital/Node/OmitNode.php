<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\Compiler;
use Goetas\Twital\Helper\DOMHelper;
class OmitNode implements Node
{
    public function visit(\DOMElement $node, Compiler $context)
    {
        DOMHelper::replaceWithSet($node, iterator_to_array($node->childNodes));
    }
}
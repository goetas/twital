<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\CompilationContext;

use Goetas\Twital\DOMHelper;
use Exception;
class OmitNode implements Node
{
    public function visit(\DOMElement $node, CompilationContext $context)
    {
        DOMHelper::replaceWithSet($node, iterator_to_array($node->childNodes));
    }
}
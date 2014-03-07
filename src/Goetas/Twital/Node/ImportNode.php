<?php
namespace Goetas\Twital\Node;

use Goetas\Twital\Node;
use Goetas\Twital\Compiler;
use Goetas\Twital\Exception;

class ImportNode implements Node
{

    public function visit(\DOMElement $node, Compiler $context)
    {
        if (! $node->hasAttribute("name")) {
            throw new Exception("Name attribute is required");
        }
        if (! $node->hasAttribute("as") && ! $node->hasAttribute("aliases")) {
            throw new Exception("As or Alias attribute is required");
        }

        if ($node->hasAttribute("as")) {
            $pi = $context->createControlNode("import " . ($node->getAttribute("name-exp") ? $node->getAttribute("name-exp") : ("'" . $node->getAttribute("name") . "'")) . " as " . $node->getAttribute("as") );
        } else {
            $pi = $context->createControlNode("from " . ($node->hasAttribute("name-exp") ? $node->getAttribute("name-exp") : ("'" . $node->getAttribute("name") . "'")) . " import as " . $node->getAttribute("aliases") );
        }

        $node->parentNode->replaceChild($pi, $node);
    }
}
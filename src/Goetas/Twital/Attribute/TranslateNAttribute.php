<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\CompilationContext;
use DOMAttr;
use Goetas\Twital\TwitalEnviroment;

class TranslateNAttribute implements Attribute
{

    public function visit(DOMAttr $att, CompilationContext $context)
    {

        $node = $att->ownerElement;
        $varNode = $node->getAttributeNodeNS(TwitalEnviroment::NS, 'trans');
        $with = '{\'%count%\':'.html_entity_decode($att->value).'}';
        if ($varNode && $varNode->value) {
            $with = "($with|merge(".html_entity_decode($varNode->value).')';
            $node->removeAttributeNode($varNode);
        }

        $start = $context->createControlNode("transchoiche ".html_entity_decode($att->value)." with $with");
        $end = $context->createControlNode("endtranschoiche");

        $node->insertBefore($start, $node->firstChild);

        $node->appendChild($end);

        $node->removeAttributeNode($att);

    }
}

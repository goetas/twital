<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use DOMAttr;

class TranslateNAttribute implements Attribute
{

    public function visit(DOMAttr $att, Compiler $twital)
    {

        $node = $att->ownerElement;
        $varNode = $node->getAttributeNodeNS(Compiler::NS, 'trans');
        $with = '{\'%count%\':'.html_entity_decode($att->value).'}';
        if ($varNode && $varNode->value) {
            $with = "($with|merge(".html_entity_decode($varNode->value).')';
            $node->removeAttributeNode($varNode);
        }

        $start = $node->ownerDocument->createTextNode("{% transchoiche ".html_entity_decode($att->value)." with $with %}");
        $end = $node->ownerDocument->createTextNode("{% endtranschoiche %}");

        $node->insertBefore($start, $node->firstChild);

        $node->appendChild($end);

        $node->removeAttributeNode($att);

    }
}

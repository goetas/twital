<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use DOMAttr;

class TranslateAttribute implements Attribute
{

    public function visit(DOMAttr $att, Compiler $twital)
    {
        $node = $att->ownerElement;

        $with = '';
        if ($att->value) {
            $with = "with ".html_entity_decode($att->value);
        }
        $start = $node->ownerDocument->createTextNode("{% trans $with %}");
        $end = $node->ownerDocument->createTextNode("{% endtrans %}");

        $node->insertBefore($start, $node->firstChild);

        $node->appendChild($end);

        $node->removeAttributeNode($att);
    }
}

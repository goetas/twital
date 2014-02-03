<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use DOMAttr;
use Goetas\Twital\ParserHelper;

class SetAttribute implements Attribute
{

    public function visit(DOMAttr $att, Compiler $twital)
    {
        $node = $att->ownerElement;

        $sets = array();
        foreach (ParserHelper::staticSplitExpression($att->value,",") as $set) {
            if (trim($set)) {
                $sets[] = "{% set " . html_entity_decode($set) . " %}";
            }
        }

        $pi = $node->ownerDocument->createTextNode(implode("", $sets));

        $node->parentNode->insertBefore($pi, $node);

        $node->removeAttributeNode($att);
    }
}

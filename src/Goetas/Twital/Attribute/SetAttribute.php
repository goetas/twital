<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use DOMAttr;

class SetAttribute implements Attribute
{

    public function visit(DOMAttr $att, Compiler $twital)
    {
        $node = $att->ownerElement;

        $sets = array();
        foreach (explode(";", $att->value) as $set) {
            if (trim($set)) {
                $sets[] = "{% set " . html_entity_decode($set) . " %}";
            }
        }

        $pi = $node->ownerDocument->createTextNode(implode("", $sets));

        if (0 && $node->namespaceURI==Compiler::NS) {
            if ($node->firstChild) {
                $node->parentNode->insertBefore($pi, $node->firstChild);
            } else {
                $node->parentNode->appendChild($pi);
            }
        } else {
            $node->parentNode->insertBefore($pi, $node);
        }

        $node->removeAttributeNode($att);
    }
}

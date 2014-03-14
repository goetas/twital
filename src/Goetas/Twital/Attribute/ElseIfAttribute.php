<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;
use DOMAttr;
use Goetas\Twital\Twital;
use Goetas\Twital\Exception;
class ElseIfAttribute implements AttributeBase
{
    public function visit(DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;

        if(!$prev = IfAttribute::findPrevElement($node)){
            throw new Exception("The attribute 'elseif' must be the very next sibiling of an 'if' of 'elseif' attribute");
        }

        $pi = $context->createControlNode("elseif " . html_entity_decode($att->value));
        $node->parentNode->insertBefore($pi, $node);

        if (!($nextElement = IfAttribute::findNextElement($node)) || (!$nextElement->hasAttributeNS(Twital::NS, 'elseif') && !$nextElement->hasAttributeNS(Twital::NS, 'else'))) {
            $pi = $context->createControlNode("endif");
            $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after
        }else{
            IfAttribute::removeWhitespace($node);
        }

        $node->removeAttributeNode($att);
    }
}

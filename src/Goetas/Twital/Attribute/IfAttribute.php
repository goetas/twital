<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;
use DOMAttr;
use Goetas\Twital\Twital;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class IfAttribute implements AttributeBase
{
    public function visit(DOMAttr $att, Compiler $context)
    {
        $node = $att->ownerElement;
        if($att->value!=="1" && $att->value!=="true"){

            $pi = $context->createControlNode("if " . html_entity_decode($att->value));
            $node->parentNode->insertBefore($pi, $node);

            if (!($nextElement = self::findNextElement($node)) || (!$nextElement->hasAttributeNS(Twital::NS, 'elseif') && !$nextElement->hasAttributeNS(Twital::NS, 'else'))) {
                $pi = $context->createControlNode("endif");
                $node->parentNode->insertBefore($pi, $node->nextSibling); // insert after
            }else{
                self::removeWhitespace($node);
            }
        }
        $node->removeAttributeNode($att);
    }
    public static function removeWhitespace(\DOMElement $element)
    {
        while ($el = $element->nextSibling){
            if ($el instanceof \DOMText) {
                $element->parentNode->removeChild($el);
            } else {
                break;
            }
        }
    }
    public static function findNextElement(\DOMElement $element)
    {
        $next = $element;
    	while ($next = $next->nextSibling){
    	    if($next instanceof \DOMText && trim($next->textContent)){
    	        return null;
    	    }
    	    if ($next instanceof \DOMElement){
    	    	return $next;
    	    }
    	}
    	return null;
    }
    public static function findPrevElement(\DOMElement $element)
    {
        $prev = $element;
        while ($prev = $prev->previousSibling){
            if($prev instanceof \DOMText && trim($prev->textContent)){
                return null;
            }
            if ($prev instanceof \DOMElement){
                return $prev;
            }
        }
        return null;
    }
}

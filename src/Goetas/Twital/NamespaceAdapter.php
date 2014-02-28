<?php
namespace Goetas\Twital;

use DOMNode;
use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\HTML5Extension;
use Goetas\Twital\Extension\I18nExtension;

class NamespaceAdapter
{
    public static function checkNamespaces(\DOMElement $element, array $namespaces = array()){

        if ($element->namespaceURI===null && preg_match('/^([a-z0-9\-]+):(.+)$/i', $element->nodeName, $mch) && isset($namespaces[$mch[1]])){

            $oldElement = $element;
            $element = $element->ownerDocument->createElementNS($namespaces[$mch[1]], $element->nodeName);

            // copy attrs
            foreach (iterator_to_array($oldElement->attributes) as $attr) {
                $oldElement->removeAttributeNode($attr);
                if ($attr->namespaceURI) {
                    $element->setAttributeNodeNS($attr);
                } else {
                    $element->setAttributeNode($attr);
                }
            }
            // copy childs
            while ($child = $oldElement->firstChild) {
                $oldElement->removeChild($child);
                $element->appendChild($child);
            }
            $oldElement->parentNode->replaceChild($element, $oldElement);
        }
        // fix attrs
        foreach (iterator_to_array($element->attributes) as $attr) {
            if ($attr->namespaceURI===null && preg_match('/^([a-z0-9\-]+):/i', $attr->name, $mch) && isset($namespaces[$mch[1]])){

                $element->removeAttributeNode($attr);
                $element->setAttributeNS($namespaces[$mch[1]], $attr->name, $attr->value);
            }
        }
        foreach (iterator_to_array($element->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                self::checkNamespaces($child, $namespaces);
            }
        }
    }
}

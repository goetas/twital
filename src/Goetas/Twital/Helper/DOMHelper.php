<?php
namespace Goetas\Twital\Helper;

class DOMHelper
{
    /**
     * @deprecated
     * @param \DOMNode $cur
     * @param \DOMNode $new
     */
    public static function insertAfter(\DOMNode $cur,\DOMNode $new)
    {
        if ($cur->nextSibling) {
            $cur->insertBefore($new, $cur->nextSibling);
        } else {
            $cur->appendChild($new);
        }
    }
    public static function removeChilds(\DOMNode $ref)
    {
        while ($ref->hasChildNodes()) {
            $ref->removeChild($ref->firstChild);
        }
    }
    public static function insertAfterSet(\DOMNode $node, array $newNodes)
    {
        $ref = $node;
        foreach ($newNodes as $newNode) {
            if ($newNode->parentNode) {
                $newNode->parentNode->removeChild($newNode);
            }
            $ref->parentNode->insertBefore($newNode, $ref->nextSibling);
            $ref = $newNode;
        }
    }

    public static function replaceWithSet(\DOMNode $node, array $newNodes)
    {
        self::insertAfterSet($node, $newNodes);
        $node->parentNode->removeChild($node);
    }
    public static function remove(\DOMNode $ref)
    {
        return $ref->parentNode->removeChild($ref);
    }
    public static function checkNamespaces(\DOMElement $element, array $namespaces = array())
    {
        if ($element->namespaceURI === null && preg_match('/^([a-z0-9\-]+):(.+)$/i', $element->nodeName, $mch) && isset($namespaces[$mch[1]])) {

            $oldElement = $element;
            $element = self::copyElementInNs($oldElement, $namespaces[$mch[1]]);
        }
        // fix attrs
        foreach (iterator_to_array($element->attributes) as $attr) {
            if ($attr->namespaceURI === null && preg_match('/^([a-z0-9\-]+):/i', $attr->name, $mch) && isset($namespaces[$mch[1]])) {

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

    public static function copyElementInNs($oldElement, $newNamespace)
    {
        $element = $oldElement->ownerDocument->createElementNS($newNamespace, $oldElement->nodeName);

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
        return $element;
    }
}

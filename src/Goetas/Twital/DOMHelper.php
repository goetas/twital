<?php
namespace Goetas\Twital;

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
}

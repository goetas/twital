<?php
namespace Goetas\Twital;

class DOMHelper
{

    public static function insertAfter(\DOMNode $cur,\DOMNode $new,\DOMNode $ref)
    {
        if ($ref->nextSibling) {
            $cur->insertBefore($new, $ref->nextSibling);
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

    public static function remove(\DOMNode $ref)
    {
        return $ref->parentNode->removeChild($this);
    }
}
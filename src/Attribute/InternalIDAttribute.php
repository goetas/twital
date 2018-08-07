<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute as AttributeBase;
use Goetas\Twital\Compiler;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class InternalIDAttribute implements AttributeBase
{
    public function visit(\DOMAttr $att, Compiler $context)
    {
        $att->ownerElement->removeAttributeNode($att);
    }
}

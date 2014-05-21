<?php
namespace Goetas\Twital;

use DOMAttr;

/**
 * Represents the handler for custom attributes.
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
interface Attribute
{

    /**
     * Stop processing current node children.
     *
     * @var int
     */
    const STOP_NODE = 1;

    /**
     * Stop processing current node attributes.
     *
     * @var int
     */
    const STOP_ATTRIBUTE = 2;

    /**
     *
     * @param \DOMAttr $att
     * @param Compiler $context
     * @return int|null Bitmask of {Attribute::STOP_NODE} and {Attribute::STOP_ATTRIBUTE}
     */
    public function visit(\DOMAttr $att, Compiler $context);
}

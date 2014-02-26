<?php
namespace Goetas\Twital;

use DOMAttr;

interface Attribute
{

    /**
     * Ferma l'elaborazione del contenuto del nodo
     *
     * @var int
     */
    const STOP_NODE = 1;

    /**
     * Ferma l'elaborazione degli attributi del nodo
     *
     * @var int
     */
    const STOP_ATTRIBUTE = 2;

    public function visit(\DOMAttr $att, CompilationContext $context);
}

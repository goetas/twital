<?php
namespace Goetas\Twital;

/**
 * Reppresents the handler for custom nodes.
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
interface Node
{
    /**
     * Visit a node.
     *
     * @param \DOMElement $node
     * @param Compiler $context
     * @return void
     */
    public function visit(\DOMElement $node, Compiler $context);
}

<?php
namespace Goetas\Twital;

Interface Node
{

    public function visit(\DOMElement $node, CompilationContext $context);
}

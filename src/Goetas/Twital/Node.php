<?php
namespace Goetas\Twital;

use goetas\xml;

Interface Node
{

    function visit(\DOMElement $node, Compiler $twital);
}

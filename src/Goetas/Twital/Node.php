<?php
namespace Goetas\Twital;

use goetas\xml;

Interface Node
{

    function visit(xml\XMLDomElement $node, TwitalLoader $twital);
}

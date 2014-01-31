<?php
namespace Goetas\Twital\Attribute;

use Goetas\Twital\Attribute;
use DOMAttr;

class AttrAppendAttribute extends AttrAttribute
{
    protected function getSetExpression($varName, $attName, $expr)
    {
        return "{% set {$varName}.{$attName} = {$varName}.{$attName}|default([])|merge($expr); %}\n";
    }
}
<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;

class I18nExtension extends AbstractExtension
{
    public function getAttributes()
    {
        $attributes = array();
        $attributes[Compiler::NS]['trans'] = new Attribute\TranslateAttribute();
        $attributes[Compiler::NS]['trans-n'] = new Attribute\TranslateNAttribute();
        return $attributes;
    }
}

<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use Goetas\Twital\TwitalEnviroment;

class I18nExtension extends AbstractExtension
{
    public function getAttributes()
    {
        $attributes = array();
        $attributes[TwitalEnviroment::NS]['trans'] = new Attribute\TranslateAttribute();
        $attributes[TwitalEnviroment::NS]['trans-n'] = new Attribute\TranslateNAttribute();
        return $attributes;
    }
}

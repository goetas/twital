<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Attribute;
use Goetas\Twital\Compiler;
use Goetas\Twital\TwitalLoader;

class I18nExtension extends AbstractExtension
{
    public function getAttributes()
    {
        $attributes = array();
        $attributes[TwitalLoader::NS]['trans'] = new Attribute\TranslateAttribute();
        $attributes[TwitalLoader::NS]['trans-n'] = new Attribute\TranslateNAttribute();
        return $attributes;
    }
}

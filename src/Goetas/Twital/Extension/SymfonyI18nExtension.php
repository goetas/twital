<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Attribute;
use Goetas\Twital\Twital;

class I18nExtension extends AbstractExtension
{
    public function getAttributes()
    {
        $attributes = array();
        $attributes[Twital::NS]['trans'] = new Attribute\TranslateAttribute();
        $attributes[Twital::NS]['trans-n'] = new Attribute\TranslateNAttribute();
        return $attributes;
    }
}

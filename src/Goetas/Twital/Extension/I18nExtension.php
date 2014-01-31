<?php
namespace Goetas\Twital\Extension;

use DOMDocument;
use Goetas\Twital\DOMLoader\XMLDOMLoader;
use Goetas\Twital\DOMLoader\XHTMLDOMLoader;
use Goetas\Twital\Extension;
use Goetas\Twital\Attribute;
use Goetas\Twital\Node;
use Goetas\Twital\Compiler;

class I18nExtension implements Extension
{


    public function getDOMLoaders()
    {
        return array();
    }

    public function getAttributes()
    {
        $attributes = array();
        $attributes[Compiler::NS]['trans'] = new Attribute\TranslateAttribute();
        $attributes[Compiler::NS]['trans-n'] = new Attribute\TranslateNAttribute();
        return $attributes;
    }

    public function getNodes()
    {
        return array();
    }

    public function getPostFilters()
    {
        return array();
    }

    public function getPreFilters()
    {
        return array();
    }
}

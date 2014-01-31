<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Extension;
use Goetas\Twital\DOMLoader\HTML5DOMLoader;

class XHTMLExtension implements Extension
{

    public function getDOMLoaders()
    {
        return array(
            'xhtml' => new XHTMLDOMLoader()
        );
    }

    public function getAttributes()
    {
        return array();
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

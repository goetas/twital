<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Extension;
use Goetas\Twital\DOMLoader\HTML5DOMLoader;
use Goetas\Twital\Dumper\HTML5Dumper;
use Goetas\Twital\Loader\HTML5Loader;

class HTML5Extension implements Extension
{

    public function getDumpers()
    {
        return array(
            'html5' => new HTML5Dumper()
        );
    }
    public function getLoaders()
    {
        return array(
            'html5' => new HTML5Loader()
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

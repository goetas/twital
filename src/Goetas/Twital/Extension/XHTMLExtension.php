<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Extension;
use Goetas\Twital\DOMLoader\HTML5DOMLoader;
use Goetas\Twital\Dumper\XHTMLDumper;

class XHTMLExtension implements Extension
{

    public function getLoaders()
    {
        return array();
    }
    public function getDumpers()
    {
    	return array(
    		'xhtml'=>new XHTMLDumper()
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

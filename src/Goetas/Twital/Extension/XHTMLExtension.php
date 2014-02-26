<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Dumper\XHTMLDumper;

class XHTMLExtension extends AbstractExtension
{
    public function getDumpers()
    {
    	return array(
    		'xhtml'=>new XHTMLDumper()
    	);
    }
}

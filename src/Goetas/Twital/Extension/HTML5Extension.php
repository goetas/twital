<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Dumper\HTML5Dumper;
use Goetas\Twital\Loader\HTML5Loader;
use Goetas\Twital\Dumper\HTML5Adapter;

class HTML5Extension extends AbstractExtension
{

    public function getSourceAdapters()
    {
        return array(
            'html5' => new HTML5Adapter()
        );
    }
}

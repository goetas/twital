<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\SourceAdapter\HTML5Adapter;

class HTML5Extension extends AbstractExtension
{

    public function getSourceAdapters()
    {
        return array(
            'html5' => new HTML5Adapter()
        );
    }
}

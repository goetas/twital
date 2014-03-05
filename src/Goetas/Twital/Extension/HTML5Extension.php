<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\SourceAdapter\HTML5Adapter;

class HTML5Extension extends AbstractExtension
{

    public function getRootSourceAdapter()
    {
        return new HTML5Adapter();
    }
}

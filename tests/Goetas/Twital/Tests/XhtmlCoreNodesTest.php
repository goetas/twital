<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\SourceAdapter\XHTMLAdapter;

class XhtmlCoreNodesTest extends CoreNodesTest
{
    protected function getSourceAdapter()
    {
        return new XHTMLAdapter();
    }
}

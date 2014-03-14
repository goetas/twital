<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\SourceAdapter\XHTMLAdapter;


class XhtmlCoreNodesTest extends CoreNodesTest
{
    protected function getSourceAdapter(){
    	return new XHTMLAdapter();
    }
}
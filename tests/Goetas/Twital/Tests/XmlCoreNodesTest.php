<?php
namespace Goetas\Twital\Tests;

use Goetas\Twital\Twital;
use Goetas\Twital\SourceAdapter\XMLAdapter;


class XmlCoreNodesTest extends CoreNodesTest
{
    protected function getSourceAdapter(){
    	return new XMLAdapter();
    }
}
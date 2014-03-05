<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Extension;

abstract class AbstractExtension implements Extension
{

    public function getAttributes()
    {
        return array();
    }

    public function getNodes()
    {
        return array();
    }

    public function getSourceAdapters()
    {
        return array();
    }

}

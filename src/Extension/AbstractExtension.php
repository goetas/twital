<?php
namespace Goetas\Twital\Extension;

use Goetas\Twital\Extension;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
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

    public function getSubscribers()
    {
        return array();
    }
}

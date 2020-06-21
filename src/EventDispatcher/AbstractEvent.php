<?php
namespace Goetas\Twital\EventDispatcher;

use Symfony\Component\EventDispatcher\Event as ComponentEvent;
use Symfony\Contracts\EventDispatcher\Event as ContractsEvent;

if (class_exists(ContractsEvent::class)) {
    /**
     * @internal
     */
    abstract class AbstractEvent extends ContractsEvent
    {
    }
} else {
    /**
     * @internal
     */
    abstract class AbstractEvent extends ComponentEvent
    {
    }
}

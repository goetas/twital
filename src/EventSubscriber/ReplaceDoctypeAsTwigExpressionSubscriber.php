<?php

namespace Goetas\Twital\EventSubscriber;

use Goetas\Twital\EventDispatcher\CompilerEvents;
use Goetas\Twital\EventDispatcher\SourceEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class ReplaceDoctypeAsTwigExpressionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            CompilerEvents::PRE_LOAD => array('replaceDoctype', 130),
        );
    }

    /**
     *
     * @param SourceEvent $event
     */
    public function replaceDoctype(SourceEvent $event)
    {
        $source = $event->getTemplate();

        $source = preg_replace_callback('/^<!doctype.*?>/im', function ($mch) {
            return '{{ \'' . addslashes($mch[0]) . '\' }}';
        }, $source);

        $event->setTemplate($source);
    }
}

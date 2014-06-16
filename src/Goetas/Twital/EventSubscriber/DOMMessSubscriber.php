<?php
namespace Goetas\Twital\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Goetas\Twital\EventDispatcher\SourceEvent;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class DOMMessSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'compiler.post_dump' => array(
                array(
                    'removeCdata'
                ),
                array(
                    'fixAttributes'
                )
            )
        );
    }

    public function removeCdata(SourceEvent $event)
    {
        $event->setTemplate(str_replace(array(
            "<![CDATA[__[__",
            "__]__]]>"
        ), "", $event->getTemplate()));
    }

    public function fixAttributes(SourceEvent $event)
    {
        $event->setTemplate(preg_replace_callback('/ __attr__="(__a[0-9a-f]+)"/', function ($mch) {
            return '{% for ____ak,____av in ' . $mch[1] . ' if ____av|length>0 %} {{____ak | raw}}="{{ ____av|join(\'\') }}"{% endfor %}';
        }, $event->getTemplate()));
    }
}

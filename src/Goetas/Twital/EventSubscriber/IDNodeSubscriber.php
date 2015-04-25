<?php
namespace Goetas\Twital\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Goetas\Twital\EventDispatcher\TemplateEvent;
use Goetas\Twital\Twital;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class IDNodeSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'compiler.post_load' => array(
                array(
                    'addAttribute'
                )
            ),
            'compiler.pre_dump' => array(
                array(
                    'removeAttribute'
                )
            )
        );
    }

    public function addAttribute(TemplateEvent $event)
    {
        $doc = $event->getTemplate()->getDocument();
        $xp = new \DOMXPath($doc);
        $nodes = $xp->query("//*[@*[namespace-uri()='".Twital::NS."']]");
        $cnt = 0;
        foreach ($nodes as $node) {
            $node->setAttributeNS(Twital::NS, '__internal-id__', ++$cnt);
        }
    }

    public function removeAttribute(TemplateEvent $event)
    {
        $doc = $event->getTemplate()->getDocument();
        $xp = new \DOMXPath($doc);
        $xp->registerNamespace('twital', Twital::NS);
        $attributes = $xp->query("//@twital:__internal-id__");
        foreach ($attributes as $attribute) {
            $attribute->ownerElement->removeAttributeNode($attribute);
        }
    }
}

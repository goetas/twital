<?php
namespace Goetas\Twital\EventSubscriber;

use Goetas\Twital\EventDispatcher\CompilerEvents;
use Goetas\Twital\EventDispatcher\TemplateEvent;
use Goetas\Twital\Twital;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
            CompilerEvents::POST_LOAD => array(
                array(
                    'addAttribute'
                )
            ),
            CompilerEvents::PRE_DUMP => array(
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
        /**
         * @var \DOMElement[] $nodes
         */
        $nodes = $xp->query("//*[@*[namespace-uri()='" . Twital::NS . "']]");
        foreach ($nodes as $node) {
            $node->setAttributeNS(Twital::NS, '__internal-id__', microtime(1) . mt_rand());
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

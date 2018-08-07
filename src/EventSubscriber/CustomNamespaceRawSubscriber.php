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
class CustomNamespaceRawSubscriber implements EventSubscriberInterface
{
    protected $customNamespaces = array();

    public function __construct(array $customNamespaces)
    {
        $this->customNamespaces = $customNamespaces;
    }

    public static function getSubscribedEvents()
    {
        return array(
            CompilerEvents::PRE_LOAD => 'addCustomNamespace',
            CompilerEvents::POST_DUMP => 'removeCustomNamespaces',
        );
    }

    public function addCustomNamespace(SourceEvent $event)
    {
        $xml = $event->getTemplate();
        $mch = null;
        if (preg_match('~<(([a-z0-9\-_]+):)?([a-z0-9\-_]+)~i', $xml, $mch, PREG_OFFSET_CAPTURE)) {
            $addPos = $mch[0][1] + strlen($mch[0][0]);
            foreach ($this->customNamespaces as $prefix => $ns) {
                if (!preg_match('/\sxmlns:([a-z0-9\-]+)="' . preg_quote($ns, '/') . '"/', $xml) && !preg_match('/\sxmlns:([a-z0-9\-]+)=".*?"/', $xml)) {
                    $xml = substr_replace($xml, ' xmlns:' . $prefix . '="' . $ns . '"', $addPos, 0);
                }
            }

            $event->setTemplate($xml);
        }
    }

    public function removeCustomNamespaces(SourceEvent $event)
    {
        $template = $event->getTemplate();
        foreach ($this->customNamespaces as $prefix => $ns) {
            $template = preg_replace('#<(.*) xmlns:' . $prefix . '="' . $ns . '"(.*)>#mi', "<\\1\\2>", $template);
        }
        $event->setTemplate($template);
    }
}

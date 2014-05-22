<?php
namespace Goetas\Twital\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Goetas\Twital\EventDispatcher\SourceEvent;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class CustomNamespaceRawSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'compiler.pre_load' => 'addCustomNamespace'
        );
    }

    /**
     *
     * @var array
     */
    protected $customNamespaces = array();

    public function __construct(array $customNamespaces)
    {
        $this->customNamespaces = $customNamespaces;
    }

    public function addCustomNamespace(SourceEvent $event)
    {
        $xml = $event->getTemplate();
        $mch = null;
        if (preg_match('~<(([a-z0-9\-_]+):)?([a-z0-9\-_]+)~i', $xml, $mch, PREG_OFFSET_CAPTURE)) {
            $addPos = $mch[0][1] + strlen($mch[0][0]);
            foreach ($this->customNamespaces as $prefix => $ns) {
                if (! preg_match('/\sxmlns:([a-z0-9\-]+)="' . preg_quote($ns, '/') . '"/', $xml) && ! preg_match('/\sxmlns:([a-z0-9\-]+)=".*?"/', $xml)) {
                    $xml = substr_replace($xml, ' xmlns:' . $prefix . '="' . $ns . '"', $addPos, 0);
                }
            }

            $event->setTemplate($xml);
        }
    }
}

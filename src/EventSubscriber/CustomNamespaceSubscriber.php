<?php
namespace Goetas\Twital\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Goetas\Twital\EventDispatcher\TemplateEvent;
use Goetas\Twital\Helper\DOMHelper;
use Goetas\Twital\EventDispatcher\SourceEvent;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class CustomNamespaceSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'compiler.post_load' => 'addCustomNamespace',
            'compiler.post_dump' => 'removeCustomNamespaces',
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

    public function addCustomNamespace(TemplateEvent $event)
    {
        foreach (iterator_to_array($event->getTemplate()->getDocument()->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                DOMHelper::checkNamespaces($child, $this->customNamespaces);
            }
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

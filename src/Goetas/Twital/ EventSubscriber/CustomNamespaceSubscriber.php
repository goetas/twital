<?php
namespace Goetas\Twital\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Goetas\Twital\EventDispatcher\TemplateEvent;
use Goetas\Twital\Helper\DOMHelper;


class CustomNamespaceSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(){
    	return array(
    		'post_load'=>'addCustomNamespace'
    	);
    }
    /**
     *
     * @var array
     */
    protected $customNamespaces=array();

    public function __construct(array $customNamespaces)
    {
        $this->customNamespaces = $customNamespaces;
    }


    protected function addCustomNamespace(TemplateEvent $event)
    {
        foreach (iterator_to_array($event->getTemplate()->getDocument()->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                DOMHelper::checkNamespaces($child, $this->customNamespaces);
            }
        }
    }
}
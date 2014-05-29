<?php
namespace Goetas\Twital\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Goetas\Twital\EventDispatcher\TemplateEvent;
use Goetas\Twital\Helper\DOMHelper;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class ContextAwareEscapingSubscriber implements EventSubscriberInterface
{

    const REGEX_STRING = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';

    public static function getSubscribedEvents()
    {
        return array(
            'compiler.pre_dump' => 'addEscpaing'
        );
    }

    public function addEscpaing(TemplateEvent $event)
    {
        $regex = '{' . preg_quote('{{') . '((' . self::REGEX_STRING . '|[^"\']*)+)' . preg_quote('}}') . '}siuU';

        $doc = $event->getTemplate()->getDocument();

        $xp = new \DOMXPath($doc);

        // script escaping
        $res = $xp->query("//script/text()[contains(., '{{') and contains(., '}}')]"); // take care about namespaces
        foreach ($res as $node) {
            $node->data = preg_replace($regex, "{{\\1 | escape('js') }}", $node->data);
        }

        // url escaping
        $res = $xp->query("//a/@href[contains(., '{{') and contains(., '}}')]"); // take care about namespaces
        foreach ($res as $node) {
            $node->value = preg_replace($regex, "{{\\1 | escape('url') }}", $node->value);
        }
    }
}

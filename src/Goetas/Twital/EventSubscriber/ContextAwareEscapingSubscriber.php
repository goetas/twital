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
        $placeholder = array('[_TWITAL_[', ']_TWITAL_]');

        $doc = $event->getTemplate()->getDocument();

        $xp = new \DOMXPath($doc);

        // js escaping
        $res = $xp->query("//style[not(@type) or @type = 'text/css']/text()[contains(., '{{') and contains(., '}}')]"); // take care about namespaces
        foreach ($res as $node) {
            $node->data = preg_replace($regex, "{{\\1 | escape('css') }}", $node->data);
        }

        // css escaping
        $res = $xp->query("//script[not(@type) or @type = 'text/javascript']/text()[contains(., '{{') and contains(., '}}')]"); // take care about namespaces
        foreach ($res as $node) {
            $node->data = preg_replace($regex, "{{\\1 | escape('js') }}", $node->data);
        }

        // url escaping
        $res = $xp->query("//a/@href[contains(., '{{') and contains(., '}}')]|//area/@href[contains(., '{{') and contains(., '}}')]|//link/@href[contains(., '{{') and contains(., '}}')]|//link/@href[contains(., '{{') and contains(., '}}')]|//img/@src[contains(., '{{') and contains(., '}}')]|//script/@src[contains(., '{{') and contains(., '}}')]"); // take care about namespaces
        foreach ($res as $node) {

            $isFullValue = preg_match('{^' . preg_quote('{{') . '((' . self::REGEX_STRING . '|[^"\']*)+)' . preg_quote('}}') . '$}siuU', str_replace($placeholder, '', $node->value));

            $node->value = preg_replace($regex, $isFullValue?"{{\\1 | escape('html_attr') }}":"{{\\1 | escape('url') }}", $node->value);
        }
    }
}

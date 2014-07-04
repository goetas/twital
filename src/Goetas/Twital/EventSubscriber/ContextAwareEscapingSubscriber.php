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
        $doc = $event->getTemplate()->getDocument();

        $xp = new \DOMXPath($doc);
        $xp->registerNamespace("xh", "http://www.w3.org/1999/xhtml");

        $this->esapeScript($doc, $xp);
        $this->esapeStyle($doc, $xp);
        $this->esapeUrls($doc, $xp);
    }

    private function esapeUrls(\DOMDocument $doc, \DOMXPath $xp)
    {
        $regex = '{' . preg_quote('{{') . '((' . self::REGEX_STRING . '|[^"\']*)+)' . preg_quote('}}') . '}siuU';
        $placeholder = array(
            '[_TWITAL_[',
            ']_TWITAL_]'
        );
        // special attr escaping
        $res = $xp->query("(//xh:*/@href|//xh:*/@src)[contains(., '{{') and contains(., '}}')]", $doc, false);
        foreach ($res as $node) {

            // href="{{ foo }}://{{ bar }}" or similar, are skipped
            if (preg_match('{^' . preg_quote('{{') . '((' . self::REGEX_STRING . '|[^"\']*)+)' . preg_quote('}}') . '$}siuU', str_replace($placeholder, '', $node->value))) {
                continue;
            }

            if (substr($node->value, 0, 11) == "javascript:" && $node->name == "href") {
                $newValue = preg_replace($regex, "{{ (\\1)  | escape('js') }}", $node->value);
            } else {
                $newValue = preg_replace($regex, "{{ (\\1)  | escape('url') }}", $node->value);
            }

            $node->value = htmlspecialchars($newValue, ENT_COMPAT, 'UTF-8');
        }
    }

    private function esapeStyle(\DOMDocument $doc, \DOMXPath $xp)
    {
        $res = $xp->query("//xh:style[not(@type) or @type = 'text/css'][contains(., '{{') and contains(., '}}')]", $doc, false);

        foreach ($res as $node) {
            $node->insertBefore($doc->createTextnode('{% autoescape \'css\' %}'), $node->firstChild);
            $node->appendChild($doc->createTextnode('{% endautoescape %}'));
        }
    }

    private function esapeScript(\DOMDocument $doc, \DOMXPath $xp)
    {
        $res = $xp->query("//xh:script[not(@type) or @type = 'text/javascript'][contains(., '{{') and contains(., '}}')]", $doc, false);
        foreach ($res as $node) {
            $node->insertBefore($doc->createTextnode('{% autoescape \'js\' %}'), $node->firstChild);
            $node->appendChild($doc->createTextnode('{% endautoescape %}'));
        }
    }
}

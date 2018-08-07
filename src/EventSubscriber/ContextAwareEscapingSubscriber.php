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
            'compiler.pre_dump' => 'addEscaping'
        );
    }

    protected $options = array();
    protected $placeholder = array();

    public function __construct(array $placeholder = array('[_TWITAL_[', ']_TWITAL_]'), array $options = array())
    {
        $this->placeholder = array(
            '[_TWITAL_[',
            ']_TWITAL_]'
        );

        $this->options = array_merge(array(
            'tag_block' => array('{%', '%}'),
            'tag_variable' => array('{{', '}}'),
        ), $options);
    }

    public function addEscaping(TemplateEvent $event)
    {
        $doc = $event->getTemplate()->getDocument();

        $xp = new \DOMXPath($doc);
        $xp->registerNamespace("xh", "http://www.w3.org/1999/xhtml");

        $this->escapeScript($doc, $xp);
        $this->escapeStyle($doc, $xp);
        $this->escapeUrls($doc, $xp);
    }

    /**
     *
     * Used only to achieve HHVM compatibility. Sett https://github.com/facebook/hhvm/issues/2810
     */
    private function xpathQuery(\DOMXPath $xp, $expression, \DOMNode $contextnode = null, $registerNodeNS = true)
    {
        if (defined('HHVM_VERSION') && HHVM_VERSION_ID < 30500) {
            return $xp->query($expression, $contextnode);
        } else {
            return $xp->query($expression, $contextnode, $registerNodeNS);
        }
    }

    private function escapeUrls(\DOMDocument $doc, \DOMXPath $xp)
    {
        $regex = '{' . preg_quote($this->options['tag_variable'][0]) . '((' . self::REGEX_STRING . '|[^"\']*)+)' . preg_quote($this->options['tag_variable'][1]) . '}siuU';

        // special attr escaping
        $res = $this->xpathQuery($xp, "(//xh:*/@href|//xh:*/@src)[contains(., '{$this->options['tag_variable'][0]}') and contains(., '{$this->options['tag_variable'][1]}')]", $doc, false);
        foreach ($res as $node) {

            // if the twig variable is at the beginning of attribute, we should skip it
            if (preg_match('{^' . preg_quote($this->options['tag_variable'][0]) . '((' . self::REGEX_STRING . '|[^"\']*)+)' . preg_quote($this->options['tag_variable'][1]) . '}siuU', str_replace($this->placeholder, '', $node->value))) {
                continue;
            }

            if (substr($node->value, 0, 11) == "javascript:" && $node->name == "href") {
                $newValue = preg_replace($regex, "{$this->options['tag_variable'][0]} (\\1)  | escape('js') {$this->options['tag_variable'][1]}", $node->value);
            } else {
                $newValue = preg_replace($regex, "{$this->options['tag_variable'][0]} (\\1)  | escape('url') {$this->options['tag_variable'][1]}", $node->value);
            }

            $node->value = htmlspecialchars($newValue, ENT_COMPAT, 'UTF-8');
        }
    }

    private function escapeStyle(\DOMDocument $doc, \DOMXPath $xp)
    {
        /**
         * @var \DOMNode[] $res
         */
        $res = $this->xpathQuery($xp, "//xh:style[not(@type) or @type = 'text/css'][contains(., '{$this->options['tag_variable'][0]}') and contains(., '{$this->options['tag_variable'][1]}')]", $doc, false);

        foreach ($res as $node) {
            $node->insertBefore($doc->createTextnode("{$this->options['tag_block'][0]} autoescape 'css' {$this->options['tag_block'][1]}"), $node->firstChild);
            $node->appendChild($doc->createTextnode("{$this->options['tag_block'][0]} endautoescape {$this->options['tag_block'][1]}"));
        }
    }

    private function escapeScript(\DOMDocument $doc, \DOMXPath $xp)
    {
        /**
         * @var \DOMNode[] $res
         */
        $res = $this->xpathQuery($xp, "//xh:script[not(@type) or @type = 'text/javascript'][contains(., '{$this->options['tag_variable'][0]}') and contains(., '{$this->options['tag_variable'][1]}')]", $doc, false);
        foreach ($res as $node) {
            $node->insertBefore($doc->createTextnode("{$this->options['tag_block'][0]} autoescape 'js' {$this->options['tag_block'][1]}"), $node->firstChild);
            $node->appendChild($doc->createTextnode("{$this->options['tag_block'][0]} endautoescape {$this->options['tag_block'][1]}"));
        }
    }
}

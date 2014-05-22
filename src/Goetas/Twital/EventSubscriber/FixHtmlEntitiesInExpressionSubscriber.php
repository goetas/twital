<?php
namespace Goetas\Twital\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Goetas\Twital\EventDispatcher\SourceEvent;
use Goetas\Twital\EventDispatcher\TemplateEvent;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class FixHtmlEntitiesInExpressionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'compiler.pre_dump' => 'addPlaceholder',
            'compiler.post_dump' => 'removePlaceholder'
        );
    }

    protected $placeholder = array(
        '[_TWITAL_[',
        ']_TWITAL_]'
    );

    public function __construct(array $placeholder = array())
    {
        if ($placeholder) {
            $this->placeholder = $placeholder;
        }
    }
    /**
     * @param TemplateEvent $event
     */
    public function addPlaceholder(TemplateEvent $event)
    {
        $xp = new \DOMXPath($event->getTemplate()->getDocument());

        foreach ($xp->query("//@*") as $node) {
            if (preg_match_all("/(" . preg_quote("{{", "/") . ".*?" . preg_quote("}}", "/") . ")/", $node->value, $mch)) {
                foreach ($mch[0] as $m) {
                    $node->value = htmlspecialchars(str_replace($m, $this->placeholder[0] . $m . $this->placeholder[1], $node->value), ENT_COMPAT, 'UTF-8');
                }
            }
        }
        foreach ($xp->query("//text()") as $node) {
            if (preg_match_all("/(" . preg_quote("{{", "/") . ".*?" . preg_quote("}}", "/") . ")/", $node->data, $mch)) {
                foreach ($mch[0] as $m) {
                    $node->data = str_replace($m, $this->placeholder[0] . $m . $this->placeholder[1], $node->data);
                }
            }
        }
    }
    /**
     *
     * @param SourceEvent $event
     */
    public function removePlaceholder(SourceEvent $event)
    {
        $source = $event->getTemplate();
        if (preg_match_all("/(" . preg_quote($this->placeholder[0], "/") . "(.*?)" . preg_quote($this->placeholder[1], "/") . ")/", $source, $mch)) {
            foreach ($mch[0] as $n => $str) {
                $source = str_replace($str, html_entity_decode($mch[2][$n], ENT_COMPAT, 'UTF-8'), $source);
            }
            $event->setTemplate($source);
        }
    }
}

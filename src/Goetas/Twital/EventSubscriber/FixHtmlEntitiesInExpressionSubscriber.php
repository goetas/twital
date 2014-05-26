<?php
namespace Goetas\Twital\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Goetas\Twital\EventDispatcher\SourceEvent;

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
            'compiler.pre_load' => 'addPlaceholderOnLoad',
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
     *
     * @param SourceEvent $event
     */
    public function addPlaceholderOnLoad(SourceEvent $event)
    {
        $source = $event->getTemplate();
        $exprs = array(
            '{{' => '}}',
            '{%' => '%}',
            '{#' => '#}'
        );
        $offset = 0;
        while (preg_match("/" . implode("|", array_map('preg_quote', array_keys($exprs))) . "/", $source, $matches, PREG_OFFSET_CAPTURE, $offset)) {

            $source = substr($source, 0, $matches[0][1]) . $this->placeholder[0] . substr($source, $matches[0][1]);

            $startoffset = $offset = $matches[0][1] + strlen($matches[0][0]) + strlen($this->placeholder[0]);

            do {
                $matches2 = array();
                if (preg_match("/" . preg_quote($exprs[$matches[0][0]]) . "/", $source, $matches2, PREG_OFFSET_CAPTURE, $offset)) {

                    $offset = $matches2[0][1] + strlen($matches2[0][0]);

                    $inApex = false;
                    for ($i = $startoffset; $i < $offset; $i ++) {
                        $chr = $source[$i];

                        if ($chr == "'" || $chr == '"') {
                            $j = 1;
                            while ($i >= $j && $source[$i - $j] === '\\') {
                                $j ++;
                            }

                            if ($j % 2 !== 0) {
                                if (! $inApex) {
                                    $inApex = $chr;
                                } elseif ($inApex === $chr) {
                                    $inApex = false;
                                }
                            }
                        }
                    }
                    if (! $inApex) {
                        $original = $offset - $startoffset;
                        $encoded = htmlspecialchars(substr($source, $startoffset, $offset - $startoffset), ENT_COMPAT, 'UTF-8');

                        $source = substr($source, 0, $startoffset) . $encoded . $this->placeholder[1] . substr($source, $offset);

                        $offset += strlen($this->placeholder[1]) + (strlen($encoded) - $original);
                    }
                } else {
                    break;
                }
            } while ($inApex && $offset < strlen($source));
        }
        $event->setTemplate($source);
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


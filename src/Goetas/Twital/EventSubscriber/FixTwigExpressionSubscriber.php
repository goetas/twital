<?php
namespace Goetas\Twital\EventSubscriber;

use Goetas\Twital\EventDispatcher\SourceEvent;

/**
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 *
 */
class FixTwigExpressionSubscriber extends AbstractTwigExpressionSubscriber
{
    public static function getSubscribedEvents()
    {
        return array(
            'compiler.pre_load' => array('addPlaceholder', 128),
            'compiler.post_dump' => array('removePlaceholder', -128),
        );
    }

    protected $placeholders = array();

    public function __construct($placeholder = array('twital', 'twital'), array $options = array())
    {
        parent::__construct($placeholder, $options);

        $this->regexes = array_merge($this->regexes, array(
            'placeholder' => '{( ?)('.preg_quote($placeholder[0]).'[a-z0-9]+?'.preg_quote($placeholder[1]).')}iu',
        ));
    }

    /**
     *
     * @param SourceEvent $event
     */
    public function addPlaceholder(SourceEvent $event)
    {
        $source = $event->getTemplate();
        $format = $this->placeholderFormat;
        $placeholders = array();

        $source = $this->processTwig($source, function ($twig, $source, $offset) use ($format, &$placeholders) {
            $before = $offset > 0 ? $source[$offset - 1] : '';
            $id = ('<' === $before || '/' === $before) ? $twig : mt_rand();
            $placeholder = sprintf($format, md5($id));

            if (!in_array($before, array(' ', '<', '>', '/'), true)) {
                $placeholder = ' '.$placeholder;
            }

            $placeholders[$placeholder] = $twig;

            return $placeholder;
        });

        $this->placeholders = $placeholders;

        $event->setTemplate($source);
    }

    /**
     *
     * @param SourceEvent $event
     */
    public function removePlaceholder(SourceEvent $event)
    {
        $source = $event->getTemplate();

        $placeholders = $this->placeholders;

        $source = $this->processPlaceholder($source, function($matches) use ($placeholders) {
            if (isset($placeholders[$matches[0]])) {
                return $placeholders[$matches[0]];
            } elseif (isset($placeholders[$matches[2]])) {
                return $matches[1].$placeholders[$matches[2]];
            } else {
                return $matches[0];
            }
        });

        $event->setTemplate($source);
    }
}

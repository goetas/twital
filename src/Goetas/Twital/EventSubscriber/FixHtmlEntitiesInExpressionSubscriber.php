<?php
namespace Goetas\Twital\EventSubscriber;

use Goetas\Twital\EventDispatcher\SourceEvent;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class FixHtmlEntitiesInExpressionSubscriber extends AbstractTwigExpressionSubscriber
{
    public static function getSubscribedEvents()
    {
        return array(
            'compiler.pre_load' => 'addPlaceholder',
            'compiler.post_dump' => 'removePlaceholder',
        );
    }

    /**
     *
     * @param SourceEvent $event
     */
    public function addPlaceholder(SourceEvent $event)
    {
        $source = $event->getTemplate();
        $format = $this->placeholderFormat;

        $source = preg_replace_callback($this->regexes['twig'], function ($matches) use ($format) {
            return sprintf($format, htmlspecialchars($matches[0], ENT_COMPAT, 'UTF-8'));
        }, $source);

        $event->setTemplate($source);
    }

    /**
     *
     * @param SourceEvent $event
     */
    public function removePlaceholder(SourceEvent $event)
    {
        $source = $event->getTemplate();

        $source = preg_replace_callback($this->regexes['placeholder'], function($matches) {
            return html_entity_decode($matches[2], ENT_COMPAT, 'UTF-8');
        }, $source);

        $event->setTemplate($source);
    }
}

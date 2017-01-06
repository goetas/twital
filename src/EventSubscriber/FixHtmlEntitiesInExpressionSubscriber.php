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

        $source = $this->processTwig($source, function ($twig) use ($format) {
            return sprintf($format, htmlspecialchars($twig, ENT_COMPAT, 'UTF-8'));
        });

        $event->setTemplate($source);
    }

    /**
     *
     * @param SourceEvent $event
     */
    public function removePlaceholder(SourceEvent $event)
    {
        $source = $event->getTemplate();

        $source = $this->processPlaceholder($source, function ($matches) {
            return html_entity_decode($matches[2], ENT_COMPAT, 'UTF-8');
        });

        $event->setTemplate($source);
    }
}

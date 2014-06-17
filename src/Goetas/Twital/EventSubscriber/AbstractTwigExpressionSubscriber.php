<?php
namespace Goetas\Twital\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 *
 */
abstract class AbstractTwigExpressionSubscriber implements EventSubscriberInterface
{
    const REGEX_STRING  = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';

    protected $placeholderFormat = '';

    protected $regexes = array();

    public function __construct(array $placeholder = array('[_TWITAL_[', ']_TWITAL_]'), array $options = array())
    {
        $this->placeholderFormat = $placeholder[0].'%s'.$placeholder[1];

        $options = array_merge(array(
            'tag_block' => array('{%', '%}'),
            'tag_variable' => array('{{', '}}'),
            'tag_comment' => array('{#', '#}'),
        ), $options);

        $this->regexes = array(
            'twig' => '{('.preg_quote($options['tag_block'][0]).
                '|'.preg_quote($options['tag_variable'][0]).'|'.preg_quote($options['tag_comment'][0]).
                ')('.self::REGEX_STRING.'|[^"\']*)+('.preg_quote($options['tag_block'][1]).
                '|'.preg_quote($options['tag_variable'][1]).'|'.preg_quote($options['tag_comment'][1]).
                ')}siuU',
            'placeholder' => '{('.preg_quote($placeholder[0]).'(.+)'.preg_quote($placeholder[1]).')}iuU',
        );
    }
}

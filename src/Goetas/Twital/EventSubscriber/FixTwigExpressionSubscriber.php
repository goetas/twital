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
            'attribute' => '{('.self::REGEX_STRING.'|'.preg_quote($placeholder[0]).'[a-z0-9]+?'.preg_quote($placeholder[1]).')}siu',
            'tag' => '{<('.self::REGEX_STRING.'|[^"\'>]*)*>}siuU',
            'placeholder' => '{( )?('.preg_quote($placeholder[0]).'[a-z0-9]+?'.preg_quote($placeholder[1]).'(="-")?)}iu',
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

        $source = preg_replace_callback($this->regexes['twig'], function ($matches) use ($format, &$placeholders) {
            $placeholder = sprintf($format, md5($matches[0]));
            $placeholders[$placeholder] = $matches[0];

            return $placeholder;
        }, $source);

        $attributeRegex = $this->regexes['attribute'];
        $source = preg_replace_callback($this->regexes['tag'], function ($matches) use ($format, $attributeRegex, &$placeholders) {
            $parts = preg_split($attributeRegex, $matches[0], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            $source = '';
            foreach ($parts as $i => $part) {
                $placeholder = $part;
                if (isset($placeholders[$placeholder])) {
                    $prevChar = isset($parts[$i - 1]) ? substr($parts[$i - 1], -1) : null;
                    switch ($prevChar) {
                        case '<':
                        case '/':
                            break;
                        case ' ':
                        case "\t":
                        case null:
                            $placeholder = sprintf($format, md5(mt_rand()));
                            break;
                        default:
                            $placeholder = ' '.sprintf($format, md5(mt_rand()));
                            break;
                    }

                    if ($placeholder !== $part && !(isset($parts[$i + 1]) && '=' === trim($parts[$i + 1]))) {
                        $placeholder .= '="-"';
                    }

                    $placeholders[$placeholder] = $placeholders[$part];
                }

                $source .= $placeholder;
            };

            return $source;
        }, $source);

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

        $source = preg_replace_callback($this->regexes['placeholder'], function($matches) use ($placeholders) {
            if (isset($placeholders[$matches[0]])) {
                return $placeholders[$matches[0]];
            } elseif (isset($placeholders[$matches[2]])) {
                return $matches[1] . $placeholders[$matches[2]];
            } else {
                return $matches[0];
            }
        }, $source);

        $event->setTemplate($source);
    }
}

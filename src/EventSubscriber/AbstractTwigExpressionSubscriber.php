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
    const REGEX_STRING = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'';

    protected $placeholderFormat = '';
    protected $regexes = array();

    public function __construct(array $placeholder = array('[_TWITAL_[', ']_TWITAL_]'), array $options = array())
    {
        $this->placeholderFormat = $placeholder[0] . '%s' . $placeholder[1];

        $options = array_merge(array(
            'tag_block' => array('{%', '%}'),
            'tag_variable' => array('{{', '}}'),
            'tag_comment' => array('{#', '#}'),
        ), $options);

        $this->regexes = array(
            'twig_start' => '{(' . preg_quote($options['tag_block'][0]) . '|' . preg_quote($options['tag_variable'][0]) . '|' . preg_quote($options['tag_comment'][0]) . ')}',
            'placeholder' => '{(' . preg_quote($placeholder[0]) . '(.+)' . preg_quote($placeholder[1]) . ')}siuU',
            'twig_inner_' . $options['tag_block'][0] => '{(' . self::REGEX_STRING . '|([^"\']*?' . preg_quote($options['tag_block'][1]) . ')|[^"\']+?)}si',
            'twig_inner_' . $options['tag_variable'][0] => '{(' . self::REGEX_STRING . '|([^"\']*?' . preg_quote($options['tag_variable'][1]) . ')|[^"\']+?)}si',
            'twig_inner_' . $options['tag_comment'][0] => '{((.*?' . preg_quote($options['tag_comment'][1]) . '))}si',
        );
    }

    protected function processTwig($template, \CLosure $processor)
    {
        $offset = 0;
        while (preg_match($this->regexes['twig_start'], $template, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $twig = '';
            $buffer = $matches[0][0];
            $from = $matches[0][1];
            $offset = $from + strlen($buffer);
            $pattern = $this->regexes['twig_inner_' . $buffer];
            while (preg_match($pattern, $template, $inners, PREG_OFFSET_CAPTURE, $offset)) {
                $buffer .= $inners[0][0];
                $offset += strlen($inners[0][0]);
                if (isset($inners[2])) {
                    $twig = $buffer;
                    break;
                }
            }

            if (!$twig) {
                continue;
            }

            $replacement = $processor($twig, $template, $from);
            $template = substr_replace($template, $replacement, $from, $offset - $from);
            $offset = $from + strlen($replacement);
        }

        return $template;
    }

    protected function processPlaceholder($template, \Closure $processor)
    {
        return preg_replace_callback($this->regexes['placeholder'], $processor, $template);
    }
}

<?php
namespace Goetas\Twital\SourceAdapter;

use HTML5;
use Goetas\Twital\SourceAdapter;
use Goetas\Twital\Template;
use Goetas\Twital\Helper\DOMHelper;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
class HTML5Adapter implements SourceAdapter
{
    /**
     * {@inheritdoc}
     */
    public function load($source)
    {
        $f = HTML5::loadHTMLFragment($source);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->importNode($f, true));

        self::fixElements($dom);

        return new Template($dom, $this->collectMetadata($dom, $source));
    }

    private static function fixElements(\DOMNode $node, $namespaces = array())
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                self::fixNss($child, $namespaces);
            }
        }
    }

    private static function fixNss(\DOMElement $element, $namespaces = array())
    {
        foreach ($element->attributes as $attr) {
            if (preg_match("/^xmlns:(.+)/", $attr->name, $mch)) {
                $namespaces[$mch[1]] = $attr->value;
            }
        }
        if (preg_match('/^([a-z0-9\-]+):(.+)/i', $element->nodeName, $mch) && isset($namespaces[$mch[1]])) {
            $oldElement = $element;
            $element = DOMHelper::copyElementInNs($oldElement, $namespaces[$mch[1]]);
        }
        // fix attrs
        foreach (iterator_to_array($element->attributes) as $attr) {
            if (preg_match('/^([a-z0-9\-]+):(.+)/', $attr->name, $mch) && isset($namespaces[$mch[1]])) {
                $element->removeAttributeNode($attr);
                $element->setAttributeNS($namespaces[$mch[1]], $attr->name, $attr->value);
            }
        }

        self::fixElements($element,$namespaces);
    }

    /**
     * {@inheritdoc}
     */
    public function dump(Template $template)
    {
        $metadata = $template->getMetadata();
        $dom = $template->getDocument();

        return HTML5::saveHTML($metadata['fragment'] ? $dom->childNodes : $dom);
    }

    /**
     * Collect some metadata about $dom and $content
     * @param \DOMDocument $dom
     * @param string $source
     * @return mixed
     */
    protected function collectMetadata(\DOMDocument $dom, $source)
    {
        $metadata = array();

        $metadata['doctype'] = !! $dom->doctype;
        $metadata['fragment'] = strpos(rtrim($source), '<!DOCTYPE html>') !== 0;

        return $metadata;
    }
}

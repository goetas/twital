<?php
namespace Goetas\Twital\SourceAdapter;

use HTML5;
use Goetas\Twital\Dumper;
use Goetas\Twital\SourceAdapter;
use Goetas\Twital\NamespaceAdapter;

class HTML5Adapter implements SourceAdapter
{

    public function load($html)
    {
        $f = HTML5::loadHTMLFragment($html);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->importNode($f, 1));

        self::fixElements($dom);
        return $dom;
    }

    protected static function fixElements(\DOMNode $node, $namespaces = array())
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                self::fixNss($child, $namespaces);
            }
        }
    }
    protected static function fixNss(\DOMElement $element, $namespaces = array())
    {
        foreach ($element->attributes as $attr) {
            if (preg_match("/^xmlns:(.+)/", $attr->name, $mch)) {
                $namespaces[$mch[1]] = $attr->value;
            }
        }
        if (preg_match('/^([a-z0-9\-]+):(.+)/i', $element->nodeName, $mch) && isset($namespaces[$mch[1]])) {
            $oldElement = $element;
            $element = NamespaceAdapter::copyElementInNs($oldElement, $namespaces[$mch[1]]);
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

    public function dump(\DOMDocument $dom, $metedata)
    {
        if (! $metedata['doctype']) {
            return HTML5::saveHTML($dom->childNodes);
        }
        return HTML5::saveHTML($dom->childNodes);
    }

    public function collectMetadata(\DOMDocument $dom, $original)
    {
        $metedata = array();

        $metedata['doctype'] = ! ! $dom->doctype;
        $metedata['fragment'] = ! ! strpos(rtrim($original), '<!DOCTYPE html>') === 0;

        return $metedata;
    }
}
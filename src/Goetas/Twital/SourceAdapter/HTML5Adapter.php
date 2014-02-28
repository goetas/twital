<?php
namespace Goetas\Twital\SourceAdapter;

use HTML5;
use Goetas\Twital\Dumper;
use Goetas\Twital\SourceAdapter;

class HTML5Adapter implements SourceAdapter
{

    public function load($html)
    {
        $f = HTML5::loadHTMLFragment($html);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->importNode($f, 1));

        foreach (iterator_to_array($dom->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                self::fixNss($child, array());
            }
        }
        return $dom;
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
            $element = $element->ownerDocument->createElementNS($namespaces[$mch[1]], $element->nodeName);

            // copy attrs
            foreach (iterator_to_array($oldElement->attributes) as $attr) {
                $oldElement->removeAttributeNode($attr);
                if ($attr->namespaceURI) {
                    $element->setAttributeNodeNS($attr);
                } else {
                    $element->setAttributeNode($attr);
                }
            }
            // copy childs
            while ($child = $oldElement->firstChild) {
                $oldElement->removeChild($child);
                $element->appendChild($child);
            }
            $oldElement->parentNode->replaceChild($element, $oldElement);
        }
        // fix attrs
        foreach (iterator_to_array($element->attributes) as $attr) {
            if (preg_match('/^([a-z0-9\-]+):(.+)/', $attr->name, $mch) && isset($namespaces[$mch[1]])) {
                $element->removeAttributeNode($attr);
                $element->setAttributeNS($namespaces[$mch[1]], $attr->name, $attr->value);
            }
        }
        foreach (iterator_to_array($element->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                self::fixNss($child, $namespaces);
            }
        }
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
<?php
namespace Goetas\Twital\DOMLoader;

use Goetas\Twital\DOMLoader;
use HTML5;
class HTML5DOMLoader implements DOMLoader
{

    public function createDom($html)
    {

        $f = HTML5::loadHTMLFragment($html);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->importNode($f,1));

        foreach(iterator_to_array($dom->childNodes) as $child){
            if($child instanceof \DOMElement){
                self::fixNss($child, array());
            }
        }
        return $dom;
    }
    protected static function fixNss(\DOMElement $element, $nss = array())
    {
        foreach($element->attributes as $attr){
        	if(preg_match("/^xmlns:(.+)/",$attr->name, $mch)){
        	    $nss[$mch[1]]=$attr->value;
        	}
        }
        if(preg_match("/^([a-z0-9\-]+):(.+)/",$element->nodeName, $mch) && isset($nss[$mch[1]])){
            $oldElement = $element;
            $element = $element->ownerDocument->createElementNS($nss[$mch[1]], $element->nodeName);

            // copy attrs
            foreach(iterator_to_array($oldElement->attributes) as $attr){
                $oldElement->removeAttributeNode($attr);
                if($attr->namespaceURI){
                    $element->setAttributeNodeNS($attr);
                }else{
                    $element->setAttributeNode($attr);
                }
            }
            //copy childs
            while ($child = $oldElement->firstChild) {
            	$oldElement->removeChild($child);
            	$element->appendChild($child);
            }
            $oldElement->parentNode->replaceChild($element, $oldElement);
	    }
	    //fix attrs
    	foreach(iterator_to_array($element->attributes) as $attr){
    	    if(preg_match("/^([a-z0-9\-]+):(.+)/",$attr->name, $mch) && isset($nss[$mch[1]])){
    	        $element->removeAttributeNode($attr);
                $element->setAttributeNS($nss[$mch[1]], $attr->name, $attr->value);
    	    }
    	}
        foreach(iterator_to_array($element->childNodes) as $child){
            if($child instanceof \DOMElement){
                self::fixNss($child, $nss);
            }
        }
    }

    public function dumpDom(\DOMDocument $dom, $metedata)
    {
        if(!$metedata['doctype']){
            return HTML5::saveHTML($dom->childNodes);
        }
        return HTML5::saveHTML($dom->childNodes);
    }

    public function collectMetadata(\DOMDocument $dom, $original)
    {
        $metedata = array();

        $metedata['xmldeclaration'] = strpos(rtrim($original), '<?xml ') === 0;
        $metedata['doctype'] = ! ! $dom->doctype;
        $metedata['fragment'] = ! ! strpos(rtrim($original), '<!DOCTYPE html>') === 0;

        return $metedata;
    }
}
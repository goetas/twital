<?php
namespace Goetas\Twital\DOMLoader;

use Goetas\Twital\DOMLoader;

class HTML5DOMLoader implements DOMLoader
{

    public function createDom($html)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        return $dom;
    }

    public function dumpDom(\DOMDocument $dom, $original)
    {
        return $dom->saveXML();
    }

    public function collectMetadata(\DOMDocument $dom, $original)
    {
        $metedata = array();
        
        $metedata['xmldeclaration'] = strpos(rtrim($original), '<?xml ') === 0;
        $metedata['doctype'] = ! ! $xml->doctype;
        
        return $metedata;
    }
}
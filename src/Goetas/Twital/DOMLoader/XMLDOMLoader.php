<?php
namespace Goetas\Twital\DOMLoader;

use Goetas\Twital\DOMLoader;

class XMLDOMLoader implements DOMLoader
{

    public function createDom($xml)
    {
        $dom = new \goetas\xml\XMLDom('1.0', 'UTF-8');
        $dom->loadXML($xml);
        return $dom;
    }

    public function collectMetadata(\DOMDocument $dom, $original)
    {
        $metedata = array();
        
        $metedata['xmldeclaration'] = strpos(rtrim($original), '<?xml ') === 0;
        $metedata['doctype'] = ! ! $dom->doctype;
        
        return $metedata;
    }

    public function dumpDom(\DOMDocument $dom, $metedata)
    {
        if ($metedata['xmldeclaration']) {
            return $dom->saveXML();
        } else {
            $cnt = array();
            
            if ($metedata['doctype']) {
                $cnt[] = $dom->saveXML($xml->doctype) . "\n";
            }
            
            foreach ($dom->childNodes as $node) {
                $cnt[] = $dom->saveXML($node);
            }
            $cnt = implode("", $cnt);
            
            return $cnt;
        }
    }
}
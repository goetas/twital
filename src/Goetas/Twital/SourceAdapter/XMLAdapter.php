<?php
namespace Goetas\Twital\SourceAdapter;

use Goetas\Twital\SourceAdapter;

class XMLAdapter implements SourceAdapter
{

    public function load($xml)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        if(!@$dom->loadXML($xml)){
            throw new \Exception("Error during XML conversion into DOM");
        }
        return $dom;
    }

    public function collectMetadata(\DOMDocument $dom, $original)
    {
        $metedata = array();

        $metedata['xmldeclaration'] = strpos(rtrim($original), '<?xml ') === 0;
        $metedata['doctype'] = ! ! $dom->doctype;

        return $metedata;
    }

    public function dump(\DOMDocument $dom, $metedata)
    {
        if ($metedata['xmldeclaration']) {
            return $dom->saveXML();
        } else {
            $cnt = array();

            foreach ($dom->childNodes as $node) {
                $cnt[] = $dom->saveXML($node);
            }
            $cnt = implode("", $cnt);

            return $cnt;
        }
    }
}
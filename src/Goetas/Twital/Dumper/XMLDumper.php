<?php
namespace Goetas\Twital\Dumper;


use Goetas\Twital\Dumper;

class XMLDumper implements Dumper
{

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
<?php
namespace Goetas\Twital\Dumper;

use HTML5;
use Goetas\Twital\Dumper;
class HTML5Dumper implements Dumper
{

    public function dump(\DOMDocument $dom, $metedata)
    {
        if(!$metedata['doctype']){
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
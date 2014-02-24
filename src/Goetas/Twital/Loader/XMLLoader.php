<?php
namespace Goetas\Twital\Loader;

use Goetas\Twital\Loader;

class XMLLoader implements Loader
{
    public function load($xml)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml);
        return $dom;
    }
}
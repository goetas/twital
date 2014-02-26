<?php
namespace Goetas\Twital\Loader;

use Goetas\Twital\Loader;

class XMLLoader implements Loader
{
    public function load($xml)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        if(!@$dom->loadXML($xml)){
            throw new \Exception("Error during XML conversion into DOM");
        }
        return $dom;
    }
}
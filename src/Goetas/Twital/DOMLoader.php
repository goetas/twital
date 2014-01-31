<?php
namespace Goetas\Twital;

interface DOMLoader
{

    public function createDOM($html);

    public function dumpDOM(\DOMDocument $dom, $metadata);

    public function collectMetadata(\DOMDocument $dom, $original);
}

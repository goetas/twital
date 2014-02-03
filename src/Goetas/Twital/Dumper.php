<?php
namespace Goetas\Twital;

interface Dumper
{

    /**
     * Dumps a DOM into a string
     * @param \DOMDocument $dom
     * @param mixed $metadata
     * @return string
     */
    public function dump(\DOMDocument $dom, $metadata);
    /**
     * Collect various metadata from original document. This metadata can be used to modify dump options
     * @param \DOMDocument $dom
     * @param string $original
     * mixed
     */
    public function collectMetadata(\DOMDocument $dom, $original);
}

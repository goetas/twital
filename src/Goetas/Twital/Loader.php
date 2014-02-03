<?php
namespace Goetas\Twital;

interface Loader
{
    /**
     *
     * @param string $string
     * @return \DOMDocument
     */
    public function load($string);
}

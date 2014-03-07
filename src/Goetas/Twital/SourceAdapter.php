<?php
namespace Goetas\Twital;

interface SourceAdapter
{
    /**
     *
     * @param string $string
     * @return \Goetas\Twital\Template
     */
    public function load($string);
    /**
     *
     * @param \Goetas\Twital\Template $dom
     * @return string
     */
    public function dump(Template $dom);
}

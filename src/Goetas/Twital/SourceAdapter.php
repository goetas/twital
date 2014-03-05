<?php
namespace Goetas\Twital;

interface SourceAdapter
{
    /**
     *
     * @param string $string
     * @return Template
     */
    public function load($string);
    /**
     *
     * @param Template $dom
     * @return string
     */
    public function dump(Template $dom);
}

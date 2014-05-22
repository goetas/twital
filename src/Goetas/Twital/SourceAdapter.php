<?php
namespace Goetas\Twital;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
interface SourceAdapter
{
    /**
     * Gets the raw template source code and return a {Goetas\Twital\Template} instance.
     *
     * @param string $string
     * @return \Goetas\Twital\Template
     */
    public function load($string);

    /**
     * Gets a {Goetas\Twital\Template}  instance and return the raw template source code.
     *
     * @param \Goetas\Twital\Template $dom
     * @return string
     */
    public function dump(Template $dom);
}

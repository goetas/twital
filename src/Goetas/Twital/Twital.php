<?php
namespace Goetas\Twital;

use DOMException;
use DOMText;
use DOMCdataSection;
use DOMNode;
use DOMProcessingInstruction;
use goetas\xml;
use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\HTML5Extension;

class TwitalLoader implements \Twig_LoaderInterface
{
    protected $loader;

    protected $compiler;

    function __construct(\Twig_LoaderInterface $loader, TwitalCompiler $compiler)
    {
        $this->loader = $loader;
        $this->compiler = $compiler;

    }

    public function getCacheKey($name)
    {
        return $this->loader->getCacheKey($name);
    }

    public function isFresh($name, $time)
    {
        return $this->loader->isFresh($name, $time);
    }

    /**
     * Ritorna una stringa del DOM presente in $xml
     *
     * @param
     *            $xml
     */
    public function getSource($name)
    {
        $cnt = $this->loader->getSource($name);
		return $this->compiler->compile($cnt);

    }
}
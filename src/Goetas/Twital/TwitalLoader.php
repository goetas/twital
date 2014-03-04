<?php
namespace Goetas\Twital;

use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\I18nExtension;
use Goetas\Twital\Extension\HTML5Extension;

class TwitalLoader implements \Twig_LoaderInterface
{

    protected $namePatterns = array();
    /**
     *
     * @var \Twig_LoaderInterface
     */
    protected $loader;

    /**
     *
     * @var \Twig_LoaderInterface
     */
    protected $compiler;

    public function __construct(\Twig_LoaderInterface $loader, array $namePatterns = array())
    {
        $this->loader = $loader;
        $this->namePatterns = $namePatterns;

        if (! count($this->namePatterns)) {
            $this->namePatterns = array(
                '/\.twital\./',
                '/\.twital$/'
            );
        }
    }

    public function setNamePatterns(array $patterns)
    {
        $this->namePatterns = $patterns;
        return $this;
    }

    public function addNamePattern($pattern)
    {
        $this->namePatterns[] = $pattern;
        return $this;
    }

    public function setCompiler(Twital $twital)
    {
        $this->compiler = $twital;
    }

    public function getCompiler()
    {
        return $this->compiler ?  : new Twital();
    }

    public function getNamePatterns()
    {
        return $this->namePatterns;
    }

    protected function shouldCompile($name)
    {
        foreach ($this->namePatterns as $pattern) {
            if (is_string($pattern)) {
                if (preg_match($pattern, basename($name))) {
                    return true;
                }
            } elseif (call_user_func($pattern, $name)) {
                return true;
            }
        }
        return false;
    }

    public function getSource($name)
    {
        $source = $this->loader->getSource($name);

        if ($this->shouldCompile($name)) {
            $source = $this->getCompiler()->compile($source, $name);
        }

        return $source;
    }

    public function getCacheKey($name)
    {
        return $this->loader->getCacheKey($name);
    }

    public function isFresh($name, $time)
    {
        return $this->loader->isFresh($name, $time);
    }

    public function getLoader()
    {
        return $this->loader;
    }

    public function setLoader(\Twig_LoaderInterface $loader)
    {
        $this->loader = $loader;
        return $this;
    }
}
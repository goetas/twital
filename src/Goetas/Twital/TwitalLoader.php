<?php
namespace Goetas\Twital;

use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\I18nExtension;
use Goetas\Twital\Extension\HTML5Extension;

/**
 * This is a Twital Loader.
 * Compile a Twital template into Twig template.
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class TwitalLoader implements \Twig_LoaderInterface
{

    /**
     * Array of patterns used to decide if a template is twital-compilable or not.
     * Items are strings or callbacks
     *
     * @var array
     */
    protected $namePatterns = array();

    /**
     * The wrapped Twig loader
     *
     * @var \Twig_LoaderInterface
     */
    protected $loader;

    /**
     * The internal Twital compiler
     *
     * @var Compiler
     */
    protected $compiler;

    public function __construct(\Twig_LoaderInterface $loader, Compiler $compiler, array $namePatterns = array())
    {
        $this->loader = $loader;
        $this->compiler = $compiler;
        $this->namePatterns = $namePatterns;

        if (! count($this->namePatterns)) {
            $this->namePatterns = array(
                '/\.twital\.[a-z]+$/i',
                '/\.twital$/i'
            );
        }
    }

    /**
     * Set all patterns used to decide if a template is twital-compilable or not.
     *
     * @see TwitalLoader::addNamePattern()
     * @param array $patterns
     * @return \Goetas\Twital\TwitalLoader
     */
    public function setNamePatterns(array $patterns)
    {
        $this->namePatterns = $patterns;
        return $this;
    }

    /**
     * Add a new pattern that can decide if a template is twital-compilable or not.
     * If $pattern is a string, then must be a valid regex that matches the template filename.
     * If $pattern is a callback, then must return true if the tempalte is compilable, false otherwise.
     *
     * @param string|callback $pattern
     * @return \Goetas\Twital\TwitalLoader
     */
    public function addNamePattern($pattern)
    {
        $this->namePatterns[] = $pattern;
        return $this;
    }

    /**
     * Set the internal compiler
     *
     * @param Twital $twital
     * @return \Goetas\Twital\TwitalLoader
     */
    public function setCompiler(Twital $twital)
    {
        $this->compiler = $twital;
        return $this;
    }

    /**
     * Get the internal compiler
     *
     * @return \Goetas\Twital\Compiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * Get all patterns used to choose if a template is twital-compilable or not
     *
     * @return array:
     */
    public function getNamePatterns()
    {
        return $this->namePatterns;
    }

    /**
     * Decide if a template is twital-compilable or not.
     *
     * @return array:
     */
    protected function shouldCompile($name)
    {
        foreach ($this->namePatterns as $pattern) {
            if (is_string($pattern)) {
                if (preg_match($pattern, $name)) {
                    return true;
                }
            } elseif (call_user_func($pattern, $name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Twig_LoaderInterface::getSource()
     */
    public function getSource($name)
    {
        $source = $this->loader->getSource($name);

        if ($this->shouldCompile($name)) {
            $source = $this->getCompiler()->compile($source, $name);
        }

        return $source;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Twig_LoaderInterface::getCacheKey()
     */
    public function getCacheKey($name)
    {
        return $this->loader->getCacheKey($name);
    }

    /**
     * (non-PHPdoc)
     *
     * @see Twig_LoaderInterface::isFresh()
     */
    public function isFresh($name, $time)
    {
        return $this->loader->isFresh($name, $time);
    }

    /**
     * Get the wrapped Twig loader
     *
     * @return Twig_LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Set the wrapped Twig loader
     *
     * @param \Twig_LoaderInterface $loader
     * @return \Goetas\Twital\TwitalLoader
     */
    public function setLoader(\Twig_LoaderInterface $loader)
    {
        $this->loader = $loader;
        return $this;
    }
}
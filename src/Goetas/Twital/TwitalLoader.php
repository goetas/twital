<?php
namespace Goetas\Twital;

use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\I18nExtension;
use Goetas\Twital\Extension\HTML5Extension;
use Goetas\Twital\SourceAdapter\HTML5Adapter;
use Goetas\Twital\SourceAdapter\XMLAdapter;
use Goetas\Twital\SourceAdapter\XHTMLAdapter;

/**
 * This is a Twital Loader.
 * Compiles a Twital template into a Twig template.
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
    protected $sourceAdapters = array();

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
    /**
     * Creates a new Twital loader.
     * @param \Twig_LoaderInterface $loader
     * @param Compiler $compiler
     * @param array $sourceAdapters If NULL, some standard rules will be used (`*.twital.*` and `*.twital`).
     */
    public function __construct(\Twig_LoaderInterface $loader, Twital $compiler, $addDefaults = true)
    {
        $this->loader = $loader;
        $this->compiler = $compiler;

        if (is_null($addDefaults)) {
            $this->addSourceAdapter('/\.twital\.html$/i',new HTML5Adapter());
            $this->addSourceAdapter('/\.twital\.xml$/i',new XMLAdapter());
            $this->addSourceAdapter('/\.twital\.xhtml$/i',new XHTMLAdapter());
        }
    }

    /**
     * Add a new pattern that can decide if a template is twital-compilable or not.
     * If $pattern is a string, then must be a valid regex that matches the template filename.
     * If $pattern is a callback, then must return true if the tempalte is compilable, false otherwise.
     *
     * @param string|callback $pattern
     * @return \Goetas\Twital\TwitalLoader
     */
    public function addSourceAdapter($pattern, SourceAdapter $adapter)
    {
        $this->sourceAdapters[$pattern] = $adapter;
        return $this;
    }

    /**
     * Get all patterns used to choose if a template is twital-compilable or not
     *
     * @return array:
     */
    public function getSourceAdapters()
    {
        return $this->sourceAdapters;
    }

    /**
     * Decide if a template is twital-compilable or not.
     *
     * @return SourceAdapter
     */
    protected function getSourceAdapter($name)
    {
        foreach (array_reverse($this->sourceAdapters) as $pattern => $adapter) {
            if (preg_match($pattern, $name)) {
                return $adapter;
            }
        }
        return null;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Twig_LoaderInterface::getSource()
     */
    public function getSource($name)
    {
        $source = $this->loader->getSource($name);

        if ($adapter = $this->getSourceAdapter($name)) {

            $source = $this->compiler->compile($adapter, $source);

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
<?php
namespace Goetas\Twital;

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
     * @var Twital
     */
    protected $twital;

    /**
     * Creates a new Twital loader.
     * @param \Twig_LoaderInterface $loader
     * @param Twital $twital
     * @param bool $addDefaults If NULL, some standard rules will be used (`*.twital.*` and `*.twital`).
     */
    public function __construct(\Twig_LoaderInterface $loader = null, Twital $twital = null, $addDefaults = true)
    {
        $this->loader = $loader;
        $this->twital = $twital;

        if ($addDefaults === true || (is_array($addDefaults) && in_array('html', $addDefaults))) {
            $this->addSourceAdapter('/\.twital\.html$/i', new HTML5Adapter());
        }
        if ($addDefaults === true || (is_array($addDefaults) && in_array('xml', $addDefaults))) {
            $this->addSourceAdapter('/\.twital\.xml$/i', new XMLAdapter());
        }
        if ($addDefaults === true || (is_array($addDefaults) && in_array('xhtml', $addDefaults))) {
            $this->addSourceAdapter('/\.twital\.xhtml$/i', new XHTMLAdapter());
        }
    }

    /**
     * Add a new pattern that can decide if a template is twital-compilable or not.
     * If $pattern is a string, then must be a valid regex that matches the template filename.
     * If $pattern is a callback, then must return true if the template is compilable, false otherwise.
     *
     * @param string|callback $pattern
     * @param SourceAdapter $adapter
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
     * @param string $name
     * @return SourceAdapter
     */
    public function getSourceAdapter($name)
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
            $source = $this->getTwital()->compile($adapter, $source);
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

    /**
     * @return \Goetas\Twital\Twital
     */
    public function getTwital()
    {
        if ($this->twital===null) {
            $this->twital = new Twital();
        }

        return $this->twital;
    }

}

<?php

namespace Goetas\Twital;

use Goetas\Twital\SourceAdapter\HTML5Adapter;
use Goetas\Twital\SourceAdapter\XHTMLAdapter;
use Goetas\Twital\SourceAdapter\XMLAdapter;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\ExistsLoaderInterface;
use Twig\Loader\LoaderInterface;
use Twig\Loader\SourceContextLoaderInterface;

/**
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
trait TwitalLoaderTrait
{
    /**
     * Array of patterns used to decide if a template is twital-compilable or not.
     * Items are strings or callbacks
     *
     * @var array
     */
    protected $sourceAdapters = array();

    /**
     * The internal Twital compiler
     *
     * @var Twital
     */
    protected $twital;

    /**
     * The wrapped Twig loader
     *
     * @var LoaderInterface|\Twig_LoaderInterface
     */
    protected $loader;

    private $twigMajorVersion;

    /**
     * Add a new pattern that can decide if a template is twital-compilable or not.
     * If $pattern is a string, then must be a valid regex that matches the template filename.
     * If $pattern is a callback, then must return true if the template is compilable, false otherwise.
     *
     * @param string|callback $pattern
     * @param SourceAdapter $adapter
     * @return TwitalLoader
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
     * Get the wrapped Twig loader
     *
     * @return LoaderInterface|\Twig_LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Set the wrapped Twig loader
     *
     * @param LoaderInterface|\Twig_LoaderInterface $loader
     * @return TwitalLoader
     */
    public function setLoader($loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * @return Twital
     */
    public function getTwital()
    {
        if ($this->twital === null) {
            $this->twital = new Twital();
        }

        return $this->twital;
    }

    protected function doGetSourceContext($name)
    {
        if ($this->getTwigMajorVersion() >= 2 || $this->loader instanceof SourceContextLoaderInterface) {
            $originalContext = $this->loader->getSourceContext($name);
            $code = $originalContext->getCode();
            $path = $originalContext->getPath();
        } else {
            $code = $this->loader->getSource($name);
            $path = null;
        }

        if ($adapter = $this->getSourceAdapter($name)) {
            $code = $this->getTwital()->compile($adapter, $code);
        }

        return array($code, $name, $path);
    }

    /**
     * Creates a new Twital loader.
     *
     * @param LoaderInterface|\Twig_LoaderInterface $loader
     * @param Twital $twital
     * @param bool $addDefaults If NULL, some standard rules will be used (`*.twital.*` and `*.twital`).
     */
    private function doConstruct($loader = null, Twital $twital = null, $addDefaults = true)
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

    private function doExists($name)
    {
        if ($this->getTwigMajorVersion() >= 2 || $this->loader instanceof ExistsLoaderInterface) {
            return $this->loader->exists($name);
        } else {
            try {
                $this->getSourceContext($name);

                return true;
            } catch (LoaderError $e) {
                return false;
            } catch (\Twig_Error_Loader $e) {
                return false;
            }
        }
    }

    private function getTwigMajorVersion()
    {
        if (null === $this->twigMajorVersion) {
            $this->twigMajorVersion = class_exists(Environment::class) ? Environment::MAJOR_VERSION : \Twig_Environment::MAJOR_VERSION;
        }

        return $this->twigMajorVersion;
    }
}

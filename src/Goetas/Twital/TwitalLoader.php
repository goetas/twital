<?php
namespace Goetas\Twital;

use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\I18nExtension;
use Goetas\Twital\Extension\HTML5Extension;
class TwitalLoader implements \Twig_LoaderInterface
{

    const NS = 'urn:goetas:twital';

    protected $namePatterns = array();

    protected $extensionsInitialized = false;

    /**
     *
     * @var array
     */
    protected $attributes = array();

    /**
     *
     * @var array
    */
    protected $nodes = array();

    protected $defaultSourceAdapter;
    /**
     *
     * @var array
    */
    protected $sourceAdapters = array();

    /**
     *
     * @var array
    */
    protected $postFilter = array();

    /**
     *
     * @var array
    */
    protected $customNamespaces = array();

    /**
     *
     * @var \Twig_LoaderInterface
     */
    protected $loader;

    public function __construct(\Twig_LoaderInterface $loader, $defaultAdapter = 'xml')
    {

        $this->loader = $loader;
        $this->defaultAdapter = $defaultAdapter;
        $this->namePatterns = array(
            '/\.twital\./',
            '/\.twital$/'
        );

        $this->addExtension(new CoreExtension());
        $this->addExtension(new I18nExtension());
        $this->addExtension(new HTML5Extension());
    }


    protected function initExtensions()
    {
        if (! $this->extensionsInitialized) {
            foreach ($this->getExtensions() as $extension) {
                $this->attributes = array_merge_recursive($this->attributes, $extension->getAttributes());
                $this->nodes = array_merge_recursive($this->nodes, $extension->getNodes());
                $this->postFilter = array_merge($this->postFilter, $extension->getPostFilters());
                $this->sourceAdapters = array_merge($this->sourceAdapters, $extension->getSourceAdapters());
                $this->customNamespaces = array_merge($this->customNamespaces, $extension->getPrefixes());
            }
            $this->extensionsInitialized = true;
        }
    }

    protected function compile($source, SourceAdapter $adapter)
    {
        $xml = $adapter->load($source);
        $this->checkDocumentNamespaces($xml);

        $metadata = $adapter->collectMetadata($xml, $source);

        $context = new CompilationContext($xml, $this, $this->nodes, $this->attributes);
        $context->compileChilds($xml);

        $source = $adapter->dump($xml, $metadata);
        foreach ($this->postFilter as $filter) {
            $source = call_user_func($filter, $source);
        }

        die($source);

        return $source;
    }

    protected function checkDocumentNamespaces(\DOMDocument $dom)
    {
        foreach (iterator_to_array($dom->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                NamespaceAdapter::checkNamespaces($child, $this->customNamespaces);
            }
        }
    }

    /**
     *
     * @param string $name
     * @throws Exception
     * @return SourceAdapter
     */
    protected function getSourceAdapter($name)
    {
        if (! isset($this->sourceAdapters[$name])) {
            throw new Exception("Can't find a source adapter called {$name}");
        }

        return $this->sourceAdapters[$name];
    }


    public function addExtension(Extension $extension)
    {
        return $this->extensions[] = $extension;
    }

    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;
    }

    public function getExtensions()
    {
        return $this->extensions;
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
            $this->initExtensions();
            $source = $this->compile($source, $this->getSourceAdapter('xml'));
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

    public function getDefaultSourceAdapter()
    {
        return $this->defaultSourceAdapter;
    }

    public function setDefaultSourceAdapter($defaultSourceAdapter)
    {
        $this->defaultSourceAdapter = $defaultSourceAdapter;
        return $this;
    }

}
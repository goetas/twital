<?php
namespace Goetas\Twital;

use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\I18nExtension;
use Goetas\Twital\Extension\HTML5Extension;
class Twital
{

    const NS = 'urn:goetas:twital';


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



    public function __construct($defaultAdapter = 'xml')
    {

        $this->defaultAdapter = $defaultAdapter;

        $this->addExtension(new CoreExtension());
        $this->addExtension(new I18nExtension());
        $this->addExtension(new HTML5Extension());
    }
    public function getNodes()
    {
        $this->initExtensions();
    	return $this->nodes;
    }
    public function getAttributes()
    {
        $this->initExtensions();
        return $this->attributes;
    }
    protected function initExtensions()
    {
        if (!$this->extensionsInitialized) {
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

    protected function compile($source, $name = null)
    {
        $this->initExtensions();

        $adapter = $this->getSourceAdapter($name);

        $xml = $adapter->load($source);
        $this->checkDocumentNamespaces($xml);

        $metadata = $adapter->collectMetadata($xml, $source);

        $context = new CompilationContext($xml, $this);
        $context->compileChilds($xml);

        $source = $adapter->dump($xml, $metadata);
        foreach ($this->postFilter as $filter) {
            $source = call_user_func($filter, $source);
        }
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
        $name = $this->getDefaultSourceAdapter();
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
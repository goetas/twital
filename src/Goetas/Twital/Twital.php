<?php
namespace Goetas\Twital;

use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\I18nExtension;
use Goetas\Twital\Extension\HTML5Extension;

class Twital implements Compiler
{

    const NS = 'urn:goetas:twital';

    protected $extensionsInitialized = false;

    /**
     *
     * @var array
     */
    private $attributes = array();

    /**
     *
     * @var array
     */
    private $nodes = array();

    /**
     *
     * @var array
     */
    private $sourceAdapters = array();

    /**
     *
     * @var array
     */
    private $extensions = array();

    protected $defaultSourceAdapter;


    public function __construct($defaultAdapter = 'xml', array $options = array(), $addDefaultExtensions = true)
    {
        $this->defaultAdapter = $defaultAdapter;
        $this->options = $options;

        $this->addExtension(new CoreExtension());

        if ($addDefaultExtensions){
            $this->addExtension(new HTML5Extension());
        }
        // $this->addExtension(new I18nExtension());
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
        if (! $this->extensionsInitialized) {
            foreach ($this->getExtensions() as $extension) {
                $this->attributes = array_merge_recursive($this->attributes, $extension->getAttributes());
                $this->nodes = array_merge_recursive($this->nodes, $extension->getNodes());
            }
            $this->extensionsInitialized = true;
        }
    }

    public function compile($source, $name = null)
    {
        $this->initExtensions();

        $adapter = $this->getSourceAdapter($name);

        $template = $adapter->load($source);

        $dom = $template->getTemplate();

        $this->compileChilds($dom, new CompilationContext($dom, $this, isset($this->options['lexer']) ? $this->options['lexer'] : array()));

        $source = $adapter->dump($template);

        return $source;
    }

    public function compileElement(\DOMElement $node, CompilationContext $context)
    {
        $nodes = $this->getNodes();
        if (isset($nodes[$node->namespaceURI][$node->localName])) {
            $nodes[$node->namespaceURI][$node->localName]->visit($node, $context);
        } elseif (isset($nodes[$node->namespaceURI]['__base__'])) {
            $nodes[$node->namespaceURI]['__base__']->visit($node, $context);
        } else {
            if ($node->namespaceURI === Twital::NS) {
                throw new Exception("Can't handle the {$node->namespaceURI}#{$node->localName} node at line ".$node->getLineNo());
            }
            if ($this->compileAttributes($node, $context)) {
                $this->compileChilds($node, $context);
            }
        }
    }

    public function compileAttributes(\DOMNode $node, CompilationContext $context)
    {
        $node->attributes = $this->compiler->getAttributes();
        $continueNode = true;
        foreach (iterator_to_array($node->attributes) as $attr) {
            if (! $attr->ownerElement) {
                continue;
            } elseif (isset($attributes[$attr->namespaceURI][$attr->localName])) {
                $attPlugin = $attributes[$attr->namespaceURI][$attr->localName];
            } elseif (isset($attributes[$attr->namespaceURI]['__base__'])) {
                $attPlugin = $attributes[$attr->namespaceURI]['__base__'];
            } elseif ($attr->namespaceURI === Twital::NS) {
                throw new Exception("Can't handle the {$attr->namespaceURI}#{$attr->localName} attribute on {$node->namespaceURI}#{$node->localName} node at line ".$attr->getLineNo());
            } else {
                continue;
            }

            $return = $attPlugin->visit($attr, $context);
            if ($return !== null) {
                $continueNode = $continueNode && ($return & Attribute::STOP_NODE);
                if ($return & Attribute::STOP_ATTRIBUTE) {
                    break;
                }
            }
        }

        return $continueNode;
    }

    public function compileChilds(\DOMNode $node, CompilationContext $context)
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                $this->compileElement($child, $context);
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
        $adapter = $this->getRootSourceAdapter();

        foreach ($this->getExtensions() as $extension) {
            if ($newAdaper = $extension->getSourceAdapter($name)){
                $adapter = $newAdaper;
            }
        }
        return $adapter;
    }

    /**
     *
     * @param string $name
     * @throws Exception
     * @return SourceAdapter
     */
    protected function getRootSourceAdapter($name)
    {
        $adapter = null;
        foreach ($this->getExtensions() as $extension) {
            if ($newAdaper = $extension->getRootSourceAdapter($name)){
                $adapter = $newAdaper;
            }
        }

        if (! $adapter ) {
            throw new Exception("Can't find a source adapter for a file called {$name}. Do you have configured it well?");
        }

        return $adapter;
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

    public function getSourceAdapters()
    {
        return $this->sourceAdapters;
    }

}
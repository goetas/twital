<?php
namespace Goetas\Twital;

use DOMNode;
use goetas\xml;
use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\HTML5Extension;
use Goetas\Twital\Extension\I18nExtension;

class Compiler
{

    const NS = 'urn:goetas:twital';

    /**
     *
     * @var array
     */
    protected $attributes = array();

    /**
     *
     * @var array
     */
    protected $extensions = array();

    /**
     *
     * @var array
     */
    protected $node = array();

    /**
     *
     * @var array
     */
    protected $preFlter = array();

    /**
     *
     * @var array
     */
    protected $domLoaders = array();

    /**
     *
     * @var array
     */
    protected $postFilter = array();

    protected $domLoader;

    public function __construct($domLoader = 'html5')
    {
        $this->domLoader = 'html5';

        $this->addExtension(new CoreExtension());
        $this->addExtension(new I18nExtension());
        $this->addExtension(new HTML5Extension());

    }
    public function addExtension(Extension $extension)
    {
        $this->extensions[] = $extension;
    }
    protected $extensionsinitialized = false;
    protected function initExtensions()
    {
        if (!$this->extensionsinitialized) {
            foreach ($this->extensions as $extensions) {
                $this->attributes = array_merge_recursive($this->attributes, $extensions->getAttributes());
                $this->node = array_merge_recursive($this->node, $extensions->getNodes());
                $this->preFlter = array_merge($this->preFlter, $extensions->getPreFilters());
                $this->postFilter = array_merge($this->postFilter, $extensions->getPostFilters());
                $this->domLoaders = array_merge($this->domLoaders, $extensions->getDomLoaders());
            }
            $this->extensionsinitialized = true;
        }
    }
    protected function loadDOM($string)
    {
        return $this->domLoaders[$this->domLoader]->createDOM($string);
    }

    /**
     * Ritorna una stringa del DOM presente in $xml
     *
     * @param
     *            $xml
     */
    public function compile($source)
    {

        $this->initExtensions();

        foreach ($this->preFlter as $filter) {
            $source = call_user_func($filter, $source);
        }

        $domLoader = $this->getDomLoader();

        $xml = $domLoader->createDOM($source);

        $metadata = $domLoader->collectMetadata($xml, $source);

        $this->applyTemplatesToChilds($xml);

        $source = $domLoader->dumpDOM($xml, $metadata);

        foreach ($this->postFilter as $filter) {
            $source = call_user_func($filter, $source);
        }

        return $source;
    }

    protected function getDomLoader()
    {
        if (! isset($this->domLoaders[$this->domLoader])) {
            throw new Exception("Can't find a domloader called {$this->domLoader}");
        }

        return $this->domLoaders[$this->domLoader];
    }

    public function applyTemplates(\DOMElement $node)
    {
        if (isset($this->node[$node->namespaceURI][$node->localName])) {
            $this->node[$node->namespaceURI][$node->localName]->visit($node, $this);
        } elseif (isset($this->node[$node->namespaceURI]['__base__'])) {
            $this->node[$node->namespaceURI]['__base__']->visit($node, $this);
        } else {
            if ($node->namespaceURI === self::NS) {
                throw new Exception("Nodo sconosciuto {$node->namespaceURI}#{$node->localName}");
            }
            if ($this->applyTemplatesToAttributes($node)) {
                $this->applyTemplatesToChilds($node);
            }
        }
    }

    public function applyTemplatesToAttributes(\DOMNode $node)
    {
        $continueNode = true;
        if ($node->childNodes) {
            foreach (iterator_to_array($node->attributes) as $attr) {
                if (!$attr->ownerElement) {
                    continue;
                } elseif (isset($this->attributes[$attr->namespaceURI][$attr->localName])) {
                    $attPlugin = $this->attributes[$attr->namespaceURI][$attr->localName];
                } elseif (isset($this->attributes[$attr->namespaceURI]['__base__'])) {
                    $attPlugin = $this->attributes[$attr->namespaceURI]['__base__'];
                } else {
                    continue;
                }

                $return = $attPlugin->visit($attr, $this);
                if ($return !== null) {
                    $continueNode = $continueNode && ($return & Attribute::STOP_NODE);
                    if ($return & Attribute::STOP_ATTRIBUTE) {
                        break;
                    }
                }
            }
        }

        return $continueNode;
    }

    public function applyTemplatesToChilds(\DOMNode $node)
    {
        if ($node->childNodes) {
            foreach (iterator_to_array($node->childNodes) as $child) {
                if ($child instanceof \DOMElement) {
                    $this->applyTemplates($child);
                }
            }
        }
    }
}

<?php
namespace Goetas\Twital;

use DOMNode;
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
    protected $extension = array();

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
    protected $domDumpers = array();

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
    protected $domLoader;
    protected $domDumper;

    protected $twital;

    public function __construct(TwitalEnviroment $twital, $domLoader = 'xml', $domDumper = 'xml')
    {
        $this->domLoader = $domLoader;
        $this->domDumper = $domDumper;
        $this->twital = $twital;

        $this->addExtension(new CoreExtension());
        $this->addExtension(new I18nExtension());
        $this->addExtension(new HTML5Extension());

    }
    public function addExtension(Extension $extension)
    {
        $this->extensions[] = $extension;
    }
    protected $extensioninitialized = false;
    protected function initExtensions()
    {
        if (!$this->extensionsinitialized) {
            foreach ($this->extensions as $extension) {
                $this->attributes = array_merge_recursive($this->attributes, $extension->getAttributes());
                $this->node = array_merge_recursive($this->node, $extension->getNodes());
                $this->preFlter = array_merge($this->preFlter, $extension->getPreFilters());
                $this->postFilter = array_merge($this->postFilter, $extension->getPostFilters());
                $this->domLoaders = array_merge($this->domLoaders, $extension->getLoaders());
                $this->domDumpers = array_merge($this->domDumpers, $extension->getDumpers());
                $this->customNamespaces = array_merge($this->customNamespaces, $extension->getPrefixes());
            }
            $this->extensionsinitialized = true;
        }

    }

    /**
     *
     * @param $source
     * @return string
     */
    public function compile($source)
    {

        $this->initExtensions();

        foreach ($this->preFlter as $filter) {
            $source = call_user_func($filter, $source);
        }

        $loader = $this->getLoader($this->domLoader);
        $dumper = $this->getDumper($this->domDumper);

        $xml = $loader->load($source);

        $this->checkDocumentNamespaces($xml);

        $metadata = $dumper->collectMetadata($xml, $source);


        $this->context = new CompilationContext($xml, $this->twital->getLexer(), $this);

        $this->applyTemplatesToChilds($xml);

        $source = $dumper->dump($xml, $metadata);

        foreach ($this->postFilter as $filter) {
            $source = call_user_func($filter, $source);
        }

        return $source;
    }
    public function checkDocumentNamespaces(\DOMDocument $dom){
        foreach (iterator_to_array($dom->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                self::checkNamespaces($child, $this->customNamespaces);
            }
        }
    }
    protected static function checkNamespaces(\DOMElement $element, array $namespaces = array()){

        if ($element->namespaceURI===null && preg_match('/^([a-z0-9\-]+):(.+)$/i', $element->nodeName, $mch) && isset($namespaces[$mch[1]])){

            $oldElement = $element;
            $element = $element->ownerDocument->createElementNS($namespaces[$mch[1]], $element->nodeName);

            // copy attrs
            foreach (iterator_to_array($oldElement->attributes) as $attr) {
                $oldElement->removeAttributeNode($attr);
                if ($attr->namespaceURI) {
                    $element->setAttributeNodeNS($attr);
                } else {
                    $element->setAttributeNode($attr);
                }
            }
            // copy childs
            while ($child = $oldElement->firstChild) {
                $oldElement->removeChild($child);
                $element->appendChild($child);
            }
            $oldElement->parentNode->replaceChild($element, $oldElement);
        }
        // fix attrs
        foreach (iterator_to_array($element->attributes) as $attr) {
            if ($attr->namespaceURI===null && preg_match('/^([a-z0-9\-]+):/i', $attr->name, $mch) && isset($namespaces[$mch[1]])){

                $element->removeAttributeNode($attr);
                $element->setAttributeNS($namespaces[$mch[1]], $attr->name, $attr->value);
            }
        }
        foreach (iterator_to_array($element->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                self::checkNamespaces($child, $namespaces);
            }
        }
    }
    /**
     *
     * @param string $loader
     * @throws Exception
     * @return Loader
     */
    protected function getLoader($loader)
    {
        if (! isset($this->domLoaders[$loader])) {
            throw new Exception("Can't find a loader called {$loader}");
        }

        return $this->domLoaders[$loader];
    }
    /**
     *
     * @param string $dumper
     * @throws Exception
     * @return Dumper
     */
    protected function getDumper($dumper)
    {
        if (! isset($this->domDumpers[$dumper])) {
            throw new Exception("Can't find a dumper called {$dumper}");
        }

        return $this->domDumpers[$dumper];
    }

    public function applyTemplates(\DOMElement $node)
    {
        if (isset($this->node[$node->namespaceURI][$node->localName])) {
            $this->node[$node->namespaceURI][$node->localName]->visit($node, $this->context);
        } elseif (isset($this->node[$node->namespaceURI]['__base__'])) {
            $this->node[$node->namespaceURI]['__base__']->visit($node, $this->context);
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

                $return = $attPlugin->visit($attr, $this->context);
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

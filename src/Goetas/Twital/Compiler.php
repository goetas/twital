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
    protected $domDumpers = array();

    /**
     *
     * @var array
     */
    protected $postFilter = array();

    protected $domLoader;
    protected $domDumper;

    protected $twital;

    public function __construct(TwitalEnviroment $twital, $domLoader = 'html5', $domDumper = 'html5')
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
    protected $extensionsinitialized = false;
    protected function initExtensions()
    {
        if (!$this->extensionsinitialized) {
            foreach ($this->extensions as $extensions) {
                $this->attributes = array_merge_recursive($this->attributes, $extensions->getAttributes());
                $this->node = array_merge_recursive($this->node, $extensions->getNodes());
                $this->preFlter = array_merge($this->preFlter, $extensions->getPreFilters());
                $this->postFilter = array_merge($this->postFilter, $extensions->getPostFilters());
                $this->domLoaders = array_merge($this->domLoaders, $extensions->getLoaders());
                $this->domDumpers = array_merge($this->domDumpers, $extensions->getDumpers());
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

        $metadata = $dumper->collectMetadata($xml, $source);


        $context = new CompilationContext($xml, $this->twital->getLexer(), $this);

        $this->applyTemplatesToChilds($xml);

        $source = $dumper->dump($xml, $metadata);

        foreach ($this->postFilter as $filter) {
            $source = call_user_func($filter, $source);
        }

        return $source;
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
            $this->node[$node->namespaceURI][$node->localName]->visit($node, $this);
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

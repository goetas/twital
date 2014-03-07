<?php
namespace Goetas\Twital;

use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\I18nExtension;
use Goetas\Twital\Extension\HTML5Extension;
use Goetas\Twital\SourceAdapter\XMLAdapter;

class Twital
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
    private $extensions = array();

    public function __construct(array $options = array(), $addDefaultExtensions = true)
    {
        $this->options = $options;

        $this->addExtension(new CoreExtension());

        if ($addDefaultExtensions) {
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

    public function compile(SourceAdapter $adapter, $source)
    {
        $this->initExtensions();

        $template = $adapter->load($source);

        $context = new CompilationContext($this, isset($this->options['lexer']) ? $this->options['lexer'] : array());

        $context->compile($template->getTemplate());

        $source = $adapter->dump($template);

        return $source;
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
}
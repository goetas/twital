<?php
namespace Goetas\Twital;

use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\I18nExtension;
use Goetas\Twital\Extension\HTML5Extension;
use Goetas\Twital\SourceAdapter\XMLAdapter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use Goetas\Twital\EventDispatcher\SourceEvent;
use Goetas\Twital\EventDispatcher\TemplateEvent;

class Twital
{

    const NS = 'urn:goetas:twital';

    protected $extensionsInitialized = false;

    /**
     *
     * @var EventDispatcher
     */
    protected $dispatcher;
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
        $this->dispatcher = new EventDispatcher();

        $this->addExtension(new CoreExtension());

        if ($addDefaultExtensions) {
            $this->addExtension(new HTML5Extension());
        }

    }
    public function getE()
    {

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

                foreach ($extension->getSubscribers() as $subscriber){
                    $this->dispatcher->addSubscriber($subscriber);
                }
            }
            $this->extensionsInitialized = true;
        }
    }

    public function compile(SourceAdapter $adapter, $source)
    {
        $this->initExtensions();

        $sourceEvent = new SourceEvent($this, $source);
        $this->dispatcher->dispatch('compiler.pre_load', $sourceEvent);
        $template = $adapter->load($sourceEvent->getTemplate());

        $templateEvent = new TemplateEvent($this, $template);
        $this->dispatcher->dispatch('compiler.post_load', $templateEvent);

        $compiler = new Compiler($this, isset($this->options['lexer']) ? $this->options['lexer'] : array());
        $template = $compiler->compile($templateEvent->getTemplate()->getDocument());

        $templateEvent = new TemplateEvent($this, $template);
        $this->dispatcher->dispatch('compiler.pre_dump', $templateEvent);
        $source = $adapter->dump($templateEvent->getTemplate());

        $sourceEvent = new SourceEvent($this, $source);
        $this->dispatcher->dispatch('compiler.post_dump', $sourceEvent);
        return $sourceEvent->getTemplate();
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
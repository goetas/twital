<?php
namespace Goetas\Twital;

use Goetas\Twital\EventDispatcher\CompilerEvents;
use Goetas\Twital\EventDispatcher\SourceEvent;
use Goetas\Twital\EventDispatcher\TemplateEvent;
use Goetas\Twital\Extension\CoreExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 */
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

    public function __construct(array $options = array())
    {
        $this->options = $options;
        $this->dispatcher = new EventDispatcher();

        $this->addExtension(new CoreExtension());
    }

    /**
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public function getEventDispatcher()
    {
        $this->initExtensions();

        return $this->dispatcher;
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

    /**
     *
     * @param SourceAdapter $adapter
     * @param string $source
     * @return string
     */
    public function compile(SourceAdapter $adapter, $source)
    {
        $this->initExtensions();

        $sourceEvent = new SourceEvent($this, $source);
        $this->dispatch($sourceEvent, CompilerEvents::PRE_LOAD);
        $template = $adapter->load($sourceEvent->getTemplate());

        $templateEvent = new TemplateEvent($this, $template);
        $this->dispatch($templateEvent, CompilerEvents::POST_LOAD);

        $compiler = new Compiler($this, isset($this->options['lexer']) ? $this->options['lexer'] : array());
        $compiler->compile($templateEvent->getTemplate()->getDocument());

        $templateEvent = new TemplateEvent($this, $templateEvent->getTemplate());
        $this->dispatch($templateEvent, CompilerEvents::PRE_DUMP);
        $source = $adapter->dump($templateEvent->getTemplate());

        $sourceEvent = new SourceEvent($this, $source);
        $this->dispatch($sourceEvent, CompilerEvents::POST_DUMP);

        return $sourceEvent->getTemplate();
    }

    public function addExtension(Extension $extension)
    {
        $this->extensionsInitialized = false;

        return $this->extensions[] = $extension;
    }

    public function setExtensions(array $extensions)
    {
        $this->extensionsInitialized = false;

        $this->extensions = $extensions;
    }

    /**
     * @return Extension[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    protected function initExtensions()
    {
        if (!$this->extensionsInitialized) {
            foreach ($this->getExtensions() as $extension) {
                $this->attributes = array_merge_recursive($this->attributes, $extension->getAttributes());
                $this->nodes = array_merge_recursive($this->nodes, $extension->getNodes());

                foreach ($extension->getSubscribers() as $subscriber) {
                    $this->dispatcher->addSubscriber($subscriber);
                }
            }
            $this->extensionsInitialized = true;
        }
    }

    protected function dispatch($event, $name)
    {
        if ($this->dispatcher instanceof EventDispatcherInterface) {
            $this->dispatcher->dispatch($event, $name);
        } else {
            $this->dispatcher->dispatch($name, $event);
        }
    }
}

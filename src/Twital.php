<?php
namespace Goetas\Twital;

use Goetas\Twital\EventDispatcher\CompilerEvents;
use Goetas\Twital\Extension\CoreExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Goetas\Twital\EventDispatcher\SourceEvent;
use Goetas\Twital\EventDispatcher\TemplateEvent;

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
        $this->dispatcher->dispatch(CompilerEvents::PRE_LOAD, $sourceEvent);
        $template = $adapter->load($sourceEvent->getTemplate());

        $templateEvent = new TemplateEvent($this, $template);
        $this->dispatcher->dispatch(CompilerEvents::POST_LOAD, $templateEvent);

        $compiler = new Compiler($this, isset($this->options['lexer']) ? $this->options['lexer'] : array());
        $compiler->compile($templateEvent->getTemplate()->getDocument());

        $templateEvent = new TemplateEvent($this, $templateEvent->getTemplate());
        $this->dispatcher->dispatch(CompilerEvents::PRE_DUMP, $templateEvent);
        $source = $adapter->dump($templateEvent->getTemplate());

        $sourceEvent = new SourceEvent($this, $source);
        $this->dispatcher->dispatch(CompilerEvents::POST_DUMP, $sourceEvent);

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
}

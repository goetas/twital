<?php
namespace Goetas\Twital;

use Goetas\Twital\Extension\CoreExtension;
use Goetas\Twital\Extension\I18nExtension;
use Goetas\Twital\Extension\HTML5Extension;
use Goetas\Twital\SourceAdapter\XMLAdapter;

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

    public function compile($source, $name)
    {
        $this->initExtensions();

        $adapter = $this->getSourceAdapter($name);

        $template = $adapter->load($source);

        $context = new CompilationContext($this, isset($this->options['lexer']) ? $this->options['lexer'] : array());

        $context->compile($template->getTemplate());

        $source = $adapter->dump($template);

        return $source;
    }
    public function setAdapter($pattern, $adapter)
    {
    	$patterns[$adapter][]=$pattern;
    }
    public function getAdapter($name)
    {
        $chid = null;
        foreach($patterns as $id => $patts){
            foreach ($patts as $patt){
            	if(preg_match($patt, $name)){
            	    $chid = $id;
            		continue 2;
            	}
            }
        }
        return $chid;
    }

    /**
     *
     * @param string $name
     * @throws Exception
     * @return SourceAdapter
     */
    protected function getSourceAdapter($name)
    {
        return array(
        	'/*.xml/i'=>new XMLAdapter(),
        );
    }
    /**
     *
     * @param string $name
     * @throws Exception
     * @return SourceAdapter
     */
    protected function getSourceAdapter($name)
    {
        $adapter = $this->getRootSourceAdapter($name);




        foreach ($this->getExtensions() as $extension) {
            if ($newAdaper = $extension->getSourceAdapter($name)) {
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
            if ($newAdaper = $extension->getRootSourceAdapter($name)) {
                $adapter = $newAdaper;
            }
        }

        if (! $adapter) {
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
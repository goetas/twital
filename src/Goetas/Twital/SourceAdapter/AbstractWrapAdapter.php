<?php
namespace Goetas\Twital\SourceAdapter;

use Goetas\Twital\SourceAdapter;
use Goetas\Twital\NamespaceAdapter;
use Goetas\Twital\Template;

abstract class AbstractWrapAdapter implements SourceAdapter
{

    /**
     * The wrapped adapter
     * @var SourceAdapter
     */
    protected $adapter;

    public function __construct(SourceAdapter $adapter)
    {
        $this->adapter = $adapter;
    }
    /**
     * (non-PHPdoc)
     * @see \Goetas\Twital\SourceAdapter::load()
     */
    public function load($xml)
    {
        return $this->adapter->load($xml);
    }
    /**
     * (non-PHPdoc)
     * @see \Goetas\Twital\SourceAdapter::dump()
     */
    public function dump(Template $template)
    {
        return $this->adapter->dump($template);
    }
}
<?php
namespace Goetas\Twital\SourceAdapter;

use Goetas\Twital\SourceAdapter;
use Goetas\Twital\Template;

class PostSourceFilterAdapter extends AbstractWrapAdapter
{

    protected $postFilters = array();

    public function __construct(SourceAdapter $adapter, array $postFilters)
    {
        parent::__construct($adapter);
        $this->postFilters = $postFilters;
    }

    public function dump(Template $template)
    {
        $source = $this->adapter->dump($template);
        $source = $this->applyPostFilters($source);
        return $source;
    }

    protected function applyPostFilters($source)
    {
        foreach ($this->getPostFilters() as $filter) {
            $source = call_user_func($filter, $source);
        }
        return $source;
    }
}
<?php
namespace Goetas\Twital\SourceAdapter;

use Goetas\Twital\SourceAdapter;

class PreSourceFilterAdapter extends AbstractWrapAdapter
{
    /**
     *
     * @var array
     */
    protected $preFilters = array();

    public function __construct(SourceAdapter $adapter, array $preFilters)
    {
        parent::__construct($adapter);
        $this->preFilters = $preFilters;
    }

    public function load($source)
    {
        $source = $this->applyPostFilters($source);
        return $this->adapter->load($source);
    }
    protected function applyPostFilters($source)
    {
        foreach ($this->preFilters as $filter) {
            $source = call_user_func($filter, $source);
        }
        return $source;
    }
}
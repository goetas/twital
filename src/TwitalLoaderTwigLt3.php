<?php

namespace Goetas\Twital;

/**
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
abstract class TwitalLoaderTwigLt3
{
    use TwitalLoaderTrait;

    public function __construct($loader = null, Twital $twital = null, $addDefaults = true)
    {
        $this->doConstruct($loader, $twital, $addDefaults);
    }

    public function getCacheKey($name)
    {
        return $this->loader->getCacheKey($name);
    }

    public function isFresh($name, $time)
    {
        return $this->loader->isFresh($name, $time);
    }

    abstract public function getSourceContext($name);

    public function exists($name)
    {
        return $this->doExists($name);
    }
}

<?php

namespace Goetas\Twital;

/**
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
abstract class TwitalLoaderTwigLt3
{
    use TwitalLoaderTrait;

    public function getCacheKey($name)
    {
        return $this->loader->getCacheKey($name);
    }

    public function isFresh($name, $time)
    {
        return $this->loader->isFresh($name, $time);
    }

    public function getSourceContext($name)
    {
        return $this->doGetSourceContext($name);
    }

    public function exists($name)
    {
        return $this->doExists($name);
    }
}

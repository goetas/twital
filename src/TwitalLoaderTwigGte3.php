<?php

namespace Goetas\Twital;

use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
abstract class TwitalLoaderTwigGte3 implements LoaderInterface
{
    use TwitalLoaderTrait;

    public function getSourceContext(string $name): Source
    {
        return $this->doGetSourceContext($name);
    }

    public function getCacheKey(string $name): string
    {
        return $this->loader->getCacheKey($name);
    }

    public function isFresh(string $name, int $time): bool
    {
        return $this->loader->isFresh($name, $time);
    }

    public function exists(string $name)
    {
        return $this->doExists($name);
    }
}

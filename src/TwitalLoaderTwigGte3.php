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

    public function __construct(LoaderInterface $loader = null, Twital $twital = null, $addDefaults = true)
    {
        $this->doConstruct($loader, $twital, $addDefaults);
    }

    public function getSourceContext(string $name): Source
    {
        $context = $this->doGetSourceContext($name);

        return new Source($context[0], $context[1], $context[2]);
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

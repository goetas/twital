<?php
namespace Goetas\Twital\Tests\Twig;

class StringLoader implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface
{
    public function getSource($name)
    {
        return $name;
    }

    public function exists($name)
    {
        return true;
    }

    public function getCacheKey($name)
    {
        return $name;
    }

    public function isFresh($name, $time)
    {
        return true;
    }
}
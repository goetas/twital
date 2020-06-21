<?php
namespace Goetas\Twital;

use Twig\Loader\ExistsLoaderInterface;
use Twig\Loader\LoaderInterface;
use Twig\Loader\SourceContextLoaderInterface;
use Twig\Source;

if (interface_exists(\Twig_ExistsLoaderInterface::class)) { // Twig 1
    abstract class BaseTwitalLoader extends TwitalLoaderTwigLt3 implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface, \Twig_SourceContextLoaderInterface
    {
        public function __construct(\Twig_LoaderInterface $loader = null, Twital $twital = null, $addDefaults = true)
        {
            parent::__construct($loader, $twital, $addDefaults);
        }

        public function getSourceContext($name)
        {
            $context = $this->doGetSourceContext($name);

            return new \Twig_Source($context[0], $context[1], $context[2]);
        }

        /**
         * {@inheritdoc}
         */
        public function getSource($name)
        {
            return $this->getSourceContext($name)->getCode();
        }
    }
} elseif (interface_exists(ExistsLoaderInterface::class)) { // Twig 2
    abstract class BaseTwitalLoader extends TwitalLoaderTwigLt3 implements LoaderInterface, ExistsLoaderInterface, SourceContextLoaderInterface
    {
        public function __construct(LoaderInterface $loader = null, Twital $twital = null, $addDefaults = true)
        {
            parent::__construct($loader, $twital, $addDefaults);
        }

        public function getSourceContext($name)
        {
            $context = $this->doGetSourceContext($name);

            return new Source($context[0], $context[1], $context[2]);
        }
    }
} else { // Twig 3
    abstract class BaseTwitalLoader extends TwitalLoaderTwigGte3
    {
    }
}

/**
 * This is a Twital Loader.
 * Compiles a Twital template into a Twig template.
 *
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class TwitalLoader extends BaseTwitalLoader
{
}

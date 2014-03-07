<?php

use Goetas\Twital\EventSubscriber\DOMMessSubscriber;
use Goetas\Twital\EventSubscriber\CustomNamespaceSubscriber;
error_reporting(E_ALL | E_STRICT);

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // dependencies were installed via composer - this is the main project
    $classLoader = require __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../../../autoload.php')) {
    // installed as a dependency in `vendor`
    $classLoader = require __DIR__ . '/../../../../../autoload.php';
} else {
    throw new Exception('Can\'t find autoload.php. Did you install dependencies via composer?');
}

/*
$twigLoader = new Twig_Loader_Filesystem();
$twitalLoader = new TwitalLoader($twigLoader, $compiler);

$twitalLoader->addNamePattern('/*.xml.twital/i', new XMLAdapter());
$twitalLoader->addNamePattern('/*.html.twital/i', new HTML5Adapter());
$twitalLoader->addNamePattern('/*.xhtml.twital/i', new XHTMLAdapter());


$twig = new Twig_Environment($twitalLoader);
*/
/*
$loader = new Twig_Loader_Filesystem(array(__DIR__."/suite/templates"));
$twitalLoader = new TwitalLoader($loader);

$twig = new Twig_Environment($twitalLoader);

echo $twig->display("1.twital.xml");
echo "\n\n";
//echo $tal->compile(__DIR__."/suite/templates/foreach.xml");

*/
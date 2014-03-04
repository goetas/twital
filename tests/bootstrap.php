<?php


error_reporting(E_ALL | E_STRICT);

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // dependencies were installed via composer - this is the main project
    $classLoader = require __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../../../autoload.php')) {
    // installed as a dependency in `vendor`
    $classLoader = require __DIR__ . '/../../../../../autoload.php';
} else {
    print('Can\'t find autoload.php. Did you install dependencies via composer?');
}



/*
$loader = new Twig_Loader_Filesystem(array(__DIR__."/suite/templates"));
$twitalLoader = new TwitalLoader($loader);

$twig = new Twig_Environment($twitalLoader);

echo $twig->display("1.twital.xml");
echo "\n\n";
//echo $tal->compile(__DIR__."/suite/templates/foreach.xml");

*/
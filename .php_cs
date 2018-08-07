<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
;

return PhpCsFixer\Config::create()
    ->setRules(array(
        '@PSR2' => true,
        'array_syntax' => array('syntax' => 'long'),
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'ordered_class_elements' => true,
    ))
    ->setFinder($finder)
;

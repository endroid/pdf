<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
    ])
;

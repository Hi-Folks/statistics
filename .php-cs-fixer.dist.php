<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
    ])
    ->setFinder($finder);

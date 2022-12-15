<?php

declare(strict_types=1);

$header = <<<'EOF'
    This file is part of postyou/contao-pdf-metadata.

    (c) POSTYOU Digital- & Filmagentur

    @license LGPL-3.0+
    EOF;

$finder = PhpCsFixer\Finder::create()
    ->exclude('templates')
    ->in([
        __DIR__.'/src',
    ])
;

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP82Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        'header_comment' => ['header' => $header],
    ])
    ->setFinder($finder)
;

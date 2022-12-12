<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-pdf-metadata.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoPdfMetadata\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('contao_pdf_metadata');
        $treeBuilder
            ->getRootNode()
            ->children()
                ->scalarNode('author')
                    ->info('Overwrites the author field in the cleaned PDF file.')
                    ->defaultValue('')
                ->end()
                ->scalarNode('qpdf_path')
                    ->info('The path to the qpdf binary.')
                    ->cannotBeEmpty()
                    ->defaultValue('/usr/bin/qpdf')
                ->end()
                ->scalarNode('exiftool_path')
                    ->info('The path to the exiftool binary.')
                    ->cannotBeEmpty()
                    ->defaultValue('/usr/bin/exiftool')
                ->end()
                ->booleanNode('clean_on_upload')
                    ->info('Clean the metadata of PDF files immediately after they were uploaded.')
                    ->defaultFalse()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

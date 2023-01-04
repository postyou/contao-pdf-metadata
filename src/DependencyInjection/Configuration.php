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

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
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
                ->append($this->addExiftoolNode())
                ->append($this->addQpdfNode())
                ->booleanNode('cleanup_on_upload')
                    ->info('Clean up the metadata of PDF files immediately after uploading.')
                    ->defaultFalse()
                ->end()
                ->arrayNode('metadata')
                    ->info('Overwrites metadata fields in the cleaned PDF file.')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('author')
                            ->defaultValue('')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function addExiftoolNode(): NodeDefinition
    {
        return (new TreeBuilder('exiftool'))
            ->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('path')
                    ->info('Path to the exiftool binary.')
                    ->cannotBeEmpty()
                    ->defaultValue('/usr/bin/exiftool')
                ->end()
                ->arrayNode('env')
                    ->info('Environment variables when running exiftool.')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;
    }

    private function addQpdfNode(): NodeDefinition
    {
        return (new TreeBuilder('qpdf'))
            ->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('path')
                    ->info('Path to the qpdf binary.')
                    ->cannotBeEmpty()
                    ->defaultValue('/usr/bin/qpdf')
                ->end()
                ->arrayNode('env')
                    ->info('Environment variables when running qpdf.')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;        
    }
}

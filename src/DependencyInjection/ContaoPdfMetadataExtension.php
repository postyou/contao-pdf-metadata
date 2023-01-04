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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ContaoPdfMetadataExtension extends Extension
{
    public function load(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));

        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $mergedConfig);

        $container->setParameter('contao_pdf_metadata.qpdf', $config['qpdf']);
        $container->setParameter('contao_pdf_metadata.exiftool', $config['exiftool']);
        $container->setParameter('contao_pdf_metadata.metadata', $config['metadata']);
        $container->setParameter('contao_pdf_metadata.cleanup_on_upload', $config['cleanup_on_upload']);
    }
}

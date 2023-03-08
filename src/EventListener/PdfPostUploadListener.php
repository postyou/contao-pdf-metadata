<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-pdf-metadata.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoPdfMetadata\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Postyou\ContaoPdfMetadata\MetadataCleaner\CleanerUtil;
use Postyou\ContaoPdfMetadata\MetadataCleaner\PdfMetadataCleaner;

#[AsHook('postUpload')]
class PdfPostUploadListener
{
    public function __construct(
        private CleanerUtil $cleanerUtil,
        private PdfMetadataCleaner $pdfMetadataCleaner,
        private bool $cleanupOnUpload,
    ) {
    }

    /**
     * @param string[] $files
     */
    public function __invoke(array $files): void
    {
        if (!$this->cleanupOnUpload) {
            return;
        }

        foreach ($files as $path) {
            // Only consider pdf files
            if ('pdf' !== mb_strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
                continue;
            }

            $result = $this->pdfMetadataCleaner->process($path);

            $this->cleanerUtil->logResult($result);
        }
    }
}

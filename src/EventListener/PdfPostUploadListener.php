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
use Postyou\ContaoPdfMetadata\Metadata\PdfMetadataCleaner;

#[AsHook('postUpload')]
class PdfPostUploadListener
{
    public function __construct(
        private PdfMetadataCleaner $pdfMetadataCleaner,
        private bool $cleanupOnUpload,
    ) {
    }

    public function __invoke(array $files): void
    {
        if (!$this->cleanupOnUpload) {
            return;
        }

        $this->pdfMetadataCleaner->clean($files);
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-pdf-metadata.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoPdfMetadata\Command;

use Contao\CoreBundle\Filesystem\FilesystemItem;
use Contao\CoreBundle\Filesystem\FilesystemItemIterator;
use Contao\CoreBundle\Filesystem\VirtualFilesystemInterface;
use Postyou\ContaoPdfMetadata\Metadata\PdfMetadataCleaner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'contao:pdf-metadata:clean',
    description: 'Clean up the metadata of all PDF files inside the files directory.'
)]
class CleanCommand extends Command
{
    public function __construct(
        private PdfMetadataCleaner $pdfMetadataCleaner,
        private VirtualFilesystemInterface $filesStorage,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->pdfFiles() as $item) {
            ($this->pdfMetadataCleaner)($item->getPath());
        }

        return 0;
    }

    private function pdfFiles(): FilesystemItemIterator
    {
        $files = $this->filesStorage->listContents('.', true, VirtualFilesystemInterface::BYPASS_DBAFS)->files();

        return $files->filter(
            fn (FilesystemItem $item): bool => 'pdf' === mb_strtolower(pathinfo($item->getPath(), PATHINFO_EXTENSION))
        );
    }
}

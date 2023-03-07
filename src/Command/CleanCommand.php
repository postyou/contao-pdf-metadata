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
use Contao\CoreBundle\Filesystem\VirtualFilesystemInterface;
use Postyou\ContaoPdfMetadata\Metadata\PdfMetadataCleaner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'pdf-metadata:clean-all',
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
        $files = $this->filesStorage->listContents('.', true, VirtualFilesystemInterface::BYPASS_DBAFS)->files();

        $files = $files->filter(
            fn (FilesystemItem $file) => 'application/pdf' === $file->getMimeType('application/octet-stream')
        );

        foreach ($files as $file) {
            $output->write('Processing file '.$file->getPath());

            if ($this->pdfMetadataCleaner->clean($file->getPath())) {
                $output->writeln(' <fg=green>✓</>');
            } else {
                $output->writeln(' <fg=red>✗</>');
            }
        }

        return Command::SUCCESS;
    }
}

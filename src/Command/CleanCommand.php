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
use Postyou\ContaoPdfMetadata\MetadataCleaner\PdfMetadataCleaner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'pdf-metadata:clean',
    description: 'Clean up the metadata of PDF files inside the files directory.'
)]
class CleanCommand extends Command
{
    public function __construct(
        private PdfMetadataCleaner $pdfMetadataCleaner,
        private VirtualFilesystemInterface $filesStorage,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::OPTIONAL, 'Optional path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $time = microtime(true);
        $files = $this->getFiles($input->getArgument('path') ?? '');
        $count = iterator_count($files);

        (new SymfonyStyle($input, $output))->info(
            sprintf('Found %s file%s', $count, 1 === $count ? '' : 's')
        );

        foreach ($files as $i => $file) {
            $current = $i + 1;
            $output->write("({$current}/{$count}) Processing {$file->getPath()}");

            $result = $this->pdfMetadataCleaner->process($file->getPath());

            if ($result->success) {
                $output->writeln(' <fg=green>✓ Success</>');
            } else {
                $output->writeln(sprintf(' <fg=red>✗ %s</>', $result->getMessage()));
            }
        }

        $timeTotal = round(microtime(true) - $time, 2);

        (new SymfonyStyle($input, $output))->success("Processing complete in {$timeTotal}s.");

        return Command::SUCCESS;
    }

    protected function getFiles(string $location): FilesystemItemIterator
    {
        $files = $this->filesStorage->listContents($location, true, VirtualFilesystemInterface::BYPASS_DBAFS)->files();

        return $files->filter(
            fn (FilesystemItem $file) => 'application/pdf' === $file->getMimeType('application/octet-stream')
        );
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-pdf-metadata.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoPdfMetadata\Metadata;

use Contao\CoreBundle\Filesystem\FilesystemItem;
use Contao\CoreBundle\Filesystem\FilesystemItemIterator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Process\Process;

class PdfMetadataCleaner
{
    public function __construct(
        private LoggerInterface $contaoErrorLogger,
        private LoggerInterface $contaoFilesLogger,
        private string $projectDir,
        private array $metadata,
        private array $qpdfConfig,
        private array $exiftoolConfig,
    ) {
    }

    public function clean(FilesystemItemIterator|array $files): void
    {
        if ($files instanceof FilesystemItemIterator) {
            $files = array_map(fn (FilesystemItem $item): string => $item->getPath(), $files->toArray());
        }

        foreach ($files as $path) {
            if ('pdf' !== mb_strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
                continue;
            }

            $this->cleanFile($path);
        }
    }

    private function cleanFile(string $path): void
    {
        if (!str_starts_with($path, 'files/')) {
            $path = Path::join('files', $path);
        }

        $absPath = Path::join($this->projectDir, $path);

        $tmpName = $this->tmpName($path);

        $exiftool = new Process([
            $this->exiftoolConfig['path'],
            '-all=',
            "-Author={$this->metadata['author']}",
            '-tagsfromfile', '@',
            '-title',
            '-keywords',
            '-subject',
            '-description',
            $absPath,
            '-o', $tmpName,
        ], env: $this->exiftoolConfig['env']);

        $qpdf = new Process([
            $this->qpdfConfig['path'],
            '--linearize',
            $tmpName,
            $absPath,
        ], env: $this->qpdfConfig['env']);

        if (0 !== $exiftool->run()) {
            $this->contaoErrorLogger->error(sprintf('File "%s" could not be processed with ExifTool: %s', $path, $exiftool->getErrorOutput()));

            return;
        }

        if (0 !== $qpdf->run()) {
            $this->contaoErrorLogger->error(sprintf('File "%s" could not be processed with QPDF: %s', $path, $qpdf->getErrorOutput()));

            return;
        }

        $this->contaoFilesLogger->info(sprintf('File "%s" has been cleaned up', $path));
    }

    private function tmpName(string $path): string
    {
        $name = pathinfo($path, PATHINFO_FILENAME);
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        $tmpName = $name.'.'.uniqid().'.'.$ext;

        return Path::join($this->projectDir, 'system/tmp', $tmpName);
    }
}

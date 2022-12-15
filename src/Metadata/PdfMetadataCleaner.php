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

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Process\Process;

class PdfMetadataCleaner
{
    public function __construct(
        private LoggerInterface $contaoErrorLogger,
        private LoggerInterface $contaoFilesLogger,
        private string $projectDir,
        private string $author,
        private string $exiftoolPath,
        private string $qpdfPath,
    ) {
    }

    public function __invoke(string $path): void
    {
        if (!str_starts_with($path, 'files/')) {
            $path = Path::join('files', $path);
        }

        $absPath = Path::join($this->projectDir, $path);

        $tmpName = $this->tmpName($path);

        $exiftool = new Process([
            $this->exiftoolPath,
            '-all=',
            "-Author={$this->author}",
            '-tagsfromfile', '@',
            '-title',
            '-keywords',
            '-subject',
            '-description',
            $absPath,
            '-o', $tmpName,
        ]);

        $qpdf = new Process([
            $this->qpdfPath,
            '--linearize',
            $tmpName,
            $absPath,
        ]);

        if (0 !== $exiftool->run()) {
            $this->logError($path, $exiftool->getOutput());

            return;
        }

        if (0 !== $qpdf->run()) {
            $this->logError($path, $qpdf->getOutput());

            return;
        }

        $this->contaoFilesLogger->info("Datei {$path} wurde erfolgreich bereinigt.");
    }

    private function logError(string $path, string $message): void
    {
        $this->contaoErrorLogger->error("Datei {$path} konnte nicht bereinigt werden ({$message}).");
    }

    private function tmpName(string $path): string
    {
        $name = pathinfo($path, PATHINFO_FILENAME);
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        $tmpName = $name.'.'.uniqid().'.'.$ext;

        return Path::join($this->projectDir, 'system/tmp', $tmpName);
    }
}

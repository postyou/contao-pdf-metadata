<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-pdf-metadata.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoPdfMetadata\MetadataCleaner;

use Postyou\ContaoPdfMetadata\Dto\ProcessResult;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Process\Process;

class PdfMetadataCleaner
{
    public function __construct(
        private CleanerUtil $cleanerUtil,
        private string $projectDir,
        private array $metadata,
        private array $qpdfConfig,
        private array $exiftoolConfig,
    ) {
    }

    public function process(string $path): ProcessResult
    {
        // Ensure that the path starts with files/
        if (!str_starts_with($path, 'files/')) {
            $path = Path::join('files', $path);
        }

        $absPath = Path::join($this->projectDir, $path);
        $tmpName = $this->cleanerUtil->tmpName($path);

        // Run the exiftool process
        $exiftool = $this->runExiftool($absPath, $tmpName);

        if (0 !== $exiftool->getExitCode()) {
            return new ProcessResult(false, $path, 'exiftool', $exiftool);
        }

        // Run the qpdf process
        $qpdf = $this->runQpdf($absPath, $tmpName);

        if (0 !== $qpdf->getExitCode()) {
            return new ProcessResult(false, $path, 'qpdf', $qpdf);
        }

        return new ProcessResult(true, $path);
    }

    private function runExiftool(string $absPath, string $tmpName): Process
    {
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

        $exiftool->run();

        return $exiftool;
    }

    private function runQpdf(string $absPath, string $tmpName): Process
    {
        $qpdf = new Process([
            $this->qpdfConfig['path'],
            '--linearize',
            $tmpName,
            $absPath,
        ], env: $this->qpdfConfig['env']);

        $qpdf->run();

        return $qpdf;
    }
}

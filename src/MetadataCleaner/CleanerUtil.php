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
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Path;

class CleanerUtil
{
    public function __construct(
        private LoggerInterface $contaoErrorLogger,
        private LoggerInterface $contaoFilesLogger,
        private string $projectDir,
    ) {
    }

    public function logResult(ProcessResult $result): void
    {
        if ($result->success) {
            $this->contaoFilesLogger->info("File {$result->path} has been cleaned up");

            return;
        }

        $this->contaoErrorLogger->error(
            "File {$result->path} could not be processed with {$result->name}: {$result->process->getErrorOutput()}"
        );
    }

    public function tmpName(string $path): string
    {
        $name = pathinfo($path, PATHINFO_FILENAME);
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        $tmpName = $name.'.'.uniqid().'.'.$ext;

        return Path::join($this->projectDir, 'system/tmp', $tmpName);
    }
}

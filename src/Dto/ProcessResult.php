<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-pdf-metadata.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoPdfMetadata\Dto;

use Symfony\Component\Process\Process;

class ProcessResult
{
    public function __construct(
        public bool $success,
        public string $path,
        public ?string $name = null,
        public ?Process $process = null,
    ) {
    }

    public function getMessage()
    {
        return rtrim($this->process?->getErrorOutput() ?? '');
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-pdf-metadata.
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoPdfMetadata;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoPdfMetadataBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}

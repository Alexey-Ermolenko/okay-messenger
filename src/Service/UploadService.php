<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class UploadService
{
    private const LINK_BOOK_PATTERN = '/upload/%d/%s';

    public function __construct(
        private readonly Filesystem $fs,
        private readonly string $uploadDir
    ) {
    }
}

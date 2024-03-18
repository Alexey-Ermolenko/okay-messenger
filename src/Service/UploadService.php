<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\UploadFileInvalidTypeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class UploadService
{
    private const LINK_BOOK_PATTERN = '/upload/%d/%s';

    public function __construct(
        private readonly Filesystem $fs,
        private readonly string $uploadDir
    ) {
    }
}

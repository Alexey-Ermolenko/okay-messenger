<?php

declare(strict_types=1);

namespace App\Util\RawLogBuffer\PersistService\DTO;

use DateTimeImmutable;

final readonly class BaseLog
{
    public function __construct(
        public DateTimeImmutable $requestedAt,
        public DateTimeImmutable $respondedAt,
        public string $requestType,
        public string $status,
        public string $requestHeaders,
        public string $responseHeaders,
        public ?string $transportException,
    ) {
    }
}

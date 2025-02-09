<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class RawLogDTO
{
    public function __construct(
        public ?int    $id = null,
        public ?string $requestedAt = null,
        public ?string $respondedAt = null,
        public ?string $status = null,
        public ?string $requestHeaders = null,
        public ?string $requestBody = null,
        public ?string $responseHeaders = null,
        public ?string $responseBody = null,
    ) {
    }
}

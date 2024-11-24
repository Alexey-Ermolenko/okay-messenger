<?php

declare(strict_types=1);

namespace App\DTO;

final class RawLogDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $requestedAt = null,
        public readonly ?string $respondedAt = null,
        public readonly ?string $status = null,
        public readonly ?string $requestHeaders = null,
        public readonly ?string $requestBody = null,
        public readonly ?string $responseHeaders = null,
        public readonly ?string $responseBody = null,
    ) {
    }
}

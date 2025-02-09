<?php

declare(strict_types=1);

namespace App\Util\RawLogBuffer\PersistService\DTO;


final readonly class ContentLog
{
    public function __construct(
        string $requestType,
        public string $hash,
        public string $httpCode,
        public string $method,
        public string $url,
        public string $query,
        public string $requestContent,
        public string $responseContent,
    ) {
    }
}

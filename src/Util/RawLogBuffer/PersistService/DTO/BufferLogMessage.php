<?php

declare(strict_types=1);

namespace App\Util\RawLogBuffer\PersistService\DTO;

final readonly class BufferLogMessage
{
    public function __construct(public string $requestId)
    {
    }
}

<?php

declare(strict_types=1);

namespace App\DTO;

final class LogDTO
{
    public function __construct(
        public readonly string $level,
        public readonly string $channel,
        public readonly string $datetime,
        public readonly string $message,
        public readonly string $context,
    ) {
    }
}

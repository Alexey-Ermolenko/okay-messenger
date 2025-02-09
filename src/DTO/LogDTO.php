<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class LogDTO
{
    public function __construct(
        public ?int    $id = null,
        public ?string $entityType = null,
        public ?int    $entityId = null,
        public ?string $createdAt = null,
        public ?int    $user_id = null,
        public ?string $action = null,
        public ?string $requestRoute = null,
        public ?string $data = null,
        public ?string $ipAddress = null,
    ) {
    }
}

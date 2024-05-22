<?php

declare(strict_types=1);

namespace App\DTO;

final class LogDTO
{
    public function __construct(
        public readonly ?int                $id = null,
        public readonly ?string             $entityType = null,
        public readonly ?int                $entityId = null,
        public readonly ?string             $createdAt = null,
        public readonly ?int                $user_id = null,
        public readonly ?string             $action = null,
        public readonly ?string             $requestRoute = null,
        public readonly ?string             $data = null,
        public readonly ?string             $ipAddress = null,
    ) {
    }
}

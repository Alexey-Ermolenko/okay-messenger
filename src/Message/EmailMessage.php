<?php

declare(strict_types=1);

namespace App\Message;

final class EmailMessage
{
    public function __construct(
        private readonly string $content
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
